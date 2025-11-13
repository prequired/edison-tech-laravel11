<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\PortfolioItem;
use App\Models\Testimonial;
use App\Models\BlogPost;
use Illuminate\View\View;

/**
 * Home Controller
 *
 * Displays the public homepage with featured content.
 */
class HomeController extends Controller
{
    /**
     * Display the homepage.
     *
     * @return View
     */
    public function index(): View
    {
        // Get featured services
        $featuredServices = Service::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->take(6)
            ->get();

        // Get featured portfolio items
        $featuredPortfolio = PortfolioItem::where('is_featured', true)
            ->where('is_published', true)
            ->orderBy('display_order')
            ->take(6)
            ->get();

        // Get approved and featured testimonials
        $testimonials = Testimonial::where('status', 'approved')
            ->where('is_featured', true)
            ->orderBy('display_order')
            ->take(10)
            ->get();

        // Get recent blog posts
        $recentPosts = BlogPost::where('status', 'published')
            ->with(['category', 'author'])
            ->latest('published_at')
            ->take(3)
            ->get();

        // Statistics for the hero section
        $stats = [
            'completed_projects' => PortfolioItem::where('is_published', true)->count(),
            'happy_clients' => Testimonial::where('status', 'approved')->distinct('company_id')->count(),
            'services_offered' => Service::where('is_active', true)->count(),
            'years_experience' => now()->year - 2010, // Adjust the founding year as needed
        ];

        return view('web.home', compact(
            'featuredServices',
            'featuredPortfolio',
            'testimonials',
            'recentPosts',
            'stats'
        ));
    }
}
