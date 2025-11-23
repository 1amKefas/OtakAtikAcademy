<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseForum extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'subject',
        'message',
        'image_path',
        'video_path',
        'is_pinned'
    ];

    protected $casts = [
        'is_pinned' => 'boolean'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'forum_id');
    }
}