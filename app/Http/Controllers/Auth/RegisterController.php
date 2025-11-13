<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTOs\CreateCompanyDTO;
use App\DTOs\CreateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\CompanyService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Controller for handling user registration.
 *
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /**
     * Create a new RegisterController instance.
     *
     * @param UserService $userService
     * @param CompanyService $companyService
     */
    public function __construct(
        private readonly UserService $userService,
        private readonly CompanyService $companyService,
    ) {
        $this->middleware('guest');
    }

    /**
     * Show the registration form.
     *
     * @return View
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param RegisterRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            // Create the company first
            $companyDTO = new CreateCompanyDTO(
                name: $request->input('company_name'),
                email: $request->input('email'),
                phone: $request->input('phone'),
                website: $request->input('website'),
                address: $request->input('address'),
                city: $request->input('city'),
                state: $request->input('state'),
                country: $request->input('country', 'US'),
                postal_code: $request->input('postal_code'),
                tax_id: null,
                logo: null,
                notes: null,
                is_active: true,
                stripe_customer_id: null,
            );

            $company = $this->companyService->create($companyDTO);

            // Create the user with client role
            $userDTO = new CreateUserDTO(
                company_id: $company->id,
                name: $request->input('name'),
                email: $request->input('email'),
                password: $request->input('password'),
                role: 'client',
                phone: $request->input('phone'),
                avatar: null,
                is_active: true,
                timezone: $request->input('timezone', 'UTC'),
                preferences: [],
            );

            $user = $this->userService->create($userDTO);

            // Send welcome email
            // TODO: Create and send welcome email notification
            // Mail::to($user->email)->send(new WelcomeEmail($user));

            // Auto-login the user
            Auth::login($user);

            // Update last login timestamp
            $this->userService->updateLastLogin($user);

            return redirect()->route('client.dashboard')
                ->with('message', 'Registration successful! Welcome to Edison Tech.');
        } catch (\Throwable $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'email' => 'An error occurred during registration. Please try again.',
                ]);
        }
    }
}
