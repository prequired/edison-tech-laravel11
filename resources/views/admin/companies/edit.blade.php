@extends('layouts.admin')

@section('content')
<div class="py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Company</h1>
            <p class="text-gray-600 mt-1">Update company information and settings</p>
        </div>
        <a
            href="{{ route('admin.companies.index') }}"
            class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-medium"
        >
            Back
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Company Name
                            <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $company->name) }}"
                            placeholder="Enter company name"
                            class="w-full px-4 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            required
                        />
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                            <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $company->email) }}"
                            placeholder="Enter company email"
                            class="w-full px-4 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            required
                        />
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $company->phone) }}"
                            placeholder="Enter company phone"
                            class="w-full px-4 py-2 border @error('phone') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        @error('phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax ID -->
                    <div>
                        <label for="tax_id" class="block text-sm font-semibold text-gray-700 mb-2">Tax ID</label>
                        <input
                            type="text"
                            id="tax_id"
                            name="tax_id"
                            value="{{ old('tax_id', $company->tax_id) }}"
                            placeholder="Enter tax ID"
                            class="w-full px-4 py-2 border @error('tax_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        @error('tax_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                        <input
                            type="url"
                            id="website"
                            name="website"
                            value="{{ old('website', $company->website) }}"
                            placeholder="https://example.com"
                            class="w-full px-4 py-2 border @error('website') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        @error('website')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Logo</label>
                        <input
                            type="file"
                            id="logo"
                            name="logo"
                            accept="image/*"
                            class="w-full px-4 py-2 border @error('logo') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, GIF (Max 5MB) - Leave empty to keep current logo</p>
                        @error('logo')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Current Logo Display -->
                @if($company->logo)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-3">Current Logo</p>
                        <div class="flex items-center justify-between">
                            <img
                                src="{{ asset('storage/' . $company->logo) }}"
                                alt="{{ $company->name }}"
                                class="w-20 h-20 rounded-lg object-cover"
                            />
                            <form
                                action="{{ route('admin.companies.delete-logo', $company->id) }}"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to remove the logo?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition font-medium"
                                >
                                    Remove Logo
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <hr />

            <!-- Address Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Address</h2>
                <div class="space-y-6">
                    <!-- Address Line -->
                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                            Street Address
                            <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="{{ old('address', $company->address) }}"
                            placeholder="Enter street address"
                            class="w-full px-4 py-2 border @error('address') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            required
                        />
                        @error('address')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                value="{{ old('city', $company->city) }}"
                                placeholder="Enter city"
                                class="w-full px-4 py-2 border @error('city') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            @error('city')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State</label>
                            <input
                                type="text"
                                id="state"
                                name="state"
                                value="{{ old('state', $company->state) }}"
                                placeholder="Enter state"
                                class="w-full px-4 py-2 border @error('state') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            @error('state')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-semibold text-gray-700 mb-2">Postal Code</label>
                            <input
                                type="text"
                                id="postal_code"
                                name="postal_code"
                                value="{{ old('postal_code', $company->postal_code) }}"
                                placeholder="Enter postal code"
                                class="w-full px-4 py-2 border @error('postal_code') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            @error('postal_code')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">Country</label>
                            <input
                                type="text"
                                id="country"
                                name="country"
                                value="{{ old('country', $company->country) }}"
                                placeholder="Enter country"
                                class="w-full px-4 py-2 border @error('country') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            @error('country')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr />

            <!-- Billing Address Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Billing Address</h2>
                <div class="space-y-6">
                    <!-- Billing Address Line -->
                    <div>
                        <label for="billing_address" class="block text-sm font-semibold text-gray-700 mb-2">Street Address</label>
                        <input
                            type="text"
                            id="billing_address"
                            name="billing_address"
                            value="{{ old('billing_address', $company->billing_address) }}"
                            placeholder="Enter billing street address"
                            class="w-full px-4 py-2 border @error('billing_address') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                        <p class="text-xs text-gray-500 mt-1">Leave empty to use the main address</p>
                        @error('billing_address')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Billing City -->
                        <div>
                            <label for="billing_city" class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                            <input
                                type="text"
                                id="billing_city"
                                name="billing_city"
                                value="{{ old('billing_city', $company->billing_city) }}"
                                placeholder="Enter city"
                                class="w-full px-4 py-2 border @error('billing_city') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            @error('billing_city')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing State -->
                        <div>
                            <label for="billing_state" class="block text-sm font-semibold text-gray-700 mb-2">State</label>
                            <input
                                type="text"
                                id="billing_state"
                                name="billing_state"
                                value="{{ old('billing_state', $company->billing_state) }}"
                                placeholder="Enter state"
                                class="w-full px-4 py-2 border @error('billing_state') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            @error('billing_state')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Postal Code -->
                        <div>
                            <label for="billing_postal_code" class="block text-sm font-semibold text-gray-700 mb-2">Postal Code</label>
                            <input
                                type="text"
                                id="billing_postal_code"
                                name="billing_postal_code"
                                value="{{ old('billing_postal_code', $company->billing_postal_code) }}"
                                placeholder="Enter postal code"
                                class="w-full px-4 py-2 border @error('billing_postal_code') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            @error('billing_postal_code')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Country -->
                        <div>
                            <label for="billing_country" class="block text-sm font-semibold text-gray-700 mb-2">Country</label>
                            <input
                                type="text"
                                id="billing_country"
                                name="billing_country"
                                value="{{ old('billing_country', $company->billing_country) }}"
                                placeholder="Enter country"
                                class="w-full px-4 py-2 border @error('billing_country') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            @error('billing_country')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr />

            <!-- Status Section -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Status</h2>
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="active"
                        name="active"
                        value="1"
                        {{ old('active', $company->active) ? 'checked' : '' }}
                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer"
                    />
                    <label for="active" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">
                        Mark as Active
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2">Active companies will be visible in the system</p>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 pt-6 border-t">
                <button
                    type="submit"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 font-semibold flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
                <a
                    href="{{ route('admin.companies.index') }}"
                    class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-200 font-semibold"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-lg shadow-sm p-8 border-2 border-red-200">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-red-900">Danger Zone</h2>
                <p class="text-red-700 text-sm mt-1">Permanently delete this company and all associated data</p>
            </div>
            <form
                action="{{ route('admin.companies.destroy', $company->id) }}"
                method="POST"
                onsubmit="return confirm('Are you absolutely sure you want to delete this company? All associated data will be permanently removed and this action cannot be undone.')"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 font-semibold flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Company
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
