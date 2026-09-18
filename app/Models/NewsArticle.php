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

    /**
     * Top-level comments on this article (excluding nested replies).
     */
    public function topLevelComments()
    {
        return $this->hasMany(NewsComment::class)->whereNull('parent_id')->latest();
    }

    /**
     * The likes on this news article.
     */
    public function likes()
    {
        return $this->hasMany(NewsArticleLike::class);
    }

    /**
     * Check if article is liked by a given user.
     */
    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
