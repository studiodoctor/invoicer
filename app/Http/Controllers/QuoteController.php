<?php

namespace App\Http\Controllers;

use App\Enums\QuoteStatus;
use App\Http\Requests\QuoteRequest;
use App\Mail\QuoteMail;
use App\Models\Activity;
use App\Models\Client;
use App\Models\Quote;
use App\Services\PdfService;
use App\Services\QuoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    public function __construct(
        protected QuoteService $quoteService,
        protected PdfService $pdfService
    ) {}

    public function index(Request $request)
    {
        $query = Quote::where('user_id', auth()->id())
            ->with('client');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::where('user_id', auth()->id())->orderBy('company_name')->get();

        return view('quotes.index', compact('quotes', 'clients'));
    }

    public function create()
    {
        $clients = Client::where('user_id', auth()->id())
            ->active()
            ->orderBy('company_name')
            ->get();

        $settings = auth()->user()->settings;

        return view('quotes.create', compact('clients', 'settings'));
    }

    public function store(QuoteRequest $request)
    {
        $quote = $this->quoteService->create($request->validated(), auth()->id());

        return redirect()
            ->route('quotes.show', $quote)
            ->with('success', 'Quote created successfully.');
    }

    public function show(Quote $quote)
    {
        $this->authorize('view', $quote);

        $quote->load(['client', 'items', 'convertedInvoice']);

        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $this->authorize('update', $quote);

        if (!in_array($quote->status, [QuoteStatus::DRAFT])) {
            return redirect()
                ->route('quotes.show', $quote)
                ->with('error', 'This quote cannot be edited.');
        }

        $clients = Client::where('user_id', auth()->id())
            ->active()
            ->orderBy('company_name')
            ->get();

        $quote->load('items');
        $settings = auth()->user()->settings;

        return view('quotes.edit', compact('quote', 'clients', 'settings'));
    }

    public function update(QuoteRequest $request, Quote $quote)
    {
        $this->authorize('update', $quote);

        $quote = $this->quoteService->update($quote, $request->validated());

        return redirect()
            ->route('quotes.show', $quote)
            ->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        $this->authorize('delete', $quote);

        $quote->delete();

        Activity::log($quote, 'deleted', 'Quote deleted');

        return redirect()
            ->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }

    public function send(Quote $quote)
    {
        $this->authorize('update', $quote);

        Mail::to($quote->client->email)->send(new QuoteMail($quote));

        $quote->markAsSent();

        Activity::log($quote, 'sent', 'Quote sent to client', ['email' => $quote->client->email]);

        return redirect()
            ->route('quotes.show', $quote)
            ->with('success', 'Quote sent successfully.');
    }

    public function pdf(Quote $quote)
    {
        $this->authorize('view', $quote);

        $pdf = $this->pdfService->generateQuotePdf($quote);

        return $pdf->download("{$quote->quote_number}.pdf");
    }

    public function preview(Quote $quote)
    {
        $this->authorize('view', $quote);

        $pdf = $this->pdfService->generateQuotePdf($quote);

        return $pdf->stream("{$quote->quote_number}.pdf");
    }

    public function duplicate(Quote $quote)
    {
        $this->authorize('view', $quote);

        $newQuote = $this->quoteService->duplicate($quote);

        return redirect()
            ->route('quotes.edit', $newQuote)
            ->with('success', 'Quote duplicated successfully.');
    }

    public function convert(Quote $quote)
    {
        $this->authorize('update', $quote);

        if (!$quote->can_be_converted) {
            return redirect()
                ->route('quotes.show', $quote)
                ->with('error', 'This quote cannot be converted to an invoice.');
        }

        $invoice = $this->quoteService->convertToInvoice($quote);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Quote converted to invoice successfully.');
    }

    // Public signing page
    public function sign(string $token)
    {
        $quote = Quote::where('sign_token', $token)
            ->with(['client', 'items', 'user'])
            ->firstOrFail();

        if (!$quote->can_be_signed) {
            return view('quotes.sign-error', [
                'quote' => $quote,
                'message' => $quote->is_expired ? 'This quote has expired.' : 'This quote has already been signed.',
            ]);
        }

        // Mark as viewed
        $quote->markAsViewed();

        $settings = $quote->user->settings;

        return view('quotes.sign', compact('quote', 'settings'));
    }

    public function processSign(Request $request, string $token)
    {
        $quote = Quote::where('sign_token', $token)->firstOrFail();

        if (!$quote->can_be_signed) {
            return redirect()->back()->with('error', 'This quote cannot be signed.');
        }

        $request->validate([
            'signer_name' => 'required|string|max:255',
            'signature' => 'required|string',
        ]);

        $quote->sign(
            $request->signer_name,
            $request->signature,
            $request->ip()
        );

        Activity::log($quote, 'signed', 'Quote signed by client', [
            'signer_name' => $request->signer_name,
            'ip' => $request->ip(),
        ]);

        return view('quotes.sign-success', compact('quote'));
    }
}