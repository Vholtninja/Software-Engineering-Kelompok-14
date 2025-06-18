<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeworkAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer',
        'explanation',
        'homework_id',
        'teacher_id',
        'attachments',
        'is_best_answer',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'is_best_answer' => 'boolean',
        ];
    }

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class);
    }
}