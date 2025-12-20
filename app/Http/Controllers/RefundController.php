<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    // ==========================================
    // STUDENT SIDE (Pengajuan)
    // ==========================================

    /**
     * Tampilkan form pengajuan refund
     */
    public function create($registrationId)
    {
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('id', $registrationId)
            ->where('status', 'paid')
            ->with('course')
            ->firstOrFail();

        // Validasi Tipe Course (Hanya Hybrid & Tatap Muka yang boleh refund manual di sini)
        // Sesuaikan string ini dengan value enum di databasemu
        $allowedTypes = ['hybrid', 'offline', 'tatap muka', 'Hybrid', 'Tatap Muka'];
        
        if (!in_array($registration->course->type, $allowedTypes)) {
            return back()->with('error', 'Fitur pengajuan refund ini hanya tersedia untuk kelas Hybrid & Tatap Muka.');
        }

        // Cek apakah sudah pernah request sebelumnya
        $existingRefund = Refund::where('course_registration_id', $registrationId)->first();
        if ($existingRefund) {
            return back()->with('error', 'Anda sudah mengajukan permintaan refund untuk kursus ini. Silakan cek statusnya.');
        }

        return view('student.refund.create', compact('registration'));
    }

    /**
     * Simpan pengajuan refund ke database
     */
    public function store(Request $request, $registrationId)
    {
        $request->validate([
            'reason' => 'required|string|min:20|max:1000'
        ], [
            'reason.required' => 'Alasan pengajuan wajib diisi.',
            'reason.min' => 'Alasan minimal 20 karakter agar kami bisa memahami masalah Anda.',
        ]);

        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('id', $registrationId)
            ->firstOrFail();

        // Create Refund Record
        Refund::create([
            'user_id' => Auth::id(),
            'course_registration_id' => $registration->id,
            'amount' => $registration->final_price ?? $registration->course->price,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('student.course-detail', $registration->id)
            ->with('success', 'Permintaan refund berhasil dikirim! Mohon tunggu konfirmasi dari Admin (1-3 hari kerja).');
    }


    // ==========================================
    // ADMIN SIDE (Manajemen / Approval)
    // ==========================================

    /**
     * List semua request refund
     */
    public function adminIndex(Request $request)
    {
        $query = Refund::with(['user', 'registration.course']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $status = $request->status;
            
            // Support old filter 'approved' yang means processing + completed
            if ($status === 'approved') {
                $query->whereIn('status', ['processing', 'completed']);
            } else {
                $query->where('status', $status);
            }
        }

        $refunds = $query->latest()->paginate(15);

        // Calculate stats
        $stats = [
            'total' => Refund::count(),
            'pending' => Refund::where('status', 'pending')->count(),
            'approved' => Refund::whereIn('status', ['processing', 'completed'])->count(),
            'rejected' => Refund::where('status', 'rejected')->count(),
            'total_refunded' => Refund::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.refunds.index', compact('refunds', 'stats'));
    }

    /**
     * Detail request refund
     */
    public function adminShow($id)
    {
        $refund = Refund::with(['user', 'registration.course'])->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Approve Refund (Start Processing - Sedang Diproses)
     */
    public function approve($id)
    {
        $refund = Refund::findOrFail($id);
        
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Refund ini sudah diproses sebelumnya.');
        }

        // 1. Update Status ke Processing
        $refund->update([
            'status' => 'processing',
            'processing_started_at' => now(),
            'admin_notes' => 'Refund sedang diproses.'
        ]);

        // 2. Send notification ke user
        try {
            $refund->user->notify(new \App\Notifications\RefundStatusNotification($refund, 'processing'));
        } catch (\Exception $notifError) {
            \Log::error('Refund notification error: ' . $notifError->getMessage());
        }

        return back()->with('success', 'Refund berhasil dimulai pemrosesan. Notifikasi telah dikirim ke user.');
    }

    /**
     * Complete Refund (Refund Berhasil)
     */
    public function complete($id)
    {
        $refund = Refund::with('registration', 'user')->findOrFail($id);
        
        if ($refund->status !== 'processing') {
            return back()->with('error', 'Refund harus dalam status processing untuk diselesaikan.');
        }

        DB::beginTransaction();
        try {
            // 1. Update Status ke Completed
            $refund->update([
                'status' => 'completed',
                'processed_at' => now(),
                'admin_notes' => 'Refund berhasil diselesaikan.'
            ]);

            // 2. Update Status Registrasi User jadi 'Cancelled' (refunded)
            if ($refund->registration) {
                $refund->registration->update(['status' => 'cancelled']);
            }

            // 3. Send notification ke user - Refund Berhasil (dispatch via queue)
            try {
                $refund->user->notify(new \App\Notifications\RefundStatusNotification($refund, 'completed'));
            } catch (\Exception $notifError) {
                // Log notification error tapi jangan hentikan proses
                \Log::error('Refund notification error: ' . $notifError->getMessage());
            }

            DB::commit();
            return back()->with('success', 'Refund berhasil diselesaikan. User telah diberitahu.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan refund: ' . $e->getMessage());
        }
    }

    /**
     * Reject Refund (Tolak)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500'
        ]);

        $refund = Refund::findOrFail($id);

        if (!in_array($refund->status, ['pending', 'processing'])) {
            return back()->with('error', 'Refund ini sudah diselesaikan atau ditolak sebelumnya.');
        }

        // Update Status jadi Rejected
        $refund->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('reason'), // Alasan penolakan dari form
            'processed_at' => now()
        ]);

        // Send rejection notification to user
        try {
            $refund->user->notify(new \App\Notifications\RefundRejectedNotification($refund));
        } catch (\Exception $notifError) {
            \Log::error('Refund rejection notification error: ' . $notifError->getMessage());
        }

        return back()->with('success', 'Permintaan refund telah ditolak. Notifikasi telah dikirim ke user.');
    }
}