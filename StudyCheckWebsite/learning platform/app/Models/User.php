<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'bio',
        'institution',
        'level',
        'is_verified',
        'points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->belongsToMany(Course::class, 'user_progress')
                    ->withPivot(['progress_percentage', 'is_completed', 'completed_at', 'time_spent'])
                    ->withTimestamps();
    }

    public function forumThreads()
    {
        return $this->hasMany(ForumThread::class);
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function votedReplies()
    {
        return $this->belongsToMany(ForumReply::class, 'forum_reply_user_upvotes');
    }

    public function homework()
    {
        return $this->hasMany(Homework::class, 'student_id');
    }

    public function homeworkAnswers()
    {
        return $this->hasMany(HomeworkAnswer::class, 'teacher_id');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function finalProjectSubmissions()
    {
        return $this->hasMany(FinalProjectSubmission::class);
    }

    public function isTeacher()
    {
        return in_array($this->role, ['teacher', 'moderator', 'admin']);
    }

    public function isModerator()
    {
        return in_array($this->role, ['moderator', 'admin']);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('images/default-avatar.png');
    }
}