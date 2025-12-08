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
     * Download sertifikat (Logic Baru: Based on Course Progress)
     */
    public function download($courseId)
    {
        $user = Auth::user();
        
        // 1. Cek apakah user terdaftar dan progress 100%
        $registration = CourseRegistration::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        if ($registration->progress < 100) {
            return back()->with('error', 'Selesaikan kursus 100% untuk mendapatkan sertifikat.');
        }

        $course = $registration->course;

        // 2. Cek apakah template tersedia di kursus ini
        if (!$course->certificate_template) {
            return back()->with('error', 'Sertifikat untuk kursus ini belum di-upload oleh admin. Silakan hubungi admin.');
        }

        // 3. Generate atau Ambil Record Sertifikat
        $certificate = Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $courseId],
            [
                'certificate_code' => 'CERT-' . strtoupper(Str::random(10)),
                'issued_at' => now()
            ]
        );

        // 4. Siapkan Data untuk PDF
        // Pastikan path image valid untuk DomPDF
        $bgPath = storage_path('app/public/' . $course->certificate_template);
        
        $data = [
            'student_name' => $user->name,
            'course_title' => $course->title,
            'date' => $certificate->issued_at->format('d F Y'),
            'code' => $certificate->certificate_code,
            'background_image' => $bgPath
        ];

        // 5. Render PDF (Landscape A4)
        $pdf = Pdf::loadView('certificates.template', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Sertifikat-' . Str::slug($course->title) . '.pdf');
    }
    
    /**
     * Tampilkan List Sertifikat User (Opsional)
     */
    public function myCertificates()
    {
        $certificates = Certificate::where('user_id', Auth::id())
            ->with('course')
            ->latest()
            ->paginate(10);
            
        return view('student.certificates.index', compact('certificates'));
    }

    /**
     * Admin Index (Opsional - Jika masih mau pakai admin view lama)
     */
    public function adminIndex()
    {
        $certificates = Certificate::with(['user', 'course'])->latest()->paginate(20);
        return view('admin.certificates.index', compact('certificates'));
    }
}