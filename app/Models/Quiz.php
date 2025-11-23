<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration_minutes',
        'available_from',
        'available_until',
        'is_published',
        'passing_score'
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'is_published' => 'boolean'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }

    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('points');
    }
}