<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseRegistration;
use App\Models\Notification; // Pastikan model Notification ada
use Carbon\Carbon;

class CheckCourseExpiration extends Command
{
    protected $signature = 'check:course-expiration';
    protected $description = 'Cek course yang mau expired h-1 dan kirim notifikasi';

    public function handle()
    {
        // Cari registrasi yang expired besok (antara sekarang sampai 24 jam ke depan)
        // Dan belum dikirimi notifikasi
        $expiringRegistrations = CourseRegistration::where('status', 'paid')
            ->where('access_expires_at', '<=', now()->addDay())
            ->where('access_expires_at', '>', now()) // Yang belum expired
            ->where('expiry_notification_sent', false)
            ->with(['user', 'course'])
            ->get();

        foreach ($expiringRegistrations as $reg) {
            // 1. Buat Notifikasi di Database (Lonceng Website)
            Notification::create([
                'user_id' => $reg->user_id,
                'title' => '⚠️ Perpanjangan Course Diperlukan',
                'message' => "Akses course '{$reg->course->name}' akan berakhir dalam 24 jam! Segera perpanjang agar progress belajar tidak terganggu.",
                'type' => 'expiration_warning',
                'action_url' => route('student.course.renew', $reg->course_id), // Direct ke halaman bayar
                'is_read' => false
            ]);

            // 2. Kirim Email (Opsional, pake Mailable)
            // Mail::to($reg->user->email)->send(new CourseExpiringMail($reg));

            // 3. Tandai sudah dikirim biar ga spamming tiap menit
            $reg->update(['expiry_notification_sent' => true]);
            
            $this->info("Notifikasi dikirim ke user: {$reg->user->name} untuk course: {$reg->course->name}");
        }
    }
}