<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminRefundController extends Controller
{
    public function index(Request $request)
    {
        // Get filter status from request
        $status = $request->get('status');
        
        // Build query with eager loading
        $query = Refund::with(['user', 'registration.course', 'approvedBy']);
        
        // Apply filter if status is provided
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }
        
        // Get paginated refunds
        $refunds = $query->latest()->paginate(15);
        
        // Calculate statistics
        $stats = [
            'total' => Refund::count(),
            'pending' => Refund::where('status', 'pending')->count(),
            'approved' => Refund::where('status', 'approved')->count(),
            'rejected' => Refund::where('status', 'rejected')->count(),
            'total_refunded' => Refund::where('status', 'approved')->sum('amount'),
        ];

        // Debug: Log the data
        Log::info('Refund Stats', $stats);
        Log::info('Refunds Count', ['count' => $refunds->count()]);

        return view('admin.refunds.index', compact('refunds', 'stats'));
    }

    public function show($id)
    {
        $refund = Refund::with(['user', 'registration.course', 'approvedBy'])
            ->findOrFail($id);
            
        return view('admin.refunds.show', compact('refund'));
    }

    public function approve($id)
    {
        $refund = Refund::findOrFail($id);
        
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
            
            return redirect()
                ->route('admin.refunds.index')
                ->with('success', 'Refund berhasil disetujui');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund Approval Error', [
                'refund_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Gagal menyetujui refund: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ], [
            'rejection_reason.required' => 'Alasan penolakan harus diisi',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter'
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