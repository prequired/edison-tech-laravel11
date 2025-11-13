@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">My Profile</h1>
            <p class="text-gray-600">Manage your account and personal information</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Profile Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6">
                        <div class="flex items-end gap-4">
                            <img src="{{ $user->avatar_url ?? 'https://via.placeholder.com/120' }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-lg shadow-lg object-cover border-4 border-white">
                            <div>
                                <h2 class="text-3xl font-bold text-white mb-1">{{ $user->name }}</h2>
                                <p class="text-indigo-100 text-lg">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Personal Information</h3>

                                <div class="space-y-4">
                                    <!-- Full Name -->
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Full Name</p>
                                        <p class="text-gray-900 font-medium text-lg">{{ $user->name }}</p>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Email Address</p>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <a href="mailto:{{ $user->email }}" class="text-gray-900 hover:text-indigo-600 font-medium">
                                                {{ $user->email }}
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    @if($user->phone)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Phone Number</p>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                <a href="tel:{{ $user->phone }}" class="text-gray-900 hover:text-indigo-600 font-medium">
                                                    {{ $user->phone }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Time Zone -->
                                    @if($user->timezone)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Time Zone</p>
                                            <p class="text-gray-900 font-medium">{{ $user->timezone }}</p>
                                        </div>
                                    @endif

                                    <!-- Account Status -->
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Account Status</p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">Company & Role</h3>

                                <div class="space-y-4">
                                    <!-- Company -->
                                    @if($user->company)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-2">Company</p>
                                            <div class="flex items-center gap-3 p-3 bg-indigo-50 rounded-lg">
                                                @if($user->company->logo_url)
                                                    <img src="{{ $user->company->logo_url }}" alt="{{ $user->company->name }}" class="w-8 h-8 rounded object-cover">
                                                @else
                                                    <div class="w-8 h-8 bg-indigo-200 rounded flex items-center justify-center">
                                                        <span class="text-indigo-600 font-bold text-xs">{{ substr($user->company->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $user->company->name }}</p>
                                                    <a href="{{ route('client.company.show') }}" class="text-xs text-indigo-600 hover:text-indigo-700">
                                                        View Company
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Role -->
                                    @if($user->role)
                                        <div>
                                            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Role</p>
                                            <p class="text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                                        </div>
                                    @endif

                                    <!-- Last Login -->
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Last Login</p>
                                        @if($user->last_login_at)
                                            <p class="text-gray-900 font-medium">{{ $user->last_login_at->diffForHumans() }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $user->last_login_at->format('F d, Y \a\t g:i A') }}</p>
                                        @else
                                            <p class="text-gray-700">Never logged in</p>
                                        @endif
                                    </div>

                                    <!-- Member Since -->
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-1">Member Since</p>
                                        <p class="text-gray-900 font-medium">{{ $user->created_at->format('F d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <p class="text-sm text-gray-600 font-medium">Active Projects</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $user->active_projects_count ?? 0 }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-4">
                        <p class="text-sm text-gray-600 font-medium">Documents</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $user->documents_count ?? 0 }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-4">
                        <p class="text-sm text-gray-600 font-medium">Invoices</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $user->invoices_count ?? 0 }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-4">
                        <p class="text-sm text-gray-600 font-medium">Support Tickets</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $user->support_tickets_count ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Quick Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">Quick Actions</h3>

                    <div class="space-y-3">
                        <!-- Edit Profile -->
                        <a href="{{ route('client.profile.edit') }}" class="w-full flex items-center gap-3 p-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit Profile</span>
                        </a>

                        <!-- Change Password -->
                        <a href="{{ route('client.profile.change-password') }}" class="w-full flex items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 7z"/>
                            </svg>
                            <span>Change Password</span>
                        </a>

                        <!-- Security Settings -->
                        <a href="{{ route('client.profile.security') }}" class="w-full flex items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Security Settings</span>
                        </a>

                        <!-- Notification Preferences -->
                        <a href="{{ route('client.profile.notifications') }}" class="w-full flex items-center gap-3 p-4 bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span>Notifications</span>
                        </a>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Account Information</h4>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email Verified</span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Yes
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Two-Factor Auth</span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $user->two_factor_enabled ? '✓ Enabled' : 'Disabled' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Last Updated</span>
                                <span class="text-gray-900 font-medium">{{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
