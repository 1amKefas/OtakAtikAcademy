<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'title',
        'description',
        'session_type',
        'meeting_type',
        'session_date',
        'start_time',
        'end_time',
        'zoom_link',
        'location',
        'offline_notes',
        'room_number',
        'agenda',
    ];

    protected $casts = [
        'session_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relationship: ClassSession belongs to Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relationship: ClassSession belongs to Instructor (User)
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Relationship: ClassSession has many Attendances
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_session_id');
    }

    /**
     * Get attendance status for a specific user
     */
    public function getUserAttendance($userId)
    {
        return $this->attendances()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Mark attendance for multiple users
     */
    public function markAttendances($userIds, $status, $notes = null)
    {
        $attendances = [];
        foreach ($userIds as $userId) {
            $attendances[] = [
                'user_id' => $userId,
                'course_id' => $this->course_id,
                'class_session_id' => $this->id,
                'status' => $status,
                'marked_at' => now(),
                'notes' => $notes,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return Attendance::upsert($attendances, ['user_id', 'class_session_id'], ['status', 'marked_at', 'notes', 'updated_at']);
    }

    /**
     * Get session format display
     */
    public function getSessionFormatAttribute()
    {
        $types = [
            'online' => 'Daring',
            'offline' => 'Tatap Muka',
            'hybrid' => 'Hybrid (Daring + Tatap Muka)',
        ];

        return $types[$this->session_type] ?? $this->session_type;
    }

    /**
     * Get meeting type display
     */
    public function getMeetingTypeDisplayAttribute()
    {
        $types = [
            'zoom' => 'Zoom Meeting',
            'tatap_muka' => 'Tatap Muka',
            'other' => 'Lainnya',
        ];

        return $types[$this->meeting_type] ?? $this->meeting_type;
    }
}
