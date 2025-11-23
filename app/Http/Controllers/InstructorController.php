<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseAssignment;
use App\Models\CourseRegistration;
use App\Models\AssignmentSubmission;
use App\Models\CourseForum;
use App\Models\ForumReply;
use Illuminate\Support\Facades\Storage;
use App\Events\AssignmentPosted;
use App\Events\AssignmentDeadlineChanged;
use App\Events\MaterialPosted;

class InstructorController extends Controller
{
    /**
     * Show instructor dashboard
     */
    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $instructor = Auth::user();
        $taughtCourses = $instructor->taughtCourses()->withCount(['registrations' => function($query) {
            $query->where('status', 'paid');
        }])->get();

        $totalStudents = 0;
        $totalAssignments = 0;

        foreach ($taughtCourses as $course) {
            $totalStudents += $course->registrations_count;
            $totalAssignments += $course->assignments()->count();
        }

        $stats = [
            'total_courses' => $taughtCourses->count(),
            'total_students' => $totalStudents,
            'total_assignments' => $totalAssignments,
            'active_courses' => $taughtCourses->where('is_active', true)->count(),
        ];

        $recentRegistrations = CourseRegistration::whereIn('course_id', $taughtCourses->pluck('id'))
            ->where('status', 'paid')
            ->with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        return view('instructor.dashboard', compact('stats', 'taughtCourses', 'recentRegistrations'));
    }

    /**
     * Show instructor's courses
     */
    public function courses()
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $instructor = Auth::user();
        $courses = $instructor->taughtCourses()->withCount(['registrations' => function($query) {
            $query->where('status', 'paid');
        }])->latest()->get();

        return view('instructor.courses', compact('courses'));
    }

    /**
     * Show specific course details for instructor
     */
    public function showCourse($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::with(['materials', 'assignments', 'quizzes', 'forums.user', 'registrations.user'])->findOrFail($id);
        
        // Check if instructor owns this course
        if ($course->instructor_id != Auth::id()) {
            abort(403, 'Not your course. Your ID: ' . Auth::id() . ', Course Instructor ID: ' . $course->instructor_id);
        }

        $students = $course->registrations()->where('status', 'paid')->with('user')->get();
        $materials = $course->materials()->orderBy('order')->get();
        $assignments = $course->assignments()->latest()->get();
        $quizzes = $course->quizzes()->latest()->get();
        $forums = $course->forums()->with('user')->latest()->get();

        return view('instructor.course-detail', compact('course', 'students', 'materials', 'assignments', 'quizzes', 'forums'));
    }

    /**
     * Show course students
     */
    public function courseStudents($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($id);
        
        // Check if instructor owns this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $students = $course->registrations()->where('status', 'paid')->with('user')->get();

        return view('instructor.course-students', compact('course', 'students'));
    }

    /**
     * Store course material
     */
        public function storeMaterial(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($id);
        
        // TEMPORARY BYPASS - Comment out authorization
        // Check if instructor owns this course
        // if ($course->instructor_id !== Auth::id()) {
        //     abort(403, 'Unauthorized action.');
        // }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('course_materials', $fileName, 'public');

            $material = CourseMaterial::create([
                'course_id' => $course->id,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'order' => $course->materials()->count() + 1,
                'is_published' => true,
            ]);

            // Dispatch event to notify students
            MaterialPosted::dispatch($material);

            return back()->with('success', 'Material berhasil diupload!');
        }

        return back()->withErrors(['error' => 'Gagal mengupload file.']);
    }

    /**
     * Delete course material
     */
    public function deleteMaterial($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $material = CourseMaterial::with('course')->findOrFail($id);
        
        // TEMPORARY BYPASS - Comment out authorization
        // Check if instructor owns this course
        // if ($material->course->instructor_id !== Auth::id()) {
        //     abort(403, 'Unauthorized action.');
        // }

        // Delete file from storage
        Storage::disk('public')->delete($material->file_path);

        $material->delete();

        return back()->with('success', 'Material berhasil dihapus!');
    }

    /**
     * Store course assignment
     */
    public function storeAssignment(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($id);
        
        // TEMPORARY BYPASS - Comment out authorization
        // Check if instructor owns this course
        // if ($course->instructor_id !== Auth::id()) {
        //     abort(403, 'Unauthorized action.');
        // }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'due_date' => 'required|date|after:now',
            'max_points' => 'nullable|integer|min:1'
        ]);

        $assignment = CourseAssignment::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            // set max points to provided value or sensible default (100)
            'max_points' => $request->input('max_points', 100),
            'is_published' => true,
        ]);

        // Dispatch event to notify students
        AssignmentPosted::dispatch($assignment);

        return back()->with('success', 'Assignment berhasil dibuat!');
    }

    /**
     * Update course assignment
     */
    public function updateAssignment(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $assignment = CourseAssignment::with('course')->findOrFail($id);
        
        // Check if instructor owns this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'due_date' => 'required|date|after:now',
            'is_published' => 'required|boolean',
        ]);

        // Store old deadline before updating
        $oldDeadline = $assignment->due_date->copy();
        $newDeadline = \Carbon\Carbon::parse($request->due_date);

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'is_published' => $request->is_published,
        ]);

        // Dispatch event if deadline changed
        if ($oldDeadline->notEqualTo($newDeadline)) {
            AssignmentDeadlineChanged::dispatch($assignment, $oldDeadline, $newDeadline);
        }

        return back()->with('success', 'Assignment berhasil diupdate!');
    }

    /**
     * Delete course assignment
     */
    public function deleteAssignment($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $assignment = CourseAssignment::with('course')->findOrFail($id);
        
        // Check if instructor owns this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->delete();

        return back()->with('success', 'Assignment berhasil dihapus!');
    }

    /**
     * Update student progress
     */
    public function updateStudentProgress(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $registration = CourseRegistration::with('course')->findOrFail($id);
        
        // Check if instructor owns this course
        if ($registration->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'progress' => 'required|numeric|min:0|max:100'
        ]);

        $registration->update([
            'progress' => $request->progress
        ]);

        if ($request->progress >= 100) {
            $registration->update([
                'completed_at' => now()
            ]);
        }

        return back()->with('success', 'Progress siswa berhasil diupdate!');
    }

    /**
     * Show assignment submissions
     */
    public function assignmentSubmissions($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $assignment = CourseAssignment::with(['course', 'submissions.user'])->findOrFail($id);
        
        // Check if instructor owns this course
        if ($assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $course = $assignment->course;
        // load submissions with user relation
        $submissions = $assignment->submissions()->with('user')->get();

        return view('instructor.assignment-submissions', compact('assignment', 'course', 'submissions'));
    }

    /**
     * Grade assignment submission
     */
    public function gradeSubmission(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $submission = AssignmentSubmission::with(['assignment.course'])->findOrFail($id);
        
        // Check if instructor owns this course
        if ($submission->assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $submission->assignment->max_points,
            'feedback' => 'nullable|string|max:1000'
        ]);

        $submission->update([
            'grade' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'status' => 'graded',
            'graded_at' => now()
        ]);

        return back()->with('success', 'Submission berhasil dinilai!');
    }

    /**
     * Show submission detail for grading UI
     */
    public function submissionDetail($assignmentId, $submissionId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $submission = AssignmentSubmission::with(['assignment.course', 'user'])->findOrFail($submissionId);

        if ($submission->assignment->id != $assignmentId || $submission->assignment->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.submission-grade', [
            'submission' => $submission,
        ]);
    }

    /**
     * Get assignment data as JSON (for AJAX requests)
     */
    public function getAssignmentJson($id)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $assignment = CourseAssignment::with('course')->findOrFail($id);
            
            // Check if instructor owns this course
            if ($assignment->course->instructor_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            return response()->json([
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'instructions' => $assignment->instructions,
                'due_date' => $assignment->due_date->format('Y-m-d\TH:i'),
                'is_published' => $assignment->is_published,
                'course_id' => $assignment->course_id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Assignment not found'], 404);
        }
    }

    /**
     * Store forum topic (Instructor posts)
     */
    public function storeForum(Request $request, $courseId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        
        // Check if instructor owns this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('forum-attachments', 'public');
        }

        CourseForum::create([
            'course_id' => $course->id,
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'image_path' => $attachmentPath, // Using existing field
        ]);

        return back()->with('success', 'Forum topic posted successfully!');
    }

    /**
     * Show forum topic detail
     */
    public function showForum($courseId, $forumId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $forum = CourseForum::with(['user', 'replies.user'])->findOrFail($forumId);

        // Check if instructor owns this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.forum.show', compact('course', 'forum'));
    }

    /**
     * Delete forum topic
     */
    public function deleteForum($courseId, $forumId)
    {
        if (!Auth::check() || !Auth::user()->is_instructor) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $forum = CourseForum::findOrFail($forumId);

        // Check if instructor owns this course
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $forum->delete();

        return back()->with('success', 'Forum topic deleted successfully!');
    }

    /**
     * Store reply for forum topic
     */
    public function storeForumReply(Request $request, $courseId, $forumId)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $forum = CourseForum::findOrFail($forumId);

        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        ForumReply::create([
            'forum_id' => $forum->id,
            'user_id' => Auth::id(),
            'message' => $data['content'],
        ]);

        return back()->with('success', 'Balasan berhasil ditambahkan!');
    }

    /**
     * Delete reply from forum topic
     */
    public function deleteForumReply($courseId, $forumId, $replyId)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access.');
        }

        $course = Course::findOrFail($courseId);
        $forum = CourseForum::findOrFail($forumId);
        $reply = ForumReply::findOrFail($replyId);

        if ($course->instructor_id !== Auth::id() || $forum->id !== $reply->forum_id) {
            abort(403, 'Unauthorized action.');
        }

        $reply->delete();

        return back()->with('success', 'Balasan berhasil dihapus!');
    }
}