<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForumThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'slug',
        'user_id',
        'category_id',
        'is_locked',
        'views',
        'replies_count',
    ];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($thread) {
            if (empty($thread->slug)) {
                $thread->slug = Str::slug($thread->title) . '-' . time();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    public function bestAnswer()
    {
        return $this->hasOne(ForumReply::class, 'thread_id')->where('is_best_answer', true);
    }
}