@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create User</h1>
            <p class="text-gray-600 mt-1">Add a new user to your system</p>
        </div>
        <a
            href="{{ route('admin.users.index') }}"
            class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
        >
            Back
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm p-8">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Name Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- First Name / Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                        value="{{ old('name') }}"
                        placeholder="Enter full name"
                        class="w-full px-4 py-3 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('name')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        value="{{ old('email') }}"
                        placeholder="Enter email address"
                        class="w-full px-4 py-3 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('email')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Enter password"
                        class="w-full px-4 py-3 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    <p class="text-xs text-gray-500 mt-2">Minimum 8 characters, must include uppercase, lowercase, number, and special character</p>
                    @error('password')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        placeholder="Confirm password"
                        class="w-full px-4 py-3 border {{ $errors->has('password_confirmation') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('password_confirmation')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Phone and Company Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="Enter phone number"
                        class="w-full px-4 py-3 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                    @error('phone')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company -->
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                    <select
                        id="company_id"
                        name="company_id"
                        class="w-full px-4 py-3 border {{ $errors->has('company_id') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Select a company</option>
                        @forelse($companies ?? [] as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @empty
                        @endforelse
                    </select>
                    @error('company_id')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Role and Timezone Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Role <span class="text-red-600">*</span>
                    </label>
                    <select
                        id="role"
                        name="role"
                        required
                        class="w-full px-4 py-3 border {{ $errors->has('role') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Select a role</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Employee</option>
                        <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>Client</option>
                    </select>
                    <div class="mt-2 p-3 bg-gray-50 rounded text-sm text-gray-600">
                        <p class="font-medium mb-2">Role Descriptions:</p>
                        <ul class="space-y-1">
                            <li><strong>Admin:</strong> Full access to all system features and user management</li>
                            <li><strong>Employee:</strong> Can manage projects, tasks, and time entries</li>
                            <li><strong>Client:</strong> View-only access to assigned projects and tasks</li>
                        </ul>
                    </div>
                    @error('role')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Timezone -->
                <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                    <select
                        id="timezone"
                        name="timezone"
                        class="w-full px-4 py-3 border {{ $errors->has('timezone') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Select a timezone</option>
                        <option value="UTC" {{ old('timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="America/New_York" {{ old('timezone') === 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                        <option value="America/Chicago" {{ old('timezone') === 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                        <option value="America/Denver" {{ old('timezone') === 'America/Denver' ? 'selected' : '' }}>Mountain Time (MT)</option>
                        <option value="America/Los_Angeles" {{ old('timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                        <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>London</option>
                        <option value="Europe/Paris" {{ old('timezone') === 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                        <option value="Europe/Berlin" {{ old('timezone') === 'Europe/Berlin' ? 'selected' : '' }}>Berlin</option>
                        <option value="Asia/Tokyo" {{ old('timezone') === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                        <option value="Asia/Singapore" {{ old('timezone') === 'Asia/Singapore' ? 'selected' : '' }}>Singapore</option>
                        <option value="Asia/Kolkata" {{ old('timezone') === 'Asia/Kolkata' ? 'selected' : '' }}>India Standard Time</option>
                        <option value="Australia/Sydney" {{ old('timezone') === 'Australia/Sydney' ? 'selected' : '' }}>Sydney</option>
                    </select>
                    @error('timezone')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Avatar Upload -->
            <div class="mb-6">
                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">Avatar</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <input
                        type="file"
                        id="avatar"
                        name="avatar"
                        accept="image/*"
                        class="w-full"
                    />
                    <p class="text-xs text-gray-500 mt-2">Accepted formats: JPG, PNG, GIF. Max size: 5MB</p>
                </div>
                @error('avatar')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status Checkbox -->
            <div class="mb-8">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500"
                    />
                    <span class="text-sm font-medium text-gray-700">User is active</span>
                </label>
                @error('is_active')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-200 font-medium"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-medium"
                >
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
