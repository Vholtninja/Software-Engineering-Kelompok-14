<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalProjectSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'final_project_id',
        'user_id',
        'notes',
        'attachments',
        'status',
        'score',
        'feedback',
        'graded_at',
        'graded_by',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'graded_at' => 'datetime',
        ];
    }

    public function finalProject()
    {
        return $this->belongsTo(FinalProject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}