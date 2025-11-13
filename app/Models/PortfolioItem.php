<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PortfolioItem Model
 *
 * Represents a portfolio item showcasing completed projects.
 *
 * @property int $id
 * @property int|null $project_id
 * @property string $title
 * @property string $slug
 * @property string|null $short_description
 * @property string|null $description
 * @property string|null $client_name
 * @property string $category
 * @property array|null $technologies
 * @property string|null $featured_image
 * @property array|null $gallery_images
 * @property string|null $project_url
 * @property \Carbon\Carbon|null $completion_date
 * @property int $sort_order
 * @property bool $is_featured
 * @property bool $is_published
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PortfolioItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'title',
        'slug',
        'short_description',
        'description',
        'client_name',
        'category',
        'technologies',
        'featured_image',
        'gallery_images',
        'project_url',
        'completion_date',
        'sort_order',
        'is_featured',
        'is_published',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'project_id' => 'integer',
        'technologies' => 'array',
        'gallery_images' => 'array',
        'completion_date' => 'date',
        'sort_order' => 'integer',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project associated with the portfolio item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
