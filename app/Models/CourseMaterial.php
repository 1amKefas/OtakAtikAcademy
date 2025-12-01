<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'course_module_id', // <--- WAJIB ADA
        'title',
        'description',
        'type',             // <--- WAJIB ADA
        'file_path',
        'external_url',     // <--- WAJIB ADA
        'file_name',
        'file_size',
        'order',
        'is_published'
    ];

    protected $casts = [
        'order' => 'integer',
        'is_published' => 'boolean'
    ];

    /**
     * Get the course that owns the material
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }
}