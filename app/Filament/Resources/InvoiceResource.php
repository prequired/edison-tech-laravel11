<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
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

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Time & Billing';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function getNavigationBadge(): ?string
    {
        $overdueCount = static::getModel()::where('status', 'sent')
            ->where('due_date', '<', now())
            ->count();

        return $overdueCount > 0 ? (string) $overdueCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Information')
                    ->description('Basic invoice details and relationships')
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

                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->default(fn () => 'INV-' . date('Y') . '-' . str_pad((string) (Invoice::count() + 1), 4, '0', STR_PAD_LEFT))
                            ->suffixIcon('heroicon-o-hashtag'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'viewed' => 'Viewed',
                                'partial' => 'Partially Paid',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-signal'),

                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD - US Dollar',
                                'EUR' => 'EUR - Euro',
                                'GBP' => 'GBP - British Pound',
                                'CAD' => 'CAD - Canadian Dollar',
                            ])
                            ->default('USD')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-currency-dollar'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Dates & Deadlines')
                    ->description('Important dates for this invoice')
                    ->schema([
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Issue Date')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->displayFormat('M d, Y')
                            ->suffixIcon('heroicon-o-calendar'),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required()
                            ->native(false)
                            ->default(now()->addDays(30))
                            ->displayFormat('M d, Y')
                            ->after('issue_date')
                            ->suffixIcon('heroicon-o-calendar-days'),

                        Forms\Components\DatePicker::make('paid_at')
                            ->label('Paid Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->suffixIcon('heroicon-o-check-badge')
                            ->hidden(fn (Forms\Get $get): bool => $get('status') !== 'paid'),

                        Forms\Components\DateTimePicker::make('sent_at')
                            ->label('Sent At')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\DateTimePicker::make('viewed_at')
                            ->label('Viewed At')
                            ->native(false)
                            ->displayFormat('M d, Y H:i')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Financial Details')
                    ->description('Amounts, taxes, and totals')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(9999999.99)
                            ->required()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $subtotal = (float) ($get('subtotal') ?? 0);
                                $taxRate = (float) ($get('tax_rate') ?? 0);
                                $discount = (float) ($get('discount_amount') ?? 0);

                                $taxAmount = $subtotal * ($taxRate / 100);
                                $total = $subtotal + $taxAmount - $discount;

                                $set('tax_amount', number_format($taxAmount, 2, '.', ''));
                                $set('total', number_format($total, 2, '.', ''));
                            }),

                        Forms\Components\TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->numeric()
                            ->suffix('%')
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $subtotal = (float) ($get('subtotal') ?? 0);
                                $taxRate = (float) ($get('tax_rate') ?? 0);
                                $discount = (float) ($get('discount_amount') ?? 0);

                                $taxAmount = $subtotal * ($taxRate / 100);
                                $total = $subtotal + $taxAmount - $discount;

                                $set('tax_amount', number_format($taxAmount, 2, '.', ''));
                                $set('total', number_format($total, 2, '.', ''));
                            }),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Tax Amount')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Discount')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $subtotal = (float) ($get('subtotal') ?? 0);
                                $taxRate = (float) ($get('tax_rate') ?? 0);
                                $discount = (float) ($get('discount_amount') ?? 0);

                                $taxAmount = $subtotal * ($taxRate / 100);
                                $total = $subtotal + $taxAmount - $discount;

                                $set('tax_amount', number_format($taxAmount, 2, '.', ''));
                                $set('total', number_format($total, 2, '.', ''));
                            }),

                        Forms\Components\TextInput::make('total')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('paid_amount')
                            ->label('Paid Amount')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $total = (float) ($get('total') ?? 0);
                                $paid = (float) ($get('paid_amount') ?? 0);
                                $balance = $total - $paid;

                                $set('balance', number_format($balance, 2, '.', ''));
                            }),

                        Forms\Components\TextInput::make('balance')
                            ->label('Balance Due')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->description('Notes, terms, and payment details')
                    ->schema([
                        Forms\Components\RichEditor::make('notes')
                            ->label('Notes')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull()
                            ->placeholder('Add any notes for the client...'),

                        Forms\Components\Textarea::make('terms')
                            ->label('Payment Terms')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Payment is due within 30 days of invoice date...'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Integration & Files')
                    ->description('Stripe integration and PDF storage')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_invoice_id')
                            ->label('Stripe Invoice ID')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Not synced with Stripe'),

                        Forms\Components\FileUpload::make('pdf_path')
                            ->label('Invoice PDF')
                            ->directory('invoices')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->previewable(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->hidden(fn (?Invoice $record) => $record === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->url(fn (Invoice $record): string => route('filament.admin.resources.invoices.view', ['record' => $record]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Invoice $record): ?string => $record->project?->name)
                    ->url(fn (Invoice $record): string => $record->company ? route('filament.admin.resources.companies.view', ['record' => $record->company]) : '#'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'viewed' => 'warning',
                        'partial' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'paid' => 'heroicon-o-check-circle',
                        'overdue' => 'heroicon-o-exclamation-triangle',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-document-text',
                    }),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Issued')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->color(fn (?string $state, Invoice $record): string =>
                        $record->due_date && $record->due_date->isPast() && $record->status !== 'paid'
                            ? 'danger'
                            : 'gray'
                    ),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money('USD')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color(fn (float $state): string => $state > 0 ? 'warning' : 'success'),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Sent')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Not sent'),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Not paid'),

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
                        'sent' => 'Sent',
                        'viewed' => 'Viewed',
                        'partial' => 'Partially Paid',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Invoices')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('due_date', '<', now())
                            ->whereNotIn('status', ['paid', 'cancelled'])
                    ),

                Tables\Filters\Filter::make('unpaid')
                    ->label('Unpaid Invoices')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('balance', '>', 0)
                            ->whereNotIn('status', ['cancelled'])
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
                                fn (Builder $query, $date): Builder => $query->whereDate('issue_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('issue_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('mark_sent')
                        ->label('Mark as Sent')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->visible(fn (Invoice $record): bool => $record->status === 'draft')
                        ->requiresConfirmation()
                        ->action(fn (Invoice $record) => $record->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]))
                        ->successNotificationTitle('Invoice marked as sent'),
                    Tables\Actions\Action::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Invoice $record): bool => $record->status !== 'paid' && $record->status !== 'cancelled')
                        ->form([
                            Forms\Components\DatePicker::make('paid_at')
                                ->label('Payment Date')
                                ->required()
                                ->default(now())
                                ->native(false),
                        ])
                        ->action(function (Invoice $record, array $data) {
                            $record->update([
                                'status' => 'paid',
                                'paid_at' => $data['paid_at'],
                                'paid_amount' => $record->total,
                                'balance' => 0,
                            ]);
                        })
                        ->successNotificationTitle('Invoice marked as paid'),
                    Tables\Actions\Action::make('record_payment')
                        ->label('Record Payment')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->visible(fn (Invoice $record): bool => $record->balance > 0 && $record->status !== 'cancelled')
                        ->form([
                            Forms\Components\TextInput::make('amount')
                                ->label('Payment Amount')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->minValue(0.01),
                            Forms\Components\DatePicker::make('payment_date')
                                ->label('Payment Date')
                                ->required()
                                ->default(now())
                                ->native(false),
                        ])
                        ->action(function (Invoice $record, array $data) {
                            $newPaidAmount = $record->paid_amount + $data['amount'];
                            $newBalance = $record->total - $newPaidAmount;

                            $status = 'partial';
                            if ($newBalance <= 0) {
                                $status = 'paid';
                            }

                            $record->update([
                                'paid_amount' => $newPaidAmount,
                                'balance' => $newBalance,
                                'status' => $status,
                                'paid_at' => $status === 'paid' ? $data['payment_date'] : $record->paid_at,
                            ]);
                        })
                        ->successNotificationTitle('Payment recorded successfully'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_sent')
                        ->label('Mark as Sent')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\DatePicker::make('paid_at')
                                ->label('Payment Date')
                                ->required()
                                ->default(now())
                                ->native(false),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => 'paid',
                                    'paid_at' => $data['paid_at'],
                                    'paid_amount' => $record->total,
                                    'balance' => 0,
                                ]);
                            }
                        })
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
                Infolists\Components\Section::make('Invoice Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_number')
                            ->label('Invoice Number')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'sent' => 'info',
                                        'viewed' => 'warning',
                                        'partial' => 'warning',
                                        'paid' => 'success',
                                        'overdue' => 'danger',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('total')
                                    ->label('Total Amount')
                                    ->money('USD')
                                    ->badge()
                                    ->color('success')
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('balance')
                                    ->label('Balance Due')
                                    ->money('USD')
                                    ->badge()
                                    ->color(fn (float $state): string => $state > 0 ? 'warning' : 'success')
                                    ->weight(FontWeight::Bold),
                            ]),
                    ]),

                Infolists\Components\Section::make('Client & Project')
                    ->schema([
                        Infolists\Components\TextEntry::make('company.name')
                            ->label('Company')
                            ->icon('heroicon-o-building-office')
                            ->url(fn (Invoice $record): string => $record->company ? route('filament.admin.resources.companies.view', ['record' => $record->company]) : '#'),

                        Infolists\Components\TextEntry::make('project.name')
                            ->label('Project')
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('No project linked')
                            ->url(fn (Invoice $record): ?string =>
                                $record->project ? route('filament.admin.resources.projects.view', ['record' => $record->project]) : null
                            ),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Important Dates')
                    ->schema([
                        Infolists\Components\TextEntry::make('issue_date')
                            ->label('Issue Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-calendar'),

                        Infolists\Components\TextEntry::make('due_date')
                            ->label('Due Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-calendar-days')
                            ->color(fn (?string $state, Invoice $record): string =>
                                $record->due_date && $record->due_date->isPast() && $record->status !== 'paid'
                                    ? 'danger'
                                    : 'gray'
                            ),

                        Infolists\Components\TextEntry::make('paid_at')
                            ->label('Paid Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-check-badge')
                            ->placeholder('Not paid')
                            ->visible(fn (Invoice $record): bool => $record->status === 'paid'),

                        Infolists\Components\TextEntry::make('sent_at')
                            ->label('Sent At')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-paper-airplane')
                            ->placeholder('Not sent'),

                        Infolists\Components\TextEntry::make('viewed_at')
                            ->label('Viewed At')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-eye')
                            ->placeholder('Not viewed'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Financial Breakdown')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('USD'),

                        Infolists\Components\TextEntry::make('tax_rate')
                            ->label('Tax Rate')
                            ->suffix('%'),

                        Infolists\Components\TextEntry::make('tax_amount')
                            ->label('Tax Amount')
                            ->money('USD'),

                        Infolists\Components\TextEntry::make('discount_amount')
                            ->label('Discount')
                            ->money('USD'),

                        Infolists\Components\TextEntry::make('total')
                            ->label('Total')
                            ->money('USD')
                            ->weight(FontWeight::Bold),

                        Infolists\Components\TextEntry::make('paid_amount')
                            ->label('Paid Amount')
                            ->money('USD')
                            ->color('success'),

                        Infolists\Components\TextEntry::make('balance')
                            ->label('Balance Due')
                            ->money('USD')
                            ->weight(FontWeight::Bold)
                            ->color(fn (float $state): string => $state > 0 ? 'warning' : 'success'),

                        Infolists\Components\TextEntry::make('currency')
                            ->label('Currency'),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->html()
                            ->placeholder('No notes provided')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('terms')
                            ->label('Payment Terms')
                            ->placeholder('No payment terms specified')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn (Invoice $record): bool => !$record->notes && !$record->terms),

                Infolists\Components\Section::make('Integration & Files')
                    ->schema([
                        Infolists\Components\TextEntry::make('stripe_invoice_id')
                            ->label('Stripe Invoice ID')
                            ->placeholder('Not synced with Stripe')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('pdf_path')
                            ->label('Invoice PDF')
                            ->placeholder('No PDF generated')
                            ->url(fn (?string $state): ?string => $state ? asset('storage/' . $state) : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
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
