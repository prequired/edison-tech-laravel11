<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\TwoFactorController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\TeamController as AdminTeamController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;

// Client Controllers
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\Client\InvoiceController as ClientInvoiceController;
use App\Http\Controllers\Client\DocumentController as ClientDocumentController;
use App\Http\Controllers\Client\CompanyController as ClientCompanyController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\SecurityController as ClientSecurityController;
use App\Http\Controllers\Client\TicketController as ClientTicketController;

// Web (Public) Controllers
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\Web\PortfolioController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\TeamController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\NewsletterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

// Home & About
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Services
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

// Portfolio
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Team
Route::get('/team', [TeamController::class, 'index'])->name('team.index');
Route::get('/team/{id}', [TeamController::class, 'show'])->name('team.show');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribePage'])->name('newsletter.unsubscribe');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe.submit');
Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify'])->name('newsletter.verify');

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

    // Password Reset
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Two-Factor Authentication
    Route::get('/two-factor/verify', [TwoFactorController::class, 'showVerifyForm'])->name('two-factor.verify');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify.submit');
});

// ============================================================================
// ADMIN ROUTES
// ============================================================================

Route::middleware(['auth', 'role:admin,employee'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Analytics
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/projects', [AdminAnalyticsController::class, 'projects'])->name('analytics.projects');
    Route::get('/analytics/financial', [AdminAnalyticsController::class, 'financial'])->name('analytics.financial');

    // Projects
    Route::resource('projects', AdminProjectController::class);

    // Invoices
    Route::resource('invoices', AdminInvoiceController::class);
    Route::post('/invoices/{invoice}/mark-paid', [AdminInvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::post('/invoices/{invoice}/send', [AdminInvoiceController::class, 'send'])->name('invoices.send');

    // Payments
    Route::resource('payments', AdminPaymentController::class)->except(['create', 'edit', 'update']);
    Route::post('/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');

    // Companies
    Route::resource('companies', AdminCompanyController::class);

    // Users
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');

    // Tasks
    Route::resource('tasks', AdminTaskController::class);
    Route::post('/tasks/{task}/assign', [AdminTaskController::class, 'assign'])->name('tasks.assign');
    Route::post('/tasks/{task}/status', [AdminTaskController::class, 'updateStatus'])->name('tasks.status');

    // Services
    Route::resource('services', AdminServiceController::class);

    // Portfolio
    Route::resource('portfolio', AdminPortfolioController::class);

    // Blog
    Route::resource('blog', AdminBlogController::class);

    // Blog Categories
    Route::resource('blog-categories', AdminBlogCategoryController::class);

    // Testimonials
    Route::resource('testimonials', AdminTestimonialController::class);
    Route::post('/testimonials/{testimonial}/approve', [AdminTestimonialController::class, 'approve'])->name('testimonials.approve');
    Route::post('/testimonials/{testimonial}/reject', [AdminTestimonialController::class, 'reject'])->name('testimonials.reject');

    // Documents
    Route::resource('documents', AdminDocumentController::class);
    Route::get('/documents/{document}/download', [AdminDocumentController::class, 'download'])->name('documents.download');
    Route::post('/documents/{document}/version', [AdminDocumentController::class, 'uploadVersion'])->name('documents.version');

    // Contacts
    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::patch('/contacts/{contact}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.status');
    Route::post('/contacts/{contact}/replied', [AdminContactController::class, 'markAsReplied'])->name('contacts.replied');
    Route::post('/contacts/{contact}/archive', [AdminContactController::class, 'archive'])->name('contacts.archive');
    Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

    // Newsletter
    Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('/newsletter/{newsletter}', [AdminNewsletterController::class, 'show'])->name('newsletter.show');
    Route::post('/newsletter', [AdminNewsletterController::class, 'store'])->name('newsletter.store');
    Route::patch('/newsletter/{newsletter}', [AdminNewsletterController::class, 'update'])->name('newsletter.update');
    Route::delete('/newsletter/{newsletter}', [AdminNewsletterController::class, 'destroy'])->name('newsletter.destroy');
    Route::get('/newsletter/export/csv', [AdminNewsletterController::class, 'export'])->name('newsletter.export');

    // Team Management
    Route::resource('team', AdminTeamController::class);
    Route::post('/team/{team}/activate', [AdminTeamController::class, 'activate'])->name('team.activate');
    Route::post('/team/{team}/deactivate', [AdminTeamController::class, 'deactivate'])->name('team.deactivate');
});

// ============================================================================
// CLIENT ROUTES
// ============================================================================

Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

    // Projects (view only)
    Route::get('/projects', [ClientProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');

    // Invoices
    Route::get('/invoices', [ClientInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [ClientInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [ClientInvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/invoices/{invoice}/pay', [ClientInvoiceController::class, 'pay'])->name('invoices.pay');
    Route::post('/invoices/{invoice}/pay', [ClientInvoiceController::class, 'processPayment'])->name('invoices.process-payment');

    // Documents
    Route::get('/documents', [ClientDocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [ClientDocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/download', [ClientDocumentController::class, 'download'])->name('documents.download');

    // Company (own company management)
    Route::get('/company', [ClientCompanyController::class, 'show'])->name('company.show');
    Route::get('/company/edit', [ClientCompanyController::class, 'edit'])->name('company.edit');
    Route::patch('/company', [ClientCompanyController::class, 'update'])->name('company.update');

    // Profile
    Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ClientProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ClientProfileController::class, 'editPassword'])->name('profile.password');
    Route::patch('/profile/password', [ClientProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ClientProfileController::class, 'destroy'])->name('profile.destroy');

    // Security & 2FA
    Route::get('/security', [ClientSecurityController::class, 'index'])->name('security.index');
    Route::get('/security/two-factor/enable', [ClientSecurityController::class, 'enableTwoFactor'])->name('security.two-factor.enable');
    Route::post('/security/two-factor/verify', [ClientSecurityController::class, 'verifyTwoFactor'])->name('security.two-factor.verify');
    Route::post('/security/two-factor/disable', [ClientSecurityController::class, 'disableTwoFactor'])->name('security.two-factor.disable');
    Route::get('/security/recovery-codes', [ClientSecurityController::class, 'recoveryCodes'])->name('security.recovery-codes');
    Route::post('/security/recovery-codes/regenerate', [ClientSecurityController::class, 'regenerateRecoveryCodes'])->name('security.recovery-codes.regenerate');

    // Tickets (placeholder - requires Ticket model)
    Route::resource('tickets', ClientTicketController::class);
    Route::post('/tickets/{ticket}/close', [ClientTicketController::class, 'close'])->name('tickets.close');
    Route::post('/tickets/{ticket}/reply', [ClientTicketController::class, 'reply'])->name('tickets.reply');
});
