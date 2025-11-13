<?php

declare(strict_types=1);

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Models\TimeEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TimeEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'timeEntries';

    protected static ?string $title = 'Time Entries';

    protected static ?string $icon = 'heroicon-o-clock';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Time Entry Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Team Member')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => Auth::id())
                            ->suffixIcon('heroicon-o-user'),

                        Forms\Components\DateTimePicker::make('start_time')
                            ->label('Start Time')
                            ->required()
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->seconds(false)
                            ->default(now())
                            ->suffixIcon('heroicon-o-play'),

                        Forms\Components\DateTimePicker::make('end_time')
                            ->label('End Time')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->seconds(false)
                            ->after('start_time')
                            ->suffixIcon('heroicon-o-stop'),

                        Forms\Components\TextInput::make('hours')
                            ->label('Hours')
                            ->numeric()
                            ->step(0.25)
                            ->minValue(0)
                            ->maxValue(24)
                            ->suffix('hrs')
                            ->required()
                            ->default(0)
                            ->columnSpan(2),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Billing Information')
                    ->schema([
                        Forms\Components\Toggle::make('is_billable')
                            ->label('Billable')
                            ->default(true)
                            ->live()
                            ->inline(false),

                        Forms\Components\TextInput::make('hourly_rate')
                            ->label('Hourly Rate')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(999999.99)
                            ->placeholder('0.00')
                            ->disabled(fn (Forms\Get $get): bool => !$get('is_billable')),

                        Forms\Components\TextInput::make('amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(999999.99)
                            ->placeholder('0.00')
                            ->disabled(fn (Forms\Get $get): bool => !$get('is_billable')),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Describe the work performed...'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Hidden::make('project_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->project_id)
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Team Member')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->iconColor('gray')
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->placeholder('No description'),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->placeholder('In progress'),

                Tables\Columns\TextColumn::make('hours')
                    ->label('Hours')
                    ->suffix(' hrs')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_billable')
                    ->label('Billable')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_invoiced')
                    ->label('Invoiced')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('Team Member')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_billable')
                    ->label('Billable')
                    ->placeholder('All entries')
                    ->trueLabel('Billable only')
                    ->falseLabel('Non-billable only'),

                Tables\Filters\TernaryFilter::make('is_invoiced')
                    ->label('Invoiced')
                    ->placeholder('All entries')
                    ->trueLabel('Invoiced')
                    ->falseLabel('Not invoiced'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_time', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_time', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $data['project_id'] = $livewire->ownerRecord->project_id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('mark_invoiced')
                        ->label('Mark as Invoiced')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn (TimeEntry $record): bool => $record->is_billable && !$record->is_invoiced)
                        ->form([
                            Forms\Components\Select::make('invoice_id')
                                ->label('Invoice')
                                ->relationship('invoice', 'invoice_number')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (TimeEntry $record, array $data) {
                            $record->update([
                                'is_invoiced' => true,
                                'invoice_id' => $data['invoice_id'],
                            ]);
                        })
                        ->successNotificationTitle('Time entry marked as invoiced'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_billable')
                        ->label('Mark as Billable')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_billable' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('mark_non_billable')
                        ->label('Mark as Non-Billable')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_billable' => false]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_time', 'desc')
            ->emptyStateHeading('No time entries yet')
            ->emptyStateDescription('Start tracking time for this task by creating your first entry.')
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $data['project_id'] = $livewire->ownerRecord->project_id;
                        return $data;
                    }),
            ]);
    }
}
