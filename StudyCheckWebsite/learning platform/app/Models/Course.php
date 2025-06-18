<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'category',
        'level',
        'thumbnail',
        'instructor_id',
        'is_active',
        'price',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    public function instructor()
    {
        return $this->belongsTo(User::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'user_progress')
                    ->withPivot(['progress_percentage', 'is_completed', 'completed_at', 'time_spent'])
                    ->withTimestamps();
    }

    public function homework()
    {
        return $this->hasMany(Homework::class);
    }

    public function finalProjects()
    {
        return $this->hasMany(FinalProject::class);
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : asset('images/default-course.png');
    }

    public function getFormattedPriceAttribute()
    {
        return $this->price > 0 ? 'Rp ' . number_format($this->price, 0, ',', '.') : 'Gratis';
    }
}