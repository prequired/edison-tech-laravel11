<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * ActivityLog Model
 *
 * Represents an activity log entry for tracking actions in the system.
 *
 * @property int $id
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property string|null $log_name
 * @property string $description
 * @property array|null $properties
 * @property string|null $event
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'log_name',
        'description',
        'properties',
        'event',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subject_id' => 'integer',
        'causer_id' => 'integer',
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the subject model that the activity was performed on.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer model (the user/model that performed the activity).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
