@extends('layouts.guest')

@section('title', 'Contact Us')

@section('content')
    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden bg-gradient-to-b from-indigo-50 to-white px-6 py-24 sm:py-32 lg:overflow-visible lg:px-0">
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <svg class="absolute left-[max(50%,25rem)] top-0 h-[64rem] w-[128rem] -translate-x-1/2 stroke-gray-200 [mask-image:radial-gradient(64rem_64rem_at_50%_0%,white,transparent)]" aria-hidden="true">
                <defs>
                    <pattern id="contact-pattern" x="50%" y="50%" patternUnits="userSpaceOnUse" patternTransform="translate(-64 0)" width="200" height="200">
                        <path d="M.5,200V.5H200" fill="none" />
                    </pattern>
                </defs>
                <svg x="50%" y="50%" class="overflow-visible fill-gray-50">
                    <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                </svg>
                <rect width="100%" height="100%" stroke-width="0" fill="url(#contact-pattern)" />
            </svg>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                Get in <span class="text-indigo-600">Touch</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600">
                Have a project in mind? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
            </p>
        </div>
    </div>

    <!-- Contact Content -->
    <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8 sm:py-32">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a message</h2>

                    @if($errors->any())
                        <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
                            <p class="text-sm font-semibold text-red-800 mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="text-sm text-red-700">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
                            <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-20 transition-all @error('name') border-red-500 @enderror" placeholder="John Doe">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-20 transition-all @error('email') border-red-500 @enderror" placeholder="john@example.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Field -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-20 transition-all @error('phone') border-red-500 @enderror" placeholder="+1 (555) 000-0000">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Field -->
                        <div>
                            <label for="company" class="block text-sm font-semibold text-gray-900 mb-2">Company Name</label>
                            <input type="text" id="company" name="company" value="{{ old('company') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-20 transition-all @error('company') border-red-500 @enderror" placeholder="Your Company">
                            @error('company')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject Field -->
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-900 mb-2">Subject</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-20 transition-all @error('subject') border-red-500 @enderror" placeholder="How can we help?">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message Field -->
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-900 mb-2">Message</label>
                            <textarea id="message" name="message" required rows="6" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 focus:ring-opacity-20 transition-all resize-none @error('message') border-red-500 @enderror" placeholder="Tell us more about your project...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 transition-colors">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Information Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Phone -->
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-600 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 4.493a1 1 0 00.502.756l2.73 1.365a1 1 0 001.262-1.169l-1.07-3.292a1 1 0 00-.524-.756L7.465 9.529a1 1 0 00-.956.692l-1.498 4.493a1 1 0 00.502.756l2.73 1.365a1 1 0 001.262-1.169l-1.07-3.292a1 1 0 00.524-.756l2.73 1.365"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase">Phone</h3>
                            <p class="text-lg font-semibold text-gray-900 mt-1">+1 (555) 123-4567</p>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-600 text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase">Email</h3>
                            <p class="text-lg font-semibold text-gray-900 mt-1">hello@edisontech.com</p>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-600 text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase">Address</h3>
                            <p class="text-gray-900 mt-1">
                                123 Tech Street<br>
                                San Francisco, CA 94105<br>
                                United States
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-600 text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase">Business Hours</h3>
                            <div class="text-gray-900 mt-1 space-y-1 text-sm">
                                <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                                <p>Saturday: 10:00 AM - 4:00 PM</p>
                                <p>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="bg-gray-100 px-6 py-24 lg:px-8 sm:py-32">
        <div class="mx-auto max-w-7xl">
            <div class="rounded-lg overflow-hidden border border-gray-200 h-96 bg-gray-200 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-600 font-semibold">Interactive Map</p>
                    <p class="text-gray-500 text-sm mt-1">123 Tech Street, San Francisco, CA 94105</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mx-auto max-w-4xl px-6 py-24 lg:px-8 sm:py-32">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 mb-4">Frequently Asked Questions</h2>
        </div>

        <div class="space-y-6">
            <!-- FAQ Item 1 -->
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">What is your typical response time?</h3>
                <p class="text-gray-600">
                    We aim to respond to all inquiries within 24 business hours. For urgent matters, please call us directly at +1 (555) 123-4567.
                </p>
            </div>

            <!-- FAQ Item 2 -->
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Do you offer free consultations?</h3>
                <p class="text-gray-600">
                    Yes! We offer a free 30-minute consultation to discuss your project, goals, and how we can help. This is a great way to explore if we're the right fit for your needs.
                </p>
            </div>

            <!-- FAQ Item 3 -->
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">What areas do you serve?</h3>
                <p class="text-gray-600">
                    While based in San Francisco, we work with clients globally. Our team is experienced in managing remote projects across different time zones.
                </p>
            </div>

            <!-- FAQ Item 4 -->
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">How do you handle project communication?</h3>
                <p class="text-gray-600">
                    We maintain regular communication through your preferred channels: email, video calls, or project management tools. You'll have a dedicated point of contact for your project.
                </p>
            </div>
        </div>
    </div>
@endsection
