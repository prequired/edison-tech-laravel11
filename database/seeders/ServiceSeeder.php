<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'short_description' => 'Custom web applications built with modern frameworks',
            'description' => 'We specialize in building scalable, responsive web applications using Laravel, Vue.js, React, and other modern technologies. Our team delivers robust solutions tailored to your business needs.',
            'icon' => 'code',
            'sort_order' => 1,
            'is_active' => true,
            'is_featured' => true,
            'features' => [
                'Responsive Design',
                'RESTful APIs',
                'Database Design',
                'Authentication & Security',
                'Performance Optimization',
            ],
            'meta_title' => 'Professional Web Development Services',
            'meta_description' => 'Custom web application development with modern technologies and best practices.',
        ]);

        Service::create([
            'name' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'short_description' => 'Native and cross-platform mobile applications',
            'description' => 'Build powerful mobile applications for iOS and Android platforms. We use React Native, Flutter, and native technologies to create high-performance mobile experiences.',
            'icon' => 'smartphone',
            'sort_order' => 2,
            'is_active' => true,
            'is_featured' => true,
            'features' => [
                'iOS Development',
                'Android Development',
                'Cross-Platform Solutions',
                'Push Notifications',
                'App Store Optimization',
            ],
            'meta_title' => 'Mobile App Development Services',
            'meta_description' => 'Professional mobile app development for iOS, Android, and cross-platform applications.',
        ]);

        Service::create([
            'name' => 'SEO & Digital Marketing',
            'slug' => 'seo-digital-marketing',
            'short_description' => 'Improve your online visibility and search rankings',
            'description' => 'Comprehensive SEO and digital marketing strategies to increase your online presence. We optimize your website for search engines and create engaging marketing campaigns.',
            'icon' => 'trending-up',
            'sort_order' => 3,
            'is_active' => true,
            'is_featured' => true,
            'features' => [
                'On-Page SEO',
                'Technical SEO',
                'Content Marketing',
                'Link Building',
                'Social Media Marketing',
            ],
            'meta_title' => 'SEO and Digital Marketing Services',
            'meta_description' => 'Expert SEO and digital marketing solutions to boost your online presence.',
        ]);

        Service::create([
            'name' => 'Business Consulting',
            'slug' => 'business-consulting',
            'short_description' => 'Strategic technology consulting for your business',
            'description' => 'Expert consulting services to help you leverage technology for business growth. We assess your current infrastructure and recommend solutions aligned with your goals.',
            'icon' => 'briefcase',
            'sort_order' => 4,
            'is_active' => true,
            'is_featured' => false,
            'features' => [
                'Technology Assessment',
                'Strategy Planning',
                'Digital Transformation',
                'Process Optimization',
                'Cost Analysis',
            ],
            'meta_title' => 'Business Technology Consulting',
            'meta_description' => 'Strategic technology consulting to optimize your business operations.',
        ]);

        Service::create([
            'name' => 'Brand Design & Identity',
            'slug' => 'brand-design-identity',
            'short_description' => 'Creative branding and design services',
            'description' => 'Create a memorable brand identity with our design experts. From logo design to complete brand guidelines, we help you stand out in your market.',
            'icon' => 'palette',
            'sort_order' => 5,
            'is_active' => true,
            'is_featured' => false,
            'features' => [
                'Logo Design',
                'Brand Guidelines',
                'Visual Identity',
                'Marketing Materials',
                'Packaging Design',
            ],
            'meta_title' => 'Professional Brand Design Services',
            'meta_description' => 'Creative branding and design services to build your unique brand identity.',
        ]);

        Service::create([
            'name' => 'Maintenance & Support',
            'slug' => 'maintenance-support',
            'short_description' => 'Ongoing maintenance and technical support',
            'description' => 'Keep your applications running smoothly with our maintenance and support services. We provide updates, security patches, and technical assistance when you need it.',
            'icon' => 'wrench',
            'sort_order' => 6,
            'is_active' => true,
            'is_featured' => false,
            'features' => [
                'Regular Updates',
                'Security Monitoring',
                '24/7 Support',
                'Bug Fixes',
                'Performance Monitoring',
            ],
            'meta_title' => 'Application Maintenance & Support',
            'meta_description' => 'Professional maintenance and support services for your applications.',
        ]);

        $this->command->info('6 services seeded successfully!');
    }
}
