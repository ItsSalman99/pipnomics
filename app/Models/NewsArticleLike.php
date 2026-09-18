<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['news_article_id', 'user_id'])]
class NewsArticleLike extends Model
{
    /**
     * The news article that was liked.
     */
    public function newsArticle()
    {
        return $this->belongsTo(NewsArticle::class);
    }

    /**
     * The user who liked the article.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
