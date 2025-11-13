<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $userCount = 0;

        foreach ($companies as $company) {
            // Create 1 admin user per company
            User::create([
                'company_id' => $company->id,
                'name' => ucfirst($company->slug) . ' Admin',
                'email' => 'admin@' . $company->slug . '.com',
                'password' => Hash::make('password'),
                'phone' => fake()->phoneNumber(),
                'role' => 'admin',
                'is_active' => true,
                'timezone' => 'America/New_York',
            ]);
            $userCount++;

            // Create 2 employee users per company
            for ($i = 1; $i <= 2; $i++) {
                User::create([
                    'company_id' => $company->id,
                    'name' => fake()->firstName() . ' ' . fake()->lastName(),
                    'email' => 'employee' . $i . '@' . $company->slug . '.com',
                    'password' => Hash::make('password'),
                    'phone' => fake()->phoneNumber(),
                    'role' => 'employee',
                    'is_active' => true,
                    'timezone' => 'America/Chicago',
                ]);
                $userCount++;
            }

            // Create 5 client users per company
            for ($i = 1; $i <= 5; $i++) {
                User::create([
                    'company_id' => $company->id,
                    'name' => fake()->firstName() . ' ' . fake()->lastName(),
                    'email' => 'client' . $i . '@' . $company->slug . '.com',
                    'password' => Hash::make('password'),
                    'phone' => fake()->phoneNumber(),
                    'role' => 'client',
                    'is_active' => true,
                    'timezone' => 'America/Los_Angeles',
                ]);
                $userCount++;
            }
        }

        $this->command->info($userCount . ' users seeded successfully!');
    }
}
