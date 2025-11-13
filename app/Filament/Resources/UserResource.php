<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
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
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Business Operations';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->description('Personal details and avatar')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Profile Picture')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->directory('users/avatars')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                            ->helperText('Upload a profile picture (PNG, JPG, or WebP, max 1MB). Recommended size: 256×256px')
                            ->imagePreviewHeight('120')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('256')
                            ->imageResizeTargetHeight('256')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),

                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Administrator',
                                'employee' => 'Employee',
                                'client' => 'Client',
                            ])
                            ->required()
                            ->default('employee')
                            ->native(false)
                            ->suffixIcon('heroicon-o-shield-check'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Company & Access')
                    ->description('Company association and permissions')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('No company assigned')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\TextInput::make('timezone')
                            ->label('Timezone')
                            ->default('UTC')
                            ->placeholder('UTC')
                            ->helperText('e.g., America/New_York, Europe/London'),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255)
                            ->revealable()
                            ->helperText('Minimum 8 characters. Leave blank to keep current password.'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Metadata')
                    ->description('System information')
                    ->schema([
                        Forms\Components\Placeholder::make('last_login_at')
                            ->label('Last Login')
                            ->content(fn (?User $record): string => $record?->last_login_at?->diffForHumans() ?? 'Never'),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created At')
                            ->content(fn (?User $record): string => $record?->created_at?->diffForHumans() ?? 'Just now'),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last Updated')
                            ->content(fn (?User $record): string => $record?->updated_at?->diffForHumans() ?? 'Just now'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed()
                    ->hidden(fn (?User $record) => $record === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=3B82F6&background=EFF6FF&size=256')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (User $record): string => $record->email),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('No company')
                    ->url(fn (User $record): string => $record->company ? route('filament.admin.resources.companies.view', ['record' => $record->company]) : '#'),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'employee' => 'info',
                        'client' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str($state)->title()->toString()),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Administrator',
                        'employee' => 'Employee',
                        'client' => 'Client',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (User $record): bool => !$record->is_active)
                        ->requiresConfirmation()
                        ->action(fn (User $record) => $record->update(['is_active' => true]))
                        ->successNotificationTitle('User activated'),
                    Tables\Actions\Action::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (User $record): bool => $record->is_active)
                        ->requiresConfirmation()
                        ->action(fn (User $record) => $record->update(['is_active' => false]))
                        ->successNotificationTitle('User deactivated'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('updateRole')
                        ->label('Update Role')
                        ->icon('heroicon-o-shield-check')
                        ->form([
                            Forms\Components\Select::make('role')
                                ->label('New Role')
                                ->options([
                                    'admin' => 'Administrator',
                                    'employee' => 'Employee',
                                    'client' => 'Client',
                                ])
                                ->required(),
                        ])
                        ->action(fn (array $data, $records) => $records->each->update(['role' => $data['role']]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('User Profile')
                    ->schema([
                        Infolists\Components\ImageEntry::make('avatar')
                            ->label('Profile Picture')
                            ->size(120)
                            ->circular()
                            ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=3B82F6&background=EFF6FF&size=256')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('name')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        Infolists\Components\TextEntry::make('email')
                            ->copyable()
                            ->icon('heroicon-o-envelope'),

                        Infolists\Components\TextEntry::make('phone')
                            ->copyable()
                            ->icon('heroicon-o-phone')
                            ->placeholder('No phone'),

                        Infolists\Components\TextEntry::make('role')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'admin' => 'danger',
                                'employee' => 'info',
                                'client' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => str($state)->title()->toString()),

                        Infolists\Components\TextEntry::make('timezone')
                            ->label('Timezone')
                            ->placeholder('UTC'),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Company Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('company.name')
                            ->label('Company')
                            ->placeholder('No company assigned')
                            ->url(fn ($record) => $record->company ? route('filament.admin.resources.companies.view', ['record' => $record->company]) : null),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Activity')
                    ->schema([
                        Infolists\Components\TextEntry::make('last_login_at')
                            ->label('Last Login')
                            ->dateTime('M d, Y H:i')
                            ->placeholder('Never logged in'),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
