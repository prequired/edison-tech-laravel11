@extends('layouts.app')

@section('title', 'Company Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header with Edit Button -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Company Profile</h1>
                <p class="text-gray-600">Manage your company information and settings</p>
            </div>
            <a href="{{ route('client.company.edit') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Profile
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Info Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Company Info Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6">
                        <div class="flex items-center gap-4">
                            @if($company->logo_url)
                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="w-20 h-20 rounded-lg shadow-lg object-cover">
                            @else
                                <div class="w-20 h-20 bg-white rounded-lg shadow-lg flex items-center justify-center">
                                    <span class="text-indigo-600 font-bold text-3xl">{{ substr($company->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-3xl font-bold text-white mb-1">{{ $company->name }}</h2>
                                <p class="text-indigo-100">{{ $company->industry ?? 'Business' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Contact Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Contact Information</h3>

                                <div class="space-y-4">
                                    <!-- Email -->
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Email</p>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <a href="mailto:{{ $company->email }}" class="text-gray-900 hover:text-indigo-600 font-medium">
                                                {{ $company->email }}
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Phone</p>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <a href="tel:{{ $company->phone }}" class="text-gray-900 hover:text-indigo-600 font-medium">
                                                {{ $company->phone }}
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Website -->
                                    @if($company->website)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Website</p>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                                </svg>
                                                <a href="{{ $company->website }}" target="_blank" class="text-gray-900 hover:text-indigo-600 font-medium break-all">
                                                    {{ parse_url($company->website, PHP_URL_HOST) ?? $company->website }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Business Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Business Details</h3>

                                <div class="space-y-4">
                                    <!-- Tax ID -->
                                    @if($company->tax_id)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Tax ID</p>
                                            <p class="text-gray-900 font-medium">{{ $company->tax_id }}</p>
                                        </div>
                                    @endif

                                    <!-- Registration Number -->
                                    @if($company->registration_number)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Registration Number</p>
                                            <p class="text-gray-900 font-medium">{{ $company->registration_number }}</p>
                                        </div>
                                    @endif

                                    <!-- Founded Date -->
                                    @if($company->founded_date)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Founded</p>
                                            <p class="text-gray-900 font-medium">{{ $company->founded_date->format('F Y') }}</p>
                                        </div>
                                    @endif

                                    <!-- Employees -->
                                    @if($company->number_of_employees)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Employees</p>
                                            <p class="text-gray-900 font-medium">{{ $company->number_of_employees }}+</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Business Address -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Business Address</h3>

                        <div class="space-y-3 text-gray-700">
                            @if($company->address_line_1)
                                <p>{{ $company->address_line_1 }}</p>
                            @endif
                            @if($company->address_line_2)
                                <p>{{ $company->address_line_2 }}</p>
                            @endif
                            <p>
                                @if($company->city) {{ $company->city }}, @endif
                                @if($company->state) {{ $company->state }} @endif
                                @if($company->postal_code) {{ $company->postal_code }} @endif
                            </p>
                            @if($company->country)
                                <p class="font-medium">{{ $company->country }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Billing Address -->
                    @if($company->billing_address_line_1 || $company->billing_same_as_address)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Billing Address</h3>

                            @if($company->billing_same_as_address)
                                <p class="text-gray-600 italic">Same as business address</p>
                            @else
                                <div class="space-y-3 text-gray-700">
                                    @if($company->billing_address_line_1)
                                        <p>{{ $company->billing_address_line_1 }}</p>
                                    @endif
                                    @if($company->billing_address_line_2)
                                        <p>{{ $company->billing_address_line_2 }}</p>
                                    @endif
                                    <p>
                                        @if($company->billing_city) {{ $company->billing_city }}, @endif
                                        @if($company->billing_state) {{ $company->billing_state }} @endif
                                        @if($company->billing_postal_code) {{ $company->billing_postal_code }} @endif
                                    </p>
                                    @if($company->billing_country)
                                        <p class="font-medium">{{ $company->billing_country }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar - Statistics -->
            <div class="lg:col-span-1">
                <!-- Statistics Card -->
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">Statistics</h3>

                    <div class="space-y-4">
                        <!-- Total Projects -->
                        <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Total Projects</p>
                                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $company->projects_count ?? 0 }}</p>
                            </div>
                            <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>

                        <!-- Active Projects -->
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Active Projects</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $company->active_projects_count ?? 0 }}</p>
                            </div>
                            <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>

                        <!-- Total Invoices -->
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Total Invoices</p>
                                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $company->invoices_count ?? 0 }}</p>
                            </div>
                            <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        <!-- Account Created -->
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 font-medium">Account Created</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $company->created_at->format('F d, Y') }}</p>
                        </div>

                        <!-- Last Updated -->
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Last Updated</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $company->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <a href="{{ route('client.company.edit') }}" class="w-full mt-6 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
