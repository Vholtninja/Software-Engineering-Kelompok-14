<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'answers',
        'score',
        'total_questions',
        'percentage',
        'points_earned',
        'passed',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
    
    public function getPercentageAttribute()
    {
        if ($this->total_questions > 0) {
            return round(($this->score / $this->total_questions) * 100, 2);
        }
        return 0;
    }
}