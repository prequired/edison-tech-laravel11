<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.users.list';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label('New User'),
        ];
    }

    public function getTitle(): string
    {
        return 'Team Members';
    }

    public function getSubheading(): ?string
    {
        $activeCount = UserResource::getModel()::where('is_active', true)->count();
        $totalCount = UserResource::getModel()::count();

        return "{$activeCount} active members out of {$totalCount} total";
    }
}
