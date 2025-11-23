# 📌 Development Todo Checklist - OtakAtik

**Last Updated:** Nov 17, 2025  
**Total Items:** 22 features  
**Critical Items:** 4  
**Estimated Hours:** 95-126  

---

## 🔴 PHASE 1 - CRITICAL (This Week) - ~20 hours

### 1. Fix Assignment Submission 🔴 BLOCKING
- [ ] Create assignment submission form view
- [ ] Implement file upload validation
- [ ] Create `StudentController@submitAssignment` method
- [ ] Add route: `POST /student/assignments/{id}/submit`
- [ ] Add file storage configuration
- [ ] Show success/error messages
- [ ] Create `resources/views/assignment-submit.blade.php`
- [ ] Test file upload (PDF, images)
- **Files:** `AssignmentSubmission` model (exists), Controller (new), Route (new), View (new)
- **Estimated:** 4-6 hours

### 2. Quiz System - Instructor Side 🔴 HIGH IMPACT
- [ ] Create `QuizController` class
- [ ] Implement `create()` method (show quiz creation form)
- [ ] Implement `store()` method (save quiz)
- [ ] Implement `edit()` method
- [ ] Implement `update()` method
- [ ] Implement `destroy()` method
- [ ] Create view: `resources/views/instructor/quiz-create.blade.php`
- [ ] Create view: `resources/views/instructor/quiz-edit.blade.php`
- [ ] Add routes in `routes/web.php`:
  - `POST /instructor/courses/{id}/quizzes`
  - `PUT /instructor/quizzes/{id}`
  - `DELETE /instructor/quizzes/{id}`
- [ ] Implement question add/edit/delete within form
- [ ] Add max points per question field
- [ ] Validation: Question types, correct answers
- **Database:** Quiz, QuizQuestion tables (exist)
- **Files:** `QuizController` (new), Views (new)
- **Estimated:** 6-8 hours

### 3. Quiz System - Student Side 🔴 HIGH IMPACT
- [ ] Implement `show()` method for student
- [ ] Implement `submit()` method for student
- [ ] Create view: `resources/views/student/quiz-take.blade.php`
- [ ] Create view: `resources/views/student/quiz-results.blade.php`
- [ ] Add timer functionality (if time limit set)
- [ ] Validate all answers are submitted
- [ ] Add routes:
  - `GET /courses/{id}/quiz/{quiz_id}`
  - `POST /quiz/{quiz_id}/submit`
- [ ] Show loading state during submission
- **Database:** QuizSubmission table (exists)
- **Estimated:** 4-6 hours

### 4. Auto-Grading Logic 🔴 CRITICAL
- [ ] Create `QuizGradingService` class
- [ ] Implement auto-grade method for multiple choice
- [ ] Implement auto-grade method for true/false
- [ ] Handle essay questions (mark as needs grading)
- [ ] Calculate total score
- [ ] Store score in `QuizSubmission.score`
- [ ] Create feedback message
- [ ] Test grading accuracy
- **Files:** `QuizGradingService` (new), adjust `QuizController`
- **Estimated:** 2-3 hours

---

## 🟠 PHASE 2 - HIGH PRIORITY (Next Week) - ~22 hours

### 5. View Submission Dashboard 🟠 HIGH
- [ ] Create `InstructorController@viewSubmissions` method
- [ ] Get all submissions for assignment
- [ ] Get all assignments for course
- [ ] Filter by assignment
- [ ] Show student names (submitted vs not submitted)
- [ ] Show submission dates/times
- [ ] Create view: `resources/views/instructor/submissions-dashboard.blade.php`
- [ ] Add sorting options (name, submission date, status)
- [ ] Add bulk actions (resend reminder, extend deadline)
- [ ] Add routes:
  - `GET /instructor/courses/{id}/submissions`
  - `GET /instructor/assignments/{id}/submissions`
- **Database:** Query AssignmentSubmission table
- **Files:** Controller method (add), View (new)
- **Estimated:** 4-5 hours

### 6. Edit Assignment Deadline 🟠 HIGH
- [ ] Create form/modal for deadline editing
- [ ] Implement `AssignmentController@updateDeadline` OR use `update()`
- [ ] Add validation for deadline (must be future date)
- [ ] Add route: `PUT /instructor/assignments/{id}`
- [ ] Show current deadline
- [ ] Allow bulk deadline changes
- [ ] Send notification to students on change
- [ ] Confirm before save
- [ ] Create view: `resources/views/instructor/edit-deadline-modal.blade.php`
- **Database:** `course_assignments.due_date` (exists)
- **Files:** Controller method, View (modal)
- **Estimated:** 3-4 hours

### 7. Course Duration Setup 🟠 HIGH
- [ ] Add field to course creation form
- [ ] Add field to course edit form
- [ ] Implement `InstructorController` method to set duration
- [ ] Add route: `PUT /instructor/courses/{id}/set-duration`
- [ ] Validation: Must be positive number
- [ ] Calculate expiry date (created_at + duration_days)
- [ ] Show duration to students
- [ ] Add to course detail view
- **Database:** `courses.duration_days` (exists)
- **Files:** Course form, Controller method
- **Estimated:** 2-3 hours

### 8. Course Rescheduling 🟠 HIGH
- [ ] Create reschedule form/modal
- [ ] Implement reschedule logic
- [ ] Add validation for new dates
- [ ] Update `is_rescheduled` and `reschedule_reason`
- [ ] Auto-check quota: if current < min, set `is_accessible = false`
- [ ] Add "Tidak Dapat diakses" badge
- [ ] Notify students of reschedule
- [ ] Add route: `PUT /instructor/courses/{id}/reschedule`
- [ ] Show reschedule reason to students
- **Database:** `courses.reschedule_reason`, `is_rescheduled`, `is_accessible`
- **Files:** Controller, View (form/modal)
- **Estimated:** 4-5 hours

### 9. Course Class Management 🟠 HIGH
- [ ] Create `CourseClassController` (CRUD)
- [ ] Implement `create()` and `store()`
- [ ] Implement `edit()` and `update()`
- [ ] Implement `destroy()`
- [ ] Form validation:
  - Required fields: class_name, room, start_date, end_date
  - Start time < end time
  - Start date < end date
  - Max students > 0
- [ ] Create views: create, edit, list
- [ ] Add routes:
  - `POST /instructor/courses/{id}/classes`
  - `PUT /instructor/classes/{id}`
  - `DELETE /instructor/classes/{id}`
  - `GET /instructor/courses/{id}/classes`
- [ ] Show current enrollment vs max capacity
- [ ] Add to course detail
- **Database:** `course_classes` table (exists)
- **Files:** `CourseClassController` (new), Views (new)
- **Estimated:** 6-8 hours

---

## 🟡 PHASE 3 - MEDIUM PRIORITY (Following Week) - ~23 hours

### 10. Forum Diskusi - Topics 🟡 MEDIUM
- [ ] Create `ForumController`
- [ ] Implement topic creation
- [ ] Implement topic editing
- [ ] Implement topic deletion
- [ ] Add file upload (image, video)
- [ ] Create form view: `resources/views/courses/forum-create.blade.php`
- [ ] Create list view: `resources/views/courses/forum-list.blade.php`
- [ ] Create detail view: `resources/views/courses/forum-detail.blade.php`
- [ ] Add permissions: students and instructors can post
- [ ] Add sorting (newest, most replied, etc.)
- [ ] Add routes:
  - `GET /courses/{id}/forum`
  - `POST /courses/{id}/forum`
  - `PUT /forum/{id}`
  - `DELETE /forum/{id}`
- **Database:** `course_forums` table (exists)
- **Estimated:** 5-6 hours

### 11. Forum Diskusi - Replies 🟡 MEDIUM
- [ ] Implement reply creation
- [ ] Implement reply editing
- [ ] Implement reply deletion
- [ ] Support nested replies (replies to replies)
- [ ] Add permissions checking
- [ ] Display threaded comments
- [ ] Add routes:
  - `POST /forum/{id}/reply`
  - `PUT /reply/{id}`
  - `DELETE /reply/{id}`
- [ ] Pagination for many replies
- **Database:** `forum_replies` table (exists)
- **Estimated:** 4-5 hours

### 12. Course Review & Rating 🟡 MEDIUM
- [ ] Create `ReviewController` OR add to `StudentController`
- [ ] Implement review creation
- [ ] Implement review editing
- [ ] Implement review deletion
- [ ] Star rating (1-5)
- [ ] Review text
- [ ] Prevent duplicate reviews (one per course per user)
- [ ] Create form view
- [ ] Display reviews on course detail
- [ ] Show average rating
- [ ] Show review count
- [ ] Add routes:
  - `POST /courses/{id}/review`
  - `PUT /review/{id}`
  - `DELETE /review/{id}`
- [ ] Calculate and display average rating
- **Database:** `course_reviews` table (exists)
- **Files:** ReviewController (new) or StudentController (add methods)
- **Estimated:** 4-5 hours

### 13. Participant List - Instructor View 🟡 MEDIUM
- [ ] Create method `InstructorController@viewParticipants`
- [ ] Get all enrolled students for course
- [ ] Show student names, emails
- [ ] Show enrollment date
- [ ] Show current progress
- [ ] Show completion status
- [ ] Add filters: by status (active, completed, dropped)
- [ ] Add sorting: by name, by progress, by enrollment date
- [ ] Create view: `resources/views/instructor/participants.blade.php`
- [ ] Add route: `GET /instructor/courses/{id}/participants`
- [ ] Add download list option (CSV)
- **Database:** Query CourseRegistration, User tables
- **Estimated:** 3-4 hours

### 14. Update Progress - UI 🟡 MEDIUM
- [ ] Create progress update form/modal
- [ ] Implement `InstructorController@updateProgress` method
- [ ] Show slider or percentage input
- [ ] Add route: `PUT /instructor/students/{id}/progress`
- [ ] Validate progress 0-100
- [ ] Show current progress
- [ ] Bulk update option
- [ ] Confirmation before save
- [ ] Create view: `resources/views/instructor/update-progress-modal.blade.php`
- [ ] Show progress update history (optional)
- **Database:** `course_registrations.progress`
- **Estimated:** 3-4 hours

### 15. Course Header - Full Width UI 🟡 LOW (UI ONLY)
- [ ] Remove `max-w-7xl` from course header container
- [ ] Stretch header to full viewport width
- [ ] Adjust padding/spacing for full width
- [ ] Maintain responsive design
- [ ] Update CSS for hero section
- [ ] Test on mobile
- **Files:** `course-detail.blade.php`, CSS
- **Estimated:** 1-2 hours

---

## 🟢 PHASE 4 - NICE TO HAVE (Later) - ~37 hours

### 16. Certificate Generation & Download 🟢 NICE
- [ ] Install package: `composer require barryvdh/laravel-dompdf`
- [ ] Create `CertificateController@download`
- [ ] Create PDF template/view
- [ ] Generate certificate when progress = 100%
- [ ] Personalize with student name, course name, completion date
- [ ] Add verification code (optional)
- [ ] Store certificate in `certificates` table
- [ ] Add route: `GET /certificates/{course_id}/download`
- [ ] Show certificate download button on course detail
- [ ] Add signature image (optional)
- **Database:** `certificates` table (exists)
- **Files:** CertificateController, PDF template
- **Estimated:** 5-6 hours

### 17. Profile Management - CRUD 🟢 NICE
- [ ] Create `ProfileController` OR add to `StudentController`
- [ ] Implement `show()` - display profile
- [ ] Implement `edit()` - show form
- [ ] Implement `update()` - save changes
- [ ] Implement profile picture upload
- [ ] Validate email uniqueness
- [ ] Show profile statistics (courses taken, completions, certificates)
- [ ] Create views: profile, edit form
- [ ] Add routes:
  - `GET /profile`
  - `PUT /profile`
  - `POST /profile/picture`
  - `DELETE /profile/picture`
- [ ] Image resize and optimization
- **Database:** User profile fields
- **Estimated:** 4-5 hours

### 18. Payment Form Optimization 🟢 NICE
- [ ] Review current checkout flow
- [ ] Auto-populate user data
- [ ] Show payment method selection
- [ ] Real-time price calculation
- [ ] Show discount after voucher
- [ ] Improve UX/styling
- [ ] Add payment method icons
- [ ] Optimize for mobile
- [ ] Test with Midtrans sandbox
- **Files:** Checkout view, PaymentController (minor adjustments)
- **Estimated:** 2-3 hours

### 19. Voucher/Discount System 🟢 NICE
- [ ] Create `Voucher` model (if not exists)
- [ ] Migration for vouchers table (if not exists)
- [ ] Implement validation logic
- [ ] Create `CheckoutController@validateVoucher` method
- [ ] Add route: `POST /checkout/validate-voucher`
- [ ] Check: code exists, not expired, not used (if single-use)
- [ ] Calculate discount percentage
- [ ] Return discounted price
- [ ] Add error messages
- **Estimated:** 2-3 hours

### 20. Refund System - User Side 🟢 NICE
- [ ] Create `RefundController`
- [ ] Implement `create()` - show refund form
- [ ] Implement `store()` - submit refund request
- [ ] Implement `show()` - view refund status
- [ ] Implement `index()` - list all refunds
- [ ] Add validation: within refund window (30 days)
- [ ] Add reason field
- [ ] Create views: form, status, history
- [ ] Add routes:
  - `GET /refunds`
  - `GET /refunds/{id}`
  - `POST /refunds`
- [ ] Show status (pending, approved, rejected)
- **Database:** `refunds` table (exists)
- **Files:** RefundController (new), Views (new)
- **Estimated:** 4-5 hours

### 21. Course Extension/Renewal 🟢 NICE
- [ ] Create `CourseExtensionController`
- [ ] Implement `create()` - show extension form
- [ ] Implement `store()` - process extension
- [ ] Show remaining days
- [ ] Calculate extension fee
- [ ] Process payment for extension
- [ ] Update course access expiry
- [ ] Create view: extension form
- [ ] Add route: `POST /courses/{id}/extend`
- [ ] Send confirmation email
- **Estimated:** 4-5 hours

### 22. Notification System 🟢 STRATEGIC
- [ ] Create `Notification` model (if not exists)
- [ ] Migration: `notifications` table
- [ ] Migration: `notification_reads` table
- [ ] Create `NotificationService`
- [ ] Implement notification types:
  - [ ] New course purchased
  - [ ] Course expiring soon
  - [ ] Material posted
  - [ ] Assignment posted
  - [ ] Quiz posted
  - [ ] Deadline approaching
  - [ ] Work graded
  - [ ] Material viewed
- [ ] Create queue job: `SendNotificationJob`
- [ ] Add notification trigger points in controllers
- [ ] Add routes:
  - `GET /notifications`
  - `POST /notification/{id}/read`
- [ ] Integration with navbar bell (dynamic count)
- [ ] Email notifications (optional)
- **Database:** Notification tables (new)
- **Files:** Model, Migration, Service, Job, Routes, Views
- **Estimated:** 10-12 hours

---

## 📊 Summary

| Phase | Duration | Features | Hours | Status |
|-------|----------|----------|-------|--------|
| Phase 1 | Week 1 | 4 Critical | 20 | 🔴 TODO |
| Phase 2 | Week 2 | 5 High | 22 | 🟠 TODO |
| Phase 3 | Week 3 | 6 Medium | 23 | 🟡 TODO |
| Phase 4 | Week 4+ | 7 Nice | 37 | 🟢 TODO |
| **TOTAL** | **5-6.5 wk** | **22** | **102** | **📋** |

---

## ✅ Checkbox Guide

- [x] = Completed
- [ ] = Not started
- [~] = In progress (if needed)

---

## 🎯 Definition of Done

Before marking any item complete:
1. Code written and tested
2. Validation added (if applicable)
3. Error handling implemented
4. Routes tested
5. View renders correctly
6. Mobile responsive (if UI)
7. Database queries optimized
8. Documentation updated
9. Tested with sample data
10. Code review passed

---

## 📝 Notes

- Database migrations are mostly ready
- Use existing models as templates
- Check `routes/web.php` for route groupings
- Follow middleware patterns (auth, instructor, admin)
- Consistent naming conventions
- Add validation on both client and server
- Test payment flows with Midtrans sandbox

---

**Last Updated:** Nov 17, 2025  
**Maintained by:** Development Team  
**Contact:** See `.github/copilot-instructions.md`
