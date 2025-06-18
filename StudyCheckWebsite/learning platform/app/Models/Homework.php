<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;

    protected $table = 'homework';

    protected $fillable = [
        'title',
        'description',
        'question',
        'student_id',
        'course_id',
        'subject',
        'difficulty',
        'attachments',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'due_date' => 'datetime',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function answers()
    {
        return $this->hasMany(HomeworkAnswer::class);
    }

    public function bestAnswer()
    {
        return $this->hasOne(HomeworkAnswer::class)->where('is_best_answer', true);
    }
}