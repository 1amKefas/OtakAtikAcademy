<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
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
    public function financial()
    {
        $revenueData = [
            'total_revenue' => CourseRegistration::where('status', 'paid')->sum('final_price'),
            'monthly_revenue' => CourseRegistration::where('status', 'paid')
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('final_price'),
            'total_registrations' => CourseRegistration::where('status', 'paid')->count(),
            'average_ticket' => CourseRegistration::where('status', 'paid')->avg('final_price'),
        ];

        return view('admin.financial', compact('revenueData'));
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
        $refundRequests = CourseRegistration::where('status', 'cancelled')
            ->with(['user', 'course'])
            ->latest()
            ->get();

        return view('admin.refund', compact('refundRequests'));
    }

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
        
        return view('admin.manage-courses', compact('courses', 'instructors'));
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
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_code' => 'nullable|string|max:50',
            'min_quota' => 'required|integer|min:1',
            'max_quota' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validasi gambar
        ]);

        $courseData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'price' => (float)$validated['price'],
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

        Course::create($courseData);

        return redirect()->route('admin.courses.manage')->with('success', 'Course "'.$validated['title'].'" berhasil dibuat!');
    }

    /**
     * Show edit course form
     */
    public function editCourse($id)
    {
        $course = Course::findOrFail($id);
        $instructors = User::where('is_instructor', true)->get();
        
        return view('admin.edit-course', compact('course', 'instructors'));
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
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_code' => 'nullable|string|max:50',
            'min_quota' => 'required|integer|min:1',
            'max_quota' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validasi gambar
        ]);

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

        $course->update($validated);

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