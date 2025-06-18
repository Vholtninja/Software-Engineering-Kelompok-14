<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'thread_id',
        'parent_id',
        'is_best_answer',
        'upvotes',
        'downvotes',
    ];

    protected function casts(): array
    {
        return [
            'is_best_answer' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(ForumThread::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }

    public function voters()
    {
        return $this->belongsToMany(User::class, 'forum_reply_user_upvotes');
    }
}