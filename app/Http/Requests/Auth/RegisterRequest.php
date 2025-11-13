<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Form request for user registration validation.
 *
 * @package App\Http\Requests\Auth
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:2'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.max' => 'Name must not exceed 255 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.mixed_case' => 'Password must contain both uppercase and lowercase letters.',
            'password.letters' => 'Password must contain letters.',
            'password.numbers' => 'Password must contain numbers.',
            'password.symbols' => 'Password must contain symbols.',
            'password.uncompromised' => 'The given password has appeared in a data leak. Please choose a different password.',
            'company_name.required' => 'Please enter your company name.',
            'company_name.max' => 'Company name must not exceed 255 characters.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'website.url' => 'Please enter a valid website URL.',
            'website.max' => 'Website URL must not exceed 255 characters.',
            'address.max' => 'Address must not exceed 500 characters.',
            'city.max' => 'City must not exceed 100 characters.',
            'state.max' => 'State must not exceed 100 characters.',
            'country.max' => 'Country code must be 2 characters.',
            'postal_code.max' => 'Postal code must not exceed 20 characters.',
            'timezone.max' => 'Timezone must not exceed 50 characters.',
        ];
    }
}
