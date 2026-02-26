@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
            <p class="mt-2 text-sm text-gray-700">Manage and track your invoices.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0">
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Create Invoice
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search invoices..."
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
            </div>
            <div>
                <select name="status" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    <option value="">All Status</option>
                    @foreach(\App\Enums\InvoiceStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="client_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'client_id']))
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Invoice</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Client</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Due Date</th>
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Amount</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($invoices as $invoice)
                <tr class="{{ $invoice->is_overdue ? 'bg-red-50' : '' }}">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                        <a href="{{ route('invoices.show', $invoice) }}" class="font-medium text-gray-900 hover:text-primary-600">
                            {{ $invoice->invoice_number }}
                        </a>
                        @if($invoice->reference)
                        <div class="text-gray-500">{{ $invoice->reference }}</div>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <a href="{{ route('clients.show', $invoice->client) }}" class="hover:text-primary-600">
                            {{ $invoice->client->company_name }}
                        </a>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ $invoice->issue_date->format('M d, Y') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ $invoice->due_date->format('M d, Y') }}
                        @if($invoice->is_overdue)
                        <span class="text-red-600">({{ $invoice->days_overdue }} days overdue)</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 text-right font-medium">
                        ${{ number_format($invoice->total, 2) }}
                        @if($invoice->amount_paid > 0 && $invoice->amount_due > 0)
                        <div class="text-xs text-gray-500">${{ number_format($invoice->amount_due, 2) }} due</div>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @include('components.status-badge', ['status' => $invoice->status])
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('invoices.preview', $invoice) }}" target="_blank" class="text-gray-400 hover:text-gray-600" title="Preview">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-900">View</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No invoices</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new invoice.</p>
                        <div class="mt-6">
                            <a href="{{ route('invoices.create') }}" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                Create Invoice
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($invoices->hasPages())
        <div class="border-t border-gray-200 px-4 py-3 sm:px-6">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection