@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold leading-7 text-gray-900">Edit Client</h2>
    </div>

    <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm font-medium leading-6 text-gray-900">Company Name *</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $client->company_name) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" required>
                    </div>

                    <div>
                        <label for="contact_name" class="block text-sm font-medium leading-6 text-gray-900">Contact Name</label>
                        <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $client->contact_name) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $client->phone) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium leading-6 text-gray-900">Website</label>
                        <input type="url" name="website" id="website" value="{{ old('website', $client->website) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="vat_number" class="block text-sm font-medium leading-6 text-gray-900">VAT Number</label>
                        <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number', $client->vat_number) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium leading-6 text-gray-900">Currency</label>
                        <select name="currency" id="currency" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="USD" {{ old('currency', $client->currency) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency', $client->currency) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency', $client->currency) === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="CAD" {{ old('currency', $client->currency) === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            <option value="AUD" {{ old('currency', $client->currency) === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                            <option value="ZAR" {{ old('currency', $client->currency) === 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address_line_1" class="block text-sm font-medium leading-6 text-gray-900">Address Line 1</label>
                        <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $client->address_line_1) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $client->city) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium leading-6 text-gray-900">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $client->state) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium leading-6 text-gray-900">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $client->postal_code) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country', $client->country) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('notes', $client->notes) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-900">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 text-right sm:px-6 rounded-b-lg">
                <a href="{{ route('clients.show', $client) }}" class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mr-3">Cancel</a>
                <button type="submit" class="inline-flex justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Update Client</button>
            </div>
        </div>
    </form>
</div>
@endsection
