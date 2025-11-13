<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Project Model
 *
 * Represents a project for a company with status tracking, budget, timeline, and team management.
 *
 * @property int $id
 * @property int $company_id
 * @property int $created_by
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $status
 * @property string|null $type
 * @property float|null $budget
 * @property float|null $estimated_hours
 * @property float $actual_hours
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $deadline
 * @property \Carbon\Carbon|null $completed_at
 * @property int $progress
 * @property string $priority
 * @property string|null $repository_url
 * @property string|null $staging_url
 * @property string|null $production_url
 * @property array|null $technologies
 * @property string|null $notes
 * @property bool $is_billable
 * @property float|null $hourly_rate
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'created_by',
        'name',
        'slug',
        'description',
        'status',
        'type',
        'budget',
        'estimated_hours',
        'actual_hours',
        'start_date',
        'deadline',
        'completed_at',
        'progress',
        'priority',
        'repository_url',
        'staging_url',
        'production_url',
        'technologies',
        'notes',
        'is_billable',
        'hourly_rate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',
        'budget' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'start_date' => 'date',
        'deadline' => 'date',
        'completed_at' => 'date',
        'progress' => 'integer',
        'technologies' => 'array',
        'is_billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the company that owns the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the contracts for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the invoices for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the tasks for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the time entries for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    /**
     * Get the status history for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(ProjectStatusHistory::class);
    }

    /**
     * Get the documents for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get the portfolio items for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    /**
     * Get the testimonials for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * The users that belong to the project (team members).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}
