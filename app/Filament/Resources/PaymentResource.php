<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Time & Billing';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'payment_number';

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = static::getModel()::where('status', 'pending')->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->description('Basic payment details and relationships')
                    ->schema([
                        Forms\Components\Select::make('invoice_id')
                            ->label('Invoice')
                            ->relationship('invoice', 'invoice_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->suffixIcon('heroicon-o-document-text')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                if ($state) {
                                    $invoice = \App\Models\Invoice::find($state);
                                    if ($invoice) {
                                        $set('company_id', $invoice->company_id);
                                        $set('amount', $invoice->balance);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (Forms\Get $get): bool => (bool) $get('invoice_id'))
                            ->suffixIcon('heroicon-o-building-office'),

                        Forms\Components\TextInput::make('payment_number')
                            ->label('Payment Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->default(fn () => 'PAY-' . date('Y') . '-' . str_pad((string) (Payment::count() + 1), 4, '0', STR_PAD_LEFT))
                            ->suffixIcon('heroicon-o-hashtag'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('completed')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-signal'),

                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'credit_card' => 'Credit Card',
                                'debit_card' => 'Debit Card',
                                'bank_transfer' => 'Bank Transfer',
                                'paypal' => 'PayPal',
                                'stripe' => 'Stripe',
                                'check' => 'Check',
                                'cash' => 'Cash',
                                'other' => 'Other',
                            ])
                            ->default('credit_card')
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-credit-card'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Payment Details')
                    ->description('Amount and transaction information')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Payment Amount')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0.01)
                            ->maxValue(9999999.99)
                            ->required()
                            ->helperText('Amount received from the client'),

                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Payment Date')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->displayFormat('M d, Y')
                            ->suffixIcon('heroicon-o-calendar'),

                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->maxLength(255)
                            ->placeholder('Optional transaction reference')
                            ->helperText('External payment gateway transaction ID'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Stripe Integration')
                    ->description('Stripe payment gateway details')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_payment_intent_id')
                            ->label('Stripe Payment Intent ID')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Automatically synced from Stripe'),

                        Forms\Components\TextInput::make('stripe_charge_id')
                            ->label('Stripe Charge ID')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Automatically synced from Stripe'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Additional Information')
                    ->description('Notes and metadata')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Add any notes about this payment...'),

                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->reorderable()
                            ->columnSpanFull()
                            ->helperText('Additional custom data in key-value format'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('Payment #')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->url(fn (Payment $record): string => route('filament.admin.resources.payments.view', ['record' => $record]))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Payment $record): ?string =>
                        $record->invoice ? route('filament.admin.resources.invoices.view', ['record' => $record->invoice]) : null
                    ),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Payment $record): string => $record->company ? route('filament.admin.resources.companies.view', ['record' => $record->company]) : '#'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()->toString())
                    ->color(fn (string $state): string => match ($state) {
                        'credit_card', 'debit_card' => 'info',
                        'stripe', 'paypal' => 'primary',
                        'bank_transfer' => 'warning',
                        'cash', 'check' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'credit_card', 'debit_card' => 'heroicon-o-credit-card',
                        'bank_transfer' => 'heroicon-o-building-library',
                        'cash' => 'heroicon-o-banknotes',
                        default => 'heroicon-o-currency-dollar',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'completed' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-x-circle',
                        'refunded' => 'heroicon-o-arrow-uturn-left',
                        'processing' => 'heroicon-o-arrow-path',
                        default => 'heroicon-o-clock',
                    }),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(20)
                    ->placeholder('N/A'),

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
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'credit_card' => 'Credit Card',
                        'debit_card' => 'Debit Card',
                        'bank_transfer' => 'Bank Transfer',
                        'paypal' => 'PayPal',
                        'stripe' => 'Stripe',
                        'check' => 'Check',
                        'cash' => 'Cash',
                        'other' => 'Other',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('invoice')
                    ->relationship('invoice', 'invoice_number')
                    ->searchable()
                    ->preload(),

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
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Payment $record): bool => $record->status !== 'completed')
                        ->requiresConfirmation()
                        ->action(fn (Payment $record) => $record->update(['status' => 'completed']))
                        ->successNotificationTitle('Payment marked as completed'),
                    Tables\Actions\Action::make('mark_failed')
                        ->label('Mark as Failed')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Payment $record): bool => $record->status === 'pending' || $record->status === 'processing')
                        ->requiresConfirmation()
                        ->action(fn (Payment $record) => $record->update(['status' => 'failed']))
                        ->successNotificationTitle('Payment marked as failed'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'completed']))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('update_payment_method')
                        ->label('Update Payment Method')
                        ->icon('heroicon-o-credit-card')
                        ->form([
                            Forms\Components\Select::make('payment_method')
                                ->label('Payment Method')
                                ->options([
                                    'credit_card' => 'Credit Card',
                                    'debit_card' => 'Debit Card',
                                    'bank_transfer' => 'Bank Transfer',
                                    'paypal' => 'PayPal',
                                    'stripe' => 'Stripe',
                                    'check' => 'Check',
                                    'cash' => 'Cash',
                                    'other' => 'Other',
                                ])
                                ->required(),
                        ])
                        ->action(fn ($records, array $data) => $records->each->update(['payment_method' => $data['payment_method']]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('payment_date', 'desc')
            ->poll('60s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Payment Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_number')
                            ->label('Payment Number')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'gray',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('amount')
                                    ->label('Payment Amount')
                                    ->money('USD')
                                    ->badge()
                                    ->color('success')
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('payment_method')
                                    ->label('Payment Method')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()->toString())
                                    ->color('info'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Invoice & Company')
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice.invoice_number')
                            ->label('Invoice')
                            ->icon('heroicon-o-document-text')
                            ->url(fn (Payment $record): ?string =>
                                $record->invoice ? route('filament.admin.resources.invoices.view', ['record' => $record->invoice]) : null
                            ),

                        Infolists\Components\TextEntry::make('company.name')
                            ->label('Company')
                            ->icon('heroicon-o-building-office')
                            ->url(fn (Payment $record): string => $record->company ? route('filament.admin.resources.companies.view', ['record' => $record->company]) : '#'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Payment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('amount')
                            ->label('Amount')
                            ->money('USD')
                            ->weight(FontWeight::Bold),

                        Infolists\Components\TextEntry::make('payment_date')
                            ->label('Payment Date')
                            ->date('M d, Y')
                            ->icon('heroicon-o-calendar'),

                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()->toString())
                            ->icon('heroicon-o-credit-card'),

                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label('Transaction ID')
                            ->placeholder('No transaction ID')
                            ->copyable(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Stripe Integration')
                    ->schema([
                        Infolists\Components\TextEntry::make('stripe_payment_intent_id')
                            ->label('Stripe Payment Intent ID')
                            ->placeholder('Not synced with Stripe')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('stripe_charge_id')
                            ->label('Stripe Charge ID')
                            ->placeholder('Not synced with Stripe')
                            ->copyable(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(fn (Payment $record): bool => !$record->stripe_payment_intent_id && !$record->stripe_charge_id),

                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->placeholder('No notes provided')
                            ->columnSpanFull(),

                        Infolists\Components\KeyValueEntry::make('metadata')
                            ->label('Metadata')
                            ->placeholder('No metadata')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn (Payment $record): bool => !$record->notes && !$record->metadata),

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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
