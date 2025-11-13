<?php

namespace App\Providers;

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
    }
}
