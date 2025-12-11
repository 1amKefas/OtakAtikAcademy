<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\HomeController;
use App\Models\Category;

// --- GOOGLE AUTH ROUTES ---
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// TEMPORARY DEBUG ROUTE - DELETE AFTER TESTING
Route::get('/debug/delete-user/{email}', function ($email) {
    if (env('APP_ENV') !== 'production' && env('APP_DEBUG')) {
        $deleted = \App\Models\User::where('email', $email)->delete();
        return response()->json(['deleted' => $deleted . ' user(s)']);
    }
    abort(403);
});

// --- EMAIL VERIFICATION ROUTES ---
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = \App\Models\User::findOrFail($id);
    
    // Verify the hash
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect('/email/verify')->with('error', __('Invalid verification link'));
    }
    
    // Verify signature
    if (!$request->hasValidSignature()) {
        return redirect('/email/verify')->with('error', __('Link has expired'));
    }
    
    // If already verified
    if ($user->hasVerifiedEmail()) {
        return redirect('/dashboard')->with('success', __('Email already verified'));
    }
    
    // Mark as verified
    $user->markEmailAsVerified();
    
    // Log in user if not authenticated
    if (!auth()->check()) {
        auth()->loginUsingId($user->id);
    }
    
    return redirect('/dashboard')->with('success', __('Email verified successfully!'));
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', __('Verification link sent!'));
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Public Routes
// GANTI JADI:
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/help', [HelpController::class, 'index'])->name('help');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Course Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/course', [CourseController::class, 'showCourse'])->name('course.index');
    // [BARU] Route untuk Search Suggestions (Ajax)
    Route::get('/courses/search', [CourseController::class, 'searchSuggestions'])->name('courses.search');
    Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show.detail');
    Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('my.courses');
    Route::get('/purchase-history', [CourseController::class, 'purchaseHistory'])->name('purchase.history');
    Route::put('/course-progress/{id}', [CourseController::class, 'updateProgress'])->name('course.progress.update');
    
    // Profile
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [StudentController::class, 'updateProfile'])->name('profile.update');
    
    // Settings
    Route::get('/settings', [StudentController::class, 'settings'])->name('settings');
    Route::post('/settings/update', [StudentController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/password', [StudentController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/locale', [StudentController::class, 'updateLocale'])->name('settings.locale.update');
    
    // Achievements & Certificates
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/user/{user}/achievements', [AchievementController::class, 'showUserProfile'])->name('achievements.user');
    Route::get('/course/{courseId}/certificate-download', [App\Http\Controllers\CertificateController::class, 'downloadFromCourse'])->name('student.certificate.download');
});

// Payment Routes
Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/{courseId}', [PaymentController::class, 'checkout'])->name('show');
    Route::post('/process/{courseId}', [PaymentController::class, 'processPayment'])->name('process');
    Route::post('/voucher-check', [PaymentController::class, 'checkVoucher'])->name('voucher.check');
    Route::post('/notification', [PaymentController::class, 'handleNotification'])->name('notification');
    Route::get('/simulate-success/{orderId}', [PaymentController::class, 'simulateSuccess'])->name('simulate.success');
});

// Notification Routes
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});

// Admin Routes - FIXED: Route tanpa parameter di atas, dengan parameter di bawah
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::put('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // COURSES: Route tanpa parameter HARUS di ATAS
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/manage', [AdminController::class, 'manageCourses'])->name('courses.manage');
    Route::get('/courses/create', [AdminController::class, 'create'])->name('courses.create.form');
    Route::post('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
    Route::get('/courses/export', [AdminController::class, 'exportCourses'])->name('courses.export');
    
    // [BARU] Route Admin Reorder Modul
    Route::post('/courses/{course}/modules/reorder', [ModuleController::class, 'reorder'])->name('modules.reorder');
    
    // [BARU] Route Admin Reorder Konten (Materi & Quiz)
    Route::post('/courses/{course}/modules/{module}/contents/reorder', [ModuleController::class, 'reorderContents'])->name('modules.contents.reorder');
    
    // COURSES: Route dengan parameter {id} HARUS di BAWAH
    Route::get('/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{id}', [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{id}', [AdminController::class, 'deleteCourse'])->name('courses.delete');
    Route::put('/courses/{id}/toggle', [AdminController::class, 'toggleCourse'])->name('courses.toggle');
    Route::put('/courses/{id}/status', [AdminController::class, 'updateCourseStatus'])->name('courses.status.update');
    Route::put('/courses/{id}/active-status', [AdminController::class, 'updateCourseActiveStatus'])->name('courses.status');
    
    Route::get('/financial', [AdminController::class, 'financial'])->name('financial');
    Route::get('/refund', [AdminController::class, 'refund'])->name('refund');
    Route::put('/refund/{id}/process', [AdminController::class, 'processRefund'])->name('refund.process');
    
    // REFUND ROUTES - Admin Side (FIXED: Pakai RefundController yang sudah ada)
    Route::get('/refunds', [RefundController::class, 'adminIndex'])->name('refunds.index');
    Route::get('/refunds/{id}', [RefundController::class, 'adminShow'])->name('refunds.show');
    Route::post('/refunds/{id}/approve', [RefundController::class, 'approve'])->name('refunds.approve');
    Route::post('/refunds/{id}/reject', [RefundController::class, 'reject'])->name('refunds.reject');
    
    // CATEGORY ROUTES - Admin Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    
    // CERTIFICATE ROUTES - Admin Certificates
    Route::get('/certificates', [CertificateController::class, 'adminIndex'])->name('certificates.index');
    Route::get('/certificates/templates', [CertificateController::class, 'templates'])->name('certificates.templates');
    Route::get('/certificates/templates/create', [CertificateController::class, 'createTemplate'])->name('certificates.templates.create');
    Route::post('/certificates/templates', [CertificateController::class, 'storeTemplate'])->name('certificates.templates.store');
    Route::get('/certificates/templates/{template}/edit', [CertificateController::class, 'editTemplate'])->name('certificates.templates.edit');
    Route::put('/certificates/templates/{template}', [CertificateController::class, 'updateTemplate'])->name('certificates.templates.update');
    Route::delete('/certificates/templates/{template}', [CertificateController::class, 'deleteTemplate'])->name('certificates.templates.destroy');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'adminView'])->name('certificates.view');
    Route::post('/certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
    Route::post('/certificates/{certificate}/revoke', [CertificateController::class, 'revoke'])->name('certificates.revoke');

    // Certificate Designer untuk Admin
    Route::get('/courses/{id}/certificate-designer', [AdminController::class, 'certificateDesigner'])->name('courses.certificate.designer');
    Route::post('/courses/{id}/certificate-save', [AdminController::class, 'certificateUpdate'])->name('courses.certificate.update');
    // [BARU] Route khusus upload elemen gambar (Logo, QR, TTD) via AJAX
    Route::post('/courses/certificate/upload-asset', [AdminController::class, 'uploadCertificateAsset'])->name('courses.certificate.upload-asset');
    
    // FINANCIAL ROUTES - Admin Financial Dashboard
    Route::get('/financial/dashboard', [FinancialController::class, 'dashboard'])->name('financial.dashboard');
    Route::get('/financial/revenue', [FinancialController::class, 'revenue'])->name('financial.revenue');
    Route::get('/financial/orders', [FinancialController::class, 'orders'])->name('financial.orders');
    Route::get('/financial/orders/{order}', [FinancialController::class, 'orderDetail'])->name('financial.orders.detail');
    Route::get('/financial/refunds', [FinancialController::class, 'refunds'])->name('financial.refunds');
    Route::get('/financial/refunds/{refund}', [FinancialController::class, 'refundDetail'])->name('financial.refunds.detail');
    Route::post('/financial/refunds/{refund}/approve', [FinancialController::class, 'approveRefund'])->name('financial.refunds.approve');
    Route::post('/financial/refunds/{refund}/reject', [FinancialController::class, 'rejectRefund'])->name('financial.refunds.reject');
    Route::get('/financial/analytics', [FinancialController::class, 'analytics'])->name('financial.analytics');
    Route::post('/financial/export/orders', [FinancialController::class, 'exportOrders'])->name('financial.export.orders');
    Route::post('/financial/export/refunds', [FinancialController::class, 'exportRefunds'])->name('financial.export.refunds');
    Route::post('/financial/export/pdf', [FinancialController::class, 'exportPdf'])->name('financial.export.pdf');
});

// Instructor Routes
// Instructor Routes
Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    
    // --- DASHBOARD & MAIN MENU ---
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [InstructorController::class, 'courses'])->name('courses');
    
    // --- COURSE MANAGEMENT (BARU - MODULAR SYSTEM) ---
    // Halaman Editor Kurikulum
    Route::get('/courses/{id}/manage', [InstructorController::class, 'manageCourse'])->name('courses.manage');
    
    // CRUD Modul
    Route::post('/courses/{id}/modules', [InstructorController::class, 'storeModule'])->name('course.module.store');
    Route::put('/modules/{id}', [InstructorController::class, 'updateModule'])->name('course.module.update');
    Route::delete('/modules/{id}', [InstructorController::class, 'deleteModule'])->name('course.module.delete');

    // Content: Connect Quiz to Module
    Route::post('/courses/{courseId}/modules/{moduleId}/quiz', [InstructorController::class, 'storeModuleQuiz'])->name('course.module.quiz.store');
    // [TAMBAHKAN INI]
    Route::delete('/courses/{courseId}/quiz/{quizId}/detach', [InstructorController::class, 'deleteModuleQuiz'])->name('course.module.quiz.delete');
  
    // CRUD Materi (Dalam Modul - SYSTEM BARU)
    Route::post('/courses/{courseId}/modules/{moduleId}/materials', [InstructorController::class, 'storeMaterialContent'])->name('course.material.store');
    Route::delete('/materials/content/{id}', [InstructorController::class, 'deleteMaterialContent'])->name('course.material.delete');

    // --- FITUR LEGACY / PENDUKUNG (JANGAN DIHAPUS DULU) ---
    // [UPDATE] Route ini sekarang jadi PREVIEW MODE
    Route::get('/courses/{id}/preview/{type?}/{contentId?}', [InstructorController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{id}/students', [InstructorController::class, 'courseStudents'])->name('courses.students');
    
    // Ini Route yang Error tadi (Route Lama) -> Tetap pertahankan untuk backward compatibility
    // Perhatikan: storeMaterialContent dan deleteMaterialContent
    // [BARU] Route Update Materi
    Route::put('/materials/{id}', [InstructorController::class, 'updateMaterialContent'])->name('course.material.update');
    
    Route::delete('/materials/content/{id}', [InstructorController::class, 'deleteMaterialContent'])->name('course.material.delete');

    // --- ASSIGNMENTS & QUIZZES ---
    Route::post('/courses/{id}/assignments', [InstructorController::class, 'storeAssignment'])->name('assignments.store');
    Route::get('/assignments/{id}/json', [InstructorController::class, 'getAssignmentJson'])->name('assignments.json');
    Route::put('/assignments/{id}', [InstructorController::class, 'updateAssignment'])->name('assignments.update');
    Route::delete('/assignments/{id}', [InstructorController::class, 'deleteAssignment'])->name('assignments.delete');
    Route::get('/assignments/{id}/submissions', [InstructorController::class, 'assignmentSubmissions'])->name('submissions');
    Route::get('/assignments/{assignmentId}/submissions/{submissionId}', [InstructorController::class, 'submissionDetail'])->name('submissions.detail');
    Route::put('/submissions/{id}/grade', [InstructorController::class, 'gradeSubmission'])->name('submissions.grade');
    Route::put('/students/{id}/progress', [InstructorController::class, 'updateStudentProgress'])->name('students.progress');
    
    // --- FORUM ---
    Route::prefix('courses/{courseId}/forum')->name('forum.')->group(function () {
        Route::post('/', [InstructorController::class, 'storeForum'])->name('store');
        Route::get('/{forumId}', [InstructorController::class, 'showForum'])->name('show');
        Route::delete('/{forumId}', [InstructorController::class, 'deleteForum'])->name('destroy');
        Route::post('/{forumId}/reply', [InstructorController::class, 'storeForumReply'])->name('reply.store');
        Route::delete('/{forumId}/reply/{replyId}', [InstructorController::class, 'deleteForumReply'])->name('reply.destroy');
    });
    
    // --- QUIZ ---
    Route::prefix('courses/{courseId}/quiz')->name('quiz.')->group(function () {
        Route::get('/', [QuizController::class, 'index'])->name('index');
        Route::get('/create', [QuizController::class, 'create'])->name('create');
        Route::post('/', [QuizController::class, 'store'])->name('store');
        Route::get('/{quizId}/edit', [QuizController::class, 'edit'])->name('edit');
        Route::put('/{quizId}', [QuizController::class, 'update'])->name('update');
        Route::delete('/{quizId}', [QuizController::class, 'destroy'])->name('destroy');
        Route::get('/{quizId}/questions/create', [QuizController::class, 'createQuestion'])->name('question.create');
        Route::get('/{quizId}/questions/{questionId}/edit', [QuizController::class, 'editQuestion'])->name('question.edit');
        Route::post('/{quizId}/questions', [QuizController::class, 'addQuestion'])->name('question.add');
        Route::put('/{quizId}/questions/{questionId}', [QuizController::class, 'updateQuestion'])->name('question.update');
        Route::delete('/{quizId}/questions/{questionId}', [QuizController::class, 'deleteQuestion'])->name('question.delete');
        Route::get('/{quizId}/submissions', [QuizController::class, 'submissions'])->name('submissions');
        Route::get('/{quizId}/submissions/{submissionId}', [QuizController::class, 'submissionDetail'])->name('submission.detail');
    });
    
    // --- MODULES (NEW MODULAR SYSTEM) ---
    Route::get('/courses/{course}/modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::get('/courses/{course}/modules/create', [ModuleController::class, 'create'])->name('modules.create');
    Route::post('/courses/{course}/modules', [ModuleController::class, 'store'])->name('modules.store');
    // [BARU] Route Reorder Modul
    Route::post('/courses/{course}/modules/reorder', [ModuleController::class, 'reorder'])->name('modules.reorder');
    Route::get('/courses/{course}/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');
    Route::get('/courses/{course}/modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
    Route::put('/courses/{course}/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('/courses/{course}/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
    Route::post('/courses/{course}/modules/{module}/materials', [ModuleController::class, 'addMaterial'])->name('modules.materials.add');
    Route::put('/courses/{course}/modules/{module}/materials/{material}', [ModuleController::class, 'updateMaterial'])->name('modules.materials.update');
    Route::delete('/courses/{course}/modules/{module}/materials/{material}', [ModuleController::class, 'deleteMaterial'])->name('modules.materials.delete');
    // Route::post('/courses/{course}/modules/{module}/materials/reorder', [ModuleController::class, 'reorderMaterials'])->name('modules.materials.reorder');
    // [BARU] Route Reorder Quiz
    // Route::post('/courses/{course}/modules/{module}/quizzes/reorder', [ModuleController::class, 'reorderQuizzes'])->name('modules.quizzes.reorder');
    // [BARU] Route Reorder KONTEN (Gabungan Materi & Quiz)
    Route::post('/courses/{course}/modules/{module}/contents/reorder', [ModuleController::class, 'reorderContents'])->name('modules.contents.reorder');
    // Reorder Materi & Quiz dalam satu modul
    Route::post('/courses/{course}/modules/{module}/reorder', [App\Http\Controllers\ModuleController::class, 'reorderContents'])->name('instructor.modules.contents.reorder');
    // --- COURSE CLASS MANAGEMENT ---
    Route::prefix('courses/{id}/classes')->name('courses.classes.')->group(function () {
        Route::get('/', [App\Http\Controllers\CourseClassController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\CourseClassController::class, 'store'])->name('store');
        Route::post('/{classId}/assign', [App\Http\Controllers\CourseClassController::class, 'assignStudent'])->name('assign');
        Route::delete('/student/{registrationId}', [App\Http\Controllers\CourseClassController::class, 'removeStudent'])->name('remove-student');
        Route::delete('/{classId}', [App\Http\Controllers\CourseClassController::class, 'destroy'])->name('destroy');
    });
});

// Student routes with refund
Route::middleware(['auth'])->group(function () {
    // FIXED: Redirect student.dashboard ke dashboard utama
    Route::get('/student/dashboard', function() {
        return redirect()->route('dashboard');
    })->name('student.dashboard');
    
    Route::get('/student/courses', [StudentController::class, 'myCourses'])->name('student.courses');
    Route::get('/student/course/{registrationId}', [StudentController::class, 'courseDetail'])->name('student.course-detail');
    
    // Profile
    Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::post('/student/profile/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    
    // Student Certificates
    Route::get('/student/certificates', [CertificateController::class, 'myCertificates'])->name('student.certificates');
    Route::get('/student/certificates/{certificate}', [CertificateController::class, 'view'])->name('student.certificates.view');
    Route::get('/student/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('student.certificates.download');
    
    // Forum Routes (Student)
    Route::prefix('student/course/{courseId}/forum')->name('student.forum.')->group(function () {
        Route::get('/', [StudentController::class, 'forumIndex'])->name('index');
        // [BARU] Tambahkan baris ini:
        Route::post('/', [StudentController::class, 'storeForum'])->name('store');
        Route::get('/{forumId}', [StudentController::class, 'forumDetail'])->name('detail');
        Route::post('/{forumId}/reply', [StudentController::class, 'storeForumReply'])->name('reply');
        Route::delete('/{replyId}/reply', [StudentController::class, 'deleteForumReply'])->name('delete-reply');
    });
    
    // Quiz Routes (Student)
    Route::prefix('student/course/{courseId}/quiz')->name('student.quiz.')->group(function () {
        Route::get('/', [QuizController::class, 'studentQuizzes'])->name('index');
        Route::get('/{quizId}/start', [QuizController::class, 'start'])->name('start');
        Route::get('/{quizId}/submission/{submissionId}', [QuizController::class, 'continue'])->name('continue');
        Route::post('/{quizId}/submission/{submissionId}/submit', [QuizController::class, 'submit'])->name('submit');
        Route::get('/{quizId}/submission/{submissionId}/result', [QuizController::class, 'result'])->name('result');
    });
    
    // Assignment Routes (Student)
    Route::prefix('student/assignments')->name('student.assignment.')->group(function () {
        Route::get('/{assignmentId}/submit', [StudentController::class, 'submitAssignmentForm'])->name('submit.form');
        Route::post('/{assignmentId}/submit', [StudentController::class, 'submitAssignment'])->name('submit');
        Route::get('/{assignmentId}/view', [StudentController::class, 'viewSubmission'])->name('view');
    });
    
    // REFUND ROUTES - Student Side (Hanya Hybrid & Tatap Muka)
    Route::prefix('refund')->name('refund.')->group(function () {
        Route::get('/create/{registrationId}', [RefundController::class, 'create'])->name('create');
        Route::post('/store/{registrationId}', [RefundController::class, 'store'])->name('store');
        Route::get('/view/{id}', [RefundController::class, 'view'])->name('view');
    });

    // [TAMBAHAN BARU] Review Course
    Route::post('/student/course/{id}/review', [StudentController::class, 'storeReview'])->name('student.course.review');

    // --- LEARNING PAGE (LMS) ---
    // Halaman utama belajar (menampilkan materi saat ini)
    Route::get('/learning/{courseId}', [StudentController::class, 'learningPage'])->name('student.learning.index');
    
    // Pindah ke konten spesifik (Materi/Quiz) dengan validasi urutan
    Route::get('/learning/{courseId}/content/{type}/{contentId}', [StudentController::class, 'learningContent'])->name('student.learning.content');
    
    // Mark materi as complete (untuk lanjut ke next step)
    Route::post('/learning/{courseId}/complete-material/{materialId}', [StudentController::class, 'completeMaterial'])->name('student.learning.complete-material');
});