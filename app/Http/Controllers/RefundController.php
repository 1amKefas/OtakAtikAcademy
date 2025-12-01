<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    // ==================== STUDENT SIDE ====================
    
    public function create($registrationId)
    {
        $registration = CourseRegistration::with('course')
            ->where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if already refunded or has pending refund
        if ($registration->payment_status === 'refunded') {
            return redirect()->route('purchase.history')
                ->with('error', 'Course ini sudah di-refund');
        }

        $existingRefund = Refund::where('registration_id', $registrationId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            return redirect()->route('purchase.history')
                ->with('error', 'Sudah ada permintaan refund untuk course ini');
        }

        return view('student.refund.create', compact('registration'));
    }

    public function store(Request $request, $registrationId)
    {
        $request->validate([
            'reason' => 'required|string|min:20|max:1000',
        ], [
            'reason.required' => 'Alasan refund harus diisi',
            'reason.min' => 'Alasan minimal 20 karakter',
            'reason.max' => 'Alasan maksimal 1000 karakter',
        ]);

        $registration = CourseRegistration::with('course')
            ->where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Validation checks
        if ($registration->payment_status === 'refunded') {
            return back()->with('error', 'Course ini sudah di-refund');
        }

        $existingRefund = Refund::where('registration_id', $registrationId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            return back()->with('error', 'Sudah ada permintaan refund untuk course ini');
        }

        try {
            Refund::create([
                'user_id' => Auth::id(),
                'registration_id' => $registrationId,
                'amount' => $registration->amount_paid,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            return redirect()->route('purchase.history')
                ->with('success', 'Permintaan refund berhasil diajukan');
                
        } catch (\Exception $e) {
            Log::error('Refund Store Error', [
                'user_id' => Auth::id(),
                'registration_id' => $registrationId,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Gagal mengajukan refund: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $refund = Refund::with(['registration.course', 'approvedBy'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('student.refund.view', compact('refund'));
    }

    // ==================== ADMIN SIDE ====================

    public function adminIndex(Request $request)
    {
        // Get filter status
        $status = $request->get('status');
        
        // Build query
        $query = Refund::with(['user', 'registration.course', 'approvedBy']);
        
        // Apply status filter
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }
        
        // Get paginated results
        $refunds = $query->latest()->paginate(15);
        
        // Calculate statistics
        $stats = [
            'total' => Refund::count(),
            'pending' => Refund::where('status', 'pending')->count(),
            'approved' => Refund::where('status', 'approved')->count(),
            'rejected' => Refund::where('status', 'rejected')->count(),
            'total_refunded' => Refund::where('status', 'approved')->sum('amount'),
        ];

        // Debug log
        Log::info('Admin Refunds Page', [
            'total_refunds' => $stats['total'],
            'pending' => $stats['pending'],
            'displayed_count' => $refunds->count(),
            'filter' => $status
        ]);

        return view('admin.refunds.index', compact('refunds', 'stats'));
    }

    public function adminShow($id)
    {
        $refund = Refund::with(['user', 'registration.course', 'approvedBy'])
            ->findOrFail($id);
            
        return view('admin.refunds.show', compact('refund'));
    }

    public function approve($id)
    {
        $refund = Refund::with('registration')->findOrFail($id);
        
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Refund sudah diproses sebelumnya');
        }

        DB::beginTransaction();
        try {
            // Update refund status
            $refund->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
            ]);

            // Update registration status
            $refund->registration->update([
                'payment_status' => 'refunded',
                'refunded_at' => now(),
            ]);

            DB::commit();
            
            Log::info('Refund Approved', [
                'refund_id' => $id,
                'approved_by' => Auth::id(),
                'amount' => $refund->amount
            ]);
            
            return redirect()
                ->route('admin.refunds.index')
                ->with('success', 'Refund berhasil disetujui');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Refund Approval Error', [
                'refund_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Gagal menyetujui refund: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500'
        ], [
            'rejection_reason.required' => 'Alasan penolakan harus diisi',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter'
        ]);

        $refund = Refund::findOrFail($id);
        
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Refund sudah diproses sebelumnya');
        }

        try {
            $refund->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            Log::info('Refund Rejected', [
                'refund_id' => $id,
                'rejected_by' => Auth::id(),
                'reason' => $request->rejection_reason
            ]);

            return redirect()
                ->route('admin.refunds.index')
                ->with('success', 'Refund berhasil ditolak');
                
        } catch (\Exception $e) {
            Log::error('Refund Rejection Error', [
                'refund_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Gagal menolak refund: ' . $e->getMessage());
        }
    }
}