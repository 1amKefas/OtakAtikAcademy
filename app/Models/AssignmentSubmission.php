<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'file_name',
        'submission_text',
        'submitted_at',
        'grade',
        'feedback',
        'status',
        'graded_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'grade' => 'decimal:2'
    ];

    /**
     * Get the assignment that owns the submission
     */
    public function assignment()
    {
        return $this->belongsTo(CourseAssignment::class);
    }

    /**
     * Get the user that owns the submission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}