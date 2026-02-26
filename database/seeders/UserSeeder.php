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
            'company_name' => 'Demo Company Inc.',
            'company_email' => 'hello@democompany.com',
            'company_phone' => '+1 (555) 123-4567',
            'company_address' => '123 Business Street',
            'company_city' => 'New York',
            'company_state' => 'NY',
            'company_postal_code' => '10001',
            'company_country' => 'United States',
            'vat_number' => 'US123456789',
            'default_currency' => 'USD',
            'default_tax_rate' => 10.00,
            'invoice_prefix' => 'INV-',
            'quote_prefix' => 'QUO-',
            'invoice_due_days' => 30,
            'quote_validity_days' => 30,
            'default_invoice_notes' => 'Thank you for your business!',
            'default_invoice_terms' => 'Payment is due within 30 days of invoice date.',
            'payment_instructions' => "Bank: First National Bank\nAccount: 1234567890\nRouting: 021000021",
        ]);
    }
}