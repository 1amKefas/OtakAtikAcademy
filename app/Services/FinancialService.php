<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    /**
     * Get top selling courses (by revenue)
     */
    public function getTopSellingCourses($limit = 10)
    {
        return Course::select(
            'courses.id',
            'courses.title',
            'courses.price',
            DB::raw('COUNT(orders.id) as total_sold'),
            DB::raw('SUM(orders.amount) as total_revenue'),
            DB::raw('courses.price * COUNT(orders.id) as calculated_revenue')
        )
        ->leftJoin('orders', function ($join) {
            $join->on('courses.id', '=', 'orders.course_id')
                 ->where('orders.status', 'paid');
        })
        ->groupBy('courses.id', 'courses.title', 'courses.price')
        ->orderByDesc('total_revenue')
        ->limit($limit)
        ->get();
    }

    /**
     * Get course performance (most profitable)
     */
    public function getCoursePerformance()
    {
        $courses = Course::with('instructors')
            ->select(
                'courses.id',
                'courses.title',
                'courses.price',
                DB::raw('COUNT(orders.id) as total_sold'),
                DB::raw('SUM(orders.amount) as total_revenue'),
                DB::raw('COALESCE(SUM(refunds.refund_amount), 0) as total_refunded'),
                DB::raw('COALESCE(SUM(orders.amount), 0) - COALESCE(SUM(refunds.refund_amount), 0) as net_revenue')
            )
            ->leftJoin('orders', function ($join) {
                $join->on('courses.id', '=', 'orders.course_id')
                     ->where('orders.status', 'paid');
            })
            ->leftJoin('refunds', 'refunds.order_id', '=', 'orders.id')
            ->groupBy('courses.id', 'courses.title', 'courses.price')
            ->orderByDesc('net_revenue')
            ->get();

        return $courses;
    }

    /**
     * Get revenue summary
     */
    public function getRevenueSummary()
    {
        $totalRevenue = Order::where('status', 'paid')->sum('amount');
        $totalRefunded = Refund::where('status', 'approved')->sum('refund_amount');
        $netRevenue = $totalRevenue - $totalRefunded;
        $totalOrders = Order::where('status', 'paid')->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_refunded' => $totalRefunded,
            'net_revenue' => $netRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $netRevenue / $totalOrders : 0
        ];
    }

    /**
     * Get revenue by date range
     */
    public function getRevenueByDateRange($startDate, $endDate)
    {
        return Order::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(amount) as daily_revenue')
            )
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get pending refunds count
     */
    public function getPendingRefundsCount()
    {
        return Refund::whereIn('status', ['unread', 'processing'])->count();
    }

    /**
     * Process refund - deduct from revenue
     */
    public function processRefund(Refund $refund)
    {
        // Update refund status
        $refund->update([
            'status' => 'approved',
            'processed_at' => now()
        ]);

        // Handle payment gateway refund
        // This should call payment gateway API (e.g., Midtrans)
        // Example: $this->midtransService->refund($refund->order->transaction_id, $refund->refund_amount);

        return $refund;
    }
}
