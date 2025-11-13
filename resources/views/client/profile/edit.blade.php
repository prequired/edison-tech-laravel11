@extends('layouts.app')

@section('title', 'Edit My Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('client.profile.show') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Profile
            </a>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Edit My Profile</h1>
            <p class="text-gray-600">Update your personal information</p>
        </div>

        <!-- Main Form Card -->
        <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Avatar Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Profile Picture</h2>

                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Current Avatar -->
                    <div class="flex flex-col items-center justify-center">
                        <div class="mb-4">
                            <img src="{{ $user->avatar_url ?? 'https://via.placeholder.com/120' }}" alt="{{ $user->name }}" class="h-32 w-32 rounded-lg shadow-md object-cover border-4 border-indigo-100">
                        </div>
                        <p class="text-sm text-gray-500 text-center">Current Picture</p>
                    </div>

                    <!-- Upload New Avatar -->
                    <div class="flex-1">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-indigo-400 transition-colors cursor-pointer" id="avatar-dropzone">
                            <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*" />
                            <label for="avatar" class="cursor-pointer">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <p class="text-sm text-gray-700 font-medium">Drop your picture here or click to browse</p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </label>
                        </div>
                        @error('avatar')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Personal Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror" />
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror" />
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') border-red-500 @enderror" />
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Time Zone -->
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Time Zone
                        </label>
                        <select name="timezone" id="timezone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('timezone') border-red-500 @enderror">
                            <option value="">Select a timezone</option>
                            <option value="UTC" {{ old('timezone', $user->timezone) === 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ old('timezone', $user->timezone) === 'America/New_York' ? 'selected' : '' }}>Eastern Time (US & Canada)</option>
                            <option value="America/Chicago" {{ old('timezone', $user->timezone) === 'America/Chicago' ? 'selected' : '' }}>Central Time (US & Canada)</option>
                            <option value="America/Denver" {{ old('timezone', $user->timezone) === 'America/Denver' ? 'selected' : '' }}>Mountain Time (US & Canada)</option>
                            <option value="America/Los_Angeles" {{ old('timezone', $user->timezone) === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (US & Canada)</option>
                            <option value="Europe/London" {{ old('timezone', $user->timezone) === 'Europe/London' ? 'selected' : '' }}>GMT/BST (UK)</option>
                            <option value="Europe/Paris" {{ old('timezone', $user->timezone) === 'Europe/Paris' ? 'selected' : '' }}>Central European Time</option>
                            <option value="Europe/Moscow" {{ old('timezone', $user->timezone) === 'Europe/Moscow' ? 'selected' : '' }}>Moscow Standard Time</option>
                            <option value="Asia/Dubai" {{ old('timezone', $user->timezone) === 'Asia/Dubai' ? 'selected' : '' }}>Gulf Standard Time</option>
                            <option value="Asia/Singapore" {{ old('timezone', $user->timezone) === 'Asia/Singapore' ? 'selected' : '' }}>Singapore Time</option>
                            <option value="Asia/Tokyo" {{ old('timezone', $user->timezone) === 'Asia/Tokyo' ? 'selected' : '' }}>Japan Standard Time</option>
                            <option value="Australia/Sydney" {{ old('timezone', $user->timezone) === 'Australia/Sydney' ? 'selected' : '' }}>Australian Eastern Time</option>
                        </select>
                        @error('timezone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
                        <p class="text-sm text-gray-600 mt-1">Update your password to keep your account secure</p>
                    </div>
                    <a href="{{ route('client.profile.change-password') }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Change Password
                    </a>
                </div>
            </div>

            <!-- Security Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Security Settings</h2>
                        <p class="text-sm text-gray-600 mt-1">Manage your account security and authentication methods</p>
                    </div>
                    <a href="{{ route('client.profile.security') }}" class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Manage Security
                    </a>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4">
                <a href="{{ route('client.profile.show') }}" class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>

        <!-- Additional Information -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex gap-4">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-900 mb-1">Profile Security Tips</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Keep your email address up to date for security notifications</li>
                        <li>• Use a strong, unique password and change it regularly</li>
                        <li>• Enable two-factor authentication for enhanced security</li>
                        <li>• Review your security settings periodically</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Drag and drop for avatar upload
    const dropzone = document.getElementById('avatar-dropzone');
    const avatarInput = document.getElementById('avatar');

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-indigo-500', 'bg-indigo-50');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');
        avatarInput.files = e.dataTransfer.files;
    });
</script>
@endpush
@endsection
