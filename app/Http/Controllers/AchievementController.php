<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    /**
     * Show achievements page
     */
    public function index()
    {
        $user = Auth::user();
        
        try {
            $achievements = Achievement::all();
            $userAchievements = $user->achievements()->pluck('achievements.id')->toArray();
            $certificates = $user->certificates()->with('course')->get();
        } catch (\Exception $e) {
            // If achievements table doesn't exist yet (before migration), return empty data
            $achievements = collect();
            $userAchievements = [];
            $certificates = collect();
        }

        // Calculate stats
        $coursesCompleted = $user->courseRegistrations()
            ->where('status', 'paid')
            ->count() ?? 0;
        
        $totalHours = $user->enrolledCourses()
            ->get()
            ->sum(function ($reg) {
                return $reg->course->duration ?? 0;
            }) ?? 0;
        
        $averageScore = $user->quizSubmissions()->avg('score') ?? 0;

        return view('achievements.index', [
            'user' => $user,
            'achievements' => $achievements,
            'userAchievements' => $userAchievements,
            'certificates' => $certificates,
            'coursesCompleted' => $coursesCompleted,
            'totalHours' => $totalHours,
            'averageScore' => round($averageScore, 1),
        ]);
    }

    /**
     * Show public user profile with achievements
     */
    public function showUserProfile(User $user)
    {
        try {
            $achievements = $user->achievements()->get();
            $certificates = $user->certificates()->with('course')->get();
        } catch (\Exception $e) {
            $achievements = collect();
            $certificates = collect();
        }

        return view('achievements.user-profile', [
            'user' => $user,
            'achievements' => $achievements,
            'certificates' => $certificates,
        ]);
    }

    /**
     * Download certificate
     */
    public function downloadCertificate(Certificate $certificate)
    {
        if ($certificate->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return abort(403);
        }

        // Generate PDF or download if already exists
        if ($certificate->pdf_path && \Storage::exists($certificate->pdf_path)) {
            return \Storage::download($certificate->pdf_path);
        }

        // Generate PDF certificate on-the-fly if not cached
        try {
            $course = $certificate->course;
            
            // Validate course has certificate template
            if (!$course->certificate_template) {
                return back()->with('error', 'Template sertifikat tidak tersedia');
            }

            // Prepare data for PDF
            $data = [
                'student_name' => $certificate->user->name,
                'course_title' => $course->title,
                'date' => $certificate->issued_date ? $certificate->issued_date->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                'code' => $certificate->certificate_number ?? $certificate->certificate_code,
                'background_image' => storage_path('app/public/' . $course->certificate_template)
            ];

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.template', $data)
                  ->setPaper('a4', 'landscape');

            return $pdf->download('Sertifikat-' . \Illuminate\Support\Str::slug($course->title) . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat sertifikat: ' . $e->getMessage());
        }
    }
}
