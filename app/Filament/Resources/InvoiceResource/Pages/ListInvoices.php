<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoices.list';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label('New Invoice'),
        ];
    }

    public function getTitle(): string
    {
        return 'Invoices';
    }

    public function getSubheading(): ?string
    {
        $totalRevenue = InvoiceResource::getModel()::where('status', 'paid')->sum('total_amount');
        $unpaidCount = InvoiceResource::getModel()::whereIn('status', ['draft', 'sent', 'overdue'])->count();

        return "$" . number_format($totalRevenue, 2) . " total revenue • {$unpaidCount} unpaid invoices";
    }
}
