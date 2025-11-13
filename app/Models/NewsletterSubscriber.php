<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * NewsletterSubscriber Model
 *
 * Represents a newsletter subscriber.
 *
 * @property int $id
 * @property string $email
 * @property string|null $name
 * @property string $status
 * @property string|null $verification_token
 * @property \Carbon\Carbon|null $verified_at
 * @property \Carbon\Carbon|null $unsubscribed_at
 * @property string|null $ip_address
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class NewsletterSubscriber extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'status',
        'verification_token',
        'verified_at',
        'unsubscribed_at',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
