# 📋 CHECKLIST - IMPLEMENTASI FITUR BARU

## FASE 1: Controllers & Routes (URGENT)

### Controllers to Create
- [ ] `app/Http/Controllers/CategoryController.php`
  - [ ] index - List categories
  - [ ] create - Show form
  - [ ] store - Save category
  - [ ] edit - Show edit form
  - [ ] update - Update category
  - [ ] destroy - Delete category

- [ ] `app/Http/Controllers/ModuleController.php`
  - [ ] store - Create module
  - [ ] update - Update module
  - [ ] destroy - Delete module
  - [ ] storeMaterial - Add material to module
  - [ ] updateMaterial - Edit material
  - [ ] destroyMaterial - Delete material

- [ ] `app/Http/Controllers/CertificateController.php`
  - [ ] templates - List templates
  - [ ] storeTemplate - Create template
  - [ ] updateTemplate - Edit template
  - [ ] download - Download certificate PDF
  - [ ] preview - Preview certificate

- [ ] `app/Http/Controllers/FinancialController.php`
  - [ ] dashboard - Main financial dashboard
  - [ ] coursePerformance - Top courses by profit
  - [ ] revenueChart - Revenue trend chart
  - [ ] refunds - List pending refunds
  - [ ] updateRefund - Update refund status
  - [ ] export - Export report

- [ ] Update `app/Http/Controllers/InstructorController.php`
  - [ ] Add dashboard method - Show instructor stats
  - [ ] Add courseStats method - Stats per course

### Routes to Add (routes/web.php)
```php
// Admin Categories
Route::prefix('admin/categories')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
});

// Admin Modules
Route::prefix('admin/courses/{courseId}/modules')->middleware(['auth', 'admin'])->group(function () {
    Route::post('/', [ModuleController::class, 'store'])->name('admin.modules.store');
    Route::put('/{moduleId}', [ModuleController::class, 'update'])->name('admin.modules.update');
    Route::delete('/{moduleId}', [ModuleController::class, 'destroy'])->name('admin.modules.destroy');
    Route::post('/{moduleId}/materials', [ModuleController::class, 'storeMaterial'])->name('admin.materials.store');
});

// Instructor Modules (can edit own courses)
Route::prefix('instructor/courses/{courseId}/modules')->middleware(['auth', 'instructor'])->group(function () {
    Route::put('/{moduleId}', [ModuleController::class, 'update'])->name('instructor.modules.update');
    Route::put('/{moduleId}/materials/{materialId}', [ModuleController::class, 'updateMaterial'])->name('instructor.materials.update');
});

// Certificates
Route::prefix('admin/certificates')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/templates', [CertificateController::class, 'templates'])->name('admin.certificates.templates');
    Route::post('/templates', [CertificateController::class, 'storeTemplate'])->name('admin.certificates.storeTemplate');
    Route::put('/templates/{templateId}', [CertificateController::class, 'updateTemplate'])->name('admin.certificates.updateTemplate');
    Route::get('/{certificateId}/download', [CertificateController::class, 'download'])->name('admin.certificates.download');
});

// Financial
Route::prefix('admin/financial')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [FinancialController::class, 'dashboard'])->name('admin.financial.dashboard');
    Route::get('/courses', [FinancialController::class, 'coursePerformance'])->name('admin.financial.courses');
    Route::get('/revenue', [FinancialController::class, 'revenueChart'])->name('admin.financial.revenue');
    Route::get('/refunds', [FinancialController::class, 'refunds'])->name('admin.financial.refunds');
    Route::put('/refunds/{refundId}', [FinancialController::class, 'updateRefund'])->name('admin.financial.updateRefund');
    Route::get('/export', [FinancialController::class, 'export'])->name('admin.financial.export');
});

// Instructor Dashboard
Route::get('/instructor/dashboard', [InstructorController::class, 'dashboard'])->middleware(['auth', 'instructor'])->name('instructor.dashboard');

// Student Certificate Download
Route::get('/certificate/{certificateId}/download', [CertificateController::class, 'download'])->middleware(['auth'])->name('certificate.download');
```

---

## FASE 2: Views/Blade Templates

### Admin Views
- [ ] `resources/views/admin/categories/index.blade.php` - List categories
- [ ] `resources/views/admin/categories/create.blade.php` - Create form
- [ ] `resources/views/admin/categories/edit.blade.php` - Edit form

- [ ] `resources/views/admin/modules/index.blade.php` - Module manager
- [ ] `resources/views/admin/modules/create.blade.php` - Add module
- [ ] `resources/views/admin/modules/materials.blade.php` - Material manager

- [ ] `resources/views/admin/certificates/index.blade.php` - Certificate list
- [ ] `resources/views/admin/certificates/templates.blade.php` - Template manager
- [ ] `resources/views/admin/certificates/template-form.blade.php` - Template form

- [ ] `resources/views/admin/financial/dashboard.blade.php` - Financial dashboard
- [ ] `resources/views/admin/financial/courses.blade.php` - Course performance
- [ ] `resources/views/admin/financial/refunds.blade.php` - Refund management
- [ ] `resources/views/admin/financial/report-export.blade.php` - Export tool

### Instructor Views
- [ ] `resources/views/instructor/dashboard.blade.php` - Instructor stats

### Student Views
- [ ] `resources/views/certificates/template.blade.php` - Certificate template for PDF
- [ ] Update course detail view untuk show modules
- [ ] Add rating form after course completion

---

## FASE 3: Security & Testing

### Input Validation
- [ ] Create FormRequest classes
  - [ ] `app/Http/Requests/StoreCategoryRequest.php`
  - [ ] `app/Http/Requests/StoreModuleRequest.php`
  - [ ] `app/Http/Requests/StoreRefundRequest.php`
  - etc.

### Security Checks
- [ ] Add CSRF @csrf ke all forms
- [ ] Escape all output {{ }} di Blade
- [ ] Validate user permissions (auth, role)
- [ ] Sanitize file uploads (for materials)
- [ ] Validate external URLs (gdrive links, etc)

### Testing
- [ ] Create unit tests untuk services
- [ ] Create feature tests untuk controllers
- [ ] Run OWASP ZAP security scan
- [ ] Test payment refund flow

---

## FASE 4: Installation & Deployment

### Dependencies
```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```

### Database
```bash
php artisan migrate
# Seed default categories if needed
php artisan db:seed CategorySeeder
```

### File Uploads
- [ ] Create storage directories: `storage/app/public/certificates`
- [ ] Create storage directories: `storage/app/public/materials`

### Configuration
- [ ] Update `.env` dengan HTTPS URLs
- [ ] Set PDF generation config in `config/dompdf.php`

---

## PROGRESS TRACKING

### ✅ COMPLETED
- [x] Models & Migrations
- [x] Services (Financial, Certificate, Report)
- [x] Model relationships
- [x] Database schema

### ⏳ IN PROGRESS
- [ ] Controllers
- [ ] Routes

### ⛔ NOT STARTED
- [ ] Views
- [ ] Security
- [ ] Testing
- [ ] Deployment

---

## ESTIMATED TIMELINE

| Task | Est. Time | Priority |
|------|-----------|----------|
| Controllers | 4 hours | HIGH |
| Routes | 1 hour | HIGH |
| Admin Views | 6 hours | MEDIUM |
| Instructor/Student Views | 3 hours | MEDIUM |
| Security | 3 hours | HIGH |
| Testing | 4 hours | MEDIUM |
| **TOTAL** | **~21 hours** | - |

---

## NOTES

1. **Modular Design**: Each controller handles its own domain
2. **Service Layer**: Business logic in services, reusable
3. **View Inheritance**: Use master layout untuk consistency
4. **Error Handling**: Add try-catch untuk critical operations
5. **Logging**: Log important actions untuk audit trail

---

**Last Updated**: December 1, 2025
**Status**: READY FOR PHASE 2 (Controllers)
