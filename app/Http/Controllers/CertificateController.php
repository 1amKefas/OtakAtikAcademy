<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Display templates list
     */
    public function templates()
    {
        $this->authorize('isAdmin');
        
        $templates = CertificateTemplate::paginate(15);
        return view('admin.certificates.templates', compact('templates'));
    }

    /**
     * Show form to create template
     */
    public function createTemplate()
    {
        $this->authorize('isAdmin');
        return view('admin.certificates.create-template');
    }

    /**
     * Store new certificate template
     */
    public function storeTemplate(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:certificate_templates',
            'description' => 'nullable|string',
            'background_image_path' => 'required|string|max:500',
            'placeholders' => 'required|json', // JSON with position data
            'signature_image_path' => 'nullable|string|max:500',
            'issuer_name' => 'required|string|max:255',
            'issuer_title' => 'required|string|max:255',
        ]);

        CertificateTemplate::create($validated);

        return redirect()->route('admin.certificates.templates')
            ->with('success', __('Certificate template created successfully'));
    }

    /**
     * Edit template
     */
    public function editTemplate(CertificateTemplate $template)
    {
        $this->authorize('isAdmin');
        return view('admin.certificates.edit-template', compact('template'));
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, CertificateTemplate $template)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:certificate_templates,name,' . $template->id,
            'description' => 'nullable|string',
            'background_image_path' => 'required|string|max:500',
            'placeholders' => 'required|json',
            'signature_image_path' => 'nullable|string|max:500',
            'issuer_name' => 'required|string|max:255',
            'issuer_title' => 'required|string|max:255',
        ]);

        $template->update($validated);

        return redirect()->route('admin.certificates.templates')
            ->with('success', __('Certificate template updated successfully'));
    }

    /**
     * Delete template
     */
    public function deleteTemplate(CertificateTemplate $template)
    {
        $this->authorize('isAdmin');
        $template->delete();

        return redirect()->route('admin.certificates.templates')
            ->with('success', __('Certificate template deleted successfully'));
    }

    /**
     * Display user certificates
     */
    public function myCertificates()
    {
        $certificates = auth()->user()->certificates()->paginate(10);
        return view('student.certificates.index', compact('certificates'));
    }

    /**
     * View single certificate
     */
    public function view(Certificate $certificate)
    {
        $this->authorize('view', $certificate);
        return view('student.certificates.view', compact('certificate'));
    }

    /**
     * Download certificate as PDF
     */
    public function download(Certificate $certificate)
    {
        $this->authorize('view', $certificate);
        
        $pdf = $this->certificateService->downloadCertificate($certificate);
        
        return response()->download(
            storage_path('app/certificates/' . $certificate->pdf_file_path),
            'certificate-' . $certificate->certificate_number . '.pdf'
        );
    }

    /**
     * Generate certificate for user (admin action)
     */
    public function generate(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'course_hours' => 'required|integer|min:1|max:1000',
            'certificate_template_id' => 'required|exists:certificate_templates,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $course = Course::findOrFail($validated['course_id']);
        $template = CertificateTemplate::findOrFail($validated['certificate_template_id']);

        $certificate = $this->certificateService->generateCertificate(
            $user,
            $course,
            $validated['course_hours'],
            $template
        );

        return redirect()->route('admin.certificates.view', $certificate)
            ->with('success', __('Certificate generated successfully'));
    }

    /**
     * Admin view all certificates
     */
    public function adminIndex()
    {
        $this->authorize('isAdmin');
        
        $certificates = Certificate::with(['user', 'course'])->paginate(20);
        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * View certificate (admin)
     */
    public function adminView(Certificate $certificate)
    {
        $this->authorize('isAdmin');
        return view('admin.certificates.view', compact('certificate'));
    }

    /**
     * Revoke certificate
     */
    public function revoke(Certificate $certificate)
    {
        $this->authorize('isAdmin');
        
        $certificate->update(['revoked_at' => now()]);

        return back()->with('success', __('Certificate revoked successfully'));
    }
}
