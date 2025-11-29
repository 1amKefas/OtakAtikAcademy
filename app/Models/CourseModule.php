<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'description', 'order'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Nanti relasi ke konten (materi/quiz) akan ditambahkan di sini
}