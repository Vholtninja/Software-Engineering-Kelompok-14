<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'course_id',
        'attachments',
        'deadline',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'deadline' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions()
    {
        return $this->hasMany(FinalProjectSubmission::class);
    }

    public function getSubmissionByUser($userId)
    {
        return $this->submissions()->where('user_id', $userId)->first();
    }
}