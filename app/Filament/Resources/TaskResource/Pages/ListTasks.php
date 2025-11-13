<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.resources.tasks.list';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label('New Task'),
        ];
    }

    public function getTitle(): string
    {
        return 'Tasks';
    }

    public function getSubheading(): ?string
    {
        $pendingCount = TaskResource::getModel()::where('status', 'pending')->count();
        $totalCount = TaskResource::getModel()::count();

        return "{$pendingCount} pending tasks out of {$totalCount} total";
    }
}
