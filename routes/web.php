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

// --- GOOGLE AUTH ROUTES ---
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// TEMPORARY SETUP ROUTE - RUN MIGRATIONS AND SEED
Route::get('/setup/achievements', function () {
    if (env('APP_ENV') === 'production') {
        // Run migrations
        \Artisan::call('migrate', ['--force' => true]);
        // Seed achievements
        \Artisan::call('db:seed', ['--class' => 'AchievementSeeder']);
        return response()->json(['message' => 'Achievements setup complete!']);
    }
    abort(403);
});
// END TEMPORARY SETUP ROUTE

// TEMPORARY DEBUG ROUTE - DELETE AFTER TESTING
Route::get('/debug/delete-user/{email}', function ($email) {
    if (env('APP_ENV') !== 'production' && env('APP_DEBUG')) {
        $deleted = \App\Models\User::where('email', $email)->delete();
        return response()->json(['deleted' => $deleted . ' user(s)']);
    }
    abort(403);
});
// END TEMPORARY DEBUG ROUTE

// --- EMAIL VERIFICATION ROUTES ---
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // Anda perlu membuat view ini
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    // Try to get user from ID
    $user = \App\Models\User::findOrFail($id);
    
    // Check signature validity
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification hash');
    }
    
    // Check signature of URL
    if (!$request->hasValidSignature()) {
        abort(403, 'Invalid URL signature');
    }
    
    // If already verified, just redirect
    if ($user->hasVerifiedEmail()) {
        return redirect('/dashboard')->with('success', 'Email sudah diverifikasi sebelumnya!');
    }
    
    // Mark email as verified
    $user->markEmailAsVerified();
    
    // Log the user in if not already logged in
    if (!auth()->check()) {
        auth()->loginUsingId($user->id);
    }
    
    return redirect('/dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang di OtakAtik Academy.');
})->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link verifikasi telah dikirim ulang!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// --- UPDATE MIDDLEWARE GROUP ---
// Tambahkan 'verified' ke middleware group dashboard user
Route::middleware(['auth', 'verified'])->group(function () {
    // Route User Course, Profile, dll masukan di sini
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ... pindahkan route user lain ke sini
});

// Public Routes
Route::get('/', function () {
    return view('dashboard');
})->name('home');

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
    Route::get('/course', [CourseController::class, 'showCourse'])->name('course.show');
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
    Route::get('/certificate/{certificate}/download', [AchievementController::class, 'downloadCertificate'])->name('certificate.download');
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

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::put('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/manage', [AdminController::class, 'manageCourses'])->name('courses.manage');
    Route::post('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
    Route::get('/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{id}', [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{id}', [AdminController::class, 'deleteCourse'])->name('courses.delete');
    Route::put('/courses/{id}/toggle', [AdminController::class, 'toggleCourse'])->name('courses.toggle');
    Route::put('/courses/{id}/status', [AdminController::class, 'updateCourseStatus'])->name('courses.status.update');
    Route::put('/courses/{id}/active-status', [AdminController::class, 'updateCourseActiveStatus'])->name('courses.status');
    Route::get('/courses/export', [AdminController::class, 'exportCourses'])->name('courses.export');
    
    Route::get('/financial', [AdminController::class, 'financial'])->name('financial');
    Route::get('/refund', [AdminController::class, 'refund'])->name('refund');
    Route::put('/refund/{id}/process', [AdminController::class, 'processRefund'])->name('refund.process');
});

// Instructor Routes
Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [InstructorController::class, 'courses'])->name('courses');
    Route::get('/courses/{id}', [InstructorController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{id}/students', [InstructorController::class, 'courseStudents'])->name('courses.students');
    Route::post('/courses/{id}/materials', [InstructorController::class, 'storeMaterial'])->name('materials.store');
    Route::delete('/materials/{id}', [InstructorController::class, 'deleteMaterial'])->name('materials.delete');
    Route::post('/courses/{id}/assignments', [InstructorController::class, 'storeAssignment'])->name('assignments.store');
    Route::get('/assignments/{id}/json', [InstructorController::class, 'getAssignmentJson'])->name('assignments.json');
    Route::put('/assignments/{id}', [InstructorController::class, 'updateAssignment'])->name('assignments.update');
    Route::delete('/assignments/{id}', [InstructorController::class, 'deleteAssignment'])->name('assignments.delete');
    Route::get('/assignments/{id}/submissions', [InstructorController::class, 'assignmentSubmissions'])->name('submissions');
    Route::get('/assignments/{assignmentId}/submissions/{submissionId}', [InstructorController::class, 'submissionDetail'])->name('submissions.detail');
    Route::put('/submissions/{id}/grade', [InstructorController::class, 'gradeSubmission'])->name('submissions.grade');
    Route::put('/students/{id}/progress', [InstructorController::class, 'updateStudentProgress'])->name('students.progress');
    
    // Forum Routes (Instructor)
    Route::prefix('courses/{courseId}/forum')->name('forum.')->group(function () {
        Route::post('/', [InstructorController::class, 'storeForum'])->name('store');
        Route::get('/{forumId}', [InstructorController::class, 'showForum'])->name('show');
        Route::delete('/{forumId}', [InstructorController::class, 'deleteForum'])->name('destroy');
        Route::post('/{forumId}/reply', [InstructorController::class, 'storeForumReply'])->name('reply.store');
        Route::delete('/{forumId}/reply/{replyId}', [InstructorController::class, 'deleteForumReply'])->name('reply.destroy');
    });
    
    // Quiz Routes (Instructor)
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
});

// Student routes with refund
Route::middleware(['auth'])->group(function () {
    // Student dashboard
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/courses', [StudentController::class, 'myCourses'])->name('student.courses');
    Route::get('/student/course/{registrationId}', [StudentController::class, 'courseDetail'])->name('student.course-detail');
    
    // Profile
    Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::post('/student/profile/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    
    // Forum Routes (Student)
    Route::prefix('student/course/{courseId}/forum')->name('student.forum.')->group(function () {
        Route::get('/', [StudentController::class, 'forumIndex'])->name('index');
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
    
    // REFUND ROUTES - Student Side
    Route::prefix('refund')->name('refund.')->group(function () {
        Route::get('/create/{registrationId}', [RefundController::class, 'create'])->name('create');
        Route::post('/store/{registrationId}', [RefundController::class, 'store'])->name('store');
        Route::get('/view/{id}', [RefundController::class, 'view'])->name('view');
    });
});