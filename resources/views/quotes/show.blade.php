@extends('layouts.app')

@section('title', 'Quote ' . $quote->quote_number)

@section('content')
<div class="space-y-6">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-900">{{ $quote->quote_number }}</h2>
                @include('components.status-badge', ['status' => $quote->status])
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Created {{ $quote->created_at->format('M d, Y') }}
                @if($quote->convertedInvoice)
                • Converted to <a href="{{ route('invoices.show', $quote->convertedInvoice) }}" class="text-primary-600 hover:text-primary-500">{{ $quote->convertedInvoice->invoice_number }}</a>
                @endif
            </p>
        </div>
        <div class="mt-4 flex flex-wrap gap-2 md:ml-4 md:mt-0">
            <a href="{{ route('quotes.preview', $quote) }}" target="_blank" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Preview
            </a>
            <a href="{{ route('quotes.pdf', $quote) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download PDF
            </a>
            @if($quote->status->value === 'draft')
            <a href="{{ route('quotes.edit', $quote) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Edit</a>
            @endif
            @if(in_array($quote->status->value, ['draft', 'sent', 'viewed']))
            <form action="{{ route('quotes.send', $quote) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ $quote->sent_at ? 'Resend' : 'Send' }} Quote
                </button>
            </form>
            @endif
            @if($quote->status->value === 'signed' && !$quote->convertedInvoice)
            <form action="{{ route('quotes.convert', $quote) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Convert to Invoice
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <!-- Client Info -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Client</h4>
                            <div class="mt-2">
                                <p class="text-sm font-medium text-gray-900">{{ $quote->client->company_name }}</p>
                                @if($quote->client->contact_name)
                                <p class="text-sm text-gray-500">{{ $quote->client->contact_name }}</p>
                                @endif
                                <p class="text-sm text-gray-500">{{ $quote->client->email }}</p>
                                @if($quote->client->full_address)
                                <p class="text-sm text-gray-500 mt-1">{{ $quote->client->full_address }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Quote Info -->
                        <div class="text-right">
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Quote Number</dt>
                                    <dd class="text-sm text-gray-900">{{ $quote->quote_number }}</dd>
                                </div>
                                @if($quote->reference)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Reference</dt>
                                    <dd class="text-sm text-gray-900">{{ $quote->reference }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $quote->issue_date->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                                    <dd class="text-sm {{ $quote->is_expired ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                        {{ $quote->expiry_date->format('M d, Y') }}
                                        @if($quote->is_expired)
                                        (Expired)
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Currency</dt>
                                    <dd class="text-sm text-gray-900">{{ $quote->currency }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="mt-8">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 text-left text-sm font-semibold text-gray-900">Description</th>
                                    <th class="py-3 text-right text-sm font-semibold text-gray-900">Qty</th>
                                    <th class="py-3 text-right text-sm font-semibold text-gray-900">Price</th>
                                    <th class="py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quote->items as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->description }}</div>
                                        @if($item->details)
                                        <div class="text-sm text-gray-500">{{ $item->details }}</div>
                                        @endif
                                    </td>
                                    <td class="py-4 text-right text-sm text-gray-500">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="py-4 text-right text-sm text-gray-500">{{ format_currency($item->unit_price, $quote->currency) }}</td>
                                    <td class="py-4 text-right text-sm font-medium text-gray-900">{{ format_currency($item->total, $quote->currency) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="mt-6 flex justify-end">
                        <div class="w-full max-w-xs space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium">{{ format_currency($quote->subtotal, $quote->currency) }}</span>
                            </div>
                            @if($quote->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Discount</span>
                                <span class="font-medium text-green-600">-{{ format_currency($quote->discount_amount, $quote->currency) }}</span>
                            </div>
                            @endif
                            @if($quote->tax_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax ({{ $quote->tax_rate }}%)</span>
                                <span class="font-medium">{{ format_currency($quote->tax_amount, $quote->currency) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-base font-semibold border-t pt-3">
                                <span>Total</span>
                                <span>{{ format_currency($quote->total, $quote->currency) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Terms -->
            @if($quote->notes || $quote->terms)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @if($quote->notes)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Notes</h4>
                            <p class="mt-2 text-sm text-gray-900 whitespace-pre-line">{{ $quote->notes }}</p>
                        </div>
                        @endif
                        @if($quote->terms)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Terms & Conditions</h4>
                            <p class="mt-2 text-sm text-gray-900 whitespace-pre-line">{{ $quote->terms }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Signature -->
            @if($quote->signature_data)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h4 class="text-sm font-medium text-gray-500">Signature</h4>
                    <div class="mt-4">
                        <img src="{{ $quote->signature_data }}" alt="Signature" class="h-20 border rounded p-2">
                        <p class="mt-2 text-sm text-gray-500">
                            Signed by <span class="font-medium text-gray-900">{{ $quote->signer_name }}</span> 
                            on {{ $quote->signed_at->format('M d, Y \a\t H:i') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quote Link -->
            @if(in_array($quote->status->value, ['sent', 'viewed']))
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Client Signing Link</h3>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 mb-2">Share this link with your client to sign:</p>
                        <div class="flex">
                            <input type="text" readonly value="{{ $quote->sign_url }}" class="flex-1 rounded-l-md border-gray-300 text-sm" id="signUrl">
                            <button onclick="navigator.clipboard.writeText(document.getElementById('signUrl').value); alert('Link copied!');" class="rounded-r-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 border border-l-0 border-gray-300 hover:bg-gray-200">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Actions</h3>
                    <div class="mt-4 space-y-3">
                        <form action="{{ route('quotes.duplicate', $quote) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Duplicate Quote
                            </button>
                        </form>
                        @if($quote->status->value === 'draft')
                        <form action="{{ route('quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quote?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-200 hover:bg-red-100">
                                Delete Quote
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activity -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Activity</h3>
                    <ul class="mt-4 space-y-3">
                        <li class="text-sm text-gray-500">
                            <span class="font-medium text-gray-900">Created</span>
                            <span class="block">{{ $quote->created_at->format('M d, Y H:i') }}</span>
                        </li>
                        @if($quote->sent_at)
                        <li class="text-sm text-gray-500">
                            <span class="font-medium text-gray-900">Sent</span>
                            <span class="block">{{ $quote->sent_at->format('M d, Y H:i') }}</span>
                        </li>
                        @endif
                        @if($quote->viewed_at)
                        <li class="text-sm text-gray-500">
                            <span class="font-medium text-gray-900">Viewed</span>
                            <span class="block">{{ $quote->viewed_at->format('M d, Y H:i') }}</span>
                        </li>
                        @endif
                        @if($quote->signed_at)
                        <li class="text-sm text-gray-500">
                            <span class="font-medium text-gray-900">Signed by {{ $quote->signer_name }}</span>
                            <span class="block">{{ $quote->signed_at->format('M d, Y H:i') }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection