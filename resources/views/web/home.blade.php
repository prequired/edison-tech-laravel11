@extends('layouts.guest')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-800 text-white overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <svg class="absolute top-0 right-0 w-96 h-96 transform translate-x-1/2 -translate-y-1/2 opacity-10" viewBox="0 0 400 400" fill="none">
            <circle cx="200" cy="200" r="200" stroke="white" stroke-width="1" fill="none"/>
            <circle cx="200" cy="200" r="150" stroke="white" stroke-width="1" fill="none"/>
            <circle cx="200" cy="200" r="100" stroke="white" stroke-width="1" fill="none"/>
        </svg>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight mb-6">
                Professional Web Development Solutions
            </h1>

            <p class="text-lg sm:text-xl text-indigo-100 mb-8 leading-relaxed">
                Transform your business with cutting-edge web development and digital solutions. We create stunning, high-performance applications that drive growth and engagement.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#contact" class="inline-flex items-center justify-center px-8 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Get Started
                </a>

                <a href="#portfolio" class="inline-flex items-center justify-center px-8 py-3 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-400 transition-colors border-2 border-white border-opacity-30">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    View Portfolio
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-white py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Completed Projects -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $stats['completed_projects'] ?? '150' }}+</div>
                <p class="text-gray-600 mt-2">Projects Completed</p>
            </div>

            <!-- Happy Clients -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $stats['happy_clients'] ?? '120' }}+</div>
                <p class="text-gray-600 mt-2">Happy Clients</p>
            </div>

            <!-- Services Offered -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 015.646 5.646 9.001 9.001 0 0120.354 15.354z" />
                    </svg>
                </div>
                <div class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $stats['services_offered'] ?? '8' }}</div>
                <p class="text-gray-600 mt-2">Services Offered</p>
            </div>

            <!-- Years Experience -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $stats['years_experience'] ?? '8' }}+</div>
                <p class="text-gray-600 mt-2">Years Experience</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Services Section -->
<section class="bg-gray-50 py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Our Featured Services</h2>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Comprehensive web solutions tailored to your business needs and goals</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($featuredServices ?? [] as $service)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all p-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-100 rounded-lg mb-6">
                        @if(isset($service['icon']))
                            {!! $service['icon'] !!}
                        @else
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        @endif
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $service['name'] ?? 'Service' }}</h3>
                    <p class="text-gray-600 mb-6">{{ $service['description'] ?? 'Professional service tailored to your needs' }}</p>
                    <a href="#" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                        Learn More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @empty
                <!-- Default Services -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all p-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-100 rounded-lg mb-6">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20m0 0l-.75 3M9 20H5m4 0h10m0 0l.75 3M19 20l.75 3M3 13h2m2 0h2m2 0h2m2 0h2m2 0h2M5 7a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Web Design</h3>
                    <p class="text-gray-600 mb-6">Create stunning, user-friendly interfaces that engage and convert your audience</p>
                    <a href="#" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                        Learn More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all p-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 rounded-lg mb-6">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Web Development</h3>
                    <p class="text-gray-600 mb-6">Build powerful, scalable web applications with modern technologies and best practices</p>
                    <a href="#" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                        Learn More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all p-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-green-100 rounded-lg mb-6">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Optimization</h3>
                    <p class="text-gray-600 mb-6">Improve performance, SEO, and user experience for maximum impact and results</p>
                    <a href="#" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                        Learn More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Portfolio Section -->
<section id="portfolio" class="bg-white py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Featured Projects</h2>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Showcasing our latest and most innovative client projects</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($featuredPortfolio ?? [] as $portfolio)
                <div class="group relative bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-xl transition-shadow">
                    <div class="relative h-64 bg-gray-200 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">{{ $portfolio['category'] ?? 'Project' }}</p>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">{{ $portfolio['title'] ?? 'Project Title' }}</h3>
                        <p class="mt-3 text-gray-600 text-sm">{{ $portfolio['description'] ?? 'Project description' }}</p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            @if(isset($portfolio['tags']))
                                @foreach($portfolio['tags'] as $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    Web Development
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    Design
                                </span>
                            @endif
                        </div>
                        <a href="#" class="mt-6 inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                            View Project
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <!-- Default Portfolio Items -->
                <div class="group relative bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-xl transition-shadow">
                    <div class="relative h-64 bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">E-Commerce</p>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Modern Marketplace Platform</h3>
                        <p class="mt-3 text-gray-600 text-sm">Fully responsive e-commerce platform with advanced filtering and payment integration</p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Laravel
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Tailwind
                            </span>
                        </div>
                        <a href="#" class="mt-6 inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                            View Project
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="group relative bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-xl transition-shadow">
                    <div class="relative h-64 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">Business Tools</p>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Corporate Dashboard System</h3>
                        <p class="mt-3 text-gray-600 text-sm">Comprehensive analytics dashboard with real-time data visualization and reporting</p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                React
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                API
                            </span>
                        </div>
                        <a href="#" class="mt-6 inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                            View Project
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="group relative bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-xl transition-shadow">
                    <div class="relative h-64 bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-indigo-600 font-semibold uppercase tracking-wide">Community</p>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Social Network Application</h3>
                        <p class="mt-3 text-gray-600 text-sm">Feature-rich social platform with messaging, notifications, and community features</p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Node.js
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Vue.js
                            </span>
                        </div>
                        <a href="#" class="mt-6 inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                            View Project
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="bg-gray-50 py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">What Our Clients Say</h2>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Trusted by businesses of all sizes</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($testimonials ?? [] as $testimonial)
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">{{ $testimonial['quote'] ?? '"Exceptional service and outstanding results. Highly recommended!"' }}</p>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-600">
                                <span class="text-white text-sm font-bold">{{ substr($testimonial['name'] ?? 'User', 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $testimonial['name'] ?? 'Client Name' }}</p>
                            <p class="text-sm text-gray-600">{{ $testimonial['company'] ?? 'Company Name' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">"The team delivered exceptional work that exceeded our expectations. Highly recommended!"</p>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-600">
                                <span class="text-white text-sm font-bold">S</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Sarah Johnson</p>
                            <p class="text-sm text-gray-600">Tech Startup CEO</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">"Professional, reliable, and results-driven. Our project was completed on time and budget."</p>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-600">
                                <span class="text-white text-sm font-bold">M</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Michael Chen</p>
                            <p class="text-sm text-gray-600">E-Commerce Director</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 italic">"Great communication, innovative solutions, and excellent support throughout the project."</p>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-600">
                                <span class="text-white text-sm font-bold">E</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Emily Rodriguez</p>
                            <p class="text-sm text-gray-600">Marketing Manager</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section id="contact" class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white py-16 sm:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4">Ready to Start Your Project?</h2>
        <p class="text-lg text-indigo-100 mb-8">Get in touch with our team today and let's build something amazing together</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#" class="inline-flex items-center justify-center px-8 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Contact Us
            </a>
            <a href="#" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-indigo-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2 1m2-1l-2-1m2 1v2.5" />
                </svg>
                Schedule a Call
            </a>
        </div>
    </div>
</section>
@endsection
