<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Testimonial;
use Illuminate\View\View;

/**
 * About Controller
 *
 * Displays the public about page.
 */
class AboutController extends Controller
{
    /**
     * Display the about page.
     *
     * @return View
     */
    public function index(): View
    {
        // Get team members (admin and employee roles)
        $teamMembers = User::whereIn('role', ['admin', 'employee'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get testimonials
        $testimonials = Testimonial::where('status', 'approved')
            ->orderBy('display_order')
            ->take(10)
            ->get();

        // Company stats
        $stats = [
            'years_experience' => now()->year - 2010, // Adjust the founding year as needed
            'team_members' => $teamMembers->count(),
            'satisfied_clients' => Testimonial::where('status', 'approved')
                ->distinct('company_id')
                ->count(),
        ];

        return view('web.about', compact('teamMembers', 'testimonials', 'stats'));
    }
}
