<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'type',
        'options',
        'correct_answer',
        'points',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}