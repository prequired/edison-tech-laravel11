<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.projects.list';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label('New Project'),
        ];
    }

    public function getTitle(): string
    {
        return 'Projects';
    }

    public function getSubheading(): ?string
    {
        $stats = $this->getStats();
        return "{$stats['active']} active projects out of {$stats['total']} total";
    }

    /**
     * Get stats for project list page
     * Cached for 60 seconds to improve performance
     */
    protected function getStats(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('projects.list.stats', 60, function () {
            $model = ProjectResource::getModel();
            return [
                'planning' => $model::where('status', 'planning')->count(),
                'active' => $model::where('status', 'active')->count(),
                'completed' => $model::where('status', 'completed')->count(),
                'on_hold' => $model::where('status', 'on_hold')->count(),
                'total' => $model::count(),
            ];
        });
    }

    /**
     * Get quick stats for header
     */
    public function getQuickStats(): array
    {
        $stats = $this->getStats();
        return [
            [
                'label' => 'Planning',
                'value' => $stats['planning'],
                'icon' => 'heroicon-o-light-bulb',
                'color' => 'blue',
            ],
            [
                'label' => 'Active',
                'value' => $stats['active'],
                'icon' => 'heroicon-o-rocket-launch',
                'color' => 'emerald',
            ],
            [
                'label' => 'Completed',
                'value' => $stats['completed'],
                'icon' => 'heroicon-o-check-circle',
                'color' => 'green',
            ],
            [
                'label' => 'On Hold',
                'value' => $stats['on_hold'],
                'icon' => 'heroicon-o-pause-circle',
                'color' => 'amber',
            ],
        ];
    }
}
