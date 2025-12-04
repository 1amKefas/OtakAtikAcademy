<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'course_id' => $registration->course_id,
            'amount' => $registration->total_paid ?? $registration->course->price, // Fallback jika total_paid null
            'reason' => $request->reason,
            'status' => 'pending', // Status awal
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
    public function adminIndex()
    {
        $refunds = Refund::with(['user', 'courseRegistration.course'])
            ->latest()
            ->paginate(15);

        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Detail request refund
     */
    public function adminShow($id)
    {
        $refund = Refund::with(['user', 'courseRegistration.course', 'courseRegistration.payment'])->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Approve Refund (Setujui & Cabut Akses)
     */
    public function approve($id)
    {
        $refund = Refund::findOrFail($id);
        
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Refund ini sudah diproses sebelumnya.');
        }

        // 1. Update Status Refund
        $refund->update([
            'status' => 'approved',
            'admin_notes' => 'Permintaan disetujui oleh Admin.', // Default note
            'processed_at' => now()
        ]);

        // 2. Update Status Registrasi User jadi 'Refunded' (Otomatis cabut akses materi)
        if ($refund->courseRegistration) {
            $refund->courseRegistration->update(['status' => 'refunded']);
        }

        // 3. (Opsional) Trigger notifikasi email ke user di sini
        // Mail::to($refund->user->email)->send(new RefundApprovedMail($refund));

        return back()->with('success', 'Refund berhasil disetujui. Akses siswa ke kursus telah dicabut.');
    }

    /**
     * Reject Refund (Tolak)
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $refund = Refund::findOrFail($id);

        if ($refund->status !== 'pending') {
            return back()->with('error', 'Refund ini sudah diproses sebelumnya.');
        }

        // Update Status jadi Rejected
        $refund->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('reason'), // Alasan penolakan dari form
            'processed_at' => now()
        ]);

        return back()->with('success', 'Permintaan refund ditolak.');
    }
}