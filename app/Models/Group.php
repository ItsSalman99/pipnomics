<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'description', 'owner_id'])]
class Group extends Model
{
    /**
     * The owner of the group.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The members of the group.
     */
    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * The posts in this group.
     */
    public function posts()
    {
        return $this->hasMany(Post::class)->latest();
    }
}
