<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

/**
 * Service Controller
 *
 * Displays public services pages.
 */
class ServiceController extends Controller
{
    /**
     * Display a listing of all services.
     *
     * @return View
     */
    public function index(): View
    {
        $services = Service::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('web.services.index', compact('services'));
    }

    /**
     * Display the specified service.
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get related services
        $relatedServices = Service::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('web.services.show', compact('service', 'relatedServices'));
    }
}
