<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'max_points',
        'is_published'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'max_points' => 'integer',
        'is_published' => 'boolean'
    ];

    /**
     * Get the course that owns the assignment
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get submissions for this assignment
     */
    public function submissions()
    {
        // assignment submissions table uses 'assignment_id' as foreign key
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }
}