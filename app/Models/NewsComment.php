<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['news_article_id', 'user_id', 'content'])]
class NewsComment extends Model
{
    /**
     * The news article the comment belongs to.
     */
    public function newsArticle()
    {
        return $this->belongsTo(NewsArticle::class);
    }

    /**
     * The author of the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
