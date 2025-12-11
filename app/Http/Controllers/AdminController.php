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
        // [OPTIMASI] Hitung semua user stats dalam 1 query
        $userStats = User::selectRaw('count(*) as total')
            ->selectRaw("count(case when is_admin = true then 1 end) as admins")
            ->selectRaw("count(case when is_instructor = true then 1 end) as instructors")
            ->selectRaw("count(case when created_at >= ? then 1 end) as new_active", [now()->subDays(30)])
            ->first();

        // [OPTIMASI] Hitung course stats dalam 1 query
        $courseStats = Course::selectRaw('count(*) as total')
            ->selectRaw("count(case when is_active = true then 1 end) as active")
            ->first();

        // [OPTIMASI] Hitung Transaksi & Revenue (Yang paling berat) dalam 1 query
        $regStats = CourseRegistration::selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when status = 'paid' then 1 end) as paid")
            ->selectRaw("count(case when status = 'cancelled' then 1 end) as cancelled")
            ->selectRaw("sum(case when status = 'paid' then final_price else 0 end) as total_revenue")
            ->first();

        // Mapping ulang biar view gak error
        $stats = [
            'total_users' => $userStats->total,
            'admin_users' => $userStats->admins,
            'instructor_users' => $userStats->instructors,
            'regular_users' => $userStats->total - ($userStats->admins + $userStats->instructors),
            'active_this_month' => $userStats->new_active,
            
            'total_courses' => $courseStats->total,
            'active_courses' => $courseStats->active,
            'inactive_courses' => $courseStats->total - $courseStats->active,
            
            'total_registrations' => $regStats->total,
            'pending_registrations' => $regStats->pending,
            'paid_registrations' => $regStats->paid,
            'cancelled_registrations' => $regStats->cancelled,
            'total_revenue' => $regStats->total_revenue ?? 0,
            
            // Sisa query ringan lainnya biarkan saja
            'pending_refunds' => Refund::where('status', 'pending')->count(),
            'pending_refund_amount' => Refund::where('status', 'pending')->sum('amount'),
            'total_refunded' => Refund::where('status', 'approved')->sum('amount'),
            'monthly_revenue' => CourseRegistration::where('status', 'paid')->where('created_at', '>=', now()->subDays(30))->sum('final_price'),
        ];

        // ... sisa code (recentRegistrations, dll) biarkan sama ...
        
        // (JANGAN LUPA: Pastikan $recentRegistrations dll tetap ada di bawah sini)
        $recentRegistrations = CourseRegistration::with(['user', 'course'])->latest()->take(5)->get();
        $recentRefunds = Refund::with(['user', 'registration.course'])->where('status', 'pending')->latest()->take(5)->get();
        $popularCourses = Course::withCount(['registrations' => function($q) { $q->where('status', 'paid'); }])->orderBy('registrations_count', 'desc')->take(5)->get();
        $revenueData = $this->getRevenueChartData();

        return view('admin.dashboard', compact('stats', 'recentRegistrations', 'recentRefunds', 'popularCourses', 'revenueData'));
    }

    /**
     * Show users management page (FIXED TYPE ERROR)
     */
    public function users(Request $request) 
    {
        // 1. Logic Search & Pagination
        $query = User::withCount('courseRegistrations')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }

        $users = $query->paginate(10)->withQueryString();
        
        // 2. User Stats
        $userStats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'instructor_users' => User::where('is_instructor', true)->count(),
            'regular_users' => User::where('is_admin', false)->where('is_instructor', false)->count(),
            'active_this_month' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // 3. Real Age Distribution
        $rawAges = User::whereNotNull('date_of_birth')->get()
            ->map(function ($user) {
                return $user->date_of_birth->age;
            });

        // Ini ARRAY BIASA (Aman)
        $ageDistribution = [
            ['range' => '18-24', 'count' => $rawAges->filter(fn($age) => $age >= 18 && $age <= 24)->count(), 'color' => '#3B82F6'],
            ['range' => '25-34', 'count' => $rawAges->filter(fn($age) => $age >= 25 && $age <= 34)->count(), 'color' => '#10B981'],
            ['range' => '35-44', 'count' => $rawAges->filter(fn($age) => $age >= 35 && $age <= 44)->count(), 'color' => '#F59E0B'],
            ['range' => '45-54', 'count' => $rawAges->filter(fn($age) => $age >= 45 && $age <= 54)->count(), 'color' => '#EF4444'],
            ['range' => '55+',   'count' => $rawAges->filter(fn($age) => $age >= 55)->count(),             'color' => '#8B5CF6'],
        ];

        // 4. Real Education Distribution
        $eduData = User::whereNotNull('education_level')
            ->select('education_level', DB::raw('count(*) as total'))
            ->groupBy('education_level')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $eduColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];
        
        // [FIX] Tambahkan ->toArray() di akhir
        $educationDistribution = $eduData->map(function($item, $key) use ($eduColors) {
            return [
                'level' => ucfirst($item->education_level),
                'count' => $item->total,
                'color' => $eduColors[$key % count($eduColors)]
            ];
        })->toArray(); // <--- INI KUNCINYA

        // 5. Real Location Distribution
        $locData = User::whereNotNull('location')
            ->select('location', DB::raw('count(*) as total'))
            ->groupBy('location')
            ->orderByDesc('total')
            ->take(5)
            ->get();
            
        // [FIX] Tambahkan ->toArray() di akhir
        $locationDistribution = $locData->map(function($item, $key) use ($eduColors) {
             return [
                'location' => ucfirst($item->location),
                'count' => $item->total,
                'color' => $eduColors[$key % count($eduColors)]
            ];
        })->toArray(); // <--- INI KUNCINYA

        return view('admin.users', compact('users', 'userStats', 'ageDistribution', 'educationDistribution', 'locationDistribution'));
    }

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

    public function deleteUser($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        CourseRegistration::where('user_id', $id)->delete();

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }

    public function courses()
    {
        $courses = CourseRegistration::with(['user', 'course.instructor'])
            ->latest()
            ->paginate(10);

        return view('admin.courses', compact('courses'));
    }

    public function updateCourseStatus(Request $request, $id)
    {
        $registration = CourseRegistration::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled'
        ]);

        $registration->update([
            'status' => $request->status
        ]);

        if ($registration->discount_code === 'INSTRUCTOR100' && $request->status === 'pending') {
            $registration->update(['status' => 'paid']);
        }

        if ($request->status === 'paid' || $registration->discount_code === 'INSTRUCTOR100') {
            $course = $registration->course;
            $course->update([
                'current_enrollment' => $course->registrations()->where('status', 'paid')->count()
            ]);
        }

        return back()->with('success', 'Course status updated successfully!');
    }

    public function exportCourses()
    {
        $fileName = 'course_registrations_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->streamDownload(function() {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User Name', 'User Email', 'Course Title', 'Price', 'Final Price', 'Status', 'Registration Date']);

            // [OPTIMASI] Gunakan cursor() untuk streaming data besar tanpa membebani RAM
            // Perhatikan: cursor() tidak support eager loading 'with' secara tradisional, 
            // tapi karena kita streaming, query N+1 di sini lebih aman daripada OOM (Out of Memory).
            // ATAU, gunakan chunk() jika ingin tetap pakai eager loading.
            // Di sini saya sarankan chunk() untuk keseimbangan speed & memory.
            
            CourseRegistration::with(['user', 'course'])
                ->chunk(100, function($registrations) use ($handle) {
                    foreach ($registrations as $registration) {
                        fputcsv($handle, [
                            $registration->id,
                            $registration->user->name ?? 'Deleted User', // Handle null safely
                            $registration->user->email ?? '-',
                            $registration->course->title ?? 'Deleted Course',
                            $registration->price,
                            $registration->final_price,
                            $registration->status,
                            $registration->created_at->format('Y-m-d H:i:s')
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, $headers);
    }

    public function registrations()
    {
        $registrations = CourseRegistration::with(['user', 'course'])
            ->latest()
            ->paginate(15);

        // [OPTIMASI] Gabungkan count stats
        $counts = CourseRegistration::selectRaw('count(*) as total')
            ->selectRaw("count(case when status = 'paid' then 1 end) as paid")
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when status = 'cancelled' then 1 end) as cancelled")
            ->first();

        $stats = [
            'total' => $counts->total,
            'paid' => $counts->paid,
            'pending' => $counts->pending,
            'cancelled' => $counts->cancelled,
        ];

        return view('admin.registrations', compact('registrations', 'stats'));
    }

    public function financial()
    {
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        $totalRevenue = CourseRegistration::where('status', 'paid')->sum('final_price');
        
        $currentMonthRevenue = CourseRegistration::where('status', 'paid')
            ->whereMonth('created_at', $currentMonth)
            ->sum('final_price');
            
        $lastMonthRevenue = CourseRegistration::where('status', 'paid')
            ->whereMonth('created_at', $lastMonth)
            ->sum('final_price');

        $growth = 0;
        if ($lastMonthRevenue > 0) {
            $growth = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($currentMonthRevenue > 0) {
            $growth = 100;
        }

        $financialStats = [
            'total_revenue' => $totalRevenue,
            'monthly_growth' => round($growth, 1),
            'average_order_value' => CourseRegistration::where('status', 'paid')->avg('final_price') ?? 0,
            'pending_revenue' => CourseRegistration::where('status', 'pending')->sum('final_price'),
        ];

        $recentTransactions = CourseRegistration::with('user', 'course')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($reg) {
                return (object) [
                    'id' => $reg->order_id ?? $reg->id,
                    'user' => $reg->user,
                    'course' => $reg->course ? $reg->course->title : 'Course Deleted',
                    'price' => $reg->final_price,
                    'status' => $reg->status,
                    'created_at' => $reg->created_at
                ];
            });

        $revenueByCourse = DB::table('course_registrations')
            ->join('courses', 'course_registrations.course_id', '=', 'courses.id')
            ->select('courses.title as course', DB::raw('SUM(course_registrations.final_price) as total_revenue'))
            ->where('course_registrations.status', 'paid')
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        $paymentStats = [
            'paid' => CourseRegistration::where('status', 'paid')->count(),
            'pending' => CourseRegistration::where('status', 'pending')->count(),
            'cancelled' => CourseRegistration::where('status', 'cancelled')->count(),
        ];

        return view('admin.financial', compact(
            'financialStats', 
            'recentTransactions', 
            'revenueByCourse', 
            'paymentStats'
        ));
    }

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

    public function refund()
    {
        $refundRequests = Refund::with(['user', 'registration.course'])
            ->latest()
            ->get();

        $refundStats = [
            'total_refunds' => Refund::count(),
            'pending_refunds' => Refund::where('status', 'pending')->count(),
            'processed_refunds' => Refund::where('status', 'approved')->count(),
            'rejected_refunds' => Refund::where('status', 'rejected')->count(),
            'total_refund_amount' => Refund::where('status', 'approved')->sum('amount'),
            'refund_rate' => 0, 
            'avg_processing_time' => 0
        ];

        $totalRegistrations = CourseRegistration::count();
        if ($totalRegistrations > 0) {
            $refundStats['refund_rate'] = round(($refundStats['processed_refunds'] / $totalRegistrations) * 100, 2);
        }

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

    public function refundShow($id)
    {
        $refund = Refund::with(['user', 'registration.course'])->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

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

    public function processRefund(Request $request, $id)
    {
        $registration = CourseRegistration::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string'
        ]);

        $action = $request->action === 'approve' ? 'approved' : 'rejected';
        
        return back()->with('success', "Refund request {$action} successfully!");
    }

    public function manageCourses()
    {
        $courses = Course::with('instructor')->latest()->paginate(20);
        $instructors = User::where('is_instructor', true)->get();
        $categories = \App\Models\Category::withCount('courses')->get();
        $certificates = \App\Models\CertificateTemplate::all(); 
        
        return view('admin.manage-courses', compact('courses', 'instructors', 'categories', 'certificates'));
    }

    /**
     * Create new course (FIXED)
     */
    public function createCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Full Online,Hybrid,Tatap Muka',
            'instructor_id' => 'nullable|exists:users,id',
            'assistants' => 'nullable|array', 
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
            'category_id' => 'nullable|exists:categories,id', // Validate single category ID
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'modules' => 'nullable|array',
            'modules.*.title' => 'required|string|max:255',
        ]);

        $courseData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'price' => (float)$validated['price'],
            'instructor_id' => $validated['instructor_id'],
            'discount_percent' => (float)$validated['discount_percent'],
            'discount_code' => $validated['discount_code'] ?? null,
            'min_quota' => (int)$validated['min_quota'],
            'max_quota' => (int)$validated['max_quota'],
            'duration_days' => (int)$validated['duration_days'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'current_enrollment' => 0,
            'is_active' => $request->has('is_active') ? true : false,
            'certificate_template_id' => $validated['certificate_template_id'] ?? null, // [FIX] Simpan Template ID
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('courses', 'public');
            $courseData['image_url'] = '/storage/' . $path;
        }

        // 1. Simpan Course
        $course = Course::create($courseData);

        // 2. Attach Kategori (Single Select from Form)
        if ($request->filled('category_id')) {
            $course->categories()->attach($request->category_id);
        }

        // 3. Simpan Instruktur Tambahan
        if ($request->has('assistants')) {
            $assistants = collect($request->assistants)->reject(fn($id) => $id == $request->instructor_id);
            $course->assistants()->sync($assistants);
        }

        // 4. Auto-enroll Admin (hanya untuk testing/preview Hybrid/Tatap Muka)
        if (Auth::check() && in_array($validated['type'], ['Hybrid', 'Tatap Muka'])) {
            $adminId = Auth::id();
            $exists = CourseRegistration::where('user_id', $adminId)
                ->where('course_id', $course->id)
                ->exists();
            if (!$exists) {
                CourseRegistration::create([
                    'user_id' => $adminId,
                    'course_id' => $course->id,
                    'price' => (int)($course->price ?? 0),
                    'final_price' => 0,
                    'status' => 'paid',
                    'progress' => 0,
                    'enrolled_at' => now(),
                    'paid_at' => now(),
                ]);
            }
        }

        // 5. Simpan Modul (Jika ada inputan)
        if (!empty($request->modules)) {
            foreach ($request->modules as $index => $moduleData) {
                if (!empty($moduleData['title'])) { // Pastikan title tidak kosong
                    CourseModule::create([
                        'course_id' => $course->id,
                        'title' => $moduleData['title'],
                        'order' => $index + 1,
                    ]);
                }
            }
        }

        // 6. Logic Simpan Sertifikat
        if ($request->hasFile('certificate_template')) {
            $certPath = $request->file('certificate_template')->store('certificates/templates', 'public');
            $courseData['certificate_template'] = $certPath;
        }

        return redirect()->route('admin.courses.manage')->with('success', 'Course "'.$validated['title'].'" berhasil dibuat!');
    }

    /**
     * [FITUR BARU] Tampilkan Designer Sertifikat (Admin Side)
     */
    public function certificateDesigner($id)
    {
        $course = Course::findOrFail($id);
        
        // Data Default jika JSON kosong
        $defaultElements = [
            ['id' => 'el_1', 'type' => 'dynamic', 'content' => 'student_name', 'text' => '[Nama Siswa]', 'x' => 50, 'y' => 40, 'font' => 'Helvetica', 'color' => '#000000', 'size' => 40, 'align' => 'center'],
            ['id' => 'el_2', 'type' => 'dynamic', 'content' => 'course_title', 'text' => '[Judul Kursus]', 'x' => 50, 'y' => 55, 'font' => 'Helvetica', 'color' => '#2563eb', 'size' => 30, 'align' => 'center'],
            ['id' => 'el_3', 'type' => 'text', 'text' => 'Diberikan kepada:', 'x' => 50, 'y' => 35, 'font' => 'Helvetica', 'color' => '#555555', 'size' => 18, 'align' => 'center'],
            ['id' => 'el_4', 'type' => 'dynamic', 'content' => 'date', 'text' => '[Tanggal]', 'x' => 20, 'y' => 75, 'font' => 'Courier', 'color' => '#777777', 'size' => 14, 'align' => 'center'],
            ['id' => 'el_5', 'type' => 'dynamic', 'content' => 'code', 'text' => '[No. Sertifikat]', 'x' => 80, 'y' => 75, 'font' => 'Courier', 'color' => '#777777', 'size' => 14, 'align' => 'center'],
        ];

        // Ambil dari DB atau pakai default
        $elements = $course->certificate_settings['elements'] ?? $defaultElements;

        return view('admin.certificate.designer', compact('course', 'elements'));
    }

    public function certificateUpdate(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        // 1. Upload Background Template (Induk)
        if ($request->hasFile('certificate_template')) {
            if($course->certificate_template) Storage::disk('public')->delete($course->certificate_template);
            $path = $request->file('certificate_template')->store('certificates/templates', 'public');
            $course->certificate_template = $path;
        }

        // 2. Simpan Struktur Elemen (JSON)
        // Kita simpan array 'elements'
        if ($request->has('elements_json')) {
            $elements = json_decode($request->input('elements_json'), true);
            $course->certificate_settings = ['elements' => $elements];
        }
        
        $course->save();

        return back()->with('success', 'Desain sertifikat berhasil disimpan!');
    }

    // [BARU] Helper untuk upload gambar elemen (Logo/TTD)
    public function uploadCertificateAsset(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('certificates/assets', 'public');
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path // Simpan path ini untuk PDF generation nanti
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Show edit course form
     */
    public function editCourse($id)
    {
        // [UPDATE] Sorting by 'order' for drag & drop consistency
        $course = Course::with(['modules' => function($q) {
            $q->orderBy('order', 'asc');
        }])->findOrFail($id);
        
        $instructors = User::where('is_instructor', true)->get();
        $categories = \App\Models\Category::all();
        $certificates = \App\Models\CertificateTemplate::all();

        return view('admin.edit-course', compact('course', 'instructors', 'categories', 'certificates'));
    }

    /**
     * Update course (FIXED)
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
            'assistants.*' => 'exists:users,id',
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_code' => 'nullable|string|max:50',
            'min_quota' => 'required|integer|min:1',
            'max_quota' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'nullable|exists:categories,id', // Validate category_id
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'modules' => 'nullable|array',
            'modules.*.id' => 'nullable|integer',
            'modules.*.title' => 'required|string|max:255',
        ]);

        // Prepare update data (excluding relations)
        $updateData = collect($validated)->except(['modules', 'categories', 'category_id', 'assistants', 'image'])->toArray();
        
        // Explicitly update relation IDs
        $updateData['certificate_template_id'] = $request->certificate_template_id;
        // Note: category_id is handled via pivot table below, not directly on courses table unless you changed schema.
        // If you have category_id on courses table, uncomment this:
        // $updateData['category_id'] = $request->category_id;

        // Handle Image
        if ($request->hasFile('image')) {
            if ($course->image_url) {
                $oldPath = str_replace('/storage/', '', $course->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('courses', 'public');
            $updateData['image_url'] = '/storage/' . $path;
        }

        // Checkbox handling
        $updateData['is_active'] = $request->has('is_active');

        // 1. Update Course
        $course->update($updateData);

        // 2. Update Category (Many-to-Many Sync)
        if ($request->filled('category_id')) {
            $course->categories()->sync([$request->category_id]);
        } else {
            $course->categories()->detach();
        }

        // 3. Update Instructors
        $course->instructors()->sync($validated['instructor_id'] ? [$validated['instructor_id']] : []);
        
        if ($request->has('assistants')) {
            $assistants = collect($request->assistants)->reject(fn($id) => $id == $request->instructor_id);
            $course->assistants()->sync($assistants);
        } else {
            $course->assistants()->detach();
        }

        // 4. Update Modules (Sync Logic)
        $submittedModules = collect($request->modules ?? []);
        $submittedIds = $submittedModules->pluck('id')->filter()->toArray();

        // Delete removed modules
        $course->modules()->whereNotIn('id', $submittedIds)->delete();

        // Create/Update modules
        foreach ($submittedModules as $index => $moduleData) {
            if (!empty($moduleData['title'])) {
                $course->modules()->updateOrCreate(
                    ['id' => $moduleData['id'] ?? null],
                    [
                        'course_id' => $course->id,
                        'title' => $moduleData['title'],
                        'order' => $index + 1
                    ]
                );
            }
        }

        // LOGIC UPLOAD SERTIFIKAT
        if ($request->hasFile('certificate_template')) {
            // Hapus file lama jika ada
            if ($course->certificate_template) {
                Storage::disk('public')->delete($course->certificate_template);
            }
            // Simpan file baru
            $path = $request->file('certificate_template')->store('certificates/templates', 'public');
            $course->certificate_template = $path;
        }

        $course->save(); // Save course changes

        return redirect()->route('admin.courses.manage')->with('success', 'Course berhasil diupdate!');
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        
        if ($course->registrations()->count() > 0) {
            return redirect()->route('admin.courses.manage')->with('error', 'Tidak bisa menghapus course yang sudah memiliki pendaftaran!');
        }

        $course->delete();

        return redirect()->route('admin.courses.manage')->with('success', 'Course berhasil dihapus!');
    }

    public function toggleCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->update([
            'is_active' => !$course->is_active
        ]);

        $status = $course->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.courses.manage')->with('success', "Course berhasil $status!");
    }

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