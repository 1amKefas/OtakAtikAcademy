<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassSessionController extends Controller
{
    /**
     * Display class sessions for a course
     */
    public function index($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check if user is instructor or admin
        if (Auth::id() !== $course->instructor_id && !Auth::user()->is_admin) {
            return abort(403);
        }

        $sessions = ClassSession::where('course_id', $courseId)
            ->orderBy('session_date', 'desc')
            ->paginate(15);

        return view('class-sessions.index', compact('course', 'sessions'));
    }

    /**
     * Create new class session form
     */
    public function create($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check authorization
        if (Auth::id() !== $course->instructor_id && !Auth::user()->is_admin) {
            return abort(403);
        }

        return view('class-sessions.create', compact('course'));
    }

    /**
     * Store new class session
     */
    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check authorization
        if (Auth::id() !== $course->instructor_id && !Auth::user()->is_admin) {
            return abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'session_type' => 'required|in:online,offline,hybrid',
            'meeting_type' => 'required|in:zoom,tatap_muka,other',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'zoom_link' => 'nullable|url|required_if:meeting_type,zoom',
            'location' => 'nullable|string|max:255|required_if:session_type,offline',
            'offline_notes' => 'nullable|string|max:1000',
            'room_number' => 'nullable|string|max:50',
            'agenda' => 'nullable|string|max:1000',
        ]);

        $validated['course_id'] = $courseId;
        $validated['instructor_id'] = Auth::id();

        // Combine dates with times
        $validated['start_time'] = $request->session_date . ' ' . $request->start_time;
        $validated['end_time'] = $request->session_date . ' ' . $request->end_time;

        ClassSession::create($validated);

        return redirect()->route('class-sessions.index', $courseId)
            ->with('success', 'Class session created successfully');
    }

    /**
     * Edit class session form
     */
    public function edit(ClassSession $classSession)
    {
        // Check authorization
        if (Auth::id() !== $classSession->instructor_id && !Auth::user()->is_admin) {
            return abort(403);
        }

        return view('class-sessions.edit', compact('classSession'));
    }

    /**
     * Update class session
     */
    public function update(Request $request, ClassSession $classSession)
    {
        // Check authorization
        if (Auth::id() !== $classSession->instructor_id && !Auth::user()->is_admin) {
            return abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'session_type' => 'required|in:online,offline,hybrid',
            'meeting_type' => 'required|in:zoom,tatap_muka,other',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'zoom_link' => 'nullable|url|required_if:meeting_type,zoom',
            'location' => 'nullable|string|max:255|required_if:session_type,offline',
            'offline_notes' => 'nullable|string|max:1000',
            'room_number' => 'nullable|string|max:50',
            'agenda' => 'nullable|string|max:1000',
        ]);

        // Combine dates with times
        $validated['start_time'] = $request->session_date . ' ' . $request->start_time;
        $validated['end_time'] = $request->session_date . ' ' . $request->end_time;

        $classSession->update($validated);

        return redirect()->route('class-sessions.index', $classSession->course_id)
            ->with('success', 'Class session updated successfully');
    }

    /**
     * Delete class session
     */
    public function destroy(ClassSession $classSession)
    {
        // Check authorization
        if (Auth::id() !== $classSession->instructor_id && !Auth::user()->is_admin) {
            return abort(403);
        }

        $courseId = $classSession->course_id;
        $classSession->delete();

        return redirect()->route('class-sessions.index', $courseId)
            ->with('success', 'Class session deleted');
    }

    /**
     * Show class session details with student list
     */
    public function show(ClassSession $classSession)
    {
        // Check authorization
        if (Auth::id() !== $classSession->instructor_id && !Auth::user()->is_admin && 
            Auth::id() !== $classSession->course->instructor_id) {
            return abort(403);
        }

        // Get all students enrolled in course
        $students = \App\Models\CourseRegistration::where('course_id', $classSession->course_id)
            ->where('status', 'paid')
            ->with('user')
            ->get();

        // Get attendance for this session
        $attendances = Attendance::where('class_session_id', $classSession->id)
            ->get()
            ->keyBy('user_id');

        return view('class-sessions.show', compact('classSession', 'students', 'attendances'));
    }
}
