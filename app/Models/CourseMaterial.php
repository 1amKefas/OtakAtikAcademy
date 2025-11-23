<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'file_path',
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
}