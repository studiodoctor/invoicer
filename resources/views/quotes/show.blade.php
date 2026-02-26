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
        </div>
        <div class="mt-4 flex flex-wrap gap-2 md:ml-4 md:mt-0">
            <a href="{{ route('quotes.pdf', $quote) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Download PDF</a>
            @if($quote->status->value === 'draft')
            <a href="{{ route('quotes.edit', $quote) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Edit</a>
            @endif
            @if(in_array($quote->status->value, ['draft', 'sent', 'viewed']))
            <form action="{{ route('quotes.send', $quote) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Send Quote</button>
            </form>
            @endif
            @if($quote->status->value === 'signed')
            <form action="{{ route('quotes.convert', $quote) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">Convert to Invoice</button>
            </form>
            @endif
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Client</h4>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $quote->client->company_name }}</p>
                    <p class="text-sm text-gray-500">{{ $quote->client->email }}</p>
                </div>
                <div class="text-right">
                    <dl class="space-y-1">
                        <div><dt class="text-sm text-gray-500">Issue Date</dt><dd class="text-sm text-gray-900">{{ $quote->issue_date->format('M d, Y') }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Expiry Date</dt><dd class="text-sm text-gray-900">{{ $quote->expiry_date->format('M d, Y') }}</dd></div>
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
                    @foreach($quote->items as $item)
                    <tr class="border-b border-gray-100">
                        <td class="py-4 text-sm text-gray-900">{{ $item->description }}</td>
                        <td class="py-4 text-right text-sm text-gray-500">{{ number_format($item->quantity, 2) }}</td>
                        <td class="py-4 text-right text-sm text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-4 text-right text-sm font-medium text-gray-900">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span>${{ number_format($quote->subtotal, 2) }}</span></div>
                    @if($quote->tax_amount > 0)
                    <div class="flex justify-between text-sm"><span class="text-gray-500">Tax ({{ $quote->tax_rate }}%)</span><span>${{ number_format($quote->tax_amount, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between text-lg font-semibold border-t pt-2"><span>Total</span><span>${{ number_format($quote->total, 2) }}</span></div>
                </div>
            </div>

            @if($quote->signature_data)
            <div class="mt-8 pt-6 border-t">
                <h4 class="text-sm font-medium text-gray-500">Signature</h4>
                <div class="mt-2">
                    <img src="{{ $quote->signature_data }}" alt="Signature" class="h-20 border rounded">
                    <p class="mt-1 text-sm text-gray-500">Signed by {{ $quote->signer_name }} on {{ $quote->signed_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
