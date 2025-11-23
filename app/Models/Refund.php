<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_registration_id',
        'user_id',
        'amount',
        'reason',
        'status',
        'admin_notes',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    public function registration()
    {
        return $this->belongsTo(CourseRegistration::class, 'course_registration_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}