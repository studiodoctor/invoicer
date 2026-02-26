@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div x-data="invoiceForm()">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Create Invoice
            </h2>
        </div>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <!-- Invoice Details -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Invoice Details</h3>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Client -->
                        <div class="sm:col-span-2">
                            <label for="client_id" class="block text-sm font-medium leading-6 text-gray-900">Client *</label>
                            <select name="client_id" 
                                    id="client_id"
                                    x-model="clientId"
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                    required>
                                <option value="">Select a client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" data-currency="{{ $client->currency }}">{{ $client->company_name }}</option>
                                @endforeach
                            </select>
                            @error('client_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reference -->
                        <div class="sm:col-span-2">
                            <label for="reference" class="block text-sm font-medium leading-6 text-gray-900">Reference / PO Number</label>
                            <input type="text" 
                                   name="reference" 
                                   id="reference"
                                   value="{{ old('reference') }}"
                                   class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                        </div>

                        <!-- Issue Date -->
                        <div>
                            <label for="issue_date" class="block text-sm font-medium leading-6 text-gray-900">Issue Date *</label>
                            <input type="date" 
                                   name="issue_date" 
                                   id="issue_date"
                                   value="{{ old('issue_date', now()->format('Y-m-d')) }}"
                                   class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                   required>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium leading-6 text-gray-900">Due Date *</label>
                            <input type="date" 
                                   name="due_date" 
                                   id="due_date"
                                   value="{{ old('due_date', now()->addDays($settings->invoice_due_days)->format('Y-m-d')) }}"
                                   class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                   required>
                        </div>

                        <!-- Currency -->
                        <div>
                            <label for="currency" class="block text-sm font-medium leading-6 text-gray-900">Currency</label>
                            <select name="currency" 
                                    id="currency"
                                    x-model="currency"
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="CAD">CAD</option>
                                <option value="AUD">AUD</option>
                            </select>
                        </div>

                        <!-- Tax Rate -->
                        <div>
                            <label for="tax_rate" class="block text-sm font-medium leading-6 text-gray-900">Tax Rate (%)</label>
                            <input type="number" 
                                   name="tax_rate" 
                                   id="tax_rate"
                                   x-model="taxRate"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   value="{{ old('tax_rate', $settings->default_tax_rate) }}"
                                   class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Line Items</h3>
                    
                    <div class="overflow-x-auto">
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
                                            <input type="text" 
                                                   :name="'items[' + index + '][description]'"
                                                   x-model="item.description"
                                                   placeholder="Item description"
                                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                                   required>
                                        </td>
                                        <td class="py-3 pr-3" style="width: 100px">
                                            <input type="number" 
                                                   :name="'items[' + index + '][quantity]'"
                                                   x-model="item.quantity"
                                                   @input="calculateTotal"
                                                   step="0.01"
                                                   min="0.01"
                                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                                   required>
                                        </td>
                                        <td class="py-3 pr-3" style="width: 140px">
                                            <input type="number" 
                                                   :name="'items[' + index + '][unit_price]'"
                                                   x-model="item.unit_price"
                                                   @input="calculateTotal"
                                                   step="0.01"
                                                   min="0"
                                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                                   required>
                                        </td>
                                        <td class="py-3 pr-3 text-right font-medium" style="width: 120px">
                                            <span x-text="formatCurrency(item.quantity * item.unit_price)"></span>
                                        </td>
                                        <td class="py-3" style="width: 40px">
                                            <button type="button" 
                                                    @click="removeItem(index)"
                                                    x-show="items.length > 1"
                                                    class="text-red-600 hover:text-red-900">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="button" 
                                @click="addItem"
                                class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Add Item
                        </button>
                    </div>

                    <!-- Totals -->
                    <div class="mt-8 flex justify-end">
                        <div class="w-full max-w-xs space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium" x-text="formatCurrency(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax (<span x-text="taxRate"></span>%)</span>
                                <span class="font-medium" x-text="formatCurrency(taxAmount)"></span>
                            </div>
                            <div class="flex justify-between text-base font-semibold border-t pt-3">
                                <span>Total</span>
                                <span x-text="formatCurrency(total)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Terms -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Notes</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="4"
                                      placeholder="Notes visible to client..."
                                      class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('notes', $settings->default_invoice_notes) }}</textarea>
                        </div>
                        <div>
                            <label for="terms" class="block text-sm font-medium leading-6 text-gray-900">Terms & Conditions</label>
                            <textarea name="terms" 
                                      id="terms" 
                                      rows="4"
                                      class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('terms', $settings->default_invoice_terms) }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="payment_instructions" class="block text-sm font-medium leading-6 text-gray-900">Payment Instructions</label>
                            <textarea name="payment_instructions" 
                                      id="payment_instructions" 
                                      rows="3"
                                      class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('payment_instructions', $settings->payment_instructions) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('invoices.index') }}" class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                    Create Invoice
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function invoiceForm() {
    return {
        clientId: '',
        currency: '{{ $settings->default_currency }}',
        taxRate: {{ $settings->default_tax_rate }},
        items: [
            { description: '', quantity: 1, unit_price: 0 }
        ],

        get subtotal() {
            return this.items.reduce((sum, item) => {
                return sum + (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
            }, 0);
        },

        get taxAmount() {
            return this.subtotal * (this.taxRate / 100);
        },

        get total() {
            return this.subtotal + this.taxAmount;
        },

        addItem() {
            this.items.push({ description: '', quantity: 1, unit_price: 0 });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        calculateTotal() {
            // This triggers reactivity
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: this.currency
            }).format(amount || 0);
        }
    }
}
</script>
@endpush