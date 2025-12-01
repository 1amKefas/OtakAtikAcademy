<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Refund;
use App\Services\FinancialService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    protected FinancialService $financialService;
    protected ReportService $reportService;

    public function __construct(FinancialService $financialService, ReportService $reportService)
    {
        $this->financialService = $financialService;
        $this->reportService = $reportService;
    }

    /**
     * Display admin financial dashboard
     */
    public function dashboard()
    {
        $this->authorize('isAdmin');

        $summary = $this->financialService->getRevenueSummary();
        $topCourses = $this->financialService->getTopSellingCourses(10);
        $coursePerformance = $this->financialService->getCoursePerformance();
        $pendingRefunds = $this->financialService->getPendingRefundsCount();

        return view('admin.financial.dashboard', compact(
            'summary',
            'topCourses',
            'coursePerformance',
            'pendingRefunds'
        ));
    }

    /**
     * Revenue analytics by date range
     */
    public function revenue(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $startDate = $validated['start_date'] ?? now()->subDays(30);
        $endDate = $validated['end_date'] ?? now();

        $revenueByDate = $this->financialService->getRevenueByDateRange($startDate, $endDate);

        return view('admin.financial.revenue', compact('revenueByDate', 'startDate', 'endDate'));
    }

    /**
     * Display all orders
     */
    public function orders(Request $request)
    {
        $this->authorize('isAdmin');

        $query = Order::with(['user', 'course']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $orders = $query->paginate(20);

        return view('admin.financial.orders', compact('orders'));
    }

    /**
     * View single order
     */
    public function orderDetail(Order $order)
    {
        $this->authorize('isAdmin');
        return view('admin.financial.order-detail', compact('order'));
    }

    /**
     * Display refund requests
     */
    public function refunds(Request $request)
    {
        $this->authorize('isAdmin');

        $query = Refund::with(['user', 'order'])->where('status', '!=', 'rejected');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $refunds = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.financial.refunds', compact('refunds'));
    }

    /**
     * View refund request
     */
    public function refundDetail(Refund $refund)
    {
        $this->authorize('isAdmin');
        return view('admin.financial.refund-detail', compact('refund'));
    }

    /**
     * Approve refund
     */
    public function approveRefund(Request $request, Refund $refund)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $refund->update([
            'status' => 'approved',
            'admin_notes' => $validated['admin_notes'] ?? $refund->admin_notes,
            'processed_at' => now(),
        ]);

        // Process refund payment via Midtrans
        $this->financialService->processRefund($refund);

        return back()->with('success', __('Refund approved successfully'));
    }

    /**
     * Reject refund
     */
    public function rejectRefund(Request $request, Refund $refund)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $refund->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
            'processed_at' => now(),
        ]);

        return back()->with('success', __('Refund rejected successfully'));
    }

    /**
     * Export orders to Excel
     */
    public function exportOrders(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'string',
        ]);

        $orders = Order::with(['user', 'course'])->get();

        return $this->reportService->exportToExcel($orders, $validated['columns'], 'orders.xlsx');
    }

    /**
     * Export refunds to Excel
     */
    public function exportRefunds(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'columns' => 'required|array|min:1',
            'columns.*' => 'string',
        ]);

        $refunds = Refund::with(['user', 'order'])->get();

        return $this->reportService->exportToExcel($refunds, $validated['columns'], 'refunds.xlsx');
    }

    /**
     * Export financial report to PDF
     */
    public function exportPdf(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'report_type' => 'required|in:orders,refunds,revenue',
            'columns' => 'required|array|min:1',
            'columns.*' => 'string',
        ]);

        if ($validated['report_type'] === 'orders') {
            $data = Order::with(['user', 'course'])->get();
            $title = __('Orders Report');
        } elseif ($validated['report_type'] === 'refunds') {
            $data = Refund::with(['user', 'order'])->get();
            $title = __('Refunds Report');
        } else {
            $startDate = now()->subDays(30);
            $endDate = now();
            $data = $this->financialService->getRevenueByDateRange($startDate, $endDate);
            $title = __('Revenue Report');
        }

        return $this->reportService->exportToPdf($data, $validated['columns'], 'financial-report.pdf', $title);
    }

    /**
     * Display advanced analytics
     */
    public function analytics()
    {
        $this->authorize('isAdmin');

        $topCourses = $this->financialService->getTopSellingCourses(10);
        $coursePerformance = $this->financialService->getCoursePerformance();

        return view('admin.financial.analytics', compact('topCourses', 'coursePerformance'));
    }
}
