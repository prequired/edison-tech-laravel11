<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/portfolio', function () {
    return view('portfolio');
})->name('portfolio');

Route::get('/blog', function () {
    return view('blog.index');
})->name('blog');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Contact form submission
Route::post('/contact', function () {
    // Handle contact form submission
    return redirect()->route('contact')->with('success', 'Message sent successfully!');
})->name('contact.submit');

// Newsletter subscription
Route::post('/newsletter/subscribe', function () {
    // Handle newsletter subscription
    return back()->with('success', 'Successfully subscribed to newsletter!');
})->name('newsletter.subscribe');

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
    Route::get('/two-factor/enable', [TwoFactorController::class, 'showEnableForm'])->name('two-factor.enable');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable.submit');
    Route::get('/two-factor/verify', [TwoFactorController::class, 'showVerifyForm'])->name('two-factor.verify');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify.submit');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

// ============================================================================
// ADMIN ROUTES
// ============================================================================

Route::middleware(['auth', 'role:admin,employee'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Projects
    Route::resource('projects', AdminProjectController::class);

    // Invoices
    Route::resource('invoices', AdminInvoiceController::class);
    Route::post('/invoices/{invoice}/mark-paid', [AdminInvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::post('/invoices/{invoice}/send', [AdminInvoiceController::class, 'send'])->name('invoices.send');

    // Payments
    Route::resource('payments', AdminPaymentController::class);
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
});

// ============================================================================
// CLIENT ROUTES
// ============================================================================

Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('client.dashboard');
    })->name('dashboard');

    // Projects (view only)
    Route::get('/projects', function () {
        return view('client.projects.index');
    })->name('projects.index');
    Route::get('/projects/{project}', function () {
        return view('client.projects.show');
    })->name('projects.show');

    // Invoices
    Route::get('/invoices', function () {
        return view('client.invoices.index');
    })->name('invoices.index');
    Route::get('/invoices/{invoice}', function () {
        return view('client.invoices.show');
    })->name('invoices.show');

    // Payments
    Route::get('/payments', function () {
        return view('client.payments.index');
    })->name('payments.index');
    Route::get('/payments/{payment}', function () {
        return view('client.payments.show');
    })->name('payments.show');

    // Documents
    Route::get('/documents', function () {
        return view('client.documents.index');
    })->name('documents.index');
    Route::get('/documents/{document}', function () {
        return view('client.documents.show');
    })->name('documents.show');
});
