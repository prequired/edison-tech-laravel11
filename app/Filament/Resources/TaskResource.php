<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Project Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'in_progress')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Task Information')
                    ->description('Core details about the task')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2)
                            ->placeholder('Enter task title'),

                        Forms\Components\Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'review' => 'Under Review',
                                'completed' => 'Completed',
                                'blocked' => 'Blocked',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-signal'),

                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('medium')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-flag'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Assignment & Scheduling')
                    ->description('Who is working on this task and when')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Unassigned')
                            ->suffixIcon('heroicon-o-user'),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->suffixIcon('heroicon-o-calendar'),

                        Forms\Components\TextInput::make('estimated_hours')
                            ->label('Estimated Hours')
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(999.99)
                            ->suffix('hrs')
                            ->placeholder('0.00'),

                        Forms\Components\DatePicker::make('completed_at')
                            ->label('Completion Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->hidden(fn (Forms\Get $get): bool => $get('status') !== 'completed'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Description & Notes')
                    ->description('Detailed information about the task')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Add any internal notes or comments'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Metadata')
                    ->description('System information')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers appear first'),

                        Forms\Components\Placeholder::make('created_by')
                            ->label('Created By')
                            ->content(fn (?Task $record): string => $record ? ($record->creator->name ?? 'N/A') : Auth::user()->name),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created At')
                            ->content(fn (?Task $record): string => $record?->created_at?->diffForHumans() ?? 'Just now'),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last Updated')
                            ->content(fn (?Task $record): string => $record?->updated_at?->diffForHumans() ?? 'Just now'),
                    ])
                    ->columns(4)
                    ->collapsible()
                    ->collapsed()
                    ->hidden(fn (?Task $record) => $record === null),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => Auth::id())
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Task $record): ?string => $record->project->name ?? null)
                    ->url(fn (Task $record): string => route('filament.admin.resources.tasks.view', ['record' => $record]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->url(fn (Task $record): string => $record->project ? route('filament.admin.resources.projects.view', ['record' => $record->project]) : '#'),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Unassigned')
                    ->toggleable()
                    ->icon('heroicon-o-user')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'info',
                        'review' => 'warning',
                        'completed' => 'success',
                        'blocked' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()->toString()),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'urgent' => 'heroicon-o-exclamation-triangle',
                        'high' => 'heroicon-o-arrow-trending-up',
                        default => 'heroicon-o-flag',
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->color(fn (?string $state, Task $record): string =>
                        $record->due_date && $record->due_date->isPast() && $record->status !== 'completed'
                            ? 'danger'
                            : 'gray'
                    ),

                Tables\Columns\TextColumn::make('estimated_hours')
                    ->label('Est. Hours')
                    ->suffix(' hrs')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'review' => 'Under Review',
                        'completed' => 'Completed',
                        'blocked' => 'Blocked',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Tasks')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereNotNull('due_date')
                            ->where('due_date', '<', now())
                            ->whereNotIn('status', ['completed', 'cancelled'])
                    ),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('complete')
                        ->label('Mark Complete')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Task $record): bool => $record->status !== 'completed')
                        ->requiresConfirmation()
                        ->action(fn (Task $record) => $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]))
                        ->successNotificationTitle('Task marked as complete'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-signal')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'in_progress' => 'In Progress',
                                    'review' => 'Under Review',
                                    'completed' => 'Completed',
                                    'blocked' => 'Blocked',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $update = ['status' => $data['status']];
                                if ($data['status'] === 'completed') {
                                    $update['completed_at'] = now();
                                }
                                $record->update($update);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('updatePriority')
                        ->label('Update Priority')
                        ->icon('heroicon-o-flag')
                        ->form([
                            Forms\Components\Select::make('priority')
                                ->label('New Priority')
                                ->options([
                                    'low' => 'Low',
                                    'medium' => 'Medium',
                                    'high' => 'High',
                                    'urgent' => 'Urgent',
                                ])
                                ->required(),
                        ])
                        ->action(fn (array $data, $records) => $records->each->update(['priority' => $data['priority']]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Task Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'gray',
                                        'in_progress' => 'info',
                                        'review' => 'warning',
                                        'completed' => 'success',
                                        'blocked' => 'danger',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()->toString()),

                                Infolists\Components\TextEntry::make('priority')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'urgent' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        'low' => 'gray',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('project.name')
                                    ->label('Project')
                                    ->url(fn (Task $record): string => $record->project ? route('filament.admin.resources.projects.view', ['record' => $record->project]) : '#'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Assignment & Timeline')
                    ->schema([
                        Infolists\Components\TextEntry::make('assignedTo.name')
                            ->label('Assigned To')
                            ->placeholder('Unassigned')
                            ->icon('heroicon-o-user'),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label('Due Date')
                            ->date('M d, Y')
                            ->placeholder('No due date'),

                        Infolists\Components\TextEntry::make('estimated_hours')
                            ->label('Estimated Hours')
                            ->suffix(' hours')
                            ->placeholder('Not estimated'),

                        Infolists\Components\TextEntry::make('completed_at')
                            ->label('Completed At')
                            ->date('M d, Y')
                            ->visible(fn (Task $record): bool => $record->status === 'completed'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->html()
                            ->placeholder('No description provided')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('notes')
                            ->label('Internal Notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Metadata')
                    ->schema([
                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('Created By'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y H:i'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TimeEntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
