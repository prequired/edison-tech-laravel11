<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TwoFactorAuthentication Model
 *
 * Represents two-factor authentication settings for a user.
 *
 * @property int $id
 * @property int $user_id
 * @property string $secret_key
 * @property array|null $recovery_codes
 * @property bool $is_enabled
 * @property \Carbon\Carbon|null $enabled_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class TwoFactorAuthentication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'secret_key',
        'recovery_codes',
        'is_enabled',
        'enabled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'recovery_codes' => 'array',
        'is_enabled' => 'boolean',
        'enabled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'secret_key',
        'recovery_codes',
    ];

    /**
     * Get the user that owns the two-factor authentication.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
