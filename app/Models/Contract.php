<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Contract Model
 *
 * Represents a contract between the company and a client for a project.
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $project_id
 * @property string $contract_number
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property string $type
 * @property float $value
 * @property float|null $deposit_amount
 * @property bool $deposit_paid
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon|null $end_date
 * @property \Carbon\Carbon|null $signed_at
 * @property string|null $signed_document
 * @property string|null $signed_by_name
 * @property string|null $signed_by_email
 * @property string|null $signed_ip_address
 * @property string|null $terms
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Contract extends Model
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
        'contract_number',
        'title',
        'description',
        'status',
        'type',
        'value',
        'deposit_amount',
        'deposit_paid',
        'start_date',
        'end_date',
        'signed_at',
        'signed_document',
        'signed_by_name',
        'signed_by_email',
        'signed_ip_address',
        'terms',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'company_id' => 'integer',
        'project_id' => 'integer',
        'value' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'deposit_paid' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the company that owns the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the project associated with the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the documents for the contract.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
