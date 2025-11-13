<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Project;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Company;
use App\Models\User;
use App\Models\Contract;
use App\Models\Task;
use App\Models\Service;
use App\Models\PortfolioItem;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Testimonial;
use App\Models\Document;
use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use App\Policies\ProjectPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\UserPolicy;
use App\Policies\ContractPolicy;
use App\Policies\TaskPolicy;
use App\Policies\ServicePolicy;
use App\Policies\PortfolioItemPolicy;
use App\Policies\BlogPostPolicy;
use App\Policies\BlogCategoryPolicy;
use App\Policies\TestimonialPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\ContactSubmissionPolicy;
use App\Policies\NewsletterSubscriberPolicy;
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
        Service::class => ServicePolicy::class,
        PortfolioItem::class => PortfolioItemPolicy::class,
        BlogPost::class => BlogPostPolicy::class,
        BlogCategory::class => BlogCategoryPolicy::class,
        Testimonial::class => TestimonialPolicy::class,
        Document::class => DocumentPolicy::class,
        ContactSubmission::class => ContactSubmissionPolicy::class,
        NewsletterSubscriber::class => NewsletterSubscriberPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
