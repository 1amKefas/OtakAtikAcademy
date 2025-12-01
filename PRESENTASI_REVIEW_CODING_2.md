# 📊 PRESENTASI REVIEW CODING 2 - QUICK VERSION

## 🎯 Ringkasan Singkat Arsitektur

### MVC Pattern Flow
```
REQUEST → ROUTING → MIDDLEWARE → CONTROLLER → MODEL/SERVICE → VIEW → RESPONSE
```

---

## 1️⃣ SKENARIO LOGIN

**USER FLOW:**
```
GET /login (View form) 
    ↓
POST /login (Submit)
    ↓
AuthController@login()
    ├─ Validate input
    ├─ Find user by email
    ├─ Check password
    ├─ Check email verified
    ├─ Create session
    └─ Redirect to dashboard
```

**KEY POINTS:**
- ✅ Password di-hash dengan `bcrypt()`
- ✅ Session management otomatis
- ✅ Email harus verified
- ✅ Remember me dengan cookie

---

## 2️⃣ SKENARIO REGISTER

**USER FLOW:**
```
GET /register (View form)
    ↓
POST /register (Submit)
    ↓
AuthController@register()
    ├─ Validate: name, email, password
    ├─ Create user (password bcrypt)
    ├─ Send verification email
    └─ Redirect ke verification notice
    
User buka email → Klik link verified
    ↓
GET /email/verify/{id}/{hash}
    ├─ Verify hash & signature
    ├─ Mark email as verified
    ├─ Auto-login user
    └─ Redirect dashboard
```

**KEY POINTS:**
- ✅ Email unik validation
- ✅ Password confirmation
- ✅ Signed URL untuk security
- ✅ Auto-login after verification

---

## 3️⃣ SKENARIO BROWSE COURSES

**USER FLOW:**
```
GET /course (List all courses)
    ↓
CourseController@showCourse()
    ├─ Get all active courses
    ├─ With instructor data
    ├─ Check enrollment status per course
    └─ Return view
    
@foreach ($courses as $course)
    ├─ Display thumbnail, title, price
    ├─ If enrolled → Show "Already enrolled"
    └─ If not → Show "Enroll now" button
```

**LIHAT DETAIL:**
```
GET /course/{id}
    ↓
CourseController@show($id)
    ├─ Get course with materials
    ├─ Get instructor info
    ├─ Check if user enrolled
    ├─ Get reviews & ratings
    └─ Return detailed view
```

**KEY POINTS:**
- ✅ Lazy load materials (hanya jika sudah enroll)
- ✅ Show progress untuk enrolled users
- ✅ Reviews & ratings display
- ✅ Related courses recommendation

---

## 4️⃣ SKENARIO CHECKOUT & PAYMENT

**USER FLOW:**
```
GET /checkout/{courseId}
    ↓
PaymentController@checkout()
    ├─ Check course exists
    ├─ Check not already enrolled
    ├─ Calculate price (with voucher)
    └─ Show checkout form

POST /checkout/voucher-check (AJAX)
    └─ Validate voucher → Return discount

POST /checkout/process/{courseId}
    ├─ Create Order record (status: pending)
    ├─ Call MidtransService
    │   └─ Generate snap token
    ├─ Return snap token to view
    └─ Load Midtrans payment gateway

User pilih payment method & bayar
    ↓
Midtrans detect settlement
    ↓
Midtrans POST /checkout/notification
    ├─ Verify signature
    ├─ Update Order → status: paid
    ├─ Create CourseRegistration
    ├─ Send confirmation email
    ├─ Award achievement (if applicable)
    └─ Return success to Midtrans

Student terima email & akses course
```

**KEY POINTS:**
- ✅ Signature verification mencegah fraud
- ✅ Idempotent webhook (safe jika dikirim 2x)
- ✅ Order status tracking
- ✅ Email confirmation otomatis

---

## 5️⃣ SKENARIO STUDENT BELAJAR

**USER FLOW:**
```
GET /student/course/{registrationId}
    ↓
StudentController@courseDetail()
    ├─ Get course & materials
    ├─ Calculate progress
    ├─ Get assignments & quizzes
    └─ Return course detail page

Tabs: Materials | Assignments | Quizzes | Forum | Certificate

USER DOWNLOAD MATERIAL:
    └─ Download link ke file (PDF/DOC)

USER SUBMIT ASSIGNMENT:
    GET /student/assignments/{id}/submit
        ├─ Show form
        ├─ Show previous submission (if any)
        └─ Show deadline
    
    POST /student/assignments/{id}/submit
        ├─ Validate answer & file
        ├─ Check deadline
        ├─ Save submission
        └─ Notify instructor
    
    INSTRUCTOR GRADE:
        PUT /instructor/submissions/{id}/grade
            ├─ Update grade & feedback
            ├─ Update course progress
            └─ Notify student

USER TAKE QUIZ:
    POST /student/course/{courseId}/quiz/{quizId}/start
        ├─ Create QuizSubmission
        └─ Redirect ke quiz attempt
    
    GET /student/course/{courseId}/quiz/{quizId}/submission/{subId}
        ├─ Load questions
        ├─ Start timer
        └─ Show progress
    
    POST /student/course/{courseId}/quiz/{quizId}/submission/{subId}/submit
        ├─ Calculate score (auto-grade multiple choice)
        ├─ Mark as completed
        ├─ Update course progress
        └─ Award achievement
    
    GET /student/course/{courseId}/quiz/{quizId}/submission/{subId}/result
        ├─ Show score & percentage
        ├─ Show answer review
        └─ Show feedback
```

**KEY POINTS:**
- ✅ Progress auto-update
- ✅ Deadline validation
- ✅ Quiz timer server-side (prevent cheating)
- ✅ Auto-grading untuk multiple choice
- ✅ Manual grading untuk essay

---

## 6️⃣ SKENARIO ADMIN MANAGEMENT

**USER FLOW:**
```
GET /admin/dashboard
    ├─ Statistics (total users, courses, revenue)
    ├─ Charts (enrollment, revenue, growth)
    └─ Recent activities

GET /admin/users
    ├─ List users dengan pagination
    ├─ Search & filter by role
    ├─ Show courses enrolled & total spent
    
UPDATE USER ROLE:
    PUT /admin/users/{id}/role
        ├─ Validate role
        ├─ Update user role
        ├─ Send notification email
        └─ Log activity

DELETE USER:
    DELETE /admin/users/{id}
        ├─ Check not only admin
        ├─ Delete cascade (orders, submissions, etc)
        └─ Redirect

GET /admin/courses
    ├─ List all courses
    ├─ Show enrollment count
    ├─ Show revenue per course

CREATE/EDIT/DELETE COURSES:
    POST/PUT/DELETE /admin/courses
        ├─ Validate input
        ├─ Upload thumbnail
        ├─ Update/create record
        └─ Log changes

GET /admin/financial
    ├─ Revenue reports
    ├─ Filter by date range
    ├─ Group by course/instructor
    └─ Export to CSV

GET /admin/refund
    ├─ List refund requests
    ├─ Review reason & proof
    
PROCESS REFUND:
    PUT /admin/refund/{id}/process
        ├─ Validate refund amount
        ├─ Call payment gateway refund API
        ├─ Update refund status
        ├─ Send confirmation email
        └─ Update financial report
```

**KEY POINTS:**
- ✅ Role-based access control
- ✅ Pagination untuk performance
- ✅ Real-time statistics
- ✅ Audit logging untuk compliance
- ✅ Refund integration dengan payment gateway

---

## 🏗️ STRUKTUR KODE KEY FILES

### Model Example
```php
class Course extends Model {
    public function instructor() {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    
    public function materials() {
        return $this->hasMany(Material::class);
    }
    
    public function students() {
        return $this->belongsToMany(
            User::class, 
            'course_registrations'
        );
    }
}
```

### Controller Example
```php
class CourseController extends Controller {
    public function show($id) {
        $course = Course::with(['materials', 'instructor'])->findOrFail($id);
        
        $isEnrolled = auth()->user()
            ->courses()
            ->where('course_id', $id)
            ->exists();
        
        return view('courses.show', [
            'course' => $course,
            'isEnrolled' => $isEnrolled
        ]);
    }
}
```

### Blade View Example
```blade
@if ($isEnrolled)
    <div class="progress-bar">{{ $progress }}%</div>
    <a href="{{ route('student.course-detail', $registration->id) }}">
        Lanjutkan Belajar
    </a>
@else
    <a href="{{ route('checkout.show', $course->id) }}" class="btn-primary">
        Daftar: Rp {{ number_format($course->price) }}
    </a>
@endif
```

### Service Example (Midtrans)
```php
class MidtransService {
    public function createSnapToken($order) {
        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $order->amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],
        ];
        
        return Snap::getSnapToken($params);
    }
    
    public function verifyWebhookSignature($orderId, $statusCode, $amount, $sig) {
        $serverKey = config('services.midtrans.server_key');
        $hash = openssl_digest($orderId . $statusCode . $amount . $serverKey, 'sha512');
        return hash_equals($hash, $sig);
    }
}
```

### Route Example
```php
// PUBLIC
Route::get('/course', [CourseController::class, 'showCourse'])->name('course.show');

// PROTECTED
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/my-courses', [CourseController::class, 'myCourses']);
});

// ADMIN ONLY
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::resource('users', AdminUserController::class);
    Route::resource('courses', AdminCourseController::class);
});
```

---

## 🔐 SECURITY MECHANISMS

| Fitur | Implementasi |
|-------|--------------|
| **Authentication** | Session + JWT tokens |
| **Authorization** | Middleware (auth, admin, instructor) |
| **Password** | bcrypt() hashing |
| **CSRF** | @csrf token di form |
| **SQL Injection** | Eloquent ORM + prepared statements |
| **XSS** | {{ }} auto-escape, {!! !!} raw (safe) |
| **Payment Security** | Signature verification, server-side amount check |
| **Email Verification** | Signed URLs + hash validation |

---

## 📊 DATABASE RELATIONS

### One-to-Many
```
User → Orders (1 user banyak orders)
Course → Materials (1 course banyak materials)
```

### Many-to-Many
```
User ↔ Course (via course_registrations)
User ↔ Achievement (via user_achievements)
```

### Foreign Keys
```sql
ALTER TABLE orders 
ADD CONSTRAINT fk_user 
FOREIGN KEY (user_id) 
REFERENCES users(id) ON DELETE CASCADE;
```

---

## 🚀 REQUEST LIFECYCLE

```
1. Browser: GET /course/123

2. Laravel Router
   └─ Match route pattern

3. Middleware Pipeline
   ├─ auth (login check)
   ├─ verified (email check)
   └─ other middleware

4. Controller Method
   ├─ Input validation
   ├─ Query database via Model
   ├─ Business logic
   └─ Return response

5. View Rendering
   ├─ Blade template engine
   ├─ Interpolate variables
   └─ Generate HTML

6. Browser
   ├─ Receive HTML + CSS + JS
   ├─ Render page
   └─ User lihat hasil
```

---

## ✅ BEST PRACTICES

### Controller
```php
✓ Keep methods small & focused
✓ Use dependency injection
✓ Validate input early
✓ Return meaningful responses
```

### Model
```php
✓ Define relationships
✓ Use query scopes
✓ Mass assign with $fillable
✓ Cast attributes
```

### View
```php
✓ Use Blade syntax (@if, @foreach)
✓ Reuse with @include/@component
✓ Use translation keys __()
✓ Escape output {{ }}
```

### Database
```php
✓ Use migrations untuk schema
✓ Add foreign keys
✓ Index frequently searched columns
✓ Use seeds untuk test data
```

---

## 🎯 RINGKASAN FITUR PROJECT

| Fitur | Implementasi |
|-------|--------------|
| **Authentication** | Login/Register + Email verification |
| **Course Management** | CRUD courses, materials, assignments |
| **Payment** | Midtrans integration dengan webhook |
| **Learning** | Quiz (auto-grade), Assignments (manual-grade) |
| **Analytics** | Admin dashboard dengan statistics |
| **User Management** | Role-based (student, instructor, admin) |
| **Forum** | Discussion per course |
| **Achievements** | Badge system + certificates |
| **Refund** | Refund request & processing |
| **Multi-language** | EN + ID localization |

---

## 📝 KESIMPULAN

OtakAtik Academy menggunakan:
- **Framework:** Laravel 11/12 (PHP)
- **Frontend:** Vue 3 + Tailwind CSS + Vite
- **Database:** Oracle
- **Payment:** Midtrans
- **Architecture:** MVC + Service layer
- **Auth:** Session-based + Middleware

Setiap request melalui pipeline yang sama:
**Request → Route → Middleware → Controller → Model → View → Response**

Dengan separation of concerns ini, kode jadi:
- 🎯 **Modular** - Mudah maintain
- 🧪 **Testable** - Mudah test
- 📈 **Scalable** - Mudah berkembang
- 🔒 **Secure** - Proteksi built-in

---

**Happy Learning! 🚀**
