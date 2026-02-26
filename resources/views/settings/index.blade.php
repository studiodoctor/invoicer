@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Settings
            </h2>
        </div>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Company Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Company Information</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm font-medium leading-6 text-gray-900">Company Name *</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings->company_name) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" required>
                    </div>

                    <div>
                        <label for="company_email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                        <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $settings->company_email) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="company_phone" class="block text-sm font-medium leading-6 text-gray-900">Phone</label>
                        <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone', $settings->company_phone) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="company_website" class="block text-sm font-medium leading-6 text-gray-900">Website</label>
                        <input type="url" name="company_website" id="company_website" value="{{ old('company_website', $settings->company_website) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="vat_number" class="block text-sm font-medium leading-6 text-gray-900">VAT Number</label>
                        <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number', $settings->vat_number) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="company_address" class="block text-sm font-medium leading-6 text-gray-900">Address</label>
                        <textarea name="company_address" id="company_address" rows="2" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('company_address', $settings->company_address) }}</textarea>
                    </div>

                    <div>
                        <label for="company_city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
                        <input type="text" name="company_city" id="company_city" value="{{ old('company_city', $settings->company_city) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="company_state" class="block text-sm font-medium leading-6 text-gray-900">State/Province</label>
                        <input type="text" name="company_state" id="company_state" value="{{ old('company_state', $settings->company_state) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="company_postal_code" class="block text-sm font-medium leading-6 text-gray-900">Postal Code</label>
                        <input type="text" name="company_postal_code" id="company_postal_code" value="{{ old('company_postal_code', $settings->company_postal_code) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="company_country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
                        <input type="text" name="company_country" id="company_country" value="{{ old('company_country', $settings->company_country) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Logo</h3>
                <div class="flex items-center gap-6">
                    @if($settings->logo_path)
                    <img src="{{ Storage::url($settings->logo_path) }}" alt="Company Logo" class="h-16 w-auto">
                    @endif
                    <div>
                        <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="mt-1 text-sm text-gray-500">PNG, JPG up to 2MB</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice & Quote Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Invoice & Quote Settings</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="default_currency" class="block text-sm font-medium leading-6 text-gray-900">Default Currency</label>
                        <select name="default_currency" id="default_currency" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="USD" {{ $settings->default_currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ $settings->default_currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ $settings->default_currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="CAD" {{ $settings->default_currency === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            <option value="AUD" {{ $settings->default_currency === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                            <option value="ZAR" {{ $settings->default_currency === 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                        </select>
                    </div>

                    <div>
                        <label for="default_tax_rate" class="block text-sm font-medium leading-6 text-gray-900">Default Tax Rate (%)</label>
                        <input type="number" name="default_tax_rate" id="default_tax_rate" value="{{ old('default_tax_rate', $settings->default_tax_rate) }}" step="0.01" min="0" max="100" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="date_format" class="block text-sm font-medium leading-6 text-gray-900">Date Format</label>
                        <select name="date_format" id="date_format" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="Y-m-d" {{ $settings->date_format === 'Y-m-d' ? 'selected' : '' }}>2024-01-15</option>
                            <option value="d/m/Y" {{ $settings->date_format === 'd/m/Y' ? 'selected' : '' }}>15/01/2024</option>
                            <option value="m/d/Y" {{ $settings->date_format === 'm/d/Y' ? 'selected' : '' }}>01/15/2024</option>
                            <option value="d M Y" {{ $settings->date_format === 'd M Y' ? 'selected' : '' }}>15 Jan 2024</option>
                        </select>
                    </div>

                    <div>
                        <label for="invoice_prefix" class="block text-sm font-medium leading-6 text-gray-900">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" id="invoice_prefix" value="{{ old('invoice_prefix', $settings->invoice_prefix) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="quote_prefix" class="block text-sm font-medium leading-6 text-gray-900">Quote Prefix</label>
                        <input type="text" name="quote_prefix" id="quote_prefix" value="{{ old('quote_prefix', $settings->quote_prefix) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="invoice_due_days" class="block text-sm font-medium leading-6 text-gray-900">Invoice Due Days</label>
                        <input type="number" name="invoice_due_days" id="invoice_due_days" value="{{ old('invoice_due_days', $settings->invoice_due_days) }}" min="1" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="quote_validity_days" class="block text-sm font-medium leading-6 text-gray-900">Quote Validity Days</label>
                        <input type="number" name="quote_validity_days" id="quote_validity_days" value="{{ old('quote_validity_days', $settings->quote_validity_days) }}" min="1" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="primary_color" class="block text-sm font-medium leading-6 text-gray-900">Primary Color</label>
                        <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $settings->primary_color) }}" class="mt-2 block h-10 w-full rounded-md border-0 p-1 shadow-sm ring-1 ring-inset ring-gray-300">
                    </div>

                    <div>
                        <label for="secondary_color" class="block text-sm font-medium leading-6 text-gray-900">Secondary Color</label>
                        <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $settings->secondary_color) }}" class="mt-2 block h-10 w-full rounded-md border-0 p-1 shadow-sm ring-1 ring-inset ring-gray-300">
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Notes & Terms -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Default Notes & Terms</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="default_invoice_notes" class="block text-sm font-medium leading-6 text-gray-900">Default Invoice Notes</label>
                        <textarea name="default_invoice_notes" id="default_invoice_notes" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('default_invoice_notes', $settings->default_invoice_notes) }}</textarea>
                    </div>

                    <div>
                        <label for="default_invoice_terms" class="block text-sm font-medium leading-6 text-gray-900">Default Invoice Terms</label>
                        <textarea name="default_invoice_terms" id="default_invoice_terms" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('default_invoice_terms', $settings->default_invoice_terms) }}</textarea>
                    </div>

                    <div>
                        <label for="default_quote_notes" class="block text-sm font-medium leading-6 text-gray-900">Default Quote Notes</label>
                        <textarea name="default_quote_notes" id="default_quote_notes" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('default_quote_notes', $settings->default_quote_notes) }}</textarea>
                    </div>

                    <div>
                        <label for="default_quote_terms" class="block text-sm font-medium leading-6 text-gray-900">Default Quote Terms</label>
                        <textarea name="default_quote_terms" id="default_quote_terms" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('default_quote_terms', $settings->default_quote_terms) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="payment_instructions" class="block text-sm font-medium leading-6 text-gray-900">Payment Instructions</label>
                        <textarea name="payment_instructions" id="payment_instructions" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('payment_instructions', $settings->payment_instructions) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkboxes -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="hidden" name="show_logo_on_documents" value="0">
                        <input type="checkbox" name="show_logo_on_documents" id="show_logo_on_documents" value="1" {{ $settings->show_logo_on_documents ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="show_logo_on_documents" class="ml-3 block text-sm font-medium leading-6 text-gray-900">Show logo on documents</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="show_payment_instructions" value="0">
                        <input type="checkbox" name="show_payment_instructions" id="show_payment_instructions" value="1" {{ $settings->show_payment_instructions ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="show_payment_instructions" class="ml-3 block text-sm font-medium leading-6 text-gray-900">Show payment instructions on invoices</label>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
                <button type="submit" class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                    Save Settings
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
