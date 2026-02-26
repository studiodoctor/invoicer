<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Activity;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::where('user_id', auth()->id())
            ->withCount(['quotes', 'invoices']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $clients = $query->orderBy('company_name')->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        $client = Client::create([
            'user_id' => auth()->id(),
            ...$request->validated(),
        ]);

        Activity::log($client, 'created', 'Client created');

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        // Check ownership
        if ($client->user_id !== auth()->id()) {
            abort(403);
        }

        $client->load(['quotes' => function ($q) {
            $q->latest()->limit(5);
        }, 'invoices' => function ($q) {
            $q->latest()->limit(5);
        }]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            abort(403);
        }

        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            abort(403);
        }

        $client->update($request->validated());

        Activity::log($client, 'updated', 'Client updated');

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            abort(403);
        }

        $client->delete();

        Activity::log($client, 'deleted', 'Client deleted');

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}