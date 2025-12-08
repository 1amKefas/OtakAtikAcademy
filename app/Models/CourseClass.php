<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'instructor_id', // PJ Kelas
        'name',          // Nama Kelas (misal: TI-4A)
        'slug',
        'quota',
        'description' // Opsional (misal: Ruang 304)
    ];

    // Relasi ke Course Utama
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi ke Instructor/Asdos Kelas
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Relasi ke Mahasiswa di kelas ini
    public function students()
    {
        return $this->hasMany(CourseRegistration::class, 'course_class_id')->where('status', 'paid');
    }
}