<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Testimonial Model
 *
 * Represents a client testimonial for a company or project.
 *
 * @property int $id
 * @property int|null $company_id
 * @property int|null $project_id
 * @property string $client_name
 * @property string|null $client_position
 * @property string|null $client_company
 * @property string|null $client_image
 * @property string $testimonial
 * @property int $rating
 * @property bool $is_featured
 * @property bool $is_published
 * @property int $sort_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Testimonial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'project_id',
        'client_name',
        'client_position',
        'client_company',
        'client_image',
        'testimonial',
        'rating',
        'is_featured',
        'is_published',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'company_id' => 'integer',
        'project_id' => 'integer',
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the testimonial.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the project associated with the testimonial.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
