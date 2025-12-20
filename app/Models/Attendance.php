<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'class_session_id',
        'status',
        'marked_at',
        'notes',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    /**
     * Relationship: Attendance belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Attendance belongs to Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relationship: Attendance belongs to ClassSession
     */
    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    /**
     * Get attendance percentage for a user in a course
     */
    public static function getAttendancePercentage($userId, $courseId)
    {
        $totalSessions = ClassSession::where('course_id', $courseId)->count();
        if ($totalSessions === 0) return 0;

        $presentCount = self::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($presentCount / $totalSessions) * 100, 2);
    }

    /**
     * Get attendance count by status
     */
    public static function getAttendanceSummary($userId, $courseId)
    {
        return self::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
