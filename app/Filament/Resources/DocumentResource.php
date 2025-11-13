<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
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

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $navigationGroup = 'Business Operations';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $recentCount = static::getModel()::where('created_at', '>=', now()->subDays(7))->count();

        return $recentCount > 0 ? (string) $recentCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->description('Basic document details and file upload')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Document File')
                            ->required()
                            ->directory('documents')
                            ->maxSize(51200) // 50MB
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'text/plain',
                            ])
                            ->columnSpan(2)
                            ->helperText('Accepted: PDF, DOC, DOCX, XLS, XLSX, Images, TXT (Max 50MB)'),

                        Forms\Components\TextInput::make('name')
                            ->label('Document Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2)
                            ->placeholder('Enter document name'),

                        Forms\Components\Select::make('category')
                            ->options([
                                'contract' => 'Contract',
                                'invoice' => 'Invoice',
                                'proposal' => 'Proposal',
                                'specification' => 'Specification',
                                'report' => 'Report',
                                'presentation' => 'Presentation',
                                'image' => 'Image',
                                'legal' => 'Legal',
                                'other' => 'Other',
                            ])
                            ->native(false)
                            ->placeholder('Select category')
                            ->suffixIcon('heroicon-o-folder'),

                        Forms\Components\Toggle::make('is_public')
                            ->label('Public Access')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Make this document publicly accessible'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Relationship')
                    ->description('Link this document to a specific record')
                    ->schema([
                        Forms\Components\Select::make('documentable_type')
                            ->label('Document Type')
                            ->options([
                                'App\\Models\\Project' => 'Project',
                                'App\\Models\\Contract' => 'Contract',
                                'App\\Models\\Company' => 'Company',
                                'App\\Models\\Task' => 'Task',
                            ])
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('documentable_id', null))
                            ->placeholder('Select type')
                            ->suffixIcon('heroicon-o-link'),

                        Forms\Components\Select::make('documentable_id')
                            ->label('Related Record')
                            ->options(function (Forms\Get $get) {
                                $type = $get('documentable_type');
                                if (!$type) {
                                    return [];
                                }

                                return match ($type) {
                                    'App\\Models\\Project' => \App\Models\Project::pluck('name', 'id'),
                                    'App\\Models\\Contract' => \App\Models\Contract::pluck('title', 'id'),
                                    'App\\Models\\Company' => \App\Models\Company::pluck('name', 'id'),
                                    'App\\Models\\Task' => \App\Models\Task::pluck('title', 'id'),
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->placeholder('Select record')
                            ->disabled(fn (Forms\Get $get): bool => !$get('documentable_type'))
                            ->helperText('Link to a specific record'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Description')
                    ->description('Additional information about the document')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Describe the document contents...'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('File Information')
                    ->description('Automatically captured metadata')
                    ->schema([
                        Forms\Components\TextInput::make('filename')
                            ->label('File Name')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('mime_type')
                            ->label('File Type')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('file_size')
                            ->label('File Size (bytes)')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('uploaded_by')
                            ->label('Uploaded By')
                            ->relationship('uploader', 'name')
                            ->default(fn () => Auth::id())
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(4)
                    ->collapsible()
                    ->collapsed()
                    ->hidden(fn (?Document $record) => $record === null),

                Forms\Components\Hidden::make('uploaded_by')
                    ->default(fn () => Auth::id())
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Document $record): ?string => $record->category)
                    ->url(fn (Document $record): string => route('filament.admin.resources.documents.view', ['record' => $record]))
                    ->color('primary')
                    ->limit(50),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): string => $state ? str($state)->title()->toString() : 'Uncategorized')
                    ->color(fn (?string $state): string => match ($state) {
                        'contract' => 'primary',
                        'invoice' => 'success',
                        'proposal' => 'warning',
                        'specification' => 'info',
                        'report' => 'indigo',
                        'legal' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('documentable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : 'None')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('filename')
                    ->label('File')
                    ->searchable()
                    ->limit(30)
                    ->toggleable()
                    ->icon(fn (Document $record): string => match (true) {
                        str_contains($record->mime_type, 'pdf') => 'heroicon-o-document-text',
                        str_contains($record->mime_type, 'word') => 'heroicon-o-document',
                        str_contains($record->mime_type, 'excel') || str_contains($record->mime_type, 'spreadsheet') => 'heroicon-o-table-cells',
                        str_contains($record->mime_type, 'image') => 'heroicon-o-photo',
                        default => 'heroicon-o-document',
                    }),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 2) . ' KB')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded By')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-user')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'contract' => 'Contract',
                        'invoice' => 'Invoice',
                        'proposal' => 'Proposal',
                        'specification' => 'Specification',
                        'report' => 'Report',
                        'presentation' => 'Presentation',
                        'image' => 'Image',
                        'legal' => 'Legal',
                        'other' => 'Other',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('documentable_type')
                    ->label('Related To')
                    ->options([
                        'App\\Models\\Project' => 'Project',
                        'App\\Models\\Contract' => 'Contract',
                        'App\\Models\\Company' => 'Company',
                        'App\\Models\\Task' => 'Task',
                    ]),

                Tables\Filters\SelectFilter::make('uploaded_by')
                    ->label('Uploaded By')
                    ->relationship('uploader', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Access Level')
                    ->placeholder('All documents')
                    ->trueLabel('Public only')
                    ->falseLabel('Private only'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Uploaded From')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('Uploaded Until')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (Document $record): string => asset('storage/' . $record->file_path))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('toggle_public')
                        ->label(fn (Document $record): string => $record->is_public ? 'Make Private' : 'Make Public')
                        ->icon(fn (Document $record): string => $record->is_public ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                        ->color(fn (Document $record): string => $record->is_public ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->action(fn (Document $record) => $record->update(['is_public' => !$record->is_public]))
                        ->successNotificationTitle(fn (Document $record): string =>
                            'Document is now ' . ($record->is_public ? 'public' : 'private')
                        ),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('update_category')
                        ->label('Update Category')
                        ->icon('heroicon-o-folder')
                        ->form([
                            Forms\Components\Select::make('category')
                                ->label('Category')
                                ->options([
                                    'contract' => 'Contract',
                                    'invoice' => 'Invoice',
                                    'proposal' => 'Proposal',
                                    'specification' => 'Specification',
                                    'report' => 'Report',
                                    'presentation' => 'Presentation',
                                    'image' => 'Image',
                                    'legal' => 'Legal',
                                    'other' => 'Other',
                                ])
                                ->required(),
                        ])
                        ->action(fn ($records, array $data) => $records->each->update(['category' => $data['category']]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('make_public')
                        ->label('Make Public')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_public' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('make_private')
                        ->label('Make Private')
                        ->icon('heroicon-o-lock-closed')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_public' => false]))
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
                Infolists\Components\Section::make('Document Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('category')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => $state ? str($state)->title()->toString() : 'Uncategorized')
                                    ->color(fn (?string $state): string => match ($state) {
                                        'contract' => 'primary',
                                        'invoice' => 'success',
                                        'proposal' => 'warning',
                                        'specification' => 'info',
                                        'report' => 'indigo',
                                        'legal' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\IconEntry::make('is_public')
                                    ->label('Public Access')
                                    ->boolean(),

                                Infolists\Components\TextEntry::make('file_size')
                                    ->label('File Size')
                                    ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 2) . ' KB')
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),

                Infolists\Components\Section::make('File Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('filename')
                            ->label('File Name')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('mime_type')
                            ->label('File Type')
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('file_path')
                            ->label('Download')
                            ->formatStateUsing(fn (): string => 'Download File')
                            ->url(fn (Document $record): string => asset('storage/' . $record->file_path))
                            ->openUrlInNewTab()
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('primary'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Related Record')
                    ->schema([
                        Infolists\Components\TextEntry::make('documentable_type')
                            ->label('Type')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : 'Not linked')
                            ->icon('heroicon-o-link'),

                        Infolists\Components\TextEntry::make('documentable_id')
                            ->label('Record')
                            ->formatStateUsing(function (Document $record): string {
                                if (!$record->documentable) {
                                    return 'Not linked';
                                }

                                return match ($record->documentable_type) {
                                    'App\\Models\\Project' => $record->documentable->name ?? 'Unknown',
                                    'App\\Models\\Contract' => $record->documentable->title ?? 'Unknown',
                                    'App\\Models\\Company' => $record->documentable->name ?? 'Unknown',
                                    'App\\Models\\Task' => $record->documentable->title ?? 'Unknown',
                                    default => 'Unknown',
                                };
                            }),
                    ])
                    ->columns(2)
                    ->visible(fn (Document $record): bool => (bool) $record->documentable_type),

                Infolists\Components\Section::make('Description')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn (Document $record): bool => !$record->description),

                Infolists\Components\Section::make('Upload Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('uploader.name')
                            ->label('Uploaded By')
                            ->icon('heroicon-o-user'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Uploaded At')
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
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
