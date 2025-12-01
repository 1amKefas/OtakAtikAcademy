<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRating extends Model
{
    protected $table = 'course_ratings';
    
    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'review'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
