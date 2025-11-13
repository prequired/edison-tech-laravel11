<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Project;
use App\Services\CacheService;

/**
 * Project Observer
 *
 * Handles model events and cache invalidation for projects.
 */
class ProjectObserver
{
    /**
     * Create a new observer instance.
     *
     * @param CacheService $cacheService
     */
    public function __construct(
        private readonly CacheService $cacheService
    ) {}

    /**
     * Handle the Project "created" event.
     *
     * @param Project $project
     * @return void
     */
    public function created(Project $project): void
    {
        $this->invalidateRelatedCaches($project);
    }

    /**
     * Handle the Project "updated" event.
     *
     * @param Project $project
     * @return void
     */
    public function updated(Project $project): void
    {
        $this->invalidateRelatedCaches($project);
    }

    /**
     * Handle the Project "deleted" event.
     *
     * @param Project $project
     * @return void
     */
    public function deleted(Project $project): void
    {
        $this->invalidateRelatedCaches($project);
    }

    /**
     * Handle the Project "restored" event.
     *
     * @param Project $project
     * @return void
     */
    public function restored(Project $project): void
    {
        $this->invalidateRelatedCaches($project);
    }

    /**
     * Invalidate all related caches
     *
     * @param Project $project
     * @return void
     */
    private function invalidateRelatedCaches(Project $project): void
    {
        // Invalidate project cache
        $this->cacheService->invalidateProject($project->id);

        // Invalidate company cache
        if ($project->company_id) {
            $this->cacheService->invalidateCompany($project->company_id);
        }

        // Invalidate project list cache
        $this->cacheService->invalidateProjectList();

        // Invalidate stats cache
        $this->cacheService->invalidateAllStats();
    }
}
