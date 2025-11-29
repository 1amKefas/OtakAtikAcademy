<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'description', 'order'];

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
}