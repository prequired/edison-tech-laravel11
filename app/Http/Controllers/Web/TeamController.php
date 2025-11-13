<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

/**
 * Team Controller
 *
 * Displays the public team page with team members.
 */
class TeamController extends Controller
{
    /**
     * Display the team page.
     *
     * @return View
     */
    public function index(): View
    {
        // Get all active team members (admin and employee roles)
        $teamMembers = User::whereIn('role', ['admin', 'employee'])
            ->where('is_active', true)
            ->orderBy('role') // Admins first
            ->orderBy('name')
            ->get();

        // Separate by role for display
        $admins = $teamMembers->where('role', 'admin');
        $employees = $teamMembers->where('role', 'employee');

        return view('web.team', compact('teamMembers', 'admins', 'employees'));
    }

    /**
     * Display a specific team member's profile.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $teamMember = User::whereIn('role', ['admin', 'employee'])
            ->where('is_active', true)
            ->with(['projects' => function ($query) {
                $query->where('status', 'completed')->latest()->take(6);
            }])
            ->findOrFail($id);

        return view('web.team-member', compact('teamMember'));
    }
}
