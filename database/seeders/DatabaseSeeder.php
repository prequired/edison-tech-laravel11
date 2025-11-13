<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            ServiceSeeder::class,
            BlogCategorySeeder::class,
            BlogPostSeeder::class,
        ]);

        $this->command->info('All seeders executed successfully!');
    }
}
