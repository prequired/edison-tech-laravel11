@extends('layouts.app')

@section('title', 'Edit Company Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('client.company.show') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Company Profile
            </a>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Edit Company Profile</h1>
            <p class="text-gray-600">Update your company information</p>
        </div>

        <!-- Main Form Card -->
        <form action="{{ route('client.company.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Logo Section -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Company Logo</h2>

                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Current Logo -->
                        <div class="flex flex-col items-center justify-center">
                            @if($company->logo_url)
                                <div class="mb-4">
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="h-32 w-32 rounded-lg shadow-md object-cover">
                                </div>
                                <p class="text-sm text-gray-500 text-center mb-2">Current Logo</p>
                            @else
                                <div class="h-32 w-32 bg-indigo-100 rounded-lg shadow-md flex items-center justify-center mb-4">
                                    <span class="text-indigo-600 font-bold text-5xl">{{ substr($company->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Upload New Logo -->
                        <div class="flex-1">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-indigo-400 transition-colors cursor-pointer" id="dropzone">
                                <input type="file" name="logo" id="logo" class="hidden" accept="image/*" />
                                <label for="logo" class="cursor-pointer">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <p class="text-sm text-gray-700 font-medium">Drop your logo here or click to browse</p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 5MB</p>
                                    </div>
                                </label>
                            </div>
                            @error('logo')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Company Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Company Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror" />
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Industry -->
                        <div>
                            <label for="industry" class="block text-sm font-medium text-gray-700 mb-2">
                                Industry
                            </label>
                            <input type="text" name="industry" id="industry" value="{{ old('industry', $company->industry) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('industry')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror" />
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $company->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('phone')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                Website
                            </label>
                            <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}" placeholder="https://example.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('website')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax ID -->
                        <div>
                            <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Tax ID
                            </label>
                            <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $company->tax_id) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('tax_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Registration Number -->
                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Registration Number
                            </label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number', $company->registration_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('registration_number')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Business Address -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Business Address</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Address Line 1 -->
                        <div class="md:col-span-2">
                            <label for="address_line_1" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 1
                            </label>
                            <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $company->address_line_1) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('address_line_1')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address Line 2 -->
                        <div class="md:col-span-2">
                            <label for="address_line_2" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 2
                            </label>
                            <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $company->address_line_2) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('address_line_2')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City
                            </label>
                            <input type="text" name="city" id="city" value="{{ old('city', $company->city) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('city')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State/Province -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                State/Province
                            </label>
                            <input type="text" name="state" id="state" value="{{ old('state', $company->state) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('state')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code
                            </label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $company->postal_code) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('postal_code')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                Country
                            </label>
                            <input type="text" name="country" id="country" value="{{ old('country', $company->country) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('country')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Billing Address</h2>

                    <div class="mb-6">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="billing_same_as_address" id="billing_same_as_address" value="1" {{ old('billing_same_as_address', $company->billing_same_as_address) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500" />
                            <span class="text-sm font-medium text-gray-700">Same as business address</span>
                        </label>
                    </div>

                    <div id="billing-address-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6 {{ old('billing_same_as_address', $company->billing_same_as_address) ? 'hidden' : '' }}">
                        <!-- Billing Address Line 1 -->
                        <div class="md:col-span-2">
                            <label for="billing_address_line_1" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 1
                            </label>
                            <input type="text" name="billing_address_line_1" id="billing_address_line_1" value="{{ old('billing_address_line_1', $company->billing_address_line_1) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('billing_address_line_1')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Address Line 2 -->
                        <div class="md:col-span-2">
                            <label for="billing_address_line_2" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 2
                            </label>
                            <input type="text" name="billing_address_line_2" id="billing_address_line_2" value="{{ old('billing_address_line_2', $company->billing_address_line_2) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('billing_address_line_2')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing City -->
                        <div>
                            <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-2">
                                City
                            </label>
                            <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city', $company->billing_city) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('billing_city')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing State -->
                        <div>
                            <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-2">
                                State/Province
                            </label>
                            <input type="text" name="billing_state" id="billing_state" value="{{ old('billing_state', $company->billing_state) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('billing_state')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Postal Code -->
                        <div>
                            <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code
                            </label>
                            <input type="text" name="billing_postal_code" id="billing_postal_code" value="{{ old('billing_postal_code', $company->billing_postal_code) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('billing_postal_code')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Country -->
                        <div>
                            <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-2">
                                Country
                            </label>
                            <input type="text" name="billing_country" id="billing_country" value="{{ old('billing_country', $company->billing_country) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                            @error('billing_country')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4">
                <a href="{{ route('client.company.show') }}" class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
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
    </div>
</div>

@push('scripts')
<script>
    // Handle billing address checkbox
    const billingCheckbox = document.getElementById('billing_same_as_address');
    const billingFields = document.getElementById('billing-address-fields');

    billingCheckbox.addEventListener('change', function() {
        billingFields.classList.toggle('hidden', this.checked);
    });

    // Drag and drop for logo upload
    const dropzone = document.getElementById('dropzone');
    const logoInput = document.getElementById('logo');

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
        logoInput.files = e.dataTransfer.files;
    });
</script>
@endpush
@endsection
