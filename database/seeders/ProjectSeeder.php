<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $projectCount = 0;

        $projectNames = [
            ['name' => 'E-Commerce Platform Redesign', 'type' => 'web'],
            ['name' => 'Mobile Banking App', 'type' => 'mobile'],
            ['name' => 'SEO Optimization Campaign', 'type' => 'seo'],
            ['name' => 'Brand Identity Design', 'type' => 'design'],
            ['name' => 'Cloud Migration Project', 'type' => 'infrastructure'],
            ['name' => 'Customer Portal Development', 'type' => 'web'],
            ['name' => 'iOS App Release v2.0', 'type' => 'mobile'],
            ['name' => 'Analytics Dashboard', 'type' => 'web'],
            ['name' => 'Security Audit & Implementation', 'type' => 'security'],
            ['name' => 'API Integration & Testing', 'type' => 'backend'],
        ];

        $statuses = ['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'];
        $priorities = ['low', 'medium', 'high', 'critical'];

        foreach ($companies as $index => $company) {
            $admin = User::where('company_id', $company->id)->where('role', 'admin')->first();

            if ($admin) {
                $projectData = $projectNames[$index];

                Project::create([
                    'company_id' => $company->id,
                    'created_by' => $admin->id,
                    'name' => $projectData['name'],
                    'slug' => str()->slug($projectData['name']),
                    'description' => fake()->paragraph(3),
                    'status' => $statuses[$index % 5],
                    'type' => $projectData['type'],
                    'budget' => fake()->numberBetween(5000, 50000),
                    'estimated_hours' => fake()->numberBetween(100, 500),
                    'actual_hours' => fake()->numberBetween(50, 400),
                    'start_date' => now()->subDays(fake()->numberBetween(30, 180)),
                    'deadline' => now()->addDays(fake()->numberBetween(30, 90)),
                    'progress' => fake()->numberBetween(0, 100),
                    'priority' => $priorities[$index % 4],
                    'is_billable' => fake()->boolean(),
                    'hourly_rate' => fake()->numberBetween(50, 200),
                    'technologies' => ['Laravel', 'Vue.js', 'PostgreSQL', 'Docker'],
                    'notes' => fake()->paragraph(2),
                ]);
                $projectCount++;
            }
        }

        // Create 7 more projects for the first company
        $company = $companies->first();
        $admin = User::where('company_id', $company->id)->where('role', 'admin')->first();

        for ($i = 0; $i < 7; $i++) {
            Project::create([
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => $projectNames[(3 + $i) % 10]['name'] . ' - v' . ($i + 2),
                'slug' => str()->slug($projectNames[(3 + $i) % 10]['name']) . '-v' . ($i + 2),
                'description' => fake()->paragraph(3),
                'status' => $statuses[$i % 5],
                'type' => $projectNames[(3 + $i) % 10]['type'],
                'budget' => fake()->numberBetween(5000, 50000),
                'estimated_hours' => fake()->numberBetween(100, 500),
                'actual_hours' => fake()->numberBetween(50, 400),
                'start_date' => now()->subDays(fake()->numberBetween(30, 180)),
                'deadline' => now()->addDays(fake()->numberBetween(30, 90)),
                'progress' => fake()->numberBetween(0, 100),
                'priority' => $priorities[$i % 4],
                'is_billable' => fake()->boolean(),
                'hourly_rate' => fake()->numberBetween(50, 200),
                'technologies' => ['React', 'Node.js', 'MongoDB', 'AWS'],
                'notes' => fake()->paragraph(2),
            ]);
            $projectCount++;
        }

        $this->command->info($projectCount . ' projects seeded successfully!');
    }
}
