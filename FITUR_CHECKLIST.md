# 🎯 OtakAtik - CHECKLIST FITUR TEMAN (16 Nov 2025)

## ✅ SUDAH DIKERJAIN

### Pembuatan Kursus (60%)
- ✅ Durasi Kursus
- ✅ Tipe Kursus (Online/Hybrid/Tatap Muka)
- ❌ Fitur buat kelas untuk Hybrid/Tatap Muka *(DB ada, UI belum)*
- ❌ Reschedule + label "Tidak dapat diakses" *(logic belum)*

### Fitur Instruktur (40%)
- ⚠️ View submission *(route ada, view belum)*
- ⚠️ Edit deadline tugas *(logic ada, form belum)*
- ❌ Hapus fitur max_points di create assignment *(masih di-validate)*
- ❌ Quiz - buat soal + set jawaban benar *(DB ada, controller belum)*
- ❌ Auto-grading quiz *(logic belum)*
- ⚠️ Update progress peserta *(logic ada, dashboard UI belum)*
- ❌ Download sertifikat saat progress 100% *(DB ada, logic belum)*

### Fitur User (30%)
- ❌ Submit assignment belum berfungsi *(DB ada, form + logic belum)*
- ⚠️ CRUD Profile peserta *(Read + Update ada, Delete belum)*

### Pembayaran & Refund (75%)
- ✅ Form pendaftaran langsung bayar (tanpa isi data pribadi)
- ✅ Pilih payment method (bank, gopay, shope pay, dll)
- ✅ Voucher discount (PROMOPNJ 10%, INSTRUCTOR100 100%)
- ✅ Button checkout
- ✅ Fitur refund user *(submit request, 30-hari window)*

### Misc
- ❌ Header course diperlebar sampai mentok layer ungu *(CSS task)*
- ✅ View peserta di kursus *(route ada, UI belum)*

---

## ❌ BELUM DIKERJAIN

| Fitur | DB | Priority |
|-------|----|----|
| **Quiz - Instruktur buat soal** | ✅ | 🔴 TINGGI |
| **Quiz - Student take exam** | ✅ | 🔴 TINGGI |
| **Quiz - Auto-grading** | ✅ | 🔴 TINGGI |
| **Assignment submit (student)** | ✅ | 🔴 TINGGI |
| **Forum diskusi** | ✅ | 🔴 TINGGI |
| **Review & Rating kursus** | ✅ | 🟡 MEDIUM |
| **Sertifikat download** | ✅ | 🟡 MEDIUM |
| **Notifikasi peserta** | ⚠️ | 🟡 MEDIUM |
| **Perpanjangan kursus** | ❌ | 🟢 RENDAH |

**Legend:**
- ✅ = DB migration + model ready
- ⚠️ = Partial (DB + controller ada, view/form belum)
- ❌ = Belum sama sekali

---

## 📁 KEY FILES UNTUK LANJUTIN

### High Priority (Mulai dari sini)
1. **Quiz Implementation**
   - Buat: `app/Http/Controllers/QuizController.php`
   - Routes di `routes/web.php`: `instructor.quizzes.*`, `student.quiz.*`
   - Views: `resources/views/instructor/quiz/`, `resources/views/student/quiz/`
   - Service: `app/Services/QuizGradingService.php`

2. **Assignment Submission**
   - Update: `app/Http/Controllers/StudentController.php` + method `submitAssignment()`
   - Routes: `POST /student/assignments/{id}/submit`
   - Views: `resources/views/student/assignment-submit.blade.php`

3. **Forum Diskusi**
   - Buat: `app/Http/Controllers/ForumController.php`
   - Models exist: `CourseForum.php`, `ForumReply.php`
   - Routes: `/course/{courseId}/forum/*`
   - Views: Topic list, reply form

### Medium Priority
4. **View Submission Dashboard (Instruktur)**
   - File: `app/Http/Controllers/InstructorController.php` (method `assignmentSubmissions()` stub ada)
   - Perlu: `resources/views/instructor/assignment-submissions.blade.php`

5. **Sertifikat Download**
   - Method di `StudentController::downloadCertificate()`
   - Service: PDF generation (gunakan `barryvdh/laravel-dompdf`)

---

## 🔗 Useful References

**Routes & Middleware** (dari teman):
- Admin: `Route::middleware(['auth', 'admin'])->prefix('admin')->...`
- Instructor: `Route::middleware(['auth', 'instructor'])->prefix('instructor')->...`
- Student: `Route::middleware(['auth'])->prefix('student')->...`

**Payment Webhook** (Midtrans):
- Endpoint: `POST /checkout/notification` → `PaymentController::handleNotification()`
- Jangan ubah URL/method tanpa test!

**Database Connection:**
- Current: Oracle
- TODO: Ganti ke MySQL/phpMyAdmin (temporary) → PostgreSQL (final)

---

## 🚀 Commands

```bash
# Dev (watch mode)
composer dev

# Tests
composer test

# Fresh migrate
php artisan migrate:fresh
```

---

**Last Checked:** 16 Nov 2025 | **Overall Completion:** ~40%
