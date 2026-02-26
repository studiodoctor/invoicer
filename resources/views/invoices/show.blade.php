@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="space-y-6">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h2>
                @include('components.status-badge', ['status' => $invoice->status])
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Created {{ $invoice->created_at->format('M d, Y') }}
                @if($invoice->quote)
                • From Quote <a href="{{ route('quotes.show', $invoice->quote) }}" class="text-primary-600 hover:text-primary-500">{{ $invoice->quote->quote_number }}</a>
                @endif
            </p>
        </div>
        <div class="mt-4 flex flex-wrap gap-2 md:ml-4 md:mt-0">
            <a href="{{ route('invoices.preview', $invoice) }}" target="_blank" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Preview</a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Download PDF</a>
            @if($invoice->status->value === 'draft')
            <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Edit</a>
            @endif
            @if(in_array($invoice->status->value, ['draft', 'sent', 'viewed', 'overdue']))
            <form action="{{ route('invoices.send', $invoice) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">{{ $invoice->sent_at ? 'Resend' : 'Send' }} Invoice</button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Bill To</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $invoice->client->company_name }}</p>
                            @if($invoice->client->contact_name)
                            <p class="text-sm text-gray-500">{{ $invoice->client->contact_name }}</p>
                            @endif
                            <p class="text-sm text-gray-500">{{ $invoice->client->email }}</p>
                        </div>
                        <div class="text-right">
                            <dl class="space-y-1">
                                <div><dt class="text-sm text-gray-500">Invoice Number</dt><dd class="text-sm text-gray-900">{{ $invoice->invoice_number }}</dd></div>
                                <div><dt class="text-sm text-gray-500">Issue Date</dt><dd class="text-sm text-gray-900">{{ $invoice->issue_date->format('M d, Y') }}</dd></div>
                                <div><dt class="text-sm text-gray-500">Due Date</dt><dd class="text-sm {{ $invoice->is_overdue ? 'text-red-600 font-medium' : 'text-gray-900' }}">{{ $invoice->due_date->format('M d, Y') }}</dd></div>
                                <div><dt class="text-sm text-gray-500">Currency</dt><dd class="text-sm text-gray-900">{{ $invoice->currency }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    <table class="min-w-full mb-6">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-3 text-left text-sm font-semibold text-gray-900">Description</th>
                                <th class="py-3 text-right text-sm font-semibold text-gray-900">Qty</th>
                                <th class="py-3 text-right text-sm font-semibold text-gray-900">Price</th>
                                <th class="py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-4 text-sm text-gray-900">{{ $item->description }}</td>
                                <td class="py-4 text-right text-sm text-gray-500">{{ number_format($item->quantity, 2) }}</td>
                                <td class="py-4 text-right text-sm text-gray-500">{{ format_currency($item->unit_price, $invoice->currency) }}</td>
                                <td class="py-4 text-right text-sm font-medium text-gray-900">{{ format_currency($item->total, $invoice->currency) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span>{{ format_currency($invoice->subtotal, $invoice->currency) }}</span></div>
                            @if($invoice->discount_amount > 0)
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="text-green-600">-{{ format_currency($invoice->discount_amount, $invoice->currency) }}</span></div>
                            @endif
                            @if($invoice->tax_amount > 0)
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Tax ({{ $invoice->tax_rate }}%)</span><span>{{ format_currency($invoice->tax_amount, $invoice->currency) }}</span></div>
                            @endif
                            <div class="flex justify-between text-lg font-semibold border-t pt-2"><span>Total</span><span>{{ format_currency($invoice->total, $invoice->currency) }}</span></div>
                            @if($invoice->amount_paid > 0)
                            <div class="flex justify-between text-sm text-green-600"><span>Paid</span><span>-{{ format_currency($invoice->amount_paid, $invoice->currency) }}</span></div>
                            <div class="flex justify-between text-lg font-semibold"><span>Due</span><span>{{ format_currency($invoice->amount_due, $invoice->currency) }}</span></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($invoice->notes || $invoice->terms || $invoice->payment_instructions)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @if($invoice->notes)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Notes</h4>
                            <p class="mt-2 text-sm text-gray-900 whitespace-pre-line">{{ $invoice->notes }}</p>
                        </div>
                        @endif
                        @if($invoice->terms)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Terms & Conditions</h4>
                            <p class="mt-2 text-sm text-gray-900 whitespace-pre-line">{{ $invoice->terms }}</p>
                        </div>
                        @endif
                        @if($invoice->payment_instructions)
                        <div class="sm:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500">Payment Instructions</h4>
                            <p class="mt-2 text-sm text-gray-900 whitespace-pre-line">{{ $invoice->payment_instructions }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            @if(in_array($invoice->status->value, ['sent', 'viewed', 'partial', 'overdue']) && $invoice->amount_due > 0)
            <div class="bg-white shadow rounded-lg" x-data="{ showForm: false }">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900">Record Payment</h3>
                    <div class="mt-4" x-show="!showForm">
                        <p class="text-sm text-gray-500 mb-4">Amount due: <span class="font-semibold text-gray-900">{{ format_currency($invoice->amount_due, $invoice->currency) }}</span></p>
                        <div class="flex gap-2">
                            <button @click="showForm = true" type="button" class="flex-1 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Record Payment</button>
                            <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">Mark Paid</button>
                            </form>
                        </div>
                    </div>

                    <form action="{{ route('invoices.payment', $invoice) }}" method="POST" x-show="showForm" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount ({{ $invoice->currency }})</label>
                            <input type="number" name="amount" id="amount" step="0.01" max="{{ $invoice->amount_due }}" value="{{ $invoice->amount_due }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="bank_transfer">Bank Transfer / EFT</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="cash">Cash</option>
                                <option value="check">Cheque</option>
                                <option value="paypal">PayPal</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="transaction_id" class="block text-sm font-medium text-gray-700">Transaction ID / Reference (optional)</label>
                            <input type="text" name="transaction_id" id="transaction_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="showForm = false" class="flex-1 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="flex-1 rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Record</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if($invoice->payments->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900">Payment History</h3>
                    <ul class="mt-4 divide-y divide-gray-200">
                        @foreach($invoice->payments as $payment)
                        <li class="py-3">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ format_currency($payment->amount, $invoice->currency) }}</p>
                                    <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                </div>
                                <p class="text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900">Actions</h3>
                    <div class="mt-4 space-y-3">
                        <form action="{{ route('invoices.duplicate', $invoice) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Duplicate Invoice</button>
                        </form>
                        @if($invoice->status->value === 'paid')
                        <a href="{{ route('invoices.receipt', $invoice) }}" class="block w-full text-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Download Receipt</a>
                        @endif
                        @if($invoice->status->value === 'draft')
                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-200 hover:bg-red-100">Delete Invoice</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection