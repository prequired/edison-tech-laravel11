<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TimeEntryResource\Pages;
use App\Models\TimeEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TimeEntryResource extends Resource
{
    protected static ?string $model = TimeEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Time & Billing';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'description';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_billable', true)
            ->where('is_invoiced', false)
            ->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Time Entry Information')
                    ->description('Track time spent on projects and tasks')
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('task_id', null))
                            ->suffixIcon('heroicon-o-briefcase'),

                        Forms\Components\Select::make('task_id')
                            ->label('Task (Optional)')
                            ->relationship(
                                'task',
                                'title',
                                fn (Builder $query, Forms\Get $get) =>
                                    $query->where('project_id', $get('project_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('No task selected')
                            ->suffixIcon('heroicon-o-check-circle')
                            ->disabled(fn (Forms\Get $get): bool => !$get('project_id')),

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
                            ->helperText('Enter total hours or it will be calculated from start/end time'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Billing Information')
                    ->description('Configure billing details for this time entry')
                    ->schema([
                        Forms\Components\Toggle::make('is_billable')
                            ->label('Billable')
                            ->default(true)
                            ->live()
                            ->inline(false)
                            ->helperText('Mark as billable to include in invoices'),

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
                            ->helperText('Calculated as hours × hourly rate')
                            ->disabled(fn (Forms\Get $get): bool => !$get('is_billable')),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Invoice Status')
                    ->description('Link this time entry to an invoice')
                    ->schema([
                        Forms\Components\Toggle::make('is_invoiced')
                            ->label('Invoiced')
                            ->default(false)
                            ->live()
                            ->inline(false)
                            ->helperText('Mark as invoiced when included in an invoice'),

                        Forms\Components\Select::make('invoice_id')
                            ->label('Invoice')
                            ->relationship('invoice', 'invoice_number')
                            ->searchable()
                            ->preload()
                            ->placeholder('Not linked to invoice')
                            ->suffixIcon('heroicon-o-document-text')
                            ->disabled(fn (Forms\Get $get): bool => !$get('is_invoiced')),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Description')
                    ->description('Provide details about the work performed')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Describe the work performed...'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Team Member')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->iconColor('gray')
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TimeEntry $record): ?string => $record->task?->title)
                    ->url(fn (TimeEntry $record): string => $record->project ? route('filament.admin.resources.projects.view', ['record' => $record->project]) : '#')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->toggleable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('End')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('hours')
                    ->label('Hours')
                    ->suffix(' hrs')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_billable')
                    ->label('Billable')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('hourly_rate')
                    ->label('Rate')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_invoiced')
                    ->label('Invoiced')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->url(fn (TimeEntry $record): ?string =>
                        $record->invoice ? route('filament.admin.resources.invoices.view', ['record' => $record->invoice]) : null
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

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

                Tables\Filters\Filter::make('unbilled')
                    ->label('Unbilled Entries')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('is_billable', true)
                            ->where('is_invoiced', false)
                    ),

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

                    Tables\Actions\BulkAction::make('mark_invoiced')
                        ->label('Mark as Invoiced')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('invoice_id')
                                ->label('Invoice')
                                ->relationship('invoice', 'invoice_number')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update([
                                'is_invoiced' => true,
                                'invoice_id' => $data['invoice_id'],
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_time', 'desc')
            ->poll('60s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Time Entry Overview')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('Team Member')
                                    ->icon('heroicon-o-user')
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('hours')
                                    ->label('Hours Logged')
                                    ->suffix(' hours')
                                    ->badge()
                                    ->color('success')
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('amount')
                                    ->label('Amount')
                                    ->money('USD')
                                    ->badge()
                                    ->color('success')
                                    ->weight(FontWeight::Bold),
                            ]),
                    ]),

                Infolists\Components\Section::make('Project & Task')
                    ->schema([
                        Infolists\Components\TextEntry::make('project.name')
                            ->label('Project')
                            ->url(fn (TimeEntry $record): string => $record->project ? route('filament.admin.resources.projects.view', ['record' => $record->project]) : '#'),

                        Infolists\Components\TextEntry::make('task.title')
                            ->label('Task')
                            ->placeholder('No task assigned')
                            ->url(fn (TimeEntry $record): ?string =>
                                $record->task ? route('filament.admin.resources.tasks.view', ['record' => $record->task]) : null
                            ),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Time Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('start_time')
                            ->label('Start Time')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-play'),

                        Infolists\Components\TextEntry::make('end_time')
                            ->label('End Time')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-stop')
                            ->placeholder('Not ended'),

                        Infolists\Components\TextEntry::make('hours')
                            ->label('Duration')
                            ->suffix(' hours')
                            ->weight(FontWeight::Bold),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Billing Information')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_billable')
                            ->label('Billable')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('hourly_rate')
                            ->label('Hourly Rate')
                            ->money('USD')
                            ->placeholder('Not set'),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('Total Amount')
                            ->money('USD')
                            ->weight(FontWeight::Bold),

                        Infolists\Components\IconEntry::make('is_invoiced')
                            ->label('Invoiced')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('invoice.invoice_number')
                            ->label('Invoice Number')
                            ->placeholder('Not invoiced')
                            ->url(fn (TimeEntry $record): ?string =>
                                $record->invoice ? route('filament.admin.resources.invoices.view', ['record' => $record->invoice]) : null
                            ),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Description')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn (TimeEntry $record): bool => !$record->description),

                Infolists\Components\Section::make('Metadata')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeEntries::route('/'),
            'create' => Pages\CreateTimeEntry::route('/create'),
            'view' => Pages\ViewTimeEntry::route('/{record}'),
            'edit' => Pages\EditTimeEntry::route('/{record}/edit'),
        ];
    }
}
