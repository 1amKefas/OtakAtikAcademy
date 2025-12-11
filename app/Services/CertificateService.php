<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Models\CourseRegistration; // Jangan lupa import ini

class CertificateService
{
    /**
     * Generate certificate PDF for a user who completed a course
     */
    public function generateCertificate($user, $course, $courseHours = 0)
    {
        // Get template (you can customize which template to use)
        $template = CertificateTemplate::first();
        
        if (!$template) {
            throw new \Exception('Certificate template not found');
        }

        // Generate certificate number
        $certificateNumber = 'CERT-' . now()->year . '-' . Str::random(6);

        // [BARU] Hitung Durasi Real Time-on-Page (Jam & Menit)
        $registration = CourseRegistration::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $durationText = $courseHours . ' Jam'; // Default fallback
        
        if ($registration) {
             $seconds = $registration->total_learning_seconds ?? 0;
             $hours = floor($seconds / 3600);
             $minutes = floor(($seconds % 3600) / 60);
             
             $durationParts = [];
             if ($hours > 0) {
                 $durationParts[] = "{$hours} Jam";
             }
             // Tampilkan menit jika ada, atau jika total jam 0 (biar gak kosong)
             if ($minutes > 0 || empty($durationParts)) {
                 $durationParts[] = "{$minutes} Menit";
             }
             
             $durationText = implode(' ', $durationParts);
        }

        // Prepare data for PDF
        $data = [
            'student_name' => $user->name,
            'course_name' => $course->title,
            'issued_date' => now()->format('d F Y'),
            'course_hours' => $courseHours,
            'total_duration' => $durationText, // [BARU] Variabel ini siap dipakai di Blade PDF
            'instructor_name' => $course->instructors->first()?->name ?? 'Admin',
            'certificate_number' => $certificateNumber,
        ];

        // Generate PDF from Blade template
        $pdf = Pdf::loadView('certificates.template', [
            'template' => $template,
            'data' => $data
        ]);

        // Save PDF file
        $filename = "certificate-{$user->id}-{$course->id}-" . time() . '.pdf';
        $path = "certificates/{$filename}";
        
        // Pastikan folder ada
        if (!file_exists(storage_path("app/public/certificates"))) {
            mkdir(storage_path("app/public/certificates"), 0755, true);
        }
        
        $pdf->save(storage_path("app/public/{$path}"));

        // Save certificate record
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_template_id' => $template->id,
            'certificate_number' => $certificateNumber,
            'pdf_file_path' => $path,
            'course_hours' => $courseHours,
            'issued_date' => now()
        ]);

        return $certificate;
    }

    /**
     * Get certificate by ID and generate download
     */
    public function downloadCertificate(Certificate $certificate)
    {
        $filePath = storage_path("app/public/{$certificate->pdf_file_path}");

        if (!file_exists($filePath)) {
            throw new \Exception('Certificate file not found');
        }

        return response()->download(
            $filePath,
            "certificate-{$certificate->certificate_number}.pdf"
        );
    }

    /**
     * Create certificate template
     */
    public function createTemplate($data)
    {
        return CertificateTemplate::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'background_image_path' => $data['background_image_path'],
            'placeholders' => $data['placeholders'], // JSON with positions
            'signature_image_path' => $data['signature_image_path'] ?? null,
            'issuer_name' => $data['issuer_name'],
            'issuer_title' => $data['issuer_title']
        ]);
    }
}