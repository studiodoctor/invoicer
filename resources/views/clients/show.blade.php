@extends('layouts.app')

@section('title', $client->company_name)

@section('content')
<div class="space-y-6">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-primary-100">
                    <span class="text-lg font-medium leading-none text-primary-700">{{ strtoupper(substr($client->company_name, 0, 2)) }}</span>
                </span>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $client->company_name }}</h2>
                    @if($client->contact_name)
                    <p class="text-sm text-gray-500">{{ $client->contact_name }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="mt-4 flex space-x-3 md:ml-4 md:mt-0">
            <a href="{{ route('quotes.create', ['client_id' => $client->id]) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">New Quote</a>
            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">New Invoice</a>
            <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Edit Client</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900"><a href="mailto:{{ $client->email }}" class="text-primary-600 hover:text-primary-500">{{ $client->email }}</a></dd>
                        </div>
                        @if($client->phone)
                        <div>
                            <dt class="text-sm text-gray-500">Phone</dt>
                            <dd class="text-sm text-gray-900">{{ $client->phone }}</dd>
                        </div>
                        @endif
                        @if($client->website)
                        <div>
                            <dt class="text-sm text-gray-500">Website</dt>
                            <dd class="text-sm text-gray-900"><a href="{{ $client->website }}" target="_blank" class="text-primary-600 hover:text-primary-500">{{ $client->website }}</a></dd>
                        </div>
                        @endif
                        @if($client->full_address)
                        <div>
                            <dt class="text-sm text-gray-500">Address</dt>
                            <dd class="text-sm text-gray-900">{{ $client->full_address }}</dd>
                        </div>
                        @endif
                        @if($client->vat_number)
                        <div>
                            <dt class="text-sm text-gray-500">VAT Number</dt>
                            <dd class="text-sm text-gray-900">{{ $client->vat_number }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg mt-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Summary</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Total Revenue</dt>
                            <dd class="text-sm font-medium text-gray-900">R{{ number_format($client->total_revenue, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Outstanding</dt>
                            <dd class="text-sm font-medium text-gray-900">R{{ number_format($client->outstanding_balance, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Invoices -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Invoices</h3>
                    @if($client->invoices->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($client->invoices as $invoice)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600">{{ $invoice->invoice_number }}</a>
                                <p class="text-sm text-gray-500">{{ $invoice->issue_date->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">R{{ number_format($invoice->total, 2) }}</p>
                                @include('components.status-badge', ['status' => $invoice->status])
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-sm text-gray-500">No invoices yet.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Quotes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Quotes</h3>
                    @if($client->quotes->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($client->quotes as $quote)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <a href="{{ route('quotes.show', $quote) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600">{{ $quote->quote_number }}</a>
                                <p class="text-sm text-gray-500">{{ $quote->issue_date->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">R{{ number_format($quote->total, 2) }}</p>
                                @include('components.status-badge', ['status' => $quote->status])
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-sm text-gray-500">No quotes yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
