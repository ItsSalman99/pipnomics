<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'image', 'description', 'url', 'source', 'is_analysis', 'published_at'])]
class NewsArticle extends Model
{
    protected $casts = [
        'is_analysis' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['summary'];

    /**
     * Get summary alias for description.
     */
    public function getSummaryAttribute(): ?string
    {
        return $this->description;
    }

    /**
     * The comments on this news article.
     */
    public function comments()
    {
        return $this->hasMany(NewsComment::class)->latest();
    }
}
