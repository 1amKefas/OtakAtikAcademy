<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'forum_id',
        'user_id',
        'message',
        'image_path'
    ];

    public function forum()
    {
        return $this->belongsTo(CourseForum::class, 'forum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}