<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progress'; // Pastikan nama tabel sesuai migration

    protected $fillable = [
        'user_id',
        'course_id',
        'course_module_id',
        'content_id',
        'content_type', // 'material' atau 'quiz'
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi ke Module
    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }
    
    // Helper untuk mengambil konten aslinya (Materi atau Quiz)
    public function content()
    {
        if ($this->content_type === 'material') {
            return $this->belongsTo(CourseMaterial::class, 'content_id');
        } elseif ($this->content_type === 'quiz') {
            return $this->belongsTo(Quiz::class, 'content_id');
        }
        
        return null;
    }
}