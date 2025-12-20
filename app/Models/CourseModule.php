<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'description', 'order',
        'module_type', 'zoom_link', 'meeting_type', 'session_date',
        'start_time', 'end_time', 'location', 'room_number', 'offline_notes'
    ];

    /**
     * Relasi ke Course induk
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relasi ke Materi Bacaan/Video (CourseMaterial)
     */
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class)->orderBy('order'); // Asumsi ada kolom order, atau hapus orderBy jika error
    }

    /**
     * Relasi ke Tugas (CourseAssignment)
     */
    public function assignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }

    /**
     * Relasi ke Kuis (Quiz)
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Relasi ke Pemberitahuan/Announcement
     */
    public function announcements()
    {
        return $this->hasMany(CourseAnnouncement::class, 'module_id');
    }
}