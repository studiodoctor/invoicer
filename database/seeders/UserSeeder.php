<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $user->settings()->create([
            'company_name' => 'Demo Company (Pty) Ltd',
            'company_email' => 'hello@democompany.co.za',
            'company_phone' => '+27 11 123 4567',
            'company_address' => '123 Business Street',
            'company_city' => 'Johannesburg',
            'company_state' => 'Gauteng',
            'company_postal_code' => '2000',
            'company_country' => 'South Africa',
            'vat_number' => '4123456789',
            'default_currency' => 'ZAR',
            'default_tax_rate' => 15.00, // South African VAT rate
            'invoice_prefix' => 'INV-',
            'quote_prefix' => 'QUO-',
            'invoice_due_days' => 30,
            'quote_validity_days' => 30,
            'default_invoice_notes' => 'Thank you for your business!',
            'default_invoice_terms' => 'Payment is due within 30 days of invoice date.',
            'payment_instructions' => "Bank: First National Bank\nAccount Name: Demo Company (Pty) Ltd\nAccount Number: 1234567890\nBranch Code: 250655\nReference: Invoice Number",
        ]);
    }
}
