<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        BlogCategory::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Articles and tutorials about web development technologies, frameworks, and best practices.',
            'sort_order' => 1,
        ]);

        BlogCategory::create([
            'name' => 'Mobile Development',
            'slug' => 'mobile-development',
            'description' => 'Resources for iOS, Android, and cross-platform mobile app development.',
            'sort_order' => 2,
        ]);

        BlogCategory::create([
            'name' => 'Business & Strategy',
            'slug' => 'business-strategy',
            'description' => 'Business insights, digital transformation, and technology strategy articles.',
            'sort_order' => 3,
        ]);

        BlogCategory::create([
            'name' => 'Design & UX',
            'slug' => 'design-ux',
            'description' => 'User experience design, UI design, and creative design inspiration.',
            'sort_order' => 4,
        ]);

        BlogCategory::create([
            'name' => 'Technology News',
            'slug' => 'technology-news',
            'description' => 'Latest news and updates in the technology industry.',
            'sort_order' => 5,
        ]);

        $this->command->info('5 blog categories seeded successfully!');
    }
}
