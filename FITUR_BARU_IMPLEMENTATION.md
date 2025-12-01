# 🚀 FITUR BARU - IMPLEMENTATION GUIDE

## ✅ Sudah Dibuat

### 1. **Course Categories** ✓
```php
// Model: App\Models\Category
// Table: categories
// Pivot: category_course

// Usage:
$category = Category::create([
    'name' => 'Web Development',
    'slug' => 'web-development',
    'description' => '...'
]);

$course->categories()->attach($category->id);
$course->categories; // Get all categories
```

---

### 2. **Course Types (Online/Offline/Hybrid)** ✓
```php
// Added to courses table:
- course_type: enum('online', 'offline', 'hybrid')
- duration_days: integer (days course available)
- rating_count: integer
- average_rating: decimal

// Logic:
- Online: No instructor needed, no forum, only materials/quiz
- Offline: Instructor needed, offline materials only, no quiz
- Hybrid: Instructor needed + Zoom link, materials + quiz + forum
```

---

### 3. **Course Modules System** ✓
```php
// Models: CourseModule, ModuleMaterial
// Tables: course_modules, module_materials

// Structure:
Course
  └─ Modules (topics)
      └─ Materials (video, file, gdrive, link, image, text)
      └─ Assignments
      └─ Quizzes

// Module material types:
- video: video file + duration
- file: PDF/DOC files
- image: image files
- link: external links
- gdrive: Google Drive links
- text: inline text/description
```

---

### 4. **Instructor-Course Relationship** ✓
```php
// Pivot table: course_instructor
// Columns:
- course_id
- instructor_id
- active_duration_days (how long instructor active)
- zoom_link (for hybrid courses)
- zoom_start_time (when zoom session)
- notes (tasks/links for students)

// Usage:
$course->instructors()->attach($instructor->id, [
    'active_duration_days' => 30,
    'zoom_link' => 'https://zoom.us/j/...',
    'zoom_start_time' => '2025-12-15 10:00:00',
    'notes' => 'Meeting link dan materials'
]);

// Offline courses:
- No instructor attached (optional, for recording purposes)

// Online courses:
- No instructor (system auto-generates certificates)

// Hybrid/Offline:
- Multiple instructors possible
```

---

### 5. **Course Ratings System** ✓
```php
// Model: CourseRating
// Table: course_ratings

// Columns:
- course_id
- user_id
- rating (1-5)
- review (text)
- unique index: (course_id, user_id)

// Usage:
CourseRating::create([
    'course_id' => 1,
    'user_id' => auth()->id(),
    'rating' => 5,
    'review' => 'Great course!'
]);

// View average:
$course->average_rating (auto-calculated)
$course->rating_count
```

---

### 6. **Refund Status Tracking** ✓
```php
// Model: Refund
// Table: refunds

// Statuses:
- unread: baru request, belum dilihat admin
- processing: admin sedang proses
- approved: disetujui dan dana dikembalikan
- rejected: ditolak

// Columns:
- order_id
- user_id
- refund_amount
- reason
- status
- admin_notes
- processed_at

// Revenue Impact:
- Ketika status = 'approved'
- Total revenue di-update: revenue -= refund_amount
```

---

### 7. **Certificate System** ✓
```php
// Models: Certificate, CertificateTemplate
// Tables: certificates, certificate_templates

// Template columns:
- name: template name
- background_image_path: background image
- placeholders: JSON (field positions)
- signature_image_path: signature image
- issuer_name: e.g., "Training Center Manager"
- issuer_title: e.g., "Head of Training"

// Certificate columns:
- course_id, user_id, template_id
- certificate_number: CERT-2025-XXXXXX
- pdf_file_path: path to generated PDF
- course_hours: berapa jam course
- issued_date: tanggal diterbitkan

// Service: CertificateService
- generateCertificate($user, $course, $hours)
- downloadCertificate($certificate)
- createTemplate($data)

// Auto-generation:
- Pake library: barryvdh/laravel-dompdf
- Template placeholders di-replace dengan data real
```

---

### 8. **Financial Analytics Service** ✓
```php
// Service: FinancialService

// Methods:
1. getTopSellingCourses($limit): Top courses by revenue
2. getCoursePerformance(): Courses ranked by profit (revenue - refunds)
3. getRevenueSummary(): Total revenue, refunds, net, orders
4. getRevenueByDateRange($start, $end): Daily revenue
5. getPendingRefundsCount(): Pending refund requests
6. processRefund($refund): Approve refund & deduct revenue

// Admin Financial Dashboard shows:
- Total revenue (sum of paid orders)
- Total refunded (sum of approved refunds)
- Net revenue (revenue - refunded)
- Top 10 courses by profit
- Revenue trend chart
- Pending refunds count
```

---

### 9. **Report Export Service** ✓
```php
// Service: ReportService

// Methods:
1. exportToExcel($data, $columns, $filename)
2. exportToPdf($data, $columns, $filename, $title)
3. generateReportTable($data, $columns)

// Usage:
Select columns dari reports UI
↓
$selectedColumns = ['name', 'email', 'courses_enrolled', 'total_spent']
↓
ReportService::exportToExcel($data, $selectedColumns, 'users-report')
↓
Download file excel/pdf dengan selected columns
```

---

## 🔄 NEXT STEPS - MASIH PERLU DIBUAT

### 10. **Instructor Dashboard** ⏳
```
GET /instructor/dashboard
Menampilkan:
- Total courses
- Total students
- Total completed assignments (by course)
- Total pending assignments
- Course dengan students terbanyak
- Earnings tracking
- Recent submissions to grade
```

### 11. **Admin Update Routes for New Features** ⏳
```php
// Add to routes/web.php:

// Course categories management
Route::prefix('admin/categories')->group(function () {
    Route::get('/', 'CategoryController@index');
    Route::post('/', 'CategoryController@store');
    Route::put('/{id}', 'CategoryController@update');
    Route::delete('/{id}', 'CategoryController@destroy');
});

// Module management (admin + instructor can edit)
Route::prefix('admin/modules')->middleware(['admin'])->group(function () {
    Route::post('/courses/{courseId}/modules', 'ModuleController@store');
    Route::put('/modules/{moduleId}', 'ModuleController@update');
    Route::delete('/modules/{moduleId}', 'ModuleController@destroy');
});

// Instructor module management (collab with admin)
Route::prefix('instructor/modules')->middleware(['instructor'])->group(function () {
    Route::put('/modules/{moduleId}', 'ModuleController@instructorUpdate');
});

// Certificate template management
Route::prefix('admin/certificates')->group(function () {
    Route::get('/templates', 'CertificateController@templates');
    Route::post('/templates', 'CertificateController@storeTemplate');
    Route::get('/{id}/download', 'CertificateController@download');
});

// Financial reports
Route::prefix('admin/financial')->group(function () {
    Route::get('/courses', 'FinancialController@coursePerformance');
    Route::get('/revenue', 'FinancialController@revenueChart');
    Route::get('/export', 'FinancialController@export');
    Route::get('/refunds', 'FinancialController@refunds');
    Route::put('/refunds/{id}', 'FinancialController@updateRefund');
});

// Instructor dashboard
Route::prefix('instructor')->middleware(['instructor'])->group(function () {
    Route::get('/dashboard', 'InstructorController@dashboard');
});
```

### 12. **Security Enhancements Needed** ⏳
```
- Input validation & sanitization
- Output escaping {{ }} in Blade
- CSRF token @csrf in forms
- SQL Injection prevention (already via Eloquent)
- XSS protection (escape user input)
- HTTPS enforcement (no HTTP assets)
- .env configuration for sensitive data
- OWASP ZAP testing
```

### 13. **Views/Templates Needed** ⏳
```
- Admin Financial Dashboard
- Instructor Dashboard
- Refund Management (Admin)
- Certificate Templates (Admin)
- Report Export (Admin)
- Course with Modules (Student)
- Rating Course (Student)
- Module Content Display (varies by type)
```

---

## 📦 DEPENDENCIES NEEDED

```bash
# Already might need to install:
composer require barryvdh/laravel-dompdf  # PDF generation
composer require maatwebsite/excel        # Excel export
```

---

## 🎯 RECOMMENDED IMPLEMENTATION ORDER

1. ✅ **Models & Migrations** - DONE
2. ⏳ **Controllers** - Next step
3. ⏳ **Routes** - Update web.php
4. ⏳ **Views** - Create blade templates
5. ⏳ **Security** - OWASP testing
6. ⏳ **Testing** - Unit & Feature tests

---

## 💡 ARCHITECTURE NOTES

### Course Type Behavior

**ONLINE:**
- No instructor needed
- No forum (students can't discuss)
- Materials only (video, file, links)
- Quiz allowed
- Certificate auto-generated by system

**OFFLINE:**
- Instructor required
- No forum
- Materials only (offline-style, just PDFs/resources)
- No quiz
- Manual attendance tracking

**HYBRID:**
- Multiple instructors allowed
- Forum enabled (discussion)
- Materials + Zoom link + quiz
- Instructor manages zoom session
- Certificate manual or auto (configurable)

### Module Material Types Priority

1. Video (most important)
2. File (PDF, DOC)
3. Google Drive Link (direct access)
4. Image (supplementary)
5. Link (external reference)
6. Text (inline content)

---

## 🔒 Security Checklist

- [ ] All user inputs validated
- [ ] All outputs escaped {{ }} in templates
- [ ] CSRF tokens in forms @csrf
- [ ] No SQL injection (using Eloquent)
- [ ] No XSS (escape output, use {!! !!} carefully)
- [ ] Sensitive config in .env
- [ ] No HTTP assets (only HTTPS)
- [ ] Passwords hashed bcrypt()
- [ ] Auth checks on protected routes
- [ ] Role-based access control
- [ ] OWASP ZAP scan completed

---

Mari lanjutkan dengan membuat Controllers untuk fitur-fitur ini!
