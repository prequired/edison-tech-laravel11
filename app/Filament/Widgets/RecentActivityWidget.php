<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\Invoice;
use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Premium Recent Activity Widget
 *
 * Shows recent business activities with beautiful timeline design
 */
class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Activity')
            ->description('Latest updates across your business')
            ->query(
                // Combine recent updates from multiple models
                Project::query()
                    ->latest('updated_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\IconColumn::make('type')
                    ->icon(fn () => 'heroicon-o-cube')
                    ->color('primary')
                    ->size(Tables\Columns\IconColumn\IconColumnSize::Large),

                Tables\Columns\TextColumn::make('name')
                    ->label('Activity')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Project $record): string =>
                        'Updated ' . $record->updated_at->diffForHumans()
                    ),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'on_hold' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('progress')
                    ->suffix('%')
                    ->numeric()
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
