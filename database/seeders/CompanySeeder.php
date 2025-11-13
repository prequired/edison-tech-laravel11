<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'Edison Tech',
            'slug' => 'edison-tech',
            'email' => 'info@edisontech.com',
            'phone' => '+1-555-0100',
            'website' => 'https://edisontech.com',
            'address' => '123 Tech Street',
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'USA',
            'postal_code' => '94102',
            'tax_id' => 'EIN-12-3456789',
            'is_active' => true,
        ]);

        Company::create([
            'name' => 'Acme Solutions',
            'slug' => 'acme-solutions',
            'email' => 'contact@acmesolutions.com',
            'phone' => '+1-555-0101',
            'website' => 'https://acmesolutions.com',
            'address' => '456 Business Ave',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
            'postal_code' => '10001',
            'tax_id' => 'EIN-98-7654321',
            'is_active' => true,
        ]);

        Company::create([
            'name' => 'Digital Ventures',
            'slug' => 'digital-ventures',
            'email' => 'hello@digitalventures.io',
            'phone' => '+1-555-0102',
            'website' => 'https://digitalventures.io',
            'address' => '789 Innovation Plaza',
            'city' => 'Austin',
            'state' => 'TX',
            'country' => 'USA',
            'postal_code' => '78701',
            'tax_id' => 'EIN-55-1234567',
            'is_active' => true,
        ]);

        $this->command->info('3 companies seeded successfully!');
    }
}
