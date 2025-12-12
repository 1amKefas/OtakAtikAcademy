<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\CourseRegistration;
use App\Services\MidtransService;
use App\Events\CourseEnrolled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Show checkout page
     */
    public function checkout($courseId)
    {
        $course = Course::where('is_active', true)->findOrFail($courseId);
        $user = Auth::user();
        
        // Check if already enrolled
        $existingEnrollment = CourseRegistration::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->exists();
        
        if ($existingEnrollment) {
            return redirect()->route('purchase.history')
                ->with('info', 'You are already enrolled in this course.');
        }
        
        // selalu kirimkan harga sebagai integer tanpa format ribuan/desimal
        $finalPrice = (int) round($this->normalizeMoney($course->price));
        
        return view('checkout', [
            'course' => $course,
            'finalPrice' => $finalPrice
        ]);
    }

    /**
     * Process payment
     */
/**
     * Process payment
     */
    public function processPayment(Request $request, $courseId)
    {
        $request->validate([
            'payment_method' => 'required|string', // Validasi string saja biar fleksibel
            'voucher_code' => 'nullable|string|max:50'
        ]);

        $course = Course::where('is_active', true)->findOrFail($courseId);
        $user = Auth::user();

        // [LOGIKA ANTI-DUPLIKASI] Cek apakah user sudah punya kursus ini (status 'paid')
        $alreadyEnrolled = CourseRegistration::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'paid')
            ->exists();
        
        if ($alreadyEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki course ini!'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // 1. Hitung Harga Awal
            $finalPrice = $this->calculateFinalPrice($course, $request->voucher_code);
            $bypassPaymentGateway = false;

            // 2. Cek Logika Bypass (Gratis / Instructor)
            // Jika User adalah Instructor DAN pakai metode 'instructor_free'
            if ($user->is_instructor && $request->payment_method === 'instructor_free') {
                $finalPrice = 0;
                $bypassPaymentGateway = true;
            } 
            // ATAU Jika Harga Akhir <= 0 (Kursus Gratis / Diskon 100%)
            elseif ($finalPrice <= 0) {
                $finalPrice = 0; // Pastikan tidak negatif
                $bypassPaymentGateway = true;
            }

            // 3. Persiapkan Data Simpan
            $orderId = Payment::generateOrderId();
            $priceToSave = (int) round($this->normalizeMoney($course->price));
            $finalToSave = (int) round($this->normalizeMoney($finalPrice));

            // 4. Buat Record Registrasi
            $registration = CourseRegistration::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_id' => $orderId,
                'nama_lengkap' => $user->name,
                'ttl' => $user->date_of_birth ? $user->date_of_birth->format('d F Y') : 'Tidak tersedia',
                'tempat_tinggal' => $user->location ?? 'Tidak tersedia', 
                'gender' => 'Laki-laki', // Sebaiknya ambil dari profile user jika ada
                'price' => $priceToSave,
                'final_price' => $finalToSave,
                'discount_code' => $request->voucher_code,
                'payment_method' => $request->payment_method,
                'status' => $bypassPaymentGateway ? 'paid' : 'pending', // Auto-paid jika gratis
                'progress' => 0,
            ]);

            // 5. Buat Record Payment
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_id' => $orderId,
                'gross_amount' => $finalToSave,
                'payment_type' => $bypassPaymentGateway ? 'free_access' : $request->payment_method,
                'transaction_status' => $bypassPaymentGateway ? 'settlement' : 'pending',
                'status_message' => $bypassPaymentGateway ? 'Free access / 100% Discount' : 'Waiting for payment'
            ]);

            // CASE 1: GRATIS / BYPASS MIDTRANS
            if ($bypassPaymentGateway) {
                $this->handleSuccessfulPayment($payment, $registration); // Aktifkan course
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'is_instructor' => true, // Flag ini dipakai frontend untuk redirect langsung
                    'message' => 'Enrolled successfully (Free)!'
                ]);
            }

            // CASE 2: BAYAR VIA MIDTRANS
            $transactionDetails = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $finalToSave, // Pastikan ini > 0
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'item_details' => [
                    [
                        'id' => $course->id,
                        'price' => $finalToSave,
                        'quantity' => 1,
                        'name' => substr($course->title, 0, 50), // Midtrans limit nama item 50 char
                    ]
                ]
            ];

            // Get Snap Token
            $midtransResponse = $this->midtransService->createTransaction($transactionDetails);

            if (!$midtransResponse['success']) {
                throw new \Exception('Payment gateway error: ' . $midtransResponse['message']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $midtransResponse['snap_token'],
                'order_id' => $orderId,
                'is_instructor' => false
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment notification from Midtrans (webhook)
     */
    public function handleNotification(Request $request)
    {
        $notification = $request->all();
        
        try {
            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? '';

            // Find payment record
            $payment = Payment::where('order_id', $orderId)->firstOrFail();
            $registration = CourseRegistration::where('order_id', $orderId)->firstOrFail();

            // [FIX] Cek Idempotency untuk Webhook juga
            // Jika status database sudah 'settlement', abaikan notifikasi susulan (kecuali kalau cancel/expire)
            if ($payment->transaction_status === 'settlement' && $transactionStatus === 'settlement') {
                return response()->json(['status' => 'ok', 'message' => 'Already processed']);
            }

            // Update payment status
            $payment->update([
                'transaction_status' => $transactionStatus,
                'transaction_id' => $notification['transaction_id'] ?? null,
                'transaction_time' => $notification['transaction_time'] ?? null,
                'settlement_time' => $notification['settlement_time'] ?? null,
                'status_code' => $notification['status_code'] ?? null,
                'status_message' => $notification['status_message'] ?? null,
                'payment_data' => $notification
            ]);

            // Handle successful payment
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $this->handleSuccessfulPayment($payment, $registration);
                }
            } 
            // Handle failed payment
            elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $this->handleFailedPayment($payment, $registration);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            \Log::error('Payment notification error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Simulate payment success (for development)
     */
    public function simulateSuccess($orderId)
    {
        try {
            $payment = Payment::where('order_id', $orderId)->firstOrFail();
            $registration = CourseRegistration::where('order_id', $orderId)->firstOrFail();

            // [FIX] Cek Idempotency: Jika sudah settlement/paid, jangan diproses lagi!
            if ($payment->transaction_status === 'settlement' || $registration->status === 'paid') {
                return redirect()->route('purchase.history')
                    ->with('info', 'Pembayaran sudah diproses sebelumnya.');
            }

            $this->handleSuccessfulPayment($payment, $registration);

            return redirect()->route('purchase.history')
                ->with('success', 'Payment successful! You are now enrolled in the course.');

        } catch (\Exception $e) {
            return redirect()->route('purchase.history')
                ->with('error', 'Payment simulation failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(Payment $payment, CourseRegistration $registration)
    {
        $course = $registration->course;
        // [FIX] Pastikan durasi minimal 1 hari. 
        // Kalau 0 atau null, anggap default 30 hari (atau ubah jadi null untuk lifetime)
        $durationDays = $course->duration && $course->duration > 0 ? $course->duration : 30; 

        if ($registration->access_expires_at && $registration->access_expires_at->isFuture()) {
            // Kalau user perpanjang sebelum expired (Stacking)
            $newExpiryDate = $registration->access_expires_at->addDays($durationDays);
        } else {
            // Kalau baru beli atau sudah expired, hitung dari SEKARANG
            $newExpiryDate = now()->addDays($durationDays);
            
            // TAPI, tidak boleh melebihi Tanggal Selesai Course (Course End Date)
            if ($course->end_date && $newExpiryDate->greaterThan($course->end_date)) {
                $newExpiryDate = $course->end_date;
            }
        }

        $registration->update([
        'status' => 'paid',
        'access_expires_at' => $newExpiryDate,
        'expiry_notification_sent' => false, // Reset notifikasi biar nanti dikirim lagi pas mau habis
        ]);

        // Cek apakah ini perpanjangan atau baru
        $newExpiryDate = null;
        // [FIX UTAMA] Double Check di dalam method ini
        // Jika status regis sudah PAID, return (berhenti) agar tidak menambah kuota 2x
        if ($registration->status === 'paid') {
            return;
        }

        DB::transaction(function () use ($payment, $registration) {
            // Update registration status
            $registration->update([
                'status' => 'paid',
                'paid_at' => now(),
                'enrolled_at' => now()
            ]);

            // Update payment
            $payment->update([
                'transaction_status' => 'settlement',
                'settlement_time' => now()
            ]);

            // Update course enrollment count
            $registration->course->increment('current_enrollment');

            // Dispatch event to create notification
            CourseEnrolled::dispatch($registration);
        });
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(Payment $payment, CourseRegistration $registration)
    {
        $registration->update(['status' => 'cancelled']);
        // You might want to send notification to user here
    }

    /**
     * Calculate final price with voucher
     */
    private function calculateFinalPrice(Course $course, $voucherCode = null)
    {
        // gunakan nilai numerik dari price untuk menghitung diskon
        $base = $this->normalizeMoney($course->price);
        $finalPrice = $base;

        if ($voucherCode) {
            // [FIX BUG 3] Pakai strtoupper biar "Diskon10" sama dengan "DISKON10"
            if (strtoupper($voucherCode) === strtoupper($course->discount_code) && $course->discount_percent > 0) {
                $discountAmount = $base * ($course->discount_percent / 100);
                $finalPrice = $base - $discountAmount;
            }
        }

        return $finalPrice;
    }

    /**
     * Check voucher validity
     */
    public function checkVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = Course::find($request->course_id);
        
        // Check if voucher code matches and has discount percent
        if ($request->voucher_code === $course->discount_code && $course->discount_percent > 0) {
            $finalPrice = $this->calculateFinalPrice($course, $request->voucher_code);
            $discountAmount = $course->price - $finalPrice;
            
            return response()->json([
                'valid' => true,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'message' => 'Voucher applied successfully!'
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Invalid voucher code'
        ], 400);
    }

    /**
     * Normalize money string to float.
     * Accepts formats like "300.000,00", "300000.00", "Rp 300.000", or numeric.
     */
    private function normalizeMoney($val)
    {
        if ($val === null) return 0.0;
        if (is_numeric($val)) return (float) $val;
        $s = (string) $val;
        $s = preg_replace('/[^\d\.,-]/', '', $s);
        if (strpos($s, '.') !== false && strpos($s, ',') !== false) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } else {
            $s = str_replace(',', '.', $s);
        }
        return floatval($s);
    }
}