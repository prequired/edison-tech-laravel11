<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\UserService;
use App\Services\TimeTrackingService;
use App\Services\ProjectService;
use App\Services\CompanyService;
use App\Services\PaymentService;
use App\Services\InvoiceService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register service bindings
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });

        $this->app->singleton(TimeTrackingService::class, function ($app) {
            return new TimeTrackingService();
        });

        $this->app->singleton(ProjectService::class, function ($app) {
            return new ProjectService();
        });

        $this->app->singleton(CompanyService::class, function ($app) {
            return new CompanyService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });

        $this->app->singleton(InvoiceService::class, function ($app) {
            return new InvoiceService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set pagination theme to Tailwind
        Paginator::useTailwind();

        // Configure rate limiting for authentication endpoints
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Strict rate limit for authentication attempts (5 attempts per minute)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many login attempts. Please try again later.',
                    ], 429);
                });
        });

        // Rate limit for password reset requests (3 attempts per minute)
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // Rate limit for 2FA verification (5 attempts per minute)
        RateLimiter::for('2fa', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate limit for API endpoints (60 requests per minute for authenticated users)
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(20)->by($request->ip());
        });

        // Rate limit for file uploads (10 per minute to prevent abuse)
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by(
                $request->user() ? $request->user()->id : $request->ip()
            );
        });

        // Rate limit for contact form submissions (3 per hour)
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Rate limit for newsletter subscriptions (5 per hour)
        RateLimiter::for('newsletter', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Global rate limit for web routes (120 requests per minute)
        RateLimiter::for('web', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(60)->by($request->ip());
        });
    }
}
