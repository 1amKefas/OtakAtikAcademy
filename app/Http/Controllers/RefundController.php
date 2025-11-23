<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    // User side - submit refund request
    public function create($registrationId)
    {
        $registration = CourseRegistration::where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Cek apakah sudah ada refund request pending
        $existingRefund = Refund::where('course_registration_id', $registrationId)
            ->where('status', 'pending')
            ->first();

        if ($existingRefund) {
            return back()->with('error', 'Anda sudah memiliki permintaan refund yang sedang diproses untuk kursus ini!');
        }

        // Cek apakah refund masih dalam window (misalnya 30 hari)
        $daysSincePayment = $registration->created_at->diffInDays(now());
        if ($daysSincePayment > 30) {
            return back()->with('error', 'Periode refund telah berlalu (maksimal 30 hari setelah pembayaran)');
        }

        return view('student.refund.create', compact('registration'));
    }

    public function store(Request $request, $registrationId)
    {
        $registration = CourseRegistration::where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'reason' => 'required|string|min:20|max:1000',
        ]);

        // Cek window refund lagi
        $daysSincePayment = $registration->created_at->diffInDays(now());
        if ($daysSincePayment > 30) {
            return back()->with('error', 'Periode refund telah berlalu');
        }

        // Create refund request
        $refund = Refund::create([
            'course_registration_id' => $registrationId,
            'user_id' => Auth::id(),
            'amount' => $registration->price,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Permintaan refund berhasil dikirim. Tim admin akan meninjau dalam 1-2 hari kerja.');
    }

    public function view($id)
    {
        $refund = Refund::with(['user', 'registration.course'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('student.refund.view', compact('refund'));
    }

    // Admin side
    public function adminIndex()
    {
        $this->authorizeAdmin();

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

    public function adminShow($id)
    {
        $this->authorizeAdmin();

        $refund = Refund::with(['user', 'registration.course'])->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    public function approve(Request $request, $id)
    {
        $this->authorizeAdmin();

        $refund = Refund::findOrFail($id);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $refund->update([
            'status' => 'approved',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'processed_at' => now(),
        ]);

        // TODO: Proses refund ke Midtrans (jika diperlukan)
        // Untuk sekarang, hanya update status

        return back()->with('success', 'Refund berhasil disetujui!');
    }

    public function reject(Request $request, $id)
    {
        $this->authorizeAdmin();

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

    protected function authorizeAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }
    }
}