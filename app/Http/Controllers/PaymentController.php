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
public function processPayment(Request $request, $courseId)
{
    $request->validate([
        'payment_method' => 'required|in:bank_transfer,credit_card,gopay,shopeepay,instructor_free',
        'voucher_code' => 'nullable|string|max:50'
    ]);

    $course = Course::where('is_active', true)->findOrFail($courseId);
    $user = Auth::user();

    // --- [UPDATE] CEK APAKAH SUDAH TERDAFTAR (STATUS PAID) ---
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
        // Check if user is instructor AND payment method is instructor_free
        if ($user->is_instructor && $request->payment_method === 'instructor_free') {
            $finalPrice = 0;
            $isInstructor = true;
        } else {
            // Calculate final price with voucher
            $finalPrice = $this->calculateFinalPrice($course, $request->voucher_code);
            $isInstructor = false;
        }
        
        // Create order ID
        $orderId = Payment::generateOrderId();

        // normalisasikan dan cast ke integer (simpan tanpa desimal)
        $priceToSave = (int) round($this->normalizeMoney($course->price));
        $finalToSave = (int) round($this->normalizeMoney($finalPrice));

        // Create registration record
        $registration = CourseRegistration::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'order_id' => $orderId,
            'nama_lengkap' => $user->name,
            'ttl' => $user->date_of_birth ? $user->date_of_birth->format('d F Y') : 'Tidak tersedia',
            'tempat_tinggal' => $user->location ?? 'Tidak tersedia', 
            'gender' => 'Laki-laki',
            'price' => $priceToSave,
            'final_price' => $finalToSave,
            'discount_code' => $request->voucher_code,
            'payment_method' => $request->payment_method,
            'status' => $isInstructor ? 'paid' : 'pending',
            'progress' => 0,
        ]);

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'order_id' => $orderId,
            'gross_amount' => $finalToSave,
            'payment_type' => $request->payment_method,
            'transaction_status' => $isInstructor ? 'settlement' : 'pending',
            'status_message' => $isInstructor ? 'Free access for instructor' : 'Waiting for payment'
        ]);

        // Jika instructor, langsung handle success
        if ($isInstructor) {
            $this->handleSuccessfulPayment($payment, $registration);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'is_instructor' => true,
                'message' => 'Enrolled successfully!'
            ]);
        }

        // Prepare Midtrans transaction for non-instructor
        $transactionDetails = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $finalToSave,
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
                    'name' => $course->title,
                ]
            ]
        ];

        // Get Snap token from Midtrans
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
            if ($voucherCode === $course->discount_code && $course->discount_percent > 0) {
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