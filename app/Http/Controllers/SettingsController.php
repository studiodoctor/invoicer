<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = auth()->user()->settings;

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_website' => 'nullable|url|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_city' => 'nullable|string|max:100',
            'company_state' => 'nullable|string|max:100',
            'company_postal_code' => 'nullable|string|max:20',
            'company_country' => 'nullable|string|max:100',
            'vat_number' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:2048',
            'default_currency' => 'required|string|size:3',
            'date_format' => 'required|string|max:20',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_prefix' => 'required|string|max:10',
            'quote_prefix' => 'required|string|max:10',
            'quote_validity_days' => 'required|integer|min:1|max:365',
            'invoice_due_days' => 'required|integer|min:1|max:365',
            'default_invoice_notes' => 'nullable|string|max:2000',
            'default_invoice_terms' => 'nullable|string|max:2000',
            'default_quote_notes' => 'nullable|string|max:2000',
            'default_quote_terms' => 'nullable|string|max:2000',
            'payment_instructions' => 'nullable|string|max:2000',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'show_logo_on_documents' => 'boolean',
            'show_payment_instructions' => 'boolean',
        ]);

        $settings = auth()->user()->settings;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::delete($settings->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);

        $settings->update($validated);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function removeLogo()
    {
        $settings = auth()->user()->settings;

        if ($settings->logo_path) {
            Storage::delete($settings->logo_path);
            $settings->update(['logo_path' => null]);
        }

        return redirect()
            ->route('settings.index')
            ->with('success', 'Logo removed successfully.');
    }
}