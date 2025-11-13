<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
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

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Business Operations';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        $activeCount = static::getModel()::where('status', 'active')->count();

        return $activeCount > 0 ? (string) $activeCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contract Information')
                    ->description('Basic contract details and relationships')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->suffixIcon('heroicon-o-building-office'),

                        Forms\Components\Select::make('project_id')
                            ->label('Project (Optional)')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('No project linked')
                            ->suffixIcon('heroicon-o-briefcase'),

                        Forms\Components\TextInput::make('contract_number')
                            ->label('Contract Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->default(fn () => 'CON-' . date('Y') . '-' . str_pad((string) (Contract::count() + 1), 4, '0', STR_PAD_LEFT))
                            ->suffixIcon('heroicon-o-hashtag'),

                        Forms\Components\TextInput::make('title')
                            ->label('Contract Title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2)
                            ->placeholder('Enter contract title'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending Signature',
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'expired' => 'Expired',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-signal'),

                        Forms\Components\Select::make('type')
                            ->options([
                                'fixed_price' => 'Fixed Price',
                                'time_and_materials' => 'Time & Materials',
                                'retainer' => 'Retainer',
                                'milestone' => 'Milestone-Based',
                                'subscription' => 'Subscription',
                                'other' => 'Other',
                            ])
                            ->default('fixed_price')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-tag'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Financial Details')
                    ->description('Contract value and deposit information')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('Contract Value')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(9999999.99)
                            ->required()
                            ->helperText('Total value of the contract'),

                        Forms\Components\TextInput::make('deposit_amount')
                            ->label('Deposit Amount')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(9999999.99)
                            ->placeholder('0.00')
                            ->helperText('Initial deposit required'),

                        Forms\Components\Toggle::make('deposit_paid')
                            ->label('Deposit Paid')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Mark if deposit has been received'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Timeline')
                    ->description('Contract dates and deadlines')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->displayFormat('M d, Y')
                            ->suffixIcon('heroicon-o-calendar'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->after('start_date')
                            ->suffixIcon('heroicon-o-calendar-days')
                            ->placeholder('No end date'),

                        Forms\Components\DatePicker::make('signed_at')
                            ->label('Signed Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->suffixIcon('heroicon-o-pencil-square')
                            ->hidden(fn (Forms\Get $get): bool => !in_array($get('status'), ['active', 'completed'])),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Description & Terms')
                    ->description('Detailed contract information')
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
                                'undo',
                            ])
                            ->columnSpanFull()
                            ->placeholder('Describe the scope of work and deliverables...'),

                        Forms\Components\Textarea::make('terms')
                            ->label('Terms & Conditions')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Specify payment terms, warranties, and other conditions...'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Add any internal notes or comments...'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Signature Information')
                    ->description('Digital signature details')
                    ->schema([
                        Forms\Components\TextInput::make('signed_by_name')
                            ->label('Signed By (Name)')
                            ->maxLength(255)
                            ->placeholder('Client full name'),

                        Forms\Components\TextInput::make('signed_by_email')
                            ->label('Signed By (Email)')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('client@company.com'),

                        Forms\Components\TextInput::make('signed_ip_address')
                            ->label('IP Address')
                            ->maxLength(45)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Automatically captured'),

                        Forms\Components\FileUpload::make('signed_document')
                            ->label('Signed Document')
                            ->directory('contracts')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpan(2),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed()
                    ->hidden(fn (?Contract $record) => $record === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('Contract #')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->url(fn (Contract $record): string => route('filament.admin.resources.contracts.view', ['record' => $record]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Contract $record): ?string => $record->company?->name)
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->url(fn (Contract $record): string => $record->company ? route('filament.admin.resources.companies.view', ['record' => $record->company]) : '#'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-o-check-circle',
                        'completed' => 'heroicon-o-check-badge',
                        'cancelled' => 'heroicon-o-x-circle',
                        'expired' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-document-duplicate',
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()->toString())
                    ->toggleable()
                    ->color('info'),

                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->money('USD')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\IconColumn::make('deposit_paid')
                    ->label('Deposit')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Ongoing')
                    ->color(fn (?string $state, Contract $record): string =>
                        $record->end_date && $record->end_date->isPast() && $record->status === 'active'
                            ? 'danger'
                            : 'gray'
                    ),

                Tables\Columns\TextColumn::make('signed_at')
                    ->label('Signed')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Not signed'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending Signature',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'fixed_price' => 'Fixed Price',
                        'time_and_materials' => 'Time & Materials',
                        'retainer' => 'Retainer',
                        'milestone' => 'Milestone-Based',
                        'subscription' => 'Subscription',
                        'other' => 'Other',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('deposit_paid')
                    ->label('Deposit Status')
                    ->placeholder('All contracts')
                    ->trueLabel('Deposit paid')
                    ->falseLabel('Deposit pending'),

                Tables\Filters\Filter::make('active_contracts')
                    ->label('Active Contracts')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('status', 'active')
                    ),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring Soon (30 days)')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('status', 'active')
                            ->whereNotNull('end_date')
                            ->whereBetween('end_date', [now(), now()->addDays(30)])
                    ),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('activate')
                        ->label('Activate Contract')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Contract $record): bool => $record->status === 'pending')
                        ->form([
                            Forms\Components\DatePicker::make('signed_at')
                                ->label('Signed Date')
                                ->required()
                                ->default(now())
                                ->native(false),
                        ])
                        ->action(function (Contract $record, array $data) {
                            $record->update([
                                'status' => 'active',
                                'signed_at' => $data['signed_at'],
                            ]);
                        })
                        ->successNotificationTitle('Contract activated successfully'),
                    Tables\Actions\Action::make('mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-badge')
                        ->color('info')
                        ->visible(fn (Contract $record): bool => $record->status === 'active')
                        ->requiresConfirmation()
                        ->action(fn (Contract $record) => $record->update(['status' => 'completed']))
                        ->successNotificationTitle('Contract marked as completed'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('update_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-signal')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('New Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'pending' => 'Pending Signature',
                                    'active' => 'Active',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                    'expired' => 'Expired',
                                ])
                                ->required(),
                        ])
                        ->action(fn ($records, array $data) => $records->each->update(['status' => $data['status']]))
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
                Infolists\Components\Section::make('Contract Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('contract_number')
                                    ->label('Contract Number')
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'pending' => 'warning',
                                        'active' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'danger',
                                        'expired' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('type')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()->toString())
                                    ->color('info'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Client & Project')
                    ->schema([
                        Infolists\Components\TextEntry::make('company.name')
                            ->label('Company')
                            ->icon('heroicon-o-building-office')
                            ->url(fn (Contract $record): string => $record->company ? route('filament.admin.resources.companies.view', ['record' => $record->company]) : '#'),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label('Project')
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('No project linked')
                            ->url(fn (Contract $record): ?string =>
                                $record->project ? route('filament.admin.resources.projects.view', ['record' => $record->project]) : null
                            ),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Financial Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('value')
                            ->label('Contract Value')
                            ->money('USD')
                            ->weight(FontWeight::Bold)
                            ->color('success'),

                        Infolists\Components\TextEntry::make('deposit_amount')
                            ->label('Deposit Amount')
                            ->money('USD')
                            ->placeholder('No deposit'),

                        Infolists\Components\IconEntry::make('deposit_paid')
                            ->label('Deposit Paid')
                            ->boolean(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Timeline')
                    ->schema([
                        Infolists\Components\TextEntry::make('start_date')
                            ->label('Start Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-calendar'),

                        Infolists\Components\TextEntry::make('end_date')
                            ->label('End Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-calendar-days')
                            ->placeholder('Ongoing')
                            ->color(fn (?string $state, Contract $record): string =>
                                $record->end_date && $record->end_date->isPast() && $record->status === 'active'
                                    ? 'danger'
                                    : 'gray'
                            ),

                        Infolists\Components\TextEntry::make('signed_at')
                            ->label('Signed Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-pencil-square')
                            ->placeholder('Not signed'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Description & Terms')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->html()
                            ->placeholder('No description provided')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('terms')
                            ->label('Terms & Conditions')
                            ->placeholder('No terms specified')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('notes')
                            ->label('Internal Notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn (Contract $record): bool => !$record->description && !$record->terms && !$record->notes),

                Infolists\Components\Section::make('Signature Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('signed_by_name')
                            ->label('Signed By (Name)')
                            ->placeholder('Not signed')
                            ->icon('heroicon-o-user'),

                        Infolists\Components\TextEntry::make('signed_by_email')
                            ->label('Signed By (Email)')
                            ->placeholder('Not signed')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('signed_ip_address')
                            ->label('IP Address')
                            ->placeholder('Not captured')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('signed_document')
                            ->label('Signed Document')
                            ->placeholder('No document uploaded')
                            ->url(fn (?string $state): ?string => $state ? asset('storage/' . $state) : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(fn (Contract $record): bool => !$record->signed_by_name && !$record->signed_document),

                Infolists\Components\Section::make('Metadata')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y H:i'),

                        Infolists\Components\TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->dateTime('M d, Y H:i')
                            ->placeholder('Active'),
                    ])
                    ->columns(3)
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'view' => Pages\ViewContract::route('/{record}'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
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
