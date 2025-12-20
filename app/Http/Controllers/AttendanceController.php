<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display attendance for a class session (Instructor view)
     */
    public function show(ClassSession $classSession)
    {
        // Check if user is instructor of this session
        if (Auth::id() !== $classSession->instructor_id && !Auth::user()->is_admin) {
            return abort(403);
        }

        // Get all registered students in this course
        $students = CourseRegistration::where('course_id', $classSession->course_id)
            ->where('status', 'paid')
            ->with(['user', 'course'])
            ->get();

        // Get attendance data for this session
        $attendances = Attendance::where('class_session_id', $classSession->id)
            ->get()
            ->keyBy('user_id');

        return view('attendance.show', compact('classSession', 'students', 'attendances'));
    }

    /**
     * Mark attendance for students
     */
    public function mark(Request $request, ClassSession $classSession)
    {
        // Check if user is instructor
        if (Auth::id() !== $classSession->instructor_id && !Auth::user()->is_admin) {
            return abort(403);
        }

        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.user_id' => 'required|integer|exists:users,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string|max:255',
        ]);

        foreach ($request->attendances as $attendance) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $attendance['user_id'],
                    'class_session_id' => $classSession->id,
                ],
                [
                    'course_id' => $classSession->course_id,
                    'status' => $attendance['status'],
                    'notes' => $attendance['notes'] ?? null,
                    'marked_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Attendance marked successfully');
    }

    /**
     * Get attendance summary for a course (Student view)
     */
    public function summary($courseId)
    {
        $user = Auth::user();

        // Check if student is enrolled in this course
        $registration = CourseRegistration::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        // Get attendance stats
        $attendances = Attendance::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->with('classSession')
            ->orderBy('created_at', 'desc')
            ->get();

        $attendancePercentage = Attendance::getAttendancePercentage($user->id, $courseId);
        $attendanceSummary = Attendance::getAttendanceSummary($user->id, $courseId);

        return view('attendance.summary', compact(
            'registration',
            'attendances',
            'attendancePercentage',
            'attendanceSummary'
        ));
    }

    /**
     * Get attendance report for admin
     */
    public function report(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return abort(403);
        }

        $courseId = $request->course_id;
        $course = \App\Models\Course::findOrFail($courseId);

        // Get all students in course
        $registrations = CourseRegistration::where('course_id', $courseId)
            ->where('status', 'paid')
            ->with(['user', 'course'])
            ->get();

        // Get attendance for each student
        $attendanceData = [];
        foreach ($registrations as $registration) {
            $attendanceData[$registration->user_id] = [
                'user' => $registration->user,
                'percentage' => Attendance::getAttendancePercentage($registration->user_id, $courseId),
                'summary' => Attendance::getAttendanceSummary($registration->user_id, $courseId),
            ];
        }

        return view('admin.attendance-report', compact('course', 'attendanceData'));
    }
}
