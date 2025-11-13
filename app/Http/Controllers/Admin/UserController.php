<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * User Controller
 *
 * Manages CRUD operations for users in the admin panel.
 */
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UserService $userService
     */
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Display a listing of users.
     *
     * Supports filtering by:
     * - status (active/inactive)
     * - role
     * - company_id
     * - search (name, email)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['company']);

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort by latest by default
        $query->latest();

        $users = $query->paginate(15)->withQueryString();

        // Get companies for filter dropdown
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('admin.users.index', compact('users', 'companies'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        $companies = Company::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.users.create', compact('companies'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'in:admin,manager,employee,client'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        try {
            $user = $this->userService->createUser($validated);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     *
     * @param User $user
     * @return View
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load([
            'company',
            'createdProjects' => function ($query) {
                $query->latest()->limit(10);
            },
            'assignedProjects' => function ($query) {
                $query->latest()->limit(10);
            },
            'assignedTasks' => function ($query) {
                $query->latest()->limit(10);
            },
            'timeEntries' => function ($query) {
                $query->with('project')->latest()->limit(10);
            },
            'activityLogs' => function ($query) {
                $query->latest()->limit(20);
            }
        ]);

        // Calculate user statistics
        $totalProjects = $user->assignedProjects()->count();
        $activeProjects = $user->assignedProjects()->where('status', 'in_progress')->count();

        $totalTasks = $user->assignedTasks()->count();
        $completedTasks = $user->assignedTasks()->where('status', 'completed')->count();
        $pendingTasks = $user->assignedTasks()->where('status', 'pending')->count();

        $totalHours = $user->timeEntries()->sum('duration');
        $monthlyHours = $user->timeEntries()
            ->whereMonth('start_time', now()->month)
            ->whereYear('start_time', now()->year)
            ->sum('duration');

        return view('admin.users.show', compact(
            'user',
            'totalProjects',
            'activeProjects',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'totalHours',
            'monthlyHours'
        ));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @return View
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $companies = Company::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.users.edit', compact('user', 'companies'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'in:admin,manager,employee,client'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        try {
            $this->userService->updateUser($user, $validated);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        try {
            $this->userService->deleteUser($user);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Activate the specified user.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function activate(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        try {
            $this->userService->activateUser($user);

            return redirect()
                ->back()
                ->with('success', 'User activated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to activate user: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate the specified user.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function deactivate(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        try {
            $this->userService->deactivateUser($user);

            return redirect()
                ->back()
                ->with('success', 'User deactivated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to deactivate user: ' . $e->getMessage());
        }
    }
}
