# 📋 Feature Implementation Checklist - Based on Kefas Feedback (12 Nov 2025)

**Source:** Chat messages from Kefas Hutabarat  
**Date:** 12 Nov 2025 (11:49 - 12:25)  
**Priority Levels:** 🔴 Critical | 🟠 High | 🟡 Medium | 🟢 Low

---

## 🔴 CRITICAL - BLOCKING FEATURES

### 1. **Quiz/Ujian System** 🔴
**Priority:** Critical - Mentioned explicitly multiple times  
**Messages:** 11:54 AM

#### Instructor Side (Fitur Instruktur)
- [ ] Tambahin fitur quiz
- [ ] Instruktur bisa nambahin soal (add questions)
- [ ] Instruktur bisa nentuin jawaban benar (set correct answers)
- [ ] Auto-grading hasil (auto-grading)
- [ ] Remove max_points dari create assignment (di create new assignment fitur max point di hapus)

**Implementation Files Needed:**
- [ ] `QuizController@create`, `@store`, `@edit`, `@update`, `@destroy` (instructor)
- [ ] `QuizController@show`, `@submit` (student)
- [ ] Route: `POST /instructor/courses/{id}/quizzes`
- [ ] Route: `POST /quiz/{quiz_id}/submit`
- [ ] View: Quiz creation form (instructor)
- [ ] View: Quiz taking interface (student)
- [ ] View: Results display
- [ ] Service: `QuizGradingService`

**Database:** Already created (`Quiz`, `QuizQuestion`, `QuizSubmission` tables)  
**Estimated Time:** 10-12 hours

---

### 2. **Assignment Submission - MASALAH UTAMA** 🔴
**Priority:** Critical - User Feature Broken  
**Message:** 11:54 AM - "Fitur user: Submit assignment belum berfungsi"

- [ ] Fix assignment submission (student side)
- [ ] Implement file upload
- [ ] Show submission status to student
- [ ] Show instructor feedback

**Implementation Files Needed:**
- [ ] `StudentController@submitAssignment`
- [ ] Route: `POST /student/assignments/{id}/submit`
- [ ] View: Assignment submission form with file upload
- [ ] Validation for file type/size

**Database:** Already created (`AssignmentSubmission` table)  
**Estimated Time:** 4-6 hours

---

### 3. **View Submission Dashboard (Instruktur)** 🔴
**Priority:** High - Core Instructor Feature  
**Message:** 11:54 AM - "View submission (Nama peserta yang sudah atau belum submit tugas)"

- [ ] Show student names who submitted
- [ ] Show student names who haven't submitted
- [ ] Filter by assignment
- [ ] View individual submission details
- [ ] Show submission timestamps

**Implementation Files Needed:**
- [ ] `InstructorController@viewSubmissions`
- [ ] Route: `GET /instructor/courses/{id}/submissions`
- [ ] Route: `GET /instructor/assignments/{id}/submissions`
- [ ] View: Submission dashboard

**Estimated Time:** 4-5 hours

---

### 4. **Edit Assignment Deadline (Instruktur)** 🔴
**Priority:** High - Instructor Management  
**Message:** 11:54 AM - "Edit deadline tugas"

- [ ] Edit deadline from instructor dashboard
- [ ] Show current deadline
- [ ] Notify students of deadline change
- [ ] Bulk edit (multiple assignments)

**Implementation Files Needed:**
- [ ] `AssignmentController@updateDeadline` OR `@update`
- [ ] Route: `PUT /instructor/assignments/{id}`
- [ ] View: Inline edit or modal form
- [ ] Notification system

**Estimated Time:** 3-4 hours

---

## 🟠 HIGH PRIORITY - COURSE MANAGEMENT

### 5. **Course Duration Setup** 🟠
**Priority:** High - Core Feature  
**Message:** 11:49 AM - "Durasi Kursus"

- [ ] Set course duration in days
- [ ] Store in `courses.duration_days`
- [ ] Calculate expiry date
- [ ] Show remaining days to students

**Implementation Files Needed:**
- [ ] `InstructorController@setDuration` OR admin course form
- [ ] Route: `PUT /instructor/courses/{id}/set-duration`
- [ ] View: Duration input form
- [ ] Validation: Positive number, reasonable range

**Database:** Column exists  
**Estimated Time:** 2-3 hours

---

### 6. **Course Rescheduling** 🟠
**Priority:** High - Hybrid/Tatap Muka Feature  
**Message:** 11:49 AM - "Reschedule kursus (hybrid/tatap muka)"

- [ ] Reschedule courses
- [ ] Show reschedule reason
- [ ] Label unavailable courses (Tidak Dapat diakses)
- [ ] Auto-label if quota not met

**Implementation Files Needed:**
- [ ] `InstructorController@reschedule` OR `@updateSchedule`
- [ ] Route: `PUT /instructor/courses/{id}/reschedule`
- [ ] View: Reschedule form
- [ ] Logic: Check quota, set availability flag
- [ ] Badge: "Tidak Dapat diakses" display

**Database:** Columns exist (`reschedule_reason`, `is_rescheduled`, `is_accessible`)  
**Estimated Time:** 4-5 hours

---

### 7. **Course Class Creation (Hybrid/Tatap Muka)** 🟠
**Priority:** High - Hybrid/Offline Support  
**Message:** 11:49 AM - "Kursus dengan tipe (hybrid/tatap muka) ada fitur pembuatan kelas"

- [ ] Create classes for hybrid/tatap muka courses
- [ ] Set room/location
- [ ] Set start/end dates
- [ ] Set start/end times
- [ ] Set days of week (Mon, Tue, Wed, etc.)
- [ ] Set max students quota
- [ ] Track current enrollment

**Implementation Files Needed:**
- [ ] `CourseClassController` (CRUD)
- [ ] Routes: `POST/PUT/DELETE /instructor/courses/{id}/classes`
- [ ] View: Class management form
- [ ] Validation: Time ranges, dates, capacity

**Database:** `course_classes` table exists with all fields  
**Estimated Time:** 6-8 hours

---

## 🟡 MEDIUM PRIORITY - ENGAGEMENT FEATURES

### 8. **Forum Diskusi** 🟡
**Priority:** Medium - Community Feature  
**Message:** 12:17 PM - "Forum diskusi di dalam kursus"

- [ ] Tambah topik (Subject, message, image, video)
- [ ] Peserta bisa ngirim pesan
- [ ] Instruktur bisa ngirim pesan
- [ ] Peserta dan instruktur bisa balas pesan
- [ ] Nested replies support
- [ ] Image/video upload

**Implementation Files Needed:**
- [ ] `ForumController` (CRUD topics, replies)
- [ ] Routes: `POST/PUT/DELETE /courses/{id}/forum`
- [ ] View: Forum thread list
- [ ] View: Thread detail with replies
- [ ] View: Reply form with file upload
- [ ] Storage: Image/video handling

**Database:** `course_forums`, `forum_replies` tables exist  
**Estimated Time:** 10-12 hours

---

### 9. **Course Review & Rating** 🟡
**Priority:** Medium - Quality Feedback  
**Message:** 12:20 PM - "di our course ada rating dan feedbacknya"

- [ ] Students can rate courses (1-5 stars)
- [ ] Students can write review text
- [ ] Display rating on course listing
- [ ] Display review text on course detail
- [ ] Show average rating
- [ ] Filter courses by rating

**Implementation Files Needed:**
- [ ] `ReviewController` OR methods in `StudentController`
- [ ] Routes: `POST/PUT/DELETE /courses/{id}/review`
- [ ] View: Rating form with stars
- [ ] View: Review display
- [ ] Logic: Prevent duplicate reviews, rating calculation

**Database:** `course_reviews` table exists  
**Estimated Time:** 5-6 hours

---

### 10. **Course Participants View** 🟡
**Priority:** Medium - Instructor Insight  
**Message:** 12:13 PM - "Di kursus bisa view pesertanya siapa aja"

- [ ] Instructor sees all enrolled students
- [ ] Show student names, emails
- [ ] Show enrollment date
- [ ] Show progress percentage
- [ ] Show completion status
- [ ] Sorting/filtering

**Implementation Files Needed:**
- [ ] `InstructorController@viewParticipants`
- [ ] Route: `GET /instructor/courses/{id}/participants`
- [ ] View: Participants table

**Estimated Time:** 3-4 hours

---

## 🟢 LOWER PRIORITY - POLISH & OPTIMIZATION

### 11. **Course Header - Full Width** 🟢
**Priority:** Low - UI Polish  
**Message:** 11:56 AM - "Header kursus di lebarin sampai mentok layer (yang ungu)"

- [ ] Remove max-width constraint from course header
- [ ] Stretch to full viewport width
- [ ] Maintain purple gradient background
- [ ] Adjust padding/spacing

**Files Affected:** `course-detail.blade.php`, CSS adjustments  
**Estimated Time:** 1-2 hours

---

### 12. **Update Progress - Instructor Dashboard** 🟢
**Priority:** Medium - Instructor Management  
**Message:** 11:58 AM - "Fitur update progress peserta di page instruktur lanjutin"

- [ ] Show student progress list
- [ ] Update progress slider/input
- [ ] Save changes
- [ ] Bulk update option
- [ ] Progress history/audit trail

**Implementation Files Needed:**
- [ ] `InstructorController@updateProgress`
- [ ] Route: `PUT /instructor/students/{id}/progress`
- [ ] View: Progress management dashboard
- [ ] Validation: 0-100% range

**Database:** `course_registrations.progress` exists  
**Estimated Time:** 3-4 hours

---

### 13. **Certificate Download** 🟢
**Priority:** Medium - Achievement Recognition  
**Message:** 11:59 AM - "kalau progress peserta 100, bisa download file sertifikat"

- [ ] Generate certificate when progress = 100%
- [ ] Download as PDF
- [ ] Personalize with student name and course name
- [ ] Include completion date
- [ ] Add verification code (optional)

**Implementation Files Needed:**
- [ ] `CertificateController@download`
- [ ] Route: `GET /certificates/{course_id}/download`
- [ ] PDF template/blade view
- [ ] Service: `CertificateService` for PDF generation
- [ ] Library: `barryvdh/laravel-dompdf`

**Database:** `certificates` table exists  
**Estimated Time:** 5-6 hours

---

### 14. **Profile Management (CRUD)** 🟢
**Priority:** Medium - User Personalization  
**Message:** 12:00 PM - "Fitur profile peserta: CRUD"

- [ ] View profile
- [ ] Edit profile information (name, email, phone, address, etc.)
- [ ] Upload profile picture
- [ ] Delete profile (optional)
- [ ] Show profile statistics

**Implementation Files Needed:**
- [ ] `ProfileController` OR `StudentController` methods
- [ ] Routes: `GET/PUT /profile`, `POST /profile/picture`
- [ ] View: Profile display
- [ ] View: Profile edit form
- [ ] Image upload & validation

**Database:** User profile fields in `users` table  
**Estimated Time:** 4-5 hours

---

### 15. **Payment Form Optimization** 🟢
**Priority:** Medium - UX Improvement  
**Message:** 12:05 AM - "Form pendaftaran kursus langsung bayar"

- [ ] Direct payment flow (no extra data entry)
- [ ] Auto-populate from logged-in user
- [ ] Select payment method (dropdown)
- [ ] Show payment methods: Bank Transfer, Credit Card, GoPay, ShopeePay
- [ ] Real-time price calculation
- [ ] Show total in local currency

**Files Affected:** Payment/Checkout views  
**Existing:** Midtrans integration done  
**Estimated Time:** 2-3 hours

---

### 16. **Voucher/Discount Code System** 🟢
**Priority:** Medium - Revenue Optimization  
**Message:** 12:06 AM - "fitur nambahin discount pakai voucher"

- [ ] Input voucher code field
- [ ] Validate voucher (check if exists, not expired)
- [ ] Calculate discount percentage
- [ ] Show discounted price
- [ ] Apply discount to final price
- [ ] Error messages for invalid codes

**Implementation Files Needed:**
- [ ] Voucher model and table (if not exists)
- [ ] `CheckoutController@validateVoucher`
- [ ] Route: `POST /checkout/validate-voucher`
- [ ] View: Voucher input field

**Existing:** Basic voucher logic exists  
**Estimated Time:** 2-3 hours

---

### 17. **Notification System** 🟢
**Priority:** Medium - User Engagement  
**Message:** 12:25 AM - "Fitur Notifikasi Peserta"

**Types of Notifications:**
- [ ] New course purchased
- [ ] Course duration expiring soon
- [ ] New material posted
- [ ] New assignment/task posted
- [ ] New quiz/exam posted
- [ ] Assignment/task/quiz deadline approaching
- [ ] Work completed (assignment graded)
- [ ] Work read (material viewed)

**Implementation Files Needed:**
- [ ] `Notification` model (if not exists)
- [ ] Migration: `notifications` table, `notification_reads` table
- [ ] `NotificationService` for sending
- [ ] Queue jobs: `SendNotificationJob`
- [ ] Routes: `GET /notifications`, `POST /notification/{id}/read`
- [ ] View: Notification center page
- [ ] Integration with navbar bell

**Estimated Time:** 10-12 hours (including queue setup)

---

### 18. **Course Extension/Renewal** 🟢
**Priority:** Low - Nice to Have  
**Message:** 12:11 AM - "Tambahin fitur refund di user"

- [ ] Request course extension
- [ ] Show remaining days
- [ ] Pay to extend access
- [ ] Update expiry date
- [ ] Email confirmation

**Implementation Files Needed:**
- [ ] `CourseExtensionController`
- [ ] Routes: `POST /courses/{id}/extend`
- [ ] View: Extension request form
- [ ] Payment integration

**Estimated Time:** 4-5 hours

---

### 19. **Refund System (User Side)** 🟢
**Priority:** Medium - User Feature  
**Message:** 12:11 AM - "Tambahin fitur refund di user"

- [ ] Request refund for purchased course
- [ ] Set refund reason
- [ ] Show refund status (pending/approved/rejected)
- [ ] View refund history
- [ ] Show refund amount
- [ ] Track processing time

**Implementation Files Needed:**
- [ ] `RefundController@create`, `@store`, `@show`, `@index`
- [ ] Routes: `GET/POST /refunds`
- [ ] View: Refund request form
- [ ] View: Refund status page
- [ ] View: Refund history list

**Database:** `refunds` table exists (created from migration)  
**Estimated Time:** 4-5 hours

---

## 🗂️ Implementation Prioritization

### Phase 1 - THIS WEEK (Critical Blocking)
1. Fix assignment submission (4-6 hrs)
2. Quiz system instructor (6-8 hrs)
3. Quiz system student (4-6 hrs)
4. Auto-grading logic (2-3 hrs)

**Phase 1 Total:** ~20 hours

---

### Phase 2 - NEXT WEEK (High Priority)
1. View submission dashboard (4-5 hrs)
2. Edit deadline form (3-4 hrs)
3. Course duration setup (2-3 hrs)
4. Course rescheduling (4-5 hrs)
5. Course class management (6-8 hrs)

**Phase 2 Total:** ~22 hours

---

### Phase 3 - FOLLOWING WEEK (Medium Priority)
1. Forum diskusi (10-12 hrs)
2. Course reviews & rating (5-6 hrs)
3. Progress update UI (3-4 hrs)
4. Participants view (3-4 hrs)

**Phase 3 Total:** ~23 hours

---

### Phase 4 - LATER (Nice to Have)
1. Certificate download (5-6 hrs)
2. Profile management (4-5 hrs)
3. Payment optimization (2-3 hrs)
4. Voucher system (2-3 hrs)
5. Notification system (10-12 hrs)
6. Course extension (4-5 hrs)
7. Refund user side (4-5 hrs)
8. UI polish (1-2 hrs)

**Phase 4 Total:** ~37 hours

---

## 📊 Grand Summary

| Item | Quantity | Estimated Hours |
|------|----------|-----------------|
| Critical Bugs | 1 | 4-6 |
| Critical Features | 2 | 18-24 |
| High Priority Features | 5 | 18-24 |
| Medium Priority Features | 7 | 32-40 |
| Low Priority/Polish | 7 | 23-32 |
| **TOTAL** | **22** | **95-126 hours** |

**Estimated Timeline (2-3 week sprints):**
- Phase 1: 1 week
- Phase 2: 1 week  
- Phase 3: 1.5 weeks
- Phase 4: 2-3 weeks

**Total:** 5-6.5 weeks for full implementation

---

## ✅ NEXT STEP: Start Phase 1

Begin with:
1. Fix assignment submission form
2. Create QuizController with instructor methods
3. Add create quiz route/form
4. Implement auto-grading logic

**Estimated completion:** 3-4 days for Phase 1

---

**Document Generated:** 17 Nov 2025  
**Based on Feedback:** Kefas Hutabarat (12 Nov 2025)  
**Maintained by:** Development Team
