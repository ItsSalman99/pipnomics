<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['news_article_id', 'user_id', 'parent_id', 'content'])]
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

    /**
     * The parent comment if this is a reply.
     */
    public function parent()
    {
        return $this->belongsTo(NewsComment::class, 'parent_id');
    }

    /**
     * The replies to this comment.
     */
    public function replies()
    {
        return $this->hasMany(NewsComment::class, 'parent_id')->with('user:id,name,is_online')->oldest();
    }
}
