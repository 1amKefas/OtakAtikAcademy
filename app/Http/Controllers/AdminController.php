<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\CourseModule;    
use App\Models\User;
use App\Models\CourseRegistration;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; 

class AdminController extends Controller
{
    /**
     * Show admin dashboard with comprehensive stats
     */
    public function dashboard()
    {
        // User stats
        $userStats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'instructor_users' => User::where('is_instructor', true)->count(),
            'regular_users' => User::where('is_admin', false)->where('is_instructor', false)->count(),
            'active_this_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Course stats
        $courseStats = [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'inactive_courses' => Course::where('is_active', false)->count(),
        ];

        // Registration & Revenue stats
        $stats = [
            'total_users' => $userStats['total_users'],
            'admin_users' => $userStats['admin_users'],
            'instructor_users' => $userStats['instructor_users'],
            'regular_users' => $userStats['regular_users'],
            'active_this_month' => $userStats['active_this_month'],
            'total_courses' => $courseStats['total_courses'],
            'active_courses' => $courseStats['active_courses'],
            'inactive_courses' => $courseStats['inactive_courses'],
            'total_registrations' => CourseRegistration::count(),
            'pending_registrations' => CourseRegistration::where('status', 'pending')->count(),
            'paid_registrations' => CourseRegistration::where('status', 'paid')->count(),
            'cancelled_registrations' => CourseRegistration::where('status', 'cancelled')->count(),
            'total_revenue' => CourseRegistration::where('status', 'paid')->sum('final_price'),
            'monthly_revenue' => CourseRegistration::where('status', 'paid')
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('final_price'),
            'pending_refunds' => Refund::where('status', 'pending')->count(),
            'pending_refund_amount' => Refund::where('status', 'pending')->sum('amount'),
            'total_refunded' => Refund::where('status', 'approved')->sum('amount'),
        ];

        // Recent registrations
        $recentRegistrations = CourseRegistration::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        // Recent refunds
        $recentRefunds = Refund::with(['user', 'registration.course'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Popular courses
        $popularCourses = Course::withCount(['registrations' => function($query) {
                $query->where('status', 'paid');
            }])
            ->orderBy('registrations_count', 'desc')
            ->take(5)
            ->get();

        // Revenue chart data
        $revenueData = $this->getRevenueChartData();

        return view('admin.dashboard', compact('stats', 'recentRegistrations', 'recentRefunds', 'popularCourses', 'revenueData'));
    }

    /**
     * Show users management page
     */
    public function users()
    {
        $users = User::withCount('courseRegistrations')->latest()->paginate(10);
        
        $userStats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'instructor_users' => User::where('is_instructor', true)->count(),
            'regular_users' => User::where('is_admin', false)->where('is_instructor', false)->count(),
            'active_this_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Sample data for charts
        $ageDistribution = [
            ['range' => '18-24', 'count' => 15, 'color' => '#3B82F6'],
            ['range' => '25-34', 'count' => 25, 'color' => '#10B981'],
            ['range' => '35-44', 'count' => 18, 'color' => '#F59E0B'],
            ['range' => '45-54', 'count' => 12, 'color' => '#EF4444'],
            ['range' => '55+', 'count' => 8, 'color' => '#8B5CF6'],
        ];

        $educationDistribution = [
            ['level' => 'High School', 'count' => 20, 'color' => '#3B82F6'],
            ['level' => 'Bachelor', 'count' => 35, 'color' => '#10B981'],
            ['level' => 'Master', 'count' => 15, 'color' => '#F59E0B'],
            ['level' => 'Doctorate', 'count' => 5, 'color' => '#EF4444'],
            ['level' => 'Other', 'count' => 3, 'color' => '#8B5CF6'],
        ];

        $locationDistribution = [
            ['location' => 'Jakarta', 'count' => 25, 'color' => '#3B82F6'],
            ['location' => 'Bandung', 'count' => 15, 'color' => '#10B981'],
            ['location' => 'Surabaya', 'count' => 12, 'color' => '#F59E0B'],
            ['location' => 'Bali', 'count' => 8, 'color' => '#EF4444'],
            ['location' => 'Lainnya', 'count' => 18, 'color' => '#8B5CF6'],
        ];

        return view('admin.users', compact('users', 'userStats', 'ageDistribution', 'educationDistribution', 'locationDistribution'));
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:user,admin,instructor'
        ]);

        $user->update([
            'is_admin' => $request->role === 'admin',
            'is_instructor' => $request->role === 'instructor'
        ]);

        return back()->with('success', 'User role updated successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        // Soft delete registrations
        CourseRegistration::where('user_id', $id)->delete();

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }

    /**
     * Show course registrations management page
     */
    public function courses()
    {
        $courses = CourseRegistration::with(['user', 'course.instructor'])
            ->latest()
            ->paginate(10);

        return view('admin.courses', compact('courses'));
    }

    /**
     * Update course registration status
     */
    public function updateCourseStatus(Request $request, $id)
    {
        $registration = CourseRegistration::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled'
        ]);

        $registration->update([
            'status' => $request->status
        ]);

        // Auto-approve jika menggunakan coupon instructor
        if ($registration->discount_code === 'INSTRUCTOR100' && $request->status === 'pending') {
            $registration->update(['status' => 'paid']);
        }

        // Update course enrollment count if status is paid
        if ($request->status === 'paid' || $registration->discount_code === 'INSTRUCTOR100') {
            $course = $registration->course;
            $course->update([
                'current_enrollment' => $course->registrations()->where('status', 'paid')->count()
            ]);
        }

        return back()->with('success', 'Course status updated successfully!');
    }

    /**
     * Export courses to CSV
     */
    public function exportCourses()
    {
        $registrations = CourseRegistration::with(['user', 'course'])->get();
        
        $fileName = 'course_registrations_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'User Name', 'User Email', 'Course Title', 'Price', 'Final Price', 'Status', 'Registration Date']);

        foreach ($registrations as $registration) {
            fputcsv($handle, [
                $registration->id,
                $registration->user->name,
                $registration->user->email,
                $registration->course->title,
                $registration->price,
                $registration->final_price,
                $registration->status,
                $registration->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($handle);
        
        return response()->streamDownload(function() use ($handle) {
            //
        }, $fileName, $headers);
    }

    /**
     * Show registrations management page
     */
    public function registrations()
    {
        $registrations = CourseRegistration::with(['user', 'course'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => CourseRegistration::count(),
            'paid' => CourseRegistration::where('status', 'paid')->count(),
            'pending' => CourseRegistration::where('status', 'pending')->count(),
            'cancelled' => CourseRegistration::where('status', 'cancelled')->count(),
        ];

        return view('admin.registrations', compact('registrations', 'stats'));
    }

    /**
     * Show financial analytics page
     */
    /**
     * Show financial analytics page (Optimized)
     */
    public function financial()
    {
        // 1. Financial Stats (Pakai Aggregate DB biar cepat)
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        $totalRevenue = CourseRegistration::where('status', 'paid')->sum('final_price');
        
        $currentMonthRevenue = CourseRegistration::where('status', 'paid')
            ->whereMonth('created_at', $currentMonth)
            ->sum('final_price');
            
        $lastMonthRevenue = CourseRegistration::where('status', 'paid')
            ->whereMonth('created_at', $lastMonth)
            ->sum('final_price');

        // Hitung Growth (hindari division by zero)
        $growth = 0;
        if ($lastMonthRevenue > 0) {
            $growth = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($currentMonthRevenue > 0) {
            $growth = 100; // Kalau bulan lalu 0 dan bulan ini ada, growth 100%
        }

        $financialStats = [
            'total_revenue' => $totalRevenue,
            'monthly_growth' => round($growth, 1),
            'average_order_value' => CourseRegistration::where('status', 'paid')->avg('final_price') ?? 0,
            'pending_revenue' => CourseRegistration::where('status', 'pending')->sum('final_price'),
        ];

        // 2. Recent Transactions (Limit 10 & Eager Loading biar gak N+1 Query)
        $recentTransactions = CourseRegistration::with('user', 'course')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($reg) {
                // Mapping data biar sesuai dengan View
                return (object) [
                    'id' => $reg->order_id ?? $reg->id,
                    'user' => $reg->user,
                    'course' => $reg->course ? $reg->course->title : 'Course Deleted',
                    'price' => $reg->final_price,
                    'status' => $reg->status,
                    'created_at' => $reg->created_at
                ];
            });

        // 3. Revenue by Course (Group by Query - Sangat Cepat)
        $revenueByCourse = DB::table('course_registrations')
            ->join('courses', 'course_registrations.course_id', '=', 'courses.id')
            ->select('courses.title as course', DB::raw('SUM(course_registrations.final_price) as total_revenue'))
            ->where('course_registrations.status', 'paid')
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // 4. Payment Status Count
        $paymentStats = [
            'paid' => CourseRegistration::where('status', 'paid')->count(),
            'pending' => CourseRegistration::where('status', 'pending')->count(),
            'cancelled' => CourseRegistration::where('status', 'cancelled')->count(),
        ];

        // Kirim semua variabel yang dibutuhkan View
        return view('admin.financial', compact(
            'financialStats', 
            'recentTransactions', 
            'revenueByCourse', 
            'paymentStats'
        ));
    }
    /**
     * Show analytics page
     */
    public function analytics()
    {
        $stats = [
            'total_revenue' => CourseRegistration::where('status', 'paid')->sum('final_price'),
            'total_refunded' => Refund::where('status', 'approved')->sum('amount'),
            'net_revenue' => CourseRegistration::where('status', 'paid')->sum('final_price') - 
                            Refund::where('status', 'approved')->sum('amount'),
            'refund_rate' => $this->calculateRefundRate(),
            'average_course_price' => Course::avg('price'),
            'most_popular_course' => $this->getMostPopularCourse(),
        ];

        $chartData = $this->getAnalyticsChartData();

        return view('admin.analytics', compact('stats', 'chartData'));
    }

    /**
     * Show refund management page
     */
    public function refund()
{
    // 1. Ambil Data Refund dengan relasi lengkap
    $refundRequests = Refund::with(['user', 'registration.course']) // ✅ Load relasi lengkap
        ->latest()
        ->get();

    // 2. Hitung Statistik untuk $refundStats
    $refundStats = [
        'total_refunds' => Refund::count(),
        'pending_refunds' => Refund::where('status', 'pending')->count(),
        'processed_refunds' => Refund::where('status', 'approved')->count(),
        'rejected_refunds' => Refund::where('status', 'rejected')->count(),
        'total_refund_amount' => Refund::where('status', 'approved')->sum('amount'),
        'refund_rate' => 0, 
        'avg_processing_time' => 0
    ];

    // Hitung Rate (Cegah pembagian dengan nol)
    $totalRegistrations = CourseRegistration::count();
    if ($totalRegistrations > 0) {
        $refundStats['refund_rate'] = round(($refundStats['processed_refunds'] / $totalRegistrations) * 100, 2);
    }

    // Hitung Rata-rata Waktu Proses
    $processedRefunds = Refund::whereNotNull('processed_at')->get();
    if ($processedRefunds->count() > 0) {
        $totalDays = 0;
        foreach ($processedRefunds as $ref) {
            $totalDays += $ref->created_at->diffInDays($ref->processed_at);
        }
        $refundStats['avg_processing_time'] = round($totalDays / $processedRefunds->count(), 1);
    }

    return view('admin.refund', compact('refundRequests', 'refundStats'));
}

     //   $refundRequests = CourseRegistration::where('status', 'cancelled')
  //          ->with(['user', 'course'])
   //         ->latest()
 //           ->get();

  //      return view('admin.refund', compact('refundRequests', 'refundStats'));
 //   }

    /**
     * Admin refunds list
     */
    public function refunds()
    {
        $refunds = Refund::with(['user', 'registration.course'])
            ->latest()
            ->paginate(15);

        $stats = [
            'pending' => Refund::where('status', 'pending')->count(),
            'approved' => Refund::where('status', 'approved')->count(),
            'rejected' => Refund::where('status', 'rejected')->count(),
            'pending_amount' => Refund::where('status', 'pending')->sum('amount'),
            'total_refunded' => Refund::where('status', 'approved')->sum('amount'),
        ];

        return view('admin.refunds.index', compact('refunds', 'stats'));
    }

    /**
     * Show refund detail
     */
    public function refundShow($id)
    {
        $refund = Refund::with(['user', 'registration.course'])->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Approve refund
     */
    public function approveRefund(Request $request, $id)
    {
        $refund = Refund::findOrFail($id);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $refund->update([
            'status' => 'approved',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Refund berhasil disetujui!');
    }

    /**
     * Reject refund
     */
    public function rejectRefund(Request $request, $id)
    {
        $refund = Refund::findOrFail($id);

        $validated = $request->validate([
            'admin_notes' => 'required|string|min:10|max:500',
        ]);

        $refund->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Refund ditolak!');
    }

    /**
     * Process refund request (legacy)
     */
    public function processRefund(Request $request, $id)
    {
        $registration = CourseRegistration::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string'
        ]);

        // Process refund logic here
        // This is where you would integrate with payment gateway

        $action = $request->action === 'approve' ? 'approved' : 'rejected';
        
        return back()->with('success', "Refund request {$action} successfully!");
    }

    /**
     * Show course management page
     */
    public function manageCourses()
    {
        $courses = Course::with('instructor')->latest()->paginate(20);
        $instructors = User::where('is_instructor', true)->get();
        
        // [BARU] Ambil data kategori & sertifikat
        $categories = \App\Models\Category::all();
        $certificates = \App\Models\CertificateTemplate::all(); 
        
        return view('admin.manage-courses', compact('courses', 'instructors', 'categories', 'certificates'));
    }

    /**
     * Create new course
     */
    public function createCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Full Online,Hybrid,Tatap Muka',
            'instructor_id' => 'nullable|exists:users,id',
            'assistants' => 'nullable|array', // Instruktur Tambahan (Array ID)
            'assistants.*' => 'exists:users,id',
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_code' => 'nullable|string|max:50',
            'min_quota' => 'required|integer|min:1',
            'max_quota' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            // Validasi Array Modul
            'modules' => 'nullable|array',
            'modules.*.title' => 'required|string|max:255',
            // Di dalam $validated rules tambahkan:
            'category_id' => 'nullable|exists:categories,id',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
        ]);

        $courseData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'price' => (float)$validated['price'],
            'instructor_id' => $request->instructor_id, // <--- Ini Instruktur Utama
            'discount_percent' => (float)$validated['discount_percent'],
            'discount_code' => $validated['discount_code'] ?? null,
            'min_quota' => (int)$validated['min_quota'],
            'max_quota' => (int)$validated['max_quota'],
            'duration_days' => (int)$validated['duration_days'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'current_enrollment' => 0,
            'is_active' => $validated['is_active'] ?? true,
        ];

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('courses', 'public');
            $courseData['image_url'] = '/storage/' . $path;
        }

        // Handle instructor - Full Online tidak butuh instructor
        if ($validated['type'] === 'Full Online') {
            $courseData['instructor_id'] = null;
        } else {
            $courseData['instructor_id'] = $validated['instructor_id'];
            
            // Validasi untuk Hybrid/Tatap Muka harus punya instructor
            if (empty($validated['instructor_id'])) {
                return redirect()->back()->withErrors(['instructor_id' => 'Instruktur harus dipilih untuk course Hybrid/Tatap Muka'])->withInput();
            }
        }

        // 1. Simpan Course
        $course = Course::create($courseData);

        // 2. Simpan Instruktur Tambahan (Jika ada)
        if ($request->has('assistants')) {
            // Pastikan instruktur utama tidak dimasukkan lagi sebagai asisten
            $assistants = collect($request->assistants)->reject(fn($id) => $id == $request->instructor_id);
            $course->assistants()->sync($assistants);
        }

        // Attach categories if provided
        if (!empty($validated['categories'])) {
            $course->categories()->attach($validated['categories']);
        }

        // 2. Simpan Modul (Jika ada)
        if (!empty($request->modules)) {
            foreach ($request->modules as $index => $moduleData) {
                CourseModule::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'order' => $index + 1, // Urutan otomatis berdasarkan input
                ]);
            }
        }

        return redirect()->route('admin.courses.manage')->with('success', 'Course "'.$validated['title'].'" berhasil dibuat!');
    }

    /**
     * Show edit course form
     */
    public function editCourse($id)
    {
        $course = Course::with('modules')->findOrFail($id);
        $instructors = User::where('is_instructor', true)->get();
        
        // [BARU] Ambil data kategori & sertifikat
        $categories = \App\Models\Category::all();
        $certificates = \App\Models\CertificateTemplate::all();

        return view('admin.edit-course', compact('course', 'instructors', 'categories', 'certificates'));
    }

    /**
     * Update course
     */
    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Full Online,Hybrid,Tatap Muka',
            'instructor_id' => 'nullable|exists:users,id',
            'assistants' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_code' => 'nullable|string|max:50',
            'min_quota' => 'required|integer|min:1',
            'max_quota' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            // Validasi Modul
            'modules' => 'nullable|array',
            'modules.*.id' => 'nullable|integer', // Kalau ada ID berarti update, kalau null berarti create
            'modules.*.title' => 'required|string|max:255',
        ]);

        // Update Instruktur Tambahan (Sync otomatis hapus yang lama, tambah yang baru)
        if ($request->has('assistants')) {
            $assistants = collect($request->assistants)->reject(fn($id) => $id == $request->instructor_id);
            $course->assistants()->sync($assistants);
        } else {
            $course->assistants()->detach(); // Hapus semua asisten jika input kosong
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada (optional, good practice)
            if ($course->image_url) {
                $oldPath = str_replace('/storage/', '', $course->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('courses', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        // Handle instructor
        if ($validated['type'] === 'Full Online') {
            $validated['instructor_id'] = null;
        }

        // 1. Update Data Course Utama
        $course->update(collect($validated)->except(['modules', 'categories'])->toArray());

        // Update categories
        if (isset($validated['categories'])) {
            $course->categories()->sync($validated['categories']);
        } else {
            $course->categories()->detach();
        }

        // 2. Logic Sinkronisasi Modul (Create/Update/Delete)
        $submittedModules = collect($request->modules ?? []);
        
        // A. Ambil ID modul yang dikirim dari form (untuk yang diedit)
        $submittedIds = $submittedModules->pluck('id')->filter()->toArray();

        // B. Hapus modul di database yang TIDAK ada di form submission (berarti user menghapusnya di UI)
        $course->modules()->whereNotIn('id', $submittedIds)->delete();

        // C. Loop untuk Update atau Create
        foreach ($submittedModules as $index => $moduleData) {
            if (isset($moduleData['id']) && $moduleData['id']) {
                // Update Existing
                CourseModule::where('id', $moduleData['id'])
                    ->update([
                        'title' => $moduleData['title'],
                        'order' => $index + 1
                    ]);
            } else {
                // Create New
                $course->modules()->create([
                    'title' => $moduleData['title'],
                    'order' => $index + 1
                ]);
            }
        }

        return redirect()->route('admin.courses.manage')->with('success', 'Course berhasil diupdate!');
    }

    /**
     * Delete course
     */
    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        
        // Check if there are any registrations for this course
        if ($course->registrations()->count() > 0) {
            return redirect()->route('admin.courses.manage')->with('error', 'Tidak bisa menghapus course yang sudah memiliki pendaftaran!');
        }

        $course->delete();

        return redirect()->route('admin.courses.manage')->with('success', 'Course berhasil dihapus!');
    }

    /**
     * Toggle course active status
     */
    public function toggleCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->update([
            'is_active' => !$course->is_active
        ]);

        $status = $course->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.courses.manage')->with('success', "Course berhasil $status!");
    }

    /**
     * Update course active status
     */
    public function updateCourseActiveStatus(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $course->update([
            'is_active' => $request->is_active
        ]);

        $status = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json(['success' => true, 'message' => "Course berhasil $status!"]);
    }

    /**
     * Helper methods
     */

    protected function getRevenueChartData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = CourseRegistration::whereDate('created_at', $date)
                ->where('status', 'paid')
                ->sum('final_price');
            
            $data[] = [
                'date' => $date->format('D'),
                'revenue' => $revenue
            ];
        }
        return $data;
    }

    protected function getAnalyticsChartData()
    {
        return [
            'revenue' => $this->getRevenueChartData(),
            'registrations_by_course' => $this->getRegistrationsByCourse(),
        ];
    }

    protected function getRegistrationsByCourse()
    {
        return Course::withCount('registrations')
            ->orderBy('registrations_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($course) {
                return [
                    'name' => $course->title,
                    'count' => $course->registrations_count
                ];
            });
    }

    protected function calculateRefundRate()
    {
        $approvedRefunds = Refund::where('status', 'approved')->count();
        $totalRegistrations = CourseRegistration::count();
        
        if ($totalRegistrations == 0) return 0;
        
        return round(($approvedRefunds / $totalRegistrations) * 100, 2);
    }

    protected function getMostPopularCourse()
    {
        return Course::withCount('registrations')
            ->orderBy('registrations_count', 'desc')
            ->first();
    }
}