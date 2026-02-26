@extends('layouts.app')

@section('title', 'Create Quote')

@section('content')
<div x-data="quoteForm()">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold leading-7 text-gray-900">Create Quote</h2>
    </div>

    <form action="{{ route('quotes.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Quote Details</h3>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="sm:col-span-2">
                            <label for="client_id" class="block text-sm font-medium text-gray-900">Client *</label>
                            <select name="client_id" id="client_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm">
                                <option value="">Select a client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="reference" class="block text-sm font-medium text-gray-900">Reference</label>
                            <input type="text" name="reference" id="reference" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">
                        </div>
                        <div>
                            <label for="issue_date" class="block text-sm font-medium text-gray-900">Issue Date *</label>
                            <input type="date" name="issue_date" id="issue_date" value="{{ now()->format('Y-m-d') }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">
                        </div>
                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-900">Expiry Date *</label>
                            <input type="date" name="expiry_date" id="expiry_date" value="{{ now()->addDays($settings->quote_validity_days)->format('Y-m-d') }}" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-900">Currency</label>
                            <select name="currency" id="currency" x-model="currency" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                        <div>
                            <label for="tax_rate" class="block text-sm font-medium text-gray-900">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" id="tax_rate" x-model="taxRate" step="0.01" min="0" max="100" value="{{ $settings->default_tax_rate }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Line Items</h3>
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-3 text-left text-sm font-semibold text-gray-900" style="width: 40%">Description</th>
                                <th class="py-3 text-left text-sm font-semibold text-gray-900">Qty</th>
                                <th class="py-3 text-left text-sm font-semibold text-gray-900">Unit Price</th>
                                <th class="py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                                <th class="py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 pr-3">
                                        <input type="text" :name="'items[' + index + '][description]'" x-model="item.description" placeholder="Item description" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">
                                    </td>
                                    <td class="py-3 pr-3" style="width: 100px">
                                        <input type="number" :name="'items[' + index + '][quantity]'" x-model="item.quantity" step="0.01" min="0.01" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">
                                    </td>
                                    <td class="py-3 pr-3" style="width: 140px">
                                        <input type="number" :name="'items[' + index + '][unit_price]'" x-model="item.unit_price" step="0.01" min="0" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">
                                    </td>
                                    <td class="py-3 pr-3 text-right font-medium" style="width: 120px">
                                        <span x-text="formatCurrency(item.quantity * item.unit_price)"></span>
                                    </td>
                                    <td class="py-3" style="width: 40px">
                                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-red-600 hover:text-red-900">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <button type="button" @click="addItem" class="mt-4 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Add Item</button>

                    <div class="mt-8 flex justify-end">
                        <div class="w-full max-w-xs space-y-3">
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span class="font-medium" x-text="formatCurrency(subtotal)"></span></div>
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Tax (<span x-text="taxRate"></span>%)</span><span class="font-medium" x-text="formatCurrency(taxAmount)"></span></div>
                            <div class="flex justify-between text-base font-semibold border-t pt-3"><span>Total</span><span x-text="formatCurrency(total)"></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-900">Notes</label>
                            <textarea name="notes" id="notes" rows="4" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">{{ $settings->default_quote_notes }}</textarea>
                        </div>
                        <div>
                            <label for="terms" class="block text-sm font-medium text-gray-900">Terms & Conditions</label>
                            <textarea name="terms" id="terms" rows="4" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm">{{ $settings->default_quote_terms }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('quotes.index') }}" class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Create Quote</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function quoteForm() {
    return {
        currency: '{{ $settings->default_currency }}',
        taxRate: {{ $settings->default_tax_rate }},
        items: [{ description: '', quantity: 1, unit_price: 0 }],
        get subtotal() { return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0), 0); },
        get taxAmount() { return this.subtotal * (this.taxRate / 100); },
        get total() { return this.subtotal + this.taxAmount; },
        addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0 }); },
        removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); },
        formatCurrency(amount) { return new Intl.NumberFormat('en-US', { style: 'currency', currency: this.currency }).format(amount || 0); }
    }
}
</script>
@endpush
