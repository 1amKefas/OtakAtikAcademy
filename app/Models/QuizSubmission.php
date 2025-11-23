<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'answers',
        'score',
        'total_points',
        'correct_answers',
        'started_at',
        'submitted_at',
        'is_passed',
        'graded_at',
        'graded_by'
    ];

    protected $casts = [
        'answers' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'is_passed' => 'boolean'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPercentageAttribute()
    {
        if ($this->total_points == 0) return 0;
        return ($this->score / $this->total_points) * 100;
    }
}