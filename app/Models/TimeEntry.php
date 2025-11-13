<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TimeEntry Model
 *
 * Represents a time tracking entry for a project or task.
 *
 * @property int $id
 * @property int $project_id
 * @property int|null $task_id
 * @property int $user_id
 * @property string|null $description
 * @property \Carbon\Carbon $start_time
 * @property \Carbon\Carbon|null $end_time
 * @property float $hours
 * @property bool $is_billable
 * @property float|null $hourly_rate
 * @property float|null $amount
 * @property bool $is_invoiced
 * @property int|null $invoice_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class TimeEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'task_id',
        'user_id',
        'description',
        'start_time',
        'end_time',
        'hours',
        'is_billable',
        'hourly_rate',
        'amount',
        'is_invoiced',
        'invoice_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'project_id' => 'integer',
        'task_id' => 'integer',
        'user_id' => 'integer',
        'invoice_id' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hours' => 'decimal:2',
        'is_billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'is_invoiced' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project that owns the time entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the task associated with the time entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who created the time entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invoice associated with the time entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
