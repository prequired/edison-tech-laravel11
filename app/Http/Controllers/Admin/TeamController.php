<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Team Controller
 *
 * Manages team members (admin and employee users) in the admin panel.
 */
class TeamController extends Controller
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
     * Display a listing of team members.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $teamMembers = User::whereIn('role', ['admin', 'employee'])
            ->with('company')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalMembers = User::whereIn('role', ['admin', 'employee'])->count();
        $activeMembers = User::whereIn('role', ['admin', 'employee'])->where('is_active', true)->count();
        $adminCount = User::where('role', 'admin')->count();
        $employeeCount = User::where('role', 'employee')->count();

        return view('admin.team.index', compact(
            'teamMembers',
            'totalMembers',
            'activeMembers',
            'adminCount',
            'employeeCount'
        ));
    }

    /**
     * Show the form for creating a new team member.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        $companies = Company::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.team.create', compact('companies'));
    }

    /**
     * Store a newly created team member in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:admin,employee'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = $this->userService->createUser($validated);

        return redirect()
            ->route('admin.team.show', $user)
            ->with('success', 'Team member created successfully.');
    }

    /**
     * Display the specified team member.
     *
     * @param User $team
     * @return View
     */
    public function show(User $team): View
    {
        $this->authorize('view', $team);

        // Only allow viewing admin and employee roles
        if (!in_array($team->role, ['admin', 'employee'])) {
            abort(404);
        }

        $team->load(['company', 'projects', 'tasks', 'timeEntries']);

        // Get recent activity
        $recentProjects = $team->projects()->latest('created_at')->take(5)->get();
        $recentTasks = $team->tasks()->latest('created_at')->take(5)->get();

        return view('admin.team.show', compact('team', 'recentProjects', 'recentTasks'));
    }

    /**
     * Show the form for editing the specified team member.
     *
     * @param User $team
     * @return View
     */
    public function edit(User $team): View
    {
        $this->authorize('update', $team);

        // Only allow editing admin and employee roles
        if (!in_array($team->role, ['admin', 'employee'])) {
            abort(404);
        }

        $companies = Company::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.team.edit', compact('team', 'companies'));
    }

    /**
     * Update the specified team member in storage.
     *
     * @param Request $request
     * @param User $team
     * @return RedirectResponse
     */
    public function update(Request $request, User $team): RedirectResponse
    {
        $this->authorize('update', $team);

        // Only allow updating admin and employee roles
        if (!in_array($team->role, ['admin', 'employee'])) {
            abort(404);
        }

        $validated = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $team->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:admin,employee'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($team->avatar && \Storage::disk('public')->exists($team->avatar)) {
                \Storage::disk('public')->delete($team->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Remove password from validated data if not provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $this->userService->updateUser($team, $validated);

        return redirect()
            ->route('admin.team.show', $team)
            ->with('success', 'Team member updated successfully.');
    }

    /**
     * Remove the specified team member from storage.
     *
     * @param User $team
     * @return RedirectResponse
     */
    public function destroy(User $team): RedirectResponse
    {
        $this->authorize('delete', $team);

        // Only allow deleting admin and employee roles
        if (!in_array($team->role, ['admin', 'employee'])) {
            abort(404);
        }

        // Prevent deleting yourself
        if ($team->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'You cannot delete your own account.');
        }

        // Delete avatar if exists
        if ($team->avatar && \Storage::disk('public')->exists($team->avatar)) {
            \Storage::disk('public')->delete($team->avatar);
        }

        $team->delete();

        return redirect()
            ->route('admin.team.index')
            ->with('success', 'Team member deleted successfully.');
    }

    /**
     * Activate the specified team member.
     *
     * @param User $team
     * @return RedirectResponse
     */
    public function activate(User $team): RedirectResponse
    {
        $this->authorize('update', $team);

        $this->userService->activateUser($team);

        return redirect()
            ->back()
            ->with('success', 'Team member activated successfully.');
    }

    /**
     * Deactivate the specified team member.
     *
     * @param User $team
     * @return RedirectResponse
     */
    public function deactivate(User $team): RedirectResponse
    {
        $this->authorize('update', $team);

        // Prevent deactivating yourself
        if ($team->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'You cannot deactivate your own account.');
        }

        $this->userService->deactivateUser($team);

        return redirect()
            ->back()
            ->with('success', 'Team member deactivated successfully.');
    }
}
