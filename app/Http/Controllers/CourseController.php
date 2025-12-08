<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class CourseController extends Controller
{
    /**
     * Show course registration page - HANYA COURSE AKTIF dengan filter category & SEARCH
     */
    public function showCourse(Request $request)
    {
        // Eager load instructor & assistants biar gak N+1 di view
        $query = Course::where('is_active', true)->with(['instructor', 'assistants']);
        
        // Filter Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Filter Category
        if ($request->has('category') && $request->category) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        // [OPTIMASI] Gunakan PAGINATE (12 per halaman)
        $courses = $query->paginate(12)->withQueryString();
        
        return view('course', compact('courses'));
    }

    /**
     * Show course dashboard - HANYA COURSE AKTIF
     */
    public function dashboard()
    {
        $user = Auth::user();
        $enrolledCourses = $user->enrolledCourses;
        
        // Calculate overall progress
        $totalProgress = 0;
        $courseCount = $enrolledCourses->count();
        
        if ($courseCount > 0) {
            foreach ($enrolledCourses as $registration) {
                $totalProgress += $registration->progress;
            }
            $overallProgress = $totalProgress / $courseCount;
        } else {
            $overallProgress = 0;
        }

        return view('course-dashboard', compact('user', 'enrolledCourses', 'overallProgress'));
    }

    /**
     * Show user's enrolled courses
     */
    public function myCourses()
    {
        $user = Auth::user();
        
        // Hanya ambil course dengan status 'paid' (yang sudah disetujui)
        $enrolledCourses = $user->courseRegistrations()
            ->where('status', 'paid')
            ->with('course.instructor')
            ->get();
        
        return view('my-courses', compact('enrolledCourses'));
    }

    /**
     * Show purchase history
     */
    public function purchaseHistory()
    {
        $user = Auth::user();
        $allRegistrations = CourseRegistration::where('user_id', $user->id)
            ->with('course')
            ->latest()
            ->get();

        return view('purchase-history', compact('allRegistrations'));
    }

    /**
     * Handle course registration dengan coupon instructor 100%
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'nama_lengkap' => 'required|string|max:255',
            'ttl' => 'required|string|max:255',
            'tempat_tinggal' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'discount_code' => 'nullable|string|max:50',
        ]);

        // --- [UPDATE] CEK DUPLIKASI COURSE ---
        $existingRegistration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $validated['course_id'])
            ->where('status', 'paid') // Cek status Paid
            ->exists();

        if ($existingRegistration) {
            return back()->withErrors(['error' => 'Anda sudah terdaftar di course ini dan tidak dapat membelinya lagi.']);
        }

        $course = Course::findOrFail($validated['course_id']);
        
        // Check if course has available slots
        if (!$course->hasAvailableSlots()) {
            return back()->withErrors(['error' => 'Maaf, kuota untuk kursus ini sudah penuh.']);
        }

        // Calculate price with discount
        $price = $course->price;
        $finalPrice = $course->price;
        $discountCode = null;
        $discountPercent = 0;

        if (!empty($validated['discount_code'])) {
            // Check for instructor coupon (100% discount) - HANYA untuk instructor
            if ($validated['discount_code'] === 'INSTRUCTOR100' && Auth::user()->is_instructor) {
                $finalPrice = 0; // 100% discount
                $discountPercent = 100;
                $discountCode = 'INSTRUCTOR100';
                $status = 'paid'; // Auto-approve untuk instructor
            }
            // Check for regular promo code (10% discount) - untuk semua user
            elseif ($validated['discount_code'] === 'PROMOPNJ') {
                $finalPrice = $price * 0.9; // 10% discount
                $discountPercent = 10;
                $discountCode = 'PROMOPNJ';
                $status = 'pending';
            }
            // Jika kode tidak valid, abaikan
            else {
                $discountCode = null;
                $status = 'pending';
            }
        } else {
            $status = 'pending';
        }

        // Jika bukan instructor tapi mencoba pakai INSTRUCTOR100, tolak
        if ($validated['discount_code'] === 'INSTRUCTOR100' && !Auth::user()->is_instructor) {
            return back()->withErrors(['discount_code' => 'Kode INSTRUCTOR100 hanya untuk instruktur.'])->withInput();
        }

        // Create registration
        $registration = CourseRegistration::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'nama_lengkap' => $validated['nama_lengkap'],
            'ttl' => $validated['ttl'],
            'tempat_tinggal' => $validated['tempat_tinggal'],
            'gender' => $validated['gender'],
            'price' => $price,
            'final_price' => $finalPrice,
            'discount_code' => $discountCode,
            'status' => $status,
            'progress' => 0,
            'enrolled_at' => $status === 'paid' ? now() : null,
        ]);

        // Update course enrollment jika auto-approved
        if ($status === 'paid') {
            $course->increment('current_enrollment');
        }

        $successMessage = 'Pendaftaran course berhasil! ';
        if ($status === 'paid') {
            $successMessage .= 'Status: APPROVED (Gratis untuk instruktur)';
        } else {
            $successMessage .= 'Menunggu persetujuan admin.';
        }

        return redirect()->route('purchase.history')->with('success', $successMessage);
    }

    /**
 * Show course details - HANYA COURSE AKTIF untuk STUDENT
 */
   public function show($id)
    {
        // Jika user adalah INSTRUCTOR, redirect ke dashboard instructor
        if (Auth::check() && Auth::user()->is_instructor) {
            return redirect()->route('instructor.courses.show', $id);
        }

        // [UPDATE] Load Modules beserta isinya (Materials, Assignments, Quizzes)
       $course = Course::where('is_active', true)
            ->with([
                'instructor', 
                'modules' => function($query) {
                    $query->orderBy('order', 'asc'); // [UPDATE] Sorting Modul
                },
                'modules.materials' => function($q) {
                    $q->orderBy('order', 'asc'); // [UPDATE] Sorting Materi
                },
                'modules.assignments', 
                'modules.quizzes' => function($q) {
                    $q->orderBy('sort_order', 'asc'); // [UPDATE] Sorting Quiz
                }
            ])
            ->findOrFail($id);
        
        // ... (Logika cek enrollment tetap sama seperti sebelumnya) ...
        $isEnrolled = false;
        $userRegistration = null;
        
        if (Auth::check()) {
            $userRegistration = CourseRegistration::where('user_id', Auth::id())
                ->where('course_id', $id)
                ->where('status', 'paid')
                ->first();
            $isEnrolled = !is_null($userRegistration);
            
            if ($isEnrolled && $userRegistration) {
                return redirect()->route('student.course-detail', $userRegistration->id);
            }
        }

        return view('course-detail', compact('course', 'isEnrolled', 'userRegistration'));
    }
     /**
     * Delete course registration
     */
    public function destroy($id)
    {
        $course = CourseRegistration::findOrFail($id);
        
        // Check if user is authorized to delete this course registration
        if (Auth::id() !== $course->user_id && !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $course->delete();

        return redirect()->route('purchase.history')->with('success', 'Pendaftaran course berhasil dihapus!');
    }

    /**
     * Update course progress (for instructors)
     */
    public function updateProgress(Request $request, $id)
    {
        $registration = CourseRegistration::findOrFail($id);
        
        // Check if user is instructor of this course
        if (Auth::user()->is_instructor && $registration->course->instructor_id !== Auth::id()) {
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

        return back()->with('success', 'Progress berhasil diupdate!');
    }

    /**
     * AJAX Search Suggestions
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $courses = Course::where('is_active', true)
            ->where('title', 'ILIKE', "%{$query}%") // ILIKE untuk PostgreSQL (Case Insensitive)
            ->select('id', 'title', 'image_url', 'type') // Ambil kolom yg perlu aja biar ringan
            ->limit(5) // Batasi 5 hasil aja biar rapi
            ->get();

        return response()->json($courses);
    }
}