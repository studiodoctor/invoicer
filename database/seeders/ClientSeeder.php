<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $clients = [
            [
                'company_name' => 'Acme Corporation',
                'contact_name' => 'John Smith',
                'email' => 'john@acme.com',
                'phone' => '+1 (555) 234-5678',
                'address_line_1' => '456 Commerce Ave',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90001',
                'country' => 'United States',
            ],
            [
                'company_name' => 'Tech Solutions Ltd',
                'contact_name' => 'Sarah Johnson',
                'email' => 'sarah@techsolutions.com',
                'phone' => '+1 (555) 345-6789',
                'address_line_1' => '789 Innovation Blvd',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postal_code' => '94102',
                'country' => 'United States',
            ],
            [
                'company_name' => 'Global Enterprises',
                'contact_name' => 'Michael Brown',
                'email' => 'michael@globalent.com',
                'phone' => '+1 (555) 456-7890',
                'address_line_1' => '321 World Trade Center',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
                'country' => 'United States',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create([
                'user_id' => $user->id,
                ...$clientData,
            ]);
        }
    }
}