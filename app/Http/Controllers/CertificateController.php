<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\User;
use App\Services\SupabaseStorageService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Generate sertifikat untuk student yang telah menyelesaikan course
     */
    public function generate($registrationId)
    {
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->findOrFail($registrationId);

        // Check jika sudah 100% progress
        if ($registration->progress < 100) {
            return redirect()->back()->with('error', 'Selesaikan kursus 100% untuk mendapat sertifikat');
        }

        $user = $registration->user;
        $course = $registration->course;
        $instructor = $course->instructor;

        // Cek jika sertifikat sudah ada
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$certificate) {
            // Determine instructor title based on course type
            $instructorTitle = in_array($course->course_type, ['tatap_muka', 'hybrid']) 
                ? 'Instructor' 
                : 'CEO / Instructor';

            // Generate sertifikat baru
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_number' => Certificate::generateCertificateNumber(),
                'completion_date' => now()->toDateString(),
                'instructor_name' => $instructor->name ?? 'OtakAtik Academy',
                'instructor_title' => $instructorTitle,
                'instructor_company' => 'OtakAtik Academy',
                'verification_code' => Certificate::generateVerificationCode(),
            ]);
        }

        return $this->renderPdf($certificate);
    }

    /**
     * Download sertifikat yang sudah ada
     */
    public function download($certificateId)
    {
        $certificate = Certificate::where('id', $certificateId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return $this->renderPdf($certificate);
    }

    /**
     * Download sertifikat dari course page (generate atau ambil yang sudah ada)
     */
    public function downloadFromCourse($courseId)
    {
        // Cek apakah user terdaftar di course dan sudah 100% progress
        $registration = CourseRegistration::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->firstOrFail();

        // Check progress
        if ($registration->progress < 100) {
            return redirect()->back()->with('error', 'Selesaikan kursus 100% untuk mendapat sertifikat');
        }

        $user = $registration->user;
        $course = $registration->course;
        $instructor = $course->instructor;

        // Determine instructor title based on course type
        $instructorTitle = in_array($course->course_type, ['tatap_muka', 'hybrid']) 
            ? 'Instructor' 
            : 'CEO / Instructor';

        // Cek jika sertifikat sudah ada
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$certificate) {
            // Generate sertifikat baru
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_number' => Certificate::generateCertificateNumber(),
                'completion_date' => now()->toDateString(),
                'instructor_name' => $instructor->name ?? 'OtakAtik Academy',
                'instructor_title' => $instructorTitle,
                'instructor_company' => 'OtakAtik Academy',
                'verification_code' => Certificate::generateVerificationCode(),
            ]);
        } else {
            // Update existing certificate with correct instructor title
            $certificate->update([
                'instructor_title' => $instructorTitle,
            ]);
        }

        return $this->renderPdf($certificate);
    }

    /**
     * Helper untuk render PDF
     */
    private function renderPdf($certificate)
    {
        $user = $certificate->user;
        $course = $certificate->course;

        // Generate verification URL
        $verificationUrl = route('certificate.verify', $certificate->verification_code);

        // Get Supabase logo URLs
        $supabaseService = new SupabaseStorageService();
        $logoUrls = $supabaseService->getLogoUrls();

        // Download images dari Supabase ke temp folder
        $tempDir = storage_path('temp');
        @mkdir($tempDir, 0777, true);

        $logoOtakAtikPath = $this->downloadImageToTemp($logoUrls['otakatik'], 'logo_otakatik_' . time() . '.png');
        $logoPNJPath = $this->downloadImageToTemp($logoUrls['pnj'], 'logo_pnj_' . time() . '.png');
        $logoTIKPath = $this->downloadImageToTemp($logoUrls['tik'], 'logo_tik_' . time() . '.png');

        // Generate PDF dengan DomPDF
        $pdf = Pdf::loadView('certificates.template', [
            'certificate' => $certificate,
            'user' => $user,
            'course' => $course,
            'verificationUrl' => $verificationUrl,
            'logoOtakAtik' => $logoOtakAtikPath,
            'logoPNJ' => $logoPNJPath,
            'logoTIK' => $logoTIKPath,
        ])->setPaper('a4', 'landscape');

        // Update download info
        $certificate->update([
            'is_downloaded' => true,
            'downloaded_at' => now()
        ]);

        $response = $pdf->download("Sertifikat-{$user->name}-{$course->slug}.pdf");

        // Clean up temp files
        @unlink($logoOtakAtikPath);
        @unlink($logoPNJPath);
        @unlink($logoTIKPath);

        return $response;
    }

    /**
     * Download image dari URL ke temp folder
     */
    private function downloadImageToTemp($url, $filename)
    {
        $tempDir = storage_path('temp');
        $filepath = $tempDir . '/' . $filename;

        try {
            $imageContent = file_get_contents($url);
            file_put_contents($filepath, $imageContent);
            return $filepath;
        } catch (\Exception $e) {
            \Log::error('Failed to download image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify sertifikat via QR code
     */
    public function verify($verificationCode)
    {
        $certificate = Certificate::where('verification_code', $verificationCode)
            ->firstOrFail();

        return view('certificates.verify', [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course
        ]);
    }

    /**
     * Admin: View semua sertifikat
     */
    public function index()
    {
        $certificates = Certificate::with(['user', 'course'])
            ->latest()
            ->paginate(15);

        return view('certificates.index', compact('certificates'));
    }
}