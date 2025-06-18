<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'video_url',
        'attachments',
        'course_id',
        'order',
        'is_published',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function isCompletedByUser($userId)
    {
        return $this->userProgress()
                    ->where('user_id', $userId)
                    ->where('is_completed', true)
                    ->exists();
    }
}
