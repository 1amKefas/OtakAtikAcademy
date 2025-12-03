<?php

namespace App\Http\Controllers;

use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Get user's enrolled courses
        $enrolledCourses = CourseRegistration::where('user_id', $user->id)
            ->with(['course', 'refund'])
            ->latest()
            ->paginate(6);

        // Get refund requests
        $refundRequests = Refund::where('user_id', $user->id)
            ->with(['registration.course'])
            ->latest()
            ->limit(5)
            ->get();

        // Calculate stats
        $stats = [
            'total_courses' => CourseRegistration::where('user_id', $user->id)->count(),
            'active_courses' => CourseRegistration::where('user_id', $user->id)
                ->where('status', 'paid')
                ->count(),
            'pending_refunds' => Refund::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'approved_refunds' => Refund::where('user_id', $user->id)
                ->where('status', 'approved')
                ->sum('amount'),
        ];

        return view('student.dashboard', compact('enrolledCourses', 'refundRequests', 'stats'));
    }

    public function myCourses()
    {
        $courses = CourseRegistration::where('user_id', Auth::id())
            ->with(['course', 'refund'])
            ->latest()
            ->paginate(12);

        return view('my-courses', compact('courses'), ['enrolledCourses' => $courses]);
    }

   /**
     * Show course detail for enrolled student
     */
    public function courseDetail($registrationId)
    {
        $registration = \App\Models\CourseRegistration::where('user_id', Auth::id())
            ->where('id', $registrationId)
            ->where('status', 'paid')
            ->firstOrFail();

        // [UPDATE] Tambahkan 'forums.user' dan 'forums.replies' di eager loading
        $course = $registration->course()->with([
            'instructor',
            'modules' => function($q) { $q->orderBy('order'); },
            'modules.materials',
            'modules.quizzes',
            'forums' => function($q) { // Load 5 diskusi terbaru
                $q->orderBy('created_at', 'desc')->take(5);
            },
            'forums.user',   // Siapa yg posting
            'forums.replies' // Hitung balasan
        ])->first();

        return view('student.course-detail', compact('course', 'registration'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('student.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'education_level' => 'nullable|string|in:SMA,Diploma,Bachelor,Master,Doctorate,Other',
            'education_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Show assignment submit form
     */
    public function submitAssignmentForm($assignmentId)
    {
        $assignment = \App\Models\CourseAssignment::findOrFail($assignmentId);
        $course = $assignment->course;

        // Check if student is enrolled in this course
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'paid')
            ->firstOrFail();

        // Check if already submitted
        $existingSubmission = \App\Models\AssignmentSubmission::where('user_id', Auth::id())
            ->where('assignment_id', $assignmentId)
            ->first();

        return view('student.assignment-submit', compact('assignment', 'course', 'existingSubmission', 'registration'));
    }

    /**
     * Store assignment submission
     */
    public function submitAssignment(Request $request, $assignmentId)
    {
        $assignment = \App\Models\CourseAssignment::findOrFail($assignmentId);
        $course = $assignment->course;

        // Check if student is enrolled
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'paid')
            ->firstOrFail();

        // Validate submission
        $validated = $request->validate([
            'submission_text' => 'nullable|string|max:5000',
            'submission_file' => 'nullable|file|mimes:pdf,doc,docx,txt,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
        ]);

        // At least one of text or file must be provided
        if (empty($validated['submission_text']) && !$request->hasFile('submission_file')) {
            return back()->withErrors(['submission' => 'Silakan upload file atau tulis jawaban Anda.'])->withInput();
        }

        // Check if already submitted
        $existingSubmission = \App\Models\AssignmentSubmission::where('user_id', Auth::id())
            ->where('assignment_id', $assignmentId)
            ->first();

        if ($existingSubmission && $request->input('action') !== 'resubmit') {
            return back()->withErrors(['submission' => 'Anda sudah submit tugas ini sebelumnya. Hubungi instruktur untuk resubmit.']);
        }

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('submission_file')) {
            $file = $request->file('submission_file');
            $fileName = Auth::id() . '_' . $assignmentId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
        }

        // Create or update submission
        if ($existingSubmission && $request->input('action') === 'resubmit') {
            // Delete old file if exists
            if ($existingSubmission->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($existingSubmission->file_path);
            }

            $existingSubmission->update([
                'submission_text' => $validated['submission_text'],
                'file_path' => $filePath ?? $existingSubmission->file_path,
                'submitted_at' => now(),
            ]);
        } else {
            \App\Models\AssignmentSubmission::create([
                'user_id' => Auth::id(),
                'assignment_id' => $assignmentId,
                'submission_text' => $validated['submission_text'],
                'file_path' => $filePath,
                'submitted_at' => now(),
            ]);
        }

        return redirect()->route('student.course-detail', $registration->id)
            ->with('success', 'Tugas berhasil disubmit! Instruktur akan me-review dan memberikan nilai.');
    }

    /**
     * View submitted assignment
     */
    public function viewSubmission($assignmentId)
    {
        $assignment = \App\Models\CourseAssignment::findOrFail($assignmentId);
        $submission = \App\Models\AssignmentSubmission::where('user_id', Auth::id())
            ->where('assignment_id', $assignmentId)
            ->firstOrFail();

        return view('student.assignment-view', compact('assignment', 'submission'));
    }

    /**
     * View forum discussions for a course
     */
    public function forumIndex(Request $request, $courseId)
    {
        $registration = \App\Models\CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        $course = $registration->course;
        
        // Logic Sorting (Terbaru / Terlama)
        $sort = $request->get('sort', 'latest');
        
        $forums = \App\Models\CourseForum::where('course_id', $courseId)
            ->with(['user', 'replies']) // Eager load biar cepat
            ->when($sort == 'oldest', function($q) {
                return $q->orderBy('created_at', 'asc');
            }, function($q) {
                return $q->orderBy('created_at', 'desc');
            })
            ->paginate(10); // Pagination 10 item per halaman

        return view('student.forum-index', compact('course', 'forums', 'sort'));
    }

    public function forumDetail($courseId, $forumId)
    {
        $forum = \App\Models\CourseForum::where('id', $forumId)
            ->where('course_id', $courseId)
            ->with(['user', 'replies.user'])
            ->firstOrFail();
        
        return view('student.forum.detail', compact('forum', 'courseId'));
    }

    /**
     * Simpan Diskusi Baru (Support Gambar di Text Editor)
     */
    public function storeForum(Request $request, $courseId)
    {
        // Validasi akses
        $registration = \App\Models\CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string', // Ini isi TinyMCE (HTML)
        ]);

        \App\Models\CourseForum::create([
            'course_id' => $courseId,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Diskusi berhasil dibuat!');
    }

    public function storeForumReply($courseId, $forumId, \Illuminate\Http\Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:1|max:5000',
        ]);

        $forum = \App\Models\CourseForum::where('id', $forumId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        \App\Models\ForumReply::create([
            'forum_id' => $forumId,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return redirect()
            ->route('student.forum.detail', [$courseId, $forumId])
            ->with('success', 'Balasan berhasil ditambahkan');
    }

    public function deleteForumReply($courseId, $replyId)
    {
        $reply = \App\Models\ForumReply::findOrFail($replyId);

        // Check if user owns the reply
        if ($reply->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $forumId = $reply->forum_id;
        $reply->delete();

        return redirect()
            ->route('student.forum.detail', [$courseId, $forumId])
            ->with('success', 'Balasan berhasil dihapus');
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view('student.settings', compact('user'));
    }

    /**
     * Update user settings (notification preferences, profile visibility)
     */
    public function updateSettings(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'notify_assignment_posted' => 'boolean',
            'notify_deadline_reminder' => 'boolean',
            'notify_quiz_posted' => 'boolean',
            'notify_material_posted' => 'boolean',
            'notify_forum_reply' => 'boolean',
            'notify_submission_graded' => 'boolean',
            'profile_visibility' => 'in:public,private',
            'show_achievements' => 'boolean',
            'allow_direct_messages' => 'boolean',
        ]);

        // Update notification preferences
        $user->update([
            'notify_assignment_posted' => $request->boolean('notify_assignment_posted'),
            'notify_deadline_reminder' => $request->boolean('notify_deadline_reminder'),
            'notify_quiz_posted' => $request->boolean('notify_quiz_posted'),
            'notify_material_posted' => $request->boolean('notify_material_posted'),
            'notify_forum_reply' => $request->boolean('notify_forum_reply'),
            'notify_submission_graded' => $request->boolean('notify_submission_graded'),
            'profile_visibility' => $request->input('profile_visibility', 'private'),
            'show_achievements' => $request->boolean('show_achievements'),
            'allow_direct_messages' => $request->boolean('allow_direct_messages'),
        ]);

        return redirect()
            ->route('settings')
            ->with('success', 'Settings berhasil disimpan');
    }

    /**
     * Update user password
     */
    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => bcrypt($request->password),
        ]);

        return redirect()
            ->route('settings')
            ->with('success', 'Password berhasil diubah');
    }

    /**
     * Update user language preference
     */
    public function updateLocale(\Illuminate\Http\Request $request)
    {
        try {
            $request->validate([
                'locale' => 'required|in:en,id',
            ]);

            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login first');
            }

            $locale = $request->input('locale');
            
            // Try to save to database (will work once migration runs)
            try {
                $user->update([
                    'locale' => $locale,
                ]);
            } catch (\Exception $e) {
                // If column doesn't exist yet, just save to session
                \Log::warning('Could not update database locale: ' . $e->getMessage());
            }
            
            // Also save to session as fallback
            session(['locale' => $locale]);

            return redirect()
                ->route('settings')
                ->with('success', __('settings.language_changed'));
        } catch (\Exception $e) {
            \Log::error('Language update error: ' . $e->getMessage());
            return redirect()
                ->route('settings')
                ->with('error', 'Failed to update language preference');
        }
    }

    /**
     * Halaman Utama Belajar (Redirect ke konten terakhir atau pertama)
     */
    public function learningPage($courseId)
    {
        // 1. Validasi Akses (Harus Enroll & Paid)
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        $course = Course::with(['modules.materials', 'modules.quizzes', 'modules.assignments'])
            ->findOrFail($courseId);

        // 2. Cari Konten Terakhir yang Diakses (Logic Sequential)
        // Untuk simplifikasi awal, kita arahkan ke item pertama di modul pertama
        // Nanti bisa dikembangkan pakai tabel 'user_progress'
        
        $firstModule = $course->modules->sortBy('order')->first();
        
        if (!$firstModule) {
            return back()->with('error', 'Belum ada konten di kursus ini.');
        }

        // Prioritas urutan: Materi -> Quiz -> Assignment
        $firstContent = null;
        $type = null;

        if ($firstModule->materials->isNotEmpty()) {
            $firstContent = $firstModule->materials->sortBy('order')->first();
            $type = 'material';
        } elseif ($firstModule->quizzes->isNotEmpty()) {
            $firstContent = $firstModule->quizzes->first();
            $type = 'quiz';
        }

        if ($firstContent) {
            return redirect()->route('student.learning.content', [
                'courseId' => $courseId, 
                'type' => $type, 
                'contentId' => $firstContent->id
            ]);
        }

        return back()->with('error', 'Konten belum tersedia.');
    }

    /**
     * Menampilkan Konten Spesifik (Materi/Quiz)
     */
    public function learningContent($courseId, $type, $contentId)
    {
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        $course = Course::with(['modules' => function($q) {
            $q->orderBy('order');
        }, 'modules.materials', 'modules.quizzes'])->findOrFail($courseId);

        // Ambil konten yang diminta
        $currentContent = null;
        if ($type == 'material') {
            $currentContent = \App\Models\CourseMaterial::findOrFail($contentId);
        } elseif ($type == 'quiz') {
            $currentContent = \App\Models\Quiz::findOrFail($contentId);
        }

        // LOGIC SEQUENTIAL ACCESS (Basic)
        // Di versi simple ini, kita izinkan akses semua. 
        // Untuk strict sequential, kita perlu tabel pivot 'course_progress' untuk mencatat item yg sudah 'completed'.
        // Implementasi strict butuh migrasi tambahan. 
        // Sesuai request, kita buat UI-nya dulu, logic kuncinya nanti di tombol "Next".

        return view('student.learning.index', compact('course', 'currentContent', 'type', 'registration'));
    }

    /**
     * Tandai Materi Selesai & Lanjut
     */
    public function completeMaterial(Request $request, $courseId, $materialId)
    {
        // Logic simpan progress di sini (nanti kita tambahkan tabel progress)
        
        // Cari Next Content
        // Ini butuh algoritma pencarian "Next Item" berdasarkan urutan modul & order item
        // Sementara redirect back dulu
        return back()->with('success', 'Materi selesai!');
    }
}