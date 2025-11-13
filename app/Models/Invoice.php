<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Invoice Model
 *
 * Represents an invoice for a company with line items, payments, and status tracking.
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $project_id
 * @property string $invoice_number
 * @property string $status
 * @property \Carbon\Carbon $issue_date
 * @property \Carbon\Carbon $due_date
 * @property \Carbon\Carbon|null $paid_at
 * @property float $subtotal
 * @property float $tax_rate
 * @property float $tax_amount
 * @property float $discount_amount
 * @property float $total
 * @property float $paid_amount
 * @property float $balance
 * @property string $currency
 * @property string|null $notes
 * @property string|null $terms
 * @property string|null $pdf_path
 * @property \Carbon\Carbon|null $sent_at
 * @property \Carbon\Carbon|null $viewed_at
 * @property string|null $stripe_invoice_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'project_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
        'paid_at',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total',
        'paid_amount',
        'balance',
        'currency',
        'notes',
        'terms',
        'pdf_path',
        'sent_at',
        'viewed_at',
        'stripe_invoice_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'company_id' => 'integer',
        'project_id' => 'integer',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the company that owns the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the project associated with the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the invoice items for the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the payments for the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the time entries associated with the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }
}
