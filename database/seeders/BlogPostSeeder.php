<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $categories = BlogCategory::all();
        $authors = User::whereIn('role', ['admin', 'employee'])->limit(5)->get();

        $posts = [
            [
                'category_id' => 1,
                'title' => '10 Essential Tips for Modern Web Development',
                'excerpt' => 'Discover the best practices and tools that every web developer should know in 2024.',
                'content' => 'In this comprehensive guide, we cover the ten most important concepts and tools for modern web development. From responsive design to API optimization, learn how to build efficient and scalable web applications. We discuss Laravel, Vue.js, database design, security practices, and deployment strategies. By implementing these tips, you\'ll improve your development workflow and create better applications.',
            ],
            [
                'category_id' => 2,
                'title' => 'Cross-Platform Mobile Development with React Native',
                'excerpt' => 'Learn how to build iOS and Android apps with a single codebase using React Native.',
                'content' => 'React Native allows developers to write mobile applications using JavaScript and React. This article explores the benefits of cross-platform development, compares React Native with native development, and provides best practices for building production-ready mobile apps. We cover state management, navigation, performance optimization, and testing strategies.',
            ],
            [
                'category_id' => 3,
                'title' => 'Digital Transformation: A Guide for Traditional Businesses',
                'excerpt' => 'How to successfully navigate digital transformation and leverage technology for growth.',
                'content' => 'Digital transformation is essential for businesses to remain competitive. This guide outlines the key steps: assessing current infrastructure, defining clear goals, choosing the right technology partners, and implementing changes strategically. We discuss common challenges, change management strategies, and how to measure success.',
            ],
            [
                'category_id' => 4,
                'title' => 'The Importance of User-Centered Design',
                'excerpt' => 'Creating products that users actually love by focusing on their needs and behaviors.',
                'content' => 'User-centered design puts users at the heart of the design process. Learn about user research methods, persona development, usability testing, and iterative design. We explore how to conduct effective user interviews, analyze user behavior, and create designs that solve real problems.',
            ],
            [
                'category_id' => 5,
                'title' => 'Latest Trends in Artificial Intelligence and Machine Learning',
                'excerpt' => 'Explore the cutting-edge developments in AI and ML that are reshaping industries.',
                'content' => 'Artificial intelligence and machine learning continue to evolve rapidly. This article covers recent breakthroughs, practical applications, and emerging trends. From natural language processing to computer vision, learn how organizations are leveraging AI to improve operations and customer experiences.',
            ],
            [
                'category_id' => 1,
                'title' => 'Building Scalable APIs with Laravel',
                'excerpt' => 'Best practices for designing and building robust, scalable REST APIs using Laravel.',
                'content' => 'Laravel is an excellent framework for building APIs. This guide covers API design principles, authentication and authorization, rate limiting, caching strategies, and testing. Learn how to structure your code for maintainability, handle errors gracefully, and document your APIs effectively.',
            ],
            [
                'category_id' => 2,
                'title' => 'iOS Development Best Practices in 2024',
                'excerpt' => 'Master the latest iOS development techniques and frameworks for building exceptional apps.',
                'content' => 'iOS development has evolved significantly with SwiftUI and other modern frameworks. This article covers architecture patterns, performance optimization, accessibility, and App Store guidelines. Learn how to build apps that users love and that perform efficiently on Apple devices.',
            ],
            [
                'category_id' => 3,
                'title' => 'Cybersecurity in the Modern Enterprise',
                'excerpt' => 'Protecting your business from evolving cyber threats with comprehensive security strategies.',
                'content' => 'Cybersecurity is more important than ever. This guide covers threat assessment, employee training, infrastructure security, data protection, and incident response planning. Learn about zero-trust models, encryption, and compliance requirements for your industry.',
            ],
            [
                'category_id' => 4,
                'title' => 'Color Theory and Psychology in Web Design',
                'excerpt' => 'How to strategically use color to enhance user experience and brand perception.',
                'content' => 'Colors have significant impact on user perception and behavior. This article explores color theory, cultural considerations, accessibility, and practical applications in web design. Learn how to create harmonious color palettes that support your design goals.',
            ],
            [
                'category_id' => 5,
                'title' => 'The Future of Web Technologies: WebAssembly and Beyond',
                'excerpt' => 'Explore emerging web technologies that will shape the future of web development.',
                'content' => 'WebAssembly enables high-performance applications on the web. This article discusses WebAssembly capabilities, use cases, and how it complements JavaScript. We also explore other emerging technologies like Web Components and Edge Computing.',
            ],
        ];

        $postCount = 0;

        foreach ($posts as $index => $postData) {
            $author = $authors->isNotEmpty() ? $authors->random() : User::first();

            if ($author) {
                BlogPost::create([
                    'category_id' => $postData['category_id'],
                    'author_id' => $author->id,
                    'title' => $postData['title'],
                    'slug' => str()->slug($postData['title']),
                    'excerpt' => $postData['excerpt'],
                    'content' => $postData['content'],
                    'status' => 'published',
                    'published_at' => now()->subDays(random_int(1, 90)),
                    'views_count' => random_int(0, 500),
                    'meta_title' => $postData['title'] . ' | Edison Tech Blog',
                    'meta_description' => $postData['excerpt'],
                    'tags' => $this->generateTags($postData['title']),
                ]);
                $postCount++;
            }
        }

        $this->command->info($postCount . ' blog posts seeded successfully!');
    }

    private function generateTags(string $title): array
    {
        $words = explode(' ', strtolower($title));
        return array_filter($words, function ($word) {
            return strlen($word) > 3;
        });
    }
}
