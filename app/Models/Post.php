<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['group_id', 'user_id', 'title', 'content'])]
class Post extends Model
{
    /**
     * The group the post belongs to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * The author of the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
