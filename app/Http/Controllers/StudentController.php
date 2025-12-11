<?php

namespace App\Http\Controllers;

use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\CourseProgress;
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

        // [OPTIMASI] Gunakan selectRaw untuk hitung statistik dalam 1 kali query ke DB
        $registrationStats = CourseRegistration::where('user_id', $user->id)
            ->selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 'paid' then 1 end) as active")
            ->first();

        $refundStats = Refund::where('user_id', $user->id)
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("sum(case when status = 'approved' then amount else 0 end) as approved_amount")
            ->first();

        $stats = [
            'total_courses' => $registrationStats->total ?? 0,
            'active_courses' => $registrationStats->active ?? 0,
            'pending_refunds' => $refundStats->pending ?? 0,
            'approved_refunds' => $refundStats->approved_amount ?? 0,
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
    // GANTI METHOD courseDetail() DENGAN INI:
    public function courseDetail($registrationId)
    {
        $user = Auth::user();
        
        $registration = CourseRegistration::where('user_id', $user->id)
            ->where('id', $registrationId)
            ->with(['course.modules.materials', 'course.modules.quizzes', 'course.instructor', 'courseClass.instructor'])
            ->firstOrFail();

        $course = $registration->course;

        // [FIX LOGIC] Hitung Ulang Progress Real-time (Self-Healing)
        // Hitung Total Item (Materi + Quiz)
        $totalItems = 0;
        foreach($course->modules as $module) {
            $totalItems += $module->materials->count();
            $totalItems += $module->quizzes->count();
        }

        // Hitung Item yang Selesai
        $completedCount = \App\Models\CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->count();

        // Kalkulasi Persentase Baru
        $realProgress = ($totalItems > 0) ? round(($completedCount / $totalItems) * 100) : 0;

        // Jika data di DB beda sama hitungan asli, update DB!
        if ($registration->progress !== $realProgress) {
            $registration->update(['progress' => $realProgress]);
        }

        return view('student.course-detail', compact('course', 'registration')); // Note: variable di view $userRegistration diganti jadi $registration biar konsisten
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
            ->with('user') // Load user pembuat topik (perlu)
            ->withCount('replies') // [OPTIMASI] Cuma hitung jumlah balasan, JANGAN load isinya
            ->when($sort == 'oldest', function($q) {
                return $q->orderBy('created_at', 'asc');
            }, function($q) {
                return $q->orderBy('created_at', 'desc');
            })
            ->paginate(10);

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
     * Helper: Mendapatkan urutan semua konten dalam course (Flattened List)
     */
    private function getCourseContentFlattened($course)
    {
        $content = collect();
        
        // Pastikan urutan modul benar
        $modules = $course->modules->sortBy('order');

        foreach ($modules as $module) {
            // Masukkan Materi (urutkan by order)
            foreach ($module->materials->sortBy('order') as $material) {
                $content->push([
                    'type' => 'material',
                    'id' => $material->id,
                    'title' => $material->title,
                    'module_id' => $module->id,
                    'key' => 'material_' . $material->id
                ]);
            }
            // Masukkan Quiz (urutkan by sort_order)
            foreach ($module->quizzes->sortBy('sort_order') as $quiz) {
                $content->push([
                    'type' => 'quiz',
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'module_id' => $module->id,
                    'key' => 'quiz_' . $quiz->id
                ]);
            }
        }
        return $content->values(); // Reset keys
    }

    /**
     * Halaman Utama Belajar (Redirect ke konten terakhir atau pertama)
     */
    public function learningPage($courseId)
    {
        // 1. Validasi Akses
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        $course = Course::with(['modules.materials', 'modules.quizzes'])->findOrFail($courseId);

        // 2. Ambil Semua Konten Urut
        $allContent = $this->getCourseContentFlattened($course);
        
        if ($allContent->isEmpty()) {
            return back()->with('error', 'Belum ada konten di kursus ini.');
        }

        // 3. Ambil Progress User
        $completedKeys = CourseProgress::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->get()
            ->map(fn($p) => $p->content_type . '_' . $p->content_id)
            ->toArray();

        // 4. Cari konten PERTAMA yang BELUM selesai (Sequential Logic)
        $nextItem = null;
        foreach ($allContent as $item) {
            if (!in_array($item['key'], $completedKeys)) {
                $nextItem = $item;
                break; // Ketemu yang belum selesai, langsung stop & ambil ini
            }
        }

        // Jika semua sudah selesai, arahkan ke item terakhir (atau halaman "Selesai")
        if (!$nextItem) {
            $nextItem = $allContent->last();
        }

        return redirect()->route('student.learning.content', [
            'courseId' => $courseId, 
            'type' => $nextItem['type'], 
            'contentId' => $nextItem['id']
        ]);
    }

    /**
     * Menampilkan Konten Spesifik (Materi/Quiz)
     */
    /**
     * Menampilkan Konten Spesifik dengan VALIDASI URUTAN (Anti-Lompat)
     */
    public function learningContent($courseId, $type, $contentId)
    {
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

       $course = Course::with([
            'modules' => function($q) { $q->orderBy('order', 'asc'); }, 
            'modules.materials' => function($q) { $q->orderBy('order', 'asc'); }, 
            'modules.quizzes' => function($q) { $q->orderBy('sort_order', 'asc'); }
        ])->findOrFail($courseId);

        // --- LOGIC ANTI LOMPAT ---
        $allContent = $this->getCourseContentFlattened($course);
        
        // Cari index konten yang diminta user
        $targetKey = $type . '_' . $contentId;
        $targetIndex = $allContent->search(function ($item) use ($targetKey) {
            return $item['key'] === $targetKey;
        });

        if ($targetIndex === false) abort(404);

        // Ambil Progress User
        $completedMap = CourseProgress::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->get()
            ->mapWithKeys(fn($p) => [$p->content_type . '_' . $p->content_id => true])
            ->toArray();

        // Cek apakah item SEBELUMNYA sudah selesai?
        // Loop dari awal sampai sebelum target index
        for ($i = 0; $i < $targetIndex; $i++) {
            $prevItem = $allContent[$i];
            if (!isset($completedMap[$prevItem['key']])) {
                // OOPS! Ada item sebelumnya yang belum selesai.
                // Redirect paksa user ke item tersebut.
                return redirect()->route('student.learning.content', [
                    'courseId' => $courseId,
                    'type' => $prevItem['type'],
                    'contentId' => $prevItem['id']
                ])->with('error', '⚠️ Materi terkunci! Selesaikan materi sebelumnya: "' . $prevItem['title'] . '"');
            }
        }
        // --- END LOGIC ANTI LOMPAT ---

        // Ambil konten yang diminta
        $currentContent = null;
        if ($type == 'material') {
            $currentContent = \App\Models\CourseMaterial::findOrFail($contentId);
        } elseif ($type == 'quiz') {
            $currentContent = \App\Models\Quiz::findOrFail($contentId);
        }

        // Kirim data completedMap ke view untuk menampilkan Checklist Hijau
        return view('student.learning.index', compact('course', 'currentContent', 'type', 'registration', 'completedMap'));
    }
    /**
     * Tandai Materi Selesai & Lanjut
     */
    /**
     * Tandai Materi Selesai & Hitung Progress Global
     */
    public function completeMaterial(Request $request, $courseId, $materialId)
    {
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        $material = \App\Models\CourseMaterial::findOrFail($materialId);

        // 1. Simpan/Update Progress Materi ini jadi Completed
        \App\Models\CourseProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $courseId,
                'content_id' => $materialId,
                'content_type' => 'material',
            ],
            [
                'course_module_id' => $material->course_module_id, // Pastikan kolom ini ada di tabel progress
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        // 2. HITUNG PROGRESS BARU (Gabungan Materi + Quiz)
        // Hitung Total Item (Materi + Quiz) di Course ini
        $totalMaterials = \App\Models\CourseMaterial::where('course_id', $courseId)->where('is_published', true)->count();
        $totalQuizzes = \App\Models\Quiz::where('course_id', $courseId)->where('is_published', true)->count();
        $totalItems = $totalMaterials + $totalQuizzes;

        // Hitung Item yang SUDAH Selesai (Materi + Quiz)
        $completedItems = \App\Models\CourseProgress::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('is_completed', true)
            ->count();

        // Kalkulasi Persentase
        if ($totalItems > 0) {
            $progress = min(100, round(($completedItems / $totalItems) * 100));
            
            // Update di tabel registrasi
            $registration->update(['progress' => $progress]);
            
            // Jika 100%, tandai course selesai
            if ($progress == 100 && !$registration->completed_at) {
                $registration->update(['completed_at' => now()]);
            }
        }

        // 3. Cari Next Content untuk Redirect (Opsional, kalau mau auto redirect di backend)
        // Tapi karena kita pakai tombol "Lanjut" di frontend, kita cukup return success json atau redirect back
        if($request->ajax()) {
            return response()->json(['success' => true, 'progress' => $progress]);
        }

        return back();
    }

    /**
     * Store Review & Rating
     */
    public function storeReview(Request $request, $courseId)
    {
        $user = Auth::user();
        
        // 1. Validasi Input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:500',
        ]);

        // 2. Cek apakah user berhak (Harus sudah enroll dan completed)
        $registration = \App\Models\CourseRegistration::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        // Cek Progress 100% (Opsional: Kalau mau strict harus lulus dulu)
        if ($registration->progress < 100) {
            return back()->with('error', 'Selesaikan kursus 100% dulu sebelum memberi review!');
        }

        // 3. Simpan / Update Review (Pakai updateOrCreate biar 1 user cuma 1 review per course)
        \App\Models\CourseReview::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $courseId],
            [
                'rating' => $request->rating,
                'review' => $request->review,
                'is_approved' => true // Default auto-approve
            ]
        );

        // 4. Hitung Ulang Rata-rata Rating Course (PENTING BIAR CATALOG UPDATE)
        $course = \App\Models\Course::findOrFail($courseId);
        $avg = $course->reviews()->avg('rating');
        $count = $course->reviews()->count();

        $course->update([
            'average_rating' => $avg,
            'rating_count' => $count
        ]);

        return back()->with('success', 'Terima kasih! Review Anda berhasil disimpan.');
    }
}