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
        $activeCount = ProjectResource::getModel()::where('status', 'active')->count();
        $totalCount = ProjectResource::getModel()::count();

        return "{$activeCount} active projects out of {$totalCount} total";
    }
}
