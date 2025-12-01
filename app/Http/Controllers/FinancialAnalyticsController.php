<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseRegistration;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialAnalyticsController extends Controller
{
    public function index()
    {
        // Total Revenue (minus refunds)
        $totalRevenue = CourseRegistration::where('payment_status', 'paid')->sum('amount');
        $totalRefunds = Refund::where('status', 'approved')->sum('amount');
        $netRevenue = $totalRevenue - $totalRefunds;

        // Recent Transactions (including refunds)
        $recentTransactions = $this->getRecentTransactions();

        // Monthly Revenue Chart
        $monthlyData = $this->getMonthlyRevenue();

        // Top Courses
        $topCourses = $this->getTopCourses();

        // Stats
        $stats = [
            'total_revenue' => $netRevenue,
            'total_refunds' => $totalRefunds,
            'pending_refunds' => Refund::where('status', 'pending')->count(),
            'total_students' => CourseRegistration::where('payment_status', 'paid')->distinct('user_id')->count(),
        ];

        return view('admin.financial-analytics', compact(
            'stats',
            'recentTransactions',
            'monthlyData',
            'topCourses'
        ));
    }

    private function getRecentTransactions()
    {
        // Get payments
        $payments = CourseRegistration::with(['user', 'course'])
            ->where('payment_status', 'paid')
            ->select('id', 'user_id', 'course_id', 'amount', 'created_at', DB::raw("'payment' as type"))
            ->get();

        // Get approved refunds
        $refunds = Refund::with(['user', 'registration.course'])
            ->where('status', 'approved')
            ->select('id', 'user_id', 'amount', 'approved_at as created_at', DB::raw("'refund' as type"), 'registration_id')
            ->get();

        // Combine and sort
        $transactions = $payments->concat($refunds)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return $transactions;
    }

    private function getMonthlyRevenue()
    {
        $months = collect();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $revenue = CourseRegistration::where('payment_status', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            
            $refunds = Refund::where('status', 'approved')
                ->whereYear('approved_at', $date->year)
                ->whereMonth('approved_at', $date->month)
                ->sum('amount');
            
            $months->push([
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
                'refunds' => $refunds,
                'net' => $revenue - $refunds,
            ]);
        }
        
        return $months;
    }

    private function getTopCourses()
    {
        return DB::table('course_registrations')
            ->join('courses', 'course_registrations.course_id', '=', 'courses.id')
            ->where('course_registrations.payment_status', 'paid')
            ->select(
                'courses.title',
                DB::raw('COUNT(course_registrations.id) as total_sales'),
                DB::raw('SUM(course_registrations.amount) as total_revenue')
            )
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
    }
}