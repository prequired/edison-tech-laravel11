<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Company;
use App\Models\User;
use App\Models\Contract;
use App\Models\Task;
use App\Policies\ProjectPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\UserPolicy;
use App\Policies\ContractPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Payment::class => PaymentPolicy::class,
        Company::class => CompanyPolicy::class,
        User::class => UserPolicy::class,
        Contract::class => ContractPolicy::class,
        Task::class => TaskPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
