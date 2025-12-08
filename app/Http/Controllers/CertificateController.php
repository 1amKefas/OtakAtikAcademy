<?php

namespace App\Http\Controllers;

use App\Models\CourseRegistration;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Generate & Download Sertifikat berdasarkan Course ID
     */
    public function downloadFromCourse($courseId)
    {
        $user = Auth::user();
        
        // 1. Validasi: User harus terdaftar & Progress 100%
        $registration = CourseRegistration::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        if ($registration->progress < 100) {
            return back()->with('error', 'Mohon selesaikan semua materi dan quiz (100%) untuk mendapatkan sertifikat.');
        }

        $course = $registration->course;

        // 2. Validasi: Admin harus sudah upload template background
        if (!$course->certificate_template) {
            return back()->with('error', 'Template sertifikat belum di-upload oleh admin/instruktur. Silakan hubungi kami.');
        }

        // 3. Generate Record Sertifikat di Database (jika belum ada)
        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $courseId],
            [
                'certificate_code' => 'CRT-' . strtoupper(Str::random(8)) . '-' . date('Y'),
                'issued_at' => now()
            ]
        );

        // 4. Siapkan Data untuk View PDF
        // Gunakan public_path() atau storage_path() agar DomPDF bisa baca gambar lokal
        $bgPath = storage_path('app/public/' . $course->certificate_template);
        
        $data = [
            'student_name' => $user->name,
            'course_title' => $course->title,
            'date' => $certificate->issued_at->translatedFormat('d F Y'), // Format tanggal Indonesia
            'code' => $certificate->certificate_code,
            'background_image' => $bgPath
        ];

        // 5. Render PDF (Landscape A4)
        $pdf = Pdf::loadView('certificates.template', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Sertifikat-' . Str::slug($course->title) . '.pdf');
    }

    /**
     * Menampilkan List Sertifikat Saya (Menu My Certificates)
     */
    public function myCertificates()
    {
        $certificates = Certificate::where('user_id', Auth::id())
            ->with('course')
            ->latest()
            ->paginate(10);
            
        return view('student.certificates.index', compact('certificates'));
    }
}