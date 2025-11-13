<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    protected static string $view = 'filament.resources.companies.list';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label('New Company'),
        ];
    }

    public function getTitle(): string
    {
        return 'Companies';
    }

    public function getSubheading(): ?string
    {
        $activeCount = CompanyResource::getModel()::where('is_active', true)->count();
        $totalCount = CompanyResource::getModel()::count();

        return "{$activeCount} active companies out of {$totalCount} total";
    }
}
