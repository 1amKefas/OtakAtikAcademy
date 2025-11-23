<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'class_name',
        'room',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'days_of_week',
        'max_students',
        'current_students',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_of_week' => 'array',
        'is_active' => 'boolean'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_class_students')
                    ->withTimestamps();
    }

    public function hasAvailableSlots()
    {
        return $this->current_students < $this->max_students;
    }
}