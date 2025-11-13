@extends('layouts.guest')

@section('title', 'About Us')

@section('content')
    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden bg-gradient-to-b from-indigo-50 to-white px-6 py-24 sm:py-32 lg:overflow-visible lg:px-0">
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <svg class="absolute left-[max(50%,25rem)] top-0 h-[64rem] w-[128rem] -translate-x-1/2 stroke-gray-200 [mask-image:radial-gradient(64rem_64rem_at_50%_0%,white,transparent)]" aria-hidden="true">
                <defs>
                    <pattern id="about-pattern" x="50%" y="50%" patternUnits="userSpaceOnUse" patternTransform="translate(-64 0)" width="200" height="200">
                        <path d="M.5,200V.5H200" fill="none" />
                    </pattern>
                </defs>
                <svg x="50%" y="50%" class="overflow-visible fill-gray-50">
                    <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                </svg>
                <rect width="100%" height="100%" stroke-width="0" fill="url(#about-pattern)" />
            </svg>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                About <span class="text-indigo-600">Edison Tech</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600">
                We are a team of passionate developers and designers committed to delivering exceptional digital solutions that transform businesses.
            </p>
        </div>
    </div>

    <!-- Company Story -->
    <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8 sm:py-32">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 items-center">
            <div>
                <h2 class="text-4xl font-bold tracking-tight text-gray-900 mb-6">Our Story</h2>
                <p class="text-lg text-gray-600 mb-4">
                    Founded in 2018, Edison Tech started with a simple mission: to help businesses leverage technology to achieve their goals. What began as a small team of passionate developers has grown into a full-service digital agency.
                </p>
                <p class="text-lg text-gray-600 mb-4">
                    Today, we work with companies of all sizes—from startups to enterprises—delivering custom web development, digital strategy, and comprehensive digital transformation solutions.
                </p>
                <p class="text-lg text-gray-600">
                    Our approach combines technical excellence with business insight, ensuring that every solution we build creates real value for our clients.
                </p>
            </div>
            <div class="rounded-lg bg-gradient-to-br from-indigo-100 to-indigo-200 p-12 flex items-center justify-center">
                <svg class="w-32 h-32 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Mission & Vision Cards -->
    <div class="bg-gray-50 px-6 py-16 lg:px-8 sm:py-24">
        <div class="mx-auto max-w-7xl">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <!-- Mission -->
                <div class="rounded-lg bg-white p-8 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-600 text-white mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Our Mission</h3>
                    <p class="text-gray-600">
                        To empower businesses with cutting-edge digital solutions that drive growth, improve efficiency, and create lasting competitive advantages in their markets.
                    </p>
                </div>

                <!-- Vision -->
                <div class="rounded-lg bg-white p-8 border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-600 text-white mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Our Vision</h3>
                    <p class="text-gray-600">
                        To be the most trusted digital transformation partner for businesses seeking innovation, reliability, and excellence in their digital journey.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    @if($stats && count($stats) > 0)
        <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8 sm:py-32">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">By The Numbers</h2>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                @foreach($stats as $stat)
                    <div class="text-center">
                        <div class="text-4xl font-bold text-indigo-600 mb-2">{{ $stat['value'] }}</div>
                        <p class="text-gray-600">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Team Section -->
    @if($teamMembers && count($teamMembers) > 0)
        <div class="bg-gray-50 px-6 py-24 lg:px-8 sm:py-32">
            <div class="mx-auto max-w-7xl">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 mb-4">Meet Our Team</h2>
                    <p class="text-lg text-gray-600">
                        Talented professionals dedicated to delivering excellence
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                    @foreach($teamMembers as $member)
                        <div class="rounded-lg overflow-hidden border border-gray-200 bg-white hover:shadow-lg transition-shadow">
                            <!-- Photo -->
                            @if($member->photo)
                                <div class="relative h-48 overflow-hidden bg-gray-100">
                                    <img src="{{ $member->photo }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="h-48 bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Info -->
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $member->name }}</h3>
                                <p class="text-indigo-600 font-semibold text-sm mb-3">{{ $member->position }}</p>
                                @if($member->bio)
                                    <p class="text-gray-600 text-sm mb-4">{{ $member->bio }}</p>
                                @endif
                                <div class="flex gap-3">
                                    @if($member->linkedin)
                                        <a href="{{ $member->linkedin }}" target="_blank" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($member->twitter)
                                        <a href="{{ $member->twitter }}" target="_blank" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2s9 5 20 5a9.5 9.5 0 00-9-5.5c4.75 2.25 7-7 7-7"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Testimonials Section -->
    @if($testimonials && count($testimonials) > 0)
        <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8 sm:py-32">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 mb-4">What Our Clients Say</h2>
                <p class="text-lg text-gray-600">
                    Don't just take our word for it—hear from our satisfied clients
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($testimonials as $testimonial)
                    <div class="rounded-lg border border-gray-200 bg-white p-8 shadow-sm hover:shadow-md transition-shadow">
                        <!-- Stars -->
                        <div class="flex gap-1 mb-4">
                            @for($i = 0; $i < ($testimonial->rating ?? 5); $i++)
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>

                        <!-- Quote -->
                        <p class="text-gray-600 mb-6 italic">
                            "{{ $testimonial->content }}"
                        </p>

                        <!-- Author -->
                        <div class="flex items-center gap-3">
                            @if($testimonial->photo)
                                <img src="{{ $testimonial->photo }}" alt="{{ $testimonial->author }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-semibold text-sm">
                                    {{ substr($testimonial->author, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">{{ $testimonial->author }}</p>
                                <p class="text-sm text-gray-500">{{ $testimonial->company }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- CTA Section -->
    <div class="bg-indigo-600 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Ready to work with us?
            </h2>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-indigo-100">
                Let's discuss how we can help bring your vision to life.
            </p>
            <div class="mt-10 flex justify-center gap-x-6">
                <a href="{{ route('contact') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 transition-colors">
                    Get in Touch
                </a>
                <a href="{{ route('services.index') }}" class="inline-flex items-center text-sm font-semibold leading-6 text-white hover:text-indigo-100">
                    Our Services
                    <span aria-hidden="true" class="ml-2">→</span>
                </a>
            </div>
        </div>
    </div>
@endsection
