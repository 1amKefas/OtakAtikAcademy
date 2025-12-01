# 📚 PRESENTASI REVIEW CODING - OtakAtik Academy

## Daftar Isi
1. [Arsitektur Aplikasi](#arsitektur-aplikasi)
2. [Model](#model)
3. [Controller](#controller)
4. [Views (Blade)](#views)
5. [Routes](#routes)
6. [Middleware](#middleware)
7. [Services](#services)
8. [Database & Migrations](#database--migrations)
9. [Contoh Alur Request](#contoh-alur-request)
10. [Best Practices](#best-practices)

---

## 🏗️ Arsitektur Aplikasi

### Gambaran Umum
OtakAtik Academy menggunakan **MVC (Model-View-Controller) Pattern** dengan beberapa layer tambahan:

```
┌─────────────────────────────────────────────────────┐
│          LAPISAN PRESENTATION (Frontend)             │
│  - Vue 3 + Vite (JavaScript Interactivity)          │
│  - Blade Templates (Server-side Rendering)          │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│          LAPISAN ROUTING (Routes)                    │
│  - routes/web.php (HTTP Routes)                     │
│  - routes/api.php (API Routes)                      │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│       LAPISAN MIDDLEWARE (Security & Logic)          │
│  - Authentication (auth)                            │
│  - Authorization (admin, instructor)                │
│  - Verification (verified email)                    │
│  - Rate Limiting (throttle)                         │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│         LAPISAN CONTROLLER (Business Logic)          │
│  - AuthController                                   │
│  - CourseController                                 │
│  - AdminController                                  │
│  - PaymentController                                │
│  - dan lain-lain...                                 │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│          LAPISAN SERVICE (Business Logic 2)          │
│  - MidtransService (Payment Gateway)                │
│  - AchievementService                               │
│  - Logical Operations & External APIs               │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│          LAPISAN MODEL & DATABASE                    │
│  - Eloquent Models                                  │
│  - Database Tables & Relations                      │
│  - Migrations (Schema)                              │
└─────────────────────────────────────────────────────┘
```

---

## 📋 Model

### Apa itu Model?
**Model** adalah representasi dari data dalam aplikasi. Dalam Laravel, model disebut **Eloquent Model** yang mewakili satu tabel di database.

### Lokasi File
`app/Models/` - Berisi semua Eloquent models

### Contoh Model: User
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // Nama tabel (jika berbeda dari nama model plural)
    protected $table = 'users';
    
    // Field yang bisa di-assign secara massal
    protected $fillable = ['name', 'email', 'password', 'role'];
    
    // Field yang di-hidden saat serialize
    protected $hidden = ['password'];
    
    // Relasi One-to-Many: 1 user banyak courses
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    
    // Relasi Many-to-Many: User punya banyak roles
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
```

### Relasi Model di OtakAtik Academy

#### 1. One-to-Many (1 ke Banyak)
```php
// Satu Instructor punya banyak Courses
class Instructor extends Model {
    public function courses() {
        return $this->hasMany(Course::class, 'instructor_id');
    }
}

// Satu Course punya banyak Materials
class Course extends Model {
    public function materials() {
        return $this->hasMany(Material::class);
    }
}
```

#### 2. Many-to-Many (Banyak ke Banyak)
```php
// Banyak Students bisa daftar banyak Courses
class Student extends Model {
    public function courses() {
        return $this->belongsToMany(
            Course::class,
            'course_registrations', // Tabel pivot
            'student_id',
            'course_id'
        );
    }
}

class Course extends Model {
    public function students() {
        return $this->belongsToMany(Student::class, 'course_registrations');
    }
}
```

#### 3. Has-One (1 ke 1)
```php
// Satu User punya 1 Profile
class User extends Model {
    public function profile() {
        return $this->hasOne(Profile::class);
    }
}
```

### Menggunakan Model di Controller
```php
// Get semua users
$users = User::all();

// Get user dengan ID tertentu
$user = User::find(1);

// Get user dengan kondisi where
$user = User::where('email', 'user@example.com')->first();

// Get courses dari seorang user
$courses = $user->courses()->get();

// Create user baru
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password123')
]);

// Update user
$user->update(['name' => 'Jane Doe']);

// Delete user
$user->delete();
```

---

## 🎮 Controller

### Apa itu Controller?
**Controller** adalah tempat business logic hidup. Controller menerima request dari routes, memanipulasi data melalui models, dan mengembalikan response (view atau JSON).

### Lokasi File
`app/Http/Controllers/` - Berisi semua controllers

### Struktur Controller Standar
```php
<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // 1. INDEX - Tampilkan daftar semua courses
    public function index()
    {
        $courses = Course::all();
        return view('courses.index', ['courses' => $courses]);
    }
    
    // 2. SHOW - Tampilkan detail course tertentu
    public function show($id)
    {
        $course = Course::findOrFail($id);
        return view('courses.show', ['course' => $course]);
    }
    
    // 3. CREATE - Tampilkan form buat course
    public function create()
    {
        return view('courses.create');
    }
    
    // 4. STORE - Simpan course baru ke database
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);
        
        // Create course
        $course = Course::create($validated);
        
        // Redirect dengan success message
        return redirect()->route('course.show', $course->id)
                        ->with('success', 'Course berhasil dibuat!');
    }
    
    // 5. EDIT - Tampilkan form edit course
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('courses.edit', ['course' => $course]);
    }
    
    // 6. UPDATE - Update course di database
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);
        
        $course->update($validated);
        
        return redirect()->route('course.show', $course->id)
                        ->with('success', 'Course berhasil diperbarui!');
    }
    
    // 7. DESTROY - Hapus course dari database
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        
        return redirect()->route('course.index')
                        ->with('success', 'Course berhasil dihapus!');
    }
}
```

### Contoh Real: AdminController (OtakAtik Academy)
```php
class AdminController extends Controller
{
    // Dashboard admin
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        
        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalCourses' => $totalCourses,
            'totalRevenue' => $totalRevenue
        ]);
    }
    
    // List semua users
    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users', ['users' => $users]);
    }
    
    // Update role user
    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'role' => 'required|in:student,instructor,admin'
        ]);
        
        $user->update($validated);
        
        return redirect()->back()->with('success', 'Role berhasil diubah!');
    }
    
    // Delete user
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->back()->with('success', 'User berhasil dihapus!');
    }
}
```

### Controller untuk Payments (Midtrans Integration)
```php
class PaymentController extends Controller
{
    protected $midtransService;
    
    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }
    
    // Tampilkan form checkout
    public function checkout($courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = auth()->user();
        
        return view('payment.checkout', [
            'course' => $course,
            'user' => $user
        ]);
    }
    
    // Proses pembayaran melalui Midtrans
    public function processPayment(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = auth()->user();
        
        // Buat order
        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'status' => 'pending'
        ]);
        
        // Kirim ke Midtrans
        $snapToken = $this->midtransService->createSnapToken($order);
        
        return view('payment.snap', ['snapToken' => $snapToken]);
    }
    
    // Handle notification dari Midtrans
    public function handleNotification(Request $request)
    {
        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            // Pembayaran berhasil
            $order = Order::where('id', $orderId)->first();
            $order->update(['status' => 'paid']);
            
            // Daftar user ke course
            CourseRegistration::create([
                'user_id' => $order->user_id,
                'course_id' => $order->course_id
            ]);
        }
        
        return response()->json(['status' => 'success']);
    }
}
```

---

## 🎨 Views (Blade)

### Apa itu View?
**View** adalah tempat presentasi data kepada user. Di Laravel menggunakan **Blade Templating Engine** yang mendukung PHP.

### Lokasi File
`resources/views/` - Berisi semua view files dengan extension `.blade.php`

### Struktur Folder Views
```
resources/views/
├── layouts/
│   ├── app.blade.php (Master layout)
│   └── guest.blade.php (Layout tanpa auth)
├── components/
│   ├── navbar.blade.php
│   ├── sidebar.blade.php
│   └── footer.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── verify-email.blade.php
├── courses/
│   ├── index.blade.php (List courses)
│   ├── show.blade.php (Detail course)
│   ├── create.blade.php (Form buat course)
│   └── edit.blade.php (Form edit course)
├── admin/
│   ├── dashboard.blade.php
│   ├── users.blade.php
│   └── courses.blade.php
├── student/
│   ├── dashboard.blade.php
│   └── profile.blade.php
└── help/
    └── index.blade.php (Help page)
```

### Syntax Blade - Dasar
```blade
<!-- 1. INTERPOLASI DATA -->
{{ $variabel }}                    <!-- Echo dengan escape -->
{!! $variabel !!}                 <!-- Echo tanpa escape (raw HTML) -->

<!-- 2. CONTROL STRUCTURES -->
@if ($condition)
    <p>Kondisi benar</p>
@elseif ($otherCondition)
    <p>Kondisi lain benar</p>
@else
    <p>Semua kondisi salah</p>
@endif

<!-- 3. LOOP -->
@foreach ($courses as $course)
    <div>
        <h3>{{ $course->title }}</h3>
        <p>{{ $course->description }}</p>
    </div>
@endforeach

<!-- 4. FOR LOOP -->
@for ($i = 0; $i < 10; $i++)
    <p>Iteration {{ $i }}</p>
@endfor

<!-- 5. WHILE LOOP -->
@while ($condition)
    <p>Masih looping</p>
@endwhile

<!-- 6. FOREACH DENGAN INDEX -->
@forelse ($courses as $course)
    <p>{{ $loop->iteration }}. {{ $course->title }}</p>
@empty
    <p>Tidak ada courses</p>
@endforelse

<!-- 7. TRANSLATION (Multi-language) -->
{{ __('messages.welcome') }}
{{ __('help.enroll_question') }}

<!-- 8. ROUTES -->
<a href="{{ route('course.show', $course->id) }}">
    Lihat Course
</a>

<!-- 9. CONDITIONAL STYLING -->
<div @class([
    'p-4',
    'bg-red-100' => $isError,
    'bg-green-100' => !$isError
])>
    Status
</div>
```

### Contoh View Lengkap: Course Detail
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-3 gap-6">
        <!-- Left: Course Content -->
        <div class="col-span-2">
            <h1 class="text-4xl font-bold mb-4">{{ $course->title }}</h1>
            
            <img src="{{ $course->thumbnail_url }}" 
                 alt="{{ $course->title }}" 
                 class="w-full rounded-lg mb-6">
            
            <div class="prose max-w-none">
                {!! $course->description !!}
            </div>
            
            <!-- Materials Section -->
            <div class="mt-8">
                <h2 class="text-2xl font-semibold mb-4">{{ __('messages.materials') }}</h2>
                
                @if ($course->materials->count() > 0)
                    <div class="space-y-3">
                        @foreach ($course->materials as $material)
                            <div class="p-4 border rounded-lg hover:bg-gray-50">
                                <h3 class="font-semibold">{{ $material->title }}</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ $material->description }}</p>
                                <a href="{{ $material->file_url }}" 
                                   class="text-blue-500 mt-2 inline-block">
                                    {{ __('messages.download') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">{{ __('messages.no_materials') }}</p>
                @endif
            </div>
        </div>
        
        <!-- Right: Course Info & CTA -->
        <div class="col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <!-- Instructor Info -->
                <div class="mb-6">
                    <p class="text-sm text-gray-500">{{ __('messages.instructor') }}</p>
                    <p class="font-semibold">{{ $course->instructor->name }}</p>
                </div>
                
                <!-- Price -->
                <div class="mb-6 border-b pb-6">
                    <p class="text-3xl font-bold">
                        Rp {{ number_format($course->price, 0, ',', '.') }}
                    </p>
                </div>
                
                <!-- Action Button -->
                @auth
                    @if (auth()->user()->isEnrolled($course->id))
                        <button class="w-full bg-gray-400 text-white py-3 rounded-lg" disabled>
                            {{ __('messages.already_enrolled') }}
                        </button>
                    @else
                        <a href="{{ route('checkout.show', $course->id) }}" 
                           class="block w-full bg-blue-500 text-white py-3 rounded-lg text-center hover:bg-blue-600">
                            {{ __('messages.enroll_now') }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" 
                       class="block w-full bg-blue-500 text-white py-3 rounded-lg text-center hover:bg-blue-600">
                        {{ __('messages.login_to_enroll') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
```

### Master Layout (app.blade.php)
```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    @include('components.navbar')
    
    <!-- Flash Messages -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('components.footer')
</body>
</html>
```

---

## 🛣️ Routes

### Apa itu Route?
**Route** adalah URL mapping yang menghubungkan HTTP request ke Controller action.

### Lokasi File
- `routes/web.php` - Web routes (render HTML)
- `routes/api.php` - API routes (JSON responses)

### Struktur Routes
```php
// 1. BASIC ROUTE
Route::get('/home', function () {
    return view('home');
});

// 2. ROUTE DENGAN CONTROLLER
Route::get('/courses', [CourseController::class, 'index'])->name('course.index');

// 3. ROUTE DENGAN PARAMETER
Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show');

// 4. ROUTE GROUP
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
});

// 5. RESOURCE ROUTES (CRUD)
Route::resource('courses', CourseController::class);

// Equivalent to:
// GET    /courses              - index
// POST   /courses              - store
// GET    /courses/create       - create
// GET    /courses/{id}         - show
// PUT    /courses/{id}         - update
// GET    /courses/{id}/edit    - edit
// DELETE /courses/{id}         - destroy

// 6. NAMED ROUTES
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Di Blade atau Controller:
// route('contact') -> /contact
// route('course.show', $id) -> /course/123
```

### Routes di OtakAtik Academy
```php
// PUBLIC ROUTES
Route::get('/', function () { return view('dashboard'); })->name('home');
Route::get('/help', [HelpController::class, 'index'])->name('help');

// AUTH REQUIRED
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('my.courses');
});

// ADMIN ONLY
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('users', AdminUserController::class);
});

// INSTRUCTOR ONLY
Route::middleware(['auth', 'instructor'])->prefix('instructor')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('instructor.dashboard');
    Route::resource('courses', InstructorCourseController::class);
});
```

---

## 🛡️ Middleware

### Apa itu Middleware?
**Middleware** adalah layer yang menangani request sebelum sampai ke controller. Digunakan untuk authentication, authorization, logging, dll.

### Lokasi File
`app/Http/Middleware/` - Berisi semua middleware

### Contoh Middleware: Authentication
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        // Jika user tidak login, redirect ke login
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Lanjutkan ke controller
        return $next($request);
    }
}
```

### Contoh Middleware: Admin Authorization
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user adalah admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        
        return $next($request);
    }
}
```

### Mendaftarkan Middleware
```php
// Di bootstrap/app.php
$middleware
    ->alias([
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'instructor' => \App\Http\Middleware\InstructorMiddleware::class,
    ]);
```

### Menggunakan Middleware di Routes
```php
// Middleware individual
Route::get('/admin', [AdminController::class, 'index'])->middleware('admin');

// Multiple middleware
Route::get('/profile', [ProfileController::class, 'show'])->middleware(['auth', 'verified']);

// Middleware group
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});
```

---

## 🔧 Services

### Apa itu Service?
**Service** adalah class yang menangani business logic kompleks, external API calls, dan shared logic yang digunakan oleh banyak controller.

### Lokasi File
`app/Services/` - Berisi semua service classes

### Contoh: MidtransService (Payment Gateway)
```php
<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans credentials
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
    }
    
    // Buat snap token untuk pembayaran
    public function createSnapToken($order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $order->amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
        ];
        
        $snapToken = Snap::getSnapToken($params);
        return $snapToken;
    }
    
    // Verifikasi signature dari Midtrans webhook
    public function verifyWebhookSignature($orderId, $statusCode, $grossAmount, $signature)
    {
        $serverKey = config('services.midtrans.server_key');
        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $hash = openssl_digest($input, 'sha512');
        
        return hash_equals($hash, $signature);
    }
}
```

### Contoh: AchievementService
```php
<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;

class AchievementService
{
    // Berikan achievement ke user
    public function awardAchievement(User $user, $achievementId)
    {
        // Cek apakah user sudah punya achievement ini
        if ($user->achievements()->where('achievement_id', $achievementId)->exists()) {
            return false;
        }
        
        // Attach achievement ke user
        $user->achievements()->attach($achievementId);
        
        return true;
    }
    
    // Check apakah user bisa dapat achievement tertentu
    public function checkCourseCompletion(User $user)
    {
        $completedCourses = $user->courses()
            ->where('progress', 100)
            ->count();
        
        // Award achievement jika sudah 5 courses selesai
        if ($completedCourses >= 5) {
            $achievement = Achievement::where('key', 'completed_5_courses')->first();
            $this->awardAchievement($user, $achievement->id);
        }
    }
}
```

### Menggunakan Service di Controller
```php
<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use App\Services\AchievementService;

class PaymentController extends Controller
{
    protected $midtransService;
    protected $achievementService;
    
    public function __construct(
        MidtransService $midtransService,
        AchievementService $achievementService
    ) {
        $this->midtransService = $midtransService;
        $this->achievementService = $achievementService;
    }
    
    public function processPayment($courseId)
    {
        // ... buat order ...
        
        // Gunakan service
        $snapToken = $this->midtransService->createSnapToken($order);
        
        return view('payment.snap', ['snapToken' => $snapToken]);
    }
    
    public function handleNotification(Request $request)
    {
        // ... verify signature ...
        $isValid = $this->midtransService->verifyWebhookSignature(...);
        
        if ($isValid && $isPaymentSuccess) {
            // Award achievement
            $this->achievementService->checkCourseCompletion($user);
        }
    }
}
```

---

## 💾 Database & Migrations

### Apa itu Migration?
**Migration** adalah file yang mendefinisikan struktur database (schema). Seperti version control untuk database.

### Lokasi File
`database/migrations/` - Berisi semua migration files

### Contoh Migration: Membuat Tabel Users
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();  // ID auto-increment
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['student', 'instructor', 'admin'])->default('student');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();  // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### Contoh Migration: Relasi Foreign Keys
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel course_registrations
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->integer('progress')->default(0); // 0-100%
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            
            // Unique constraint: 1 user tidak bisa daftar 2x course yang sama
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};
```

### Menjalankan Migrations
```bash
# Jalankan semua migrations yang belum dijalankan
php artisan migrate

# Rollback migration terakhir
php artisan migrate:rollback

# Rollback semua migrations
php artisan migrate:reset

# Rollback dan re-run semua
php artisan migrate:refresh

# Migrate + seed
php artisan migrate:fresh --seed
```

### Seeding (Data Awal)
```php
<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        Achievement::create([
            'name' => 'Course Completer',
            'description' => 'Selesaikan 1 course',
            'key' => 'completed_1_course',
            'icon_url' => '/icons/achievement-1.png'
        ]);
        
        Achievement::create([
            'name' => 'Learning Master',
            'description' => 'Selesaikan 5 courses',
            'key' => 'completed_5_courses',
            'icon_url' => '/icons/achievement-2.png'
        ]);
    }
}
```

---

## 📊 Contoh Alur Request (Step by Step)

---

### 🔐 SKENARIO 1: USER LOGIN

```
1. USER KLIK LINK LOGIN
   ↓
   Browser: GET /login
   
2. ROUTING
   ↓
   Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
   
3. MIDDLEWARE CHECK
   ↓
   Middleware 'guest' → Cek apakah user sudah login?
   │
   ├─ JA: User sudah login → redirect ke /dashboard
   └─ TIDAK: Lanjutkan ke controller
   
4. CONTROLLER ACTION
   ↓
   AuthController@showLogin()
   │
   └─ Return view form login
   
5. VIEW RENDERING
   ↓
   resources/views/auth/login.blade.php
   │
   ├─ Form dengan input:
   │  - Email/Username
   │  - Password
   │  - Remember me (optional)
   │
   └─ Tombol "Login"
   
6. USER INPUT EMAIL & PASSWORD
   ↓
   User: email=john@example.com, password=rahasia123
   
7. USER KLIK "LOGIN" (SUBMIT FORM)
   ↓
   Browser: POST /login
   
8. ROUTING
   ↓
   Route::post('/login', [AuthController::class, 'login']);
   
9. CONTROLLER ACTION - LOGIN
   ↓
   AuthController@login($request)
   │
   ├─ Step 1: VALIDASI INPUT
   │  $request->validate([
   │      'email' => 'required|email',
   │      'password' => 'required'
   │  ])
   │
   ├─ Step 2: QUERY DATABASE
   │  $user = User::where('email', $request->email)->first()
   │  │
   │  ├─ User TIDAK ditemukan?
   │  │  ↓ Back dengan error "Email/password salah"
   │  │
   │  └─ User ditemukan?
   │     ↓ Lanjut ke step 3
   │
   ├─ Step 3: CEK PASSWORD
   │  if (Hash::check($request->password, $user->password)) {
   │      // Password benar
   │  } else {
   │      // Password salah
   │      return back()->withErrors(['password' => 'Password salah']);
   │  }
   │
   ├─ Step 4: CEK EMAIL VERIFIED
   │  if ($user->email_verified_at === null) {
   │      // Email belum verified
   │      return redirect()->route('verification.notice');
   │  }
   │
   ├─ Step 5: AUTHENTICATE USER
   │  auth()->login($user, $request->remember)
   │  
   │  Ini akan:
   │  - Create session untuk user
   │  - Simpan user_id di session
   │  - Set cookie authentication
   │
   ├─ Step 6: LOG ACTIVITY (Optional)
   │  Log::info("User {$user->email} logged in");
   │
   └─ Step 7: REDIRECT
      return redirect()->route('dashboard')
                      ->with('success', 'Login berhasil!');

10. BROWSER REDIRECT
    ↓
    GET /dashboard
    
11. MIDDLEWARE CHECK
    ↓
    Middleware 'auth' → Cek apakah user login?
    │
    ├─ JA: User login → Lanjutkan ke controller
    └─ TIDAK: redirect ke /login
    
12. CONTROLLER ACTION
    ↓
    DashboardController@index()
    │
    ├─ Get current user:
    │  $user = auth()->user()
    │
    ├─ Get user data:
    │  $enrolledCourses = $user->courses()->count()
    │  $completedCourses = $user->courses()->where('progress', 100)->count()
    │  $achievements = $user->achievements()->get()
    │
    └─ Return view dengan data
    
13. VIEW RENDERING
    ↓
    resources/views/dashboard.blade.php
    │
    ├─ Welcome message:
    │  "Halo, {{ auth()->user()->name }}!"
    │
    ├─ Stats:
    │  - Total courses enrolled
    │  - Completed courses
    │  - Achievements
    │
    └─ List recommended courses
    
14. BROWSER DISPLAY
    ↓
    ✓ Dashboard ditampilkan dengan welcome message & data user
    
15. NEXT TIME USER AKSES
    ↓
    Laravel automatically check session cookie
    │
    ├─ Session valid?
    │  ↓ auth()->user() available
    │
    └─ Session invalid/expired?
       ↓ User harus login ulang
```

---

### 🔑 SKENARIO 2: USER REGISTER (SIGN UP)

```
1. USER KLIK "REGISTER" DI NAVBAR
   ↓
   Browser: GET /register
   
2. ROUTING
   ↓
   Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
   
3. MIDDLEWARE CHECK
   ↓
   Middleware 'guest'
   
4. CONTROLLER & VIEW
   ↓
   AuthController@showRegister() → resources/views/auth/register.blade.php
   
5. USER LIHAT FORM REGISTER
   ↓
   Form inputs:
   ├─ Name
   ├─ Email
   ├─ Password
   ├─ Confirm Password
   └─ Role (Student / Instructor - optional)
   
6. USER ISI FORM & KLIK "REGISTER"
   ↓
   POST /register
   
7. CONTROLLER ACTION - REGISTER
   ↓
   AuthController@register($request)
   │
   ├─ Step 1: VALIDASI
   │  $request->validate([
   │      'name' => 'required|string|max:255',
   │      'email' => 'required|email|unique:users',
   │      'password' => 'required|min:8|confirmed',
   │      'role' => 'nullable|in:student,instructor'
   │  ])
   │
   ├─ Step 2: CREATE USER
   │  $user = User::create([
   │      'name' => $request->name,
   │      'email' => $request->email,
   │      'password' => bcrypt($request->password),
   │      'role' => $request->role ?? 'student'
   │  ])
   │
   ├─ Step 3: SEND VERIFICATION EMAIL
   │  Mail::send('emails.verify-email', [
   │      'user' => $user,
   │      'verificationUrl' => URL::signedRoute(
   │          'verification.verify',
   │          ['id' => $user->id, 'hash' => sha1($user->email)]
   │      )
   │  ], $user->email)
   │
   ├─ Step 4: CREATE EVENT (Optional)
   │  UserRegistered event (untuk background jobs)
   │
   └─ Step 5: REDIRECT
      return redirect()->route('verification.notice')
                      ->with('message', 'Check email untuk verifikasi!');

8. USER LIHAT HALAMAN VERIFICATION NOTICE
   ↓
   resources/views/auth/verify-email.blade.php
   │
   ├─ Message: "Cek email Anda untuk link verifikasi"
   ├─ Tombol: "Resend Email"
   └─ Jika sudah verified: redirect ke dashboard
   
9. USER BUKA EMAIL & KLIK LINK VERIFIKASI
   ↓
   Link format:
   https://otakatikaacademy.com/email/verify/123/abc123hash?signature=xyz
   
10. BROWSER AKSES VERIFICATION LINK
    ↓
    GET /email/verify/{id}/{hash}?signature=xyz
    
11. ROUTING & VERIFICATION
    ↓
    Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {...});
    │
    ├─ Step 1: GET USER
    │  $user = User::findOrFail($id)
    │
    ├─ Step 2: VERIFY HASH
    │  if (!hash_equals($hash, sha1($user->email))) {
    │      abort(403, 'Invalid hash');
    │  }
    │
    ├─ Step 3: VERIFY SIGNATURE
    │  if (!$request->hasValidSignature()) {
    │      abort(403, 'Invalid signature');
    │  }
    │  (Cegah URL manipulation)
    │
    ├─ Step 4: MARK AS VERIFIED
    │  $user->markEmailAsVerified()
    │  
    │  (Set email_verified_at column)
    │
    ├─ Step 5: LOGIN USER OTOMATIS
    │  auth()->loginUsingId($user->id)
    │
    └─ Step 6: REDIRECT
       return redirect('/dashboard')
              ->with('success', 'Email verified! Welcome!');

12. USER AUTO LOGIN & REDIRECT KE DASHBOARD
    ↓
    ✓ User bisa langsung akses courses
    ✓ Profile sudah ter-verify
```

---

### 📚 SKENARIO 3: USER AKSES & LIHAT COURSE DETAIL

```
1. USER DI DASHBOARD, KLIK "LIHAT COURSES" DI NAVBAR
   ↓
   Browser: GET /course
   
2. ROUTING
   ↓
   Route::get('/course', [CourseController::class, 'showCourse'])->name('course.show');
   
3. MIDDLEWARE CHECK
   ↓
   Middleware 'auth' → User harus login
   
4. CONTROLLER ACTION
   ↓
   CourseController@showCourse()
   │
   ├─ Query all courses:
   │  $courses = Course::where('is_active', true)
   │             ->with('instructor')
   │             ->get()
   │
   ├─ Add status untuk setiap course:
   │  foreach ($courses as $course) {
   │      $course->isEnrolled = auth()->user()
   │                             ->courses()
   │                             ->where('course_id', $course->id)
   │                             ->exists();
   │  }
   │
   └─ Return view
   
5. VIEW RENDERING
   ↓
   resources/views/courses/index.blade.php
   │
   ├─ Display grid courses:
   │  @foreach ($courses as $course)
   │      <div class="course-card">
   │          <img src="{{ $course->thumbnail }}" />
   │          <h3>{{ $course->title }}</h3>
   │          <p>Rp {{ $course->price }}</p>
   │          
   │          @if ($course->isEnrolled)
   │              <button disabled>Sudah Daftar</button>
   │          @else
   │              <a href="{{ route('checkout.show', $course->id) }}">
   │                  Daftar Sekarang
   │              </a>
   │          @endif
   │      </div>
   │  @endforeach
   │
   └─ Pagination (jika banyak courses)
   
6. USER LIHAT LIST COURSES
   ↓
   Courses ditampilkan dalam grid/list
   
7. USER KLIK SALAH SATU COURSE
   ↓
   Browser: GET /course/42 (courseId=42)
   
8. ROUTING
   ↓
   Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show.detail');
   
9. CONTROLLER ACTION
   ↓
   CourseController@show($id)
   │
   ├─ Step 1: GET COURSE
   │  $course = Course::findOrFail($id)
   │            ->with(['materials', 'instructor', 'ratings'])
   │            ->first()
   │
   ├─ Step 2: GET CURRENT USER DATA
   │  $user = auth()->user()
   │  $isEnrolled = $user->courses()
   │                     ->where('course_id', $id)
   │                     ->exists()
   │  $progress = $user->courseProgress($id) ?? 0
   │
   ├─ Step 3: GET MATERIALS (jika sudah daftar)
   │  if ($isEnrolled) {
   │      $materials = $course->materials()->get();
   │      $assignments = $course->assignments()->get();
   │      $quizzes = $course->quizzes()->get();
   │  } else {
   │      $materials = null; // Jangan show content
   │  }
   │
   ├─ Step 4: GET REVIEWS & RATINGS
   │  $reviews = $course->reviews()->with('user')->get();
   │  $avgRating = $course->reviews()->avg('rating');
   │
   └─ Step 5: RETURN VIEW
      return view('courses.show', [
          'course' => $course,
          'isEnrolled' => $isEnrolled,
          'progress' => $progress,
          'materials' => $materials,
          'reviews' => $reviews,
          'avgRating' => $avgRating
      ]);

10. VIEW RENDERING
    ↓
    resources/views/courses/show.blade.php
    │
    ├─ COURSE HEADER
    │  ├─ Course title: {{ $course->title }}
    │  ├─ Thumbnail image
    │  ├─ Instructor: {{ $course->instructor->name }}
    │  ├─ Rating: ⭐⭐⭐⭐⭐ ({{ $avgRating }})
    │  └─ Price: Rp {{ $course->price }}
    │
    ├─ COURSE DESCRIPTION
    │  {!! $course->description !!}
    │
    ├─ ENROLLMENT STATUS & ACTION
    │  @if ($isEnrolled)
    │      <div class="progress-bar">{{ $progress }}% Completed</div>
    │      <a href="{{ route('student.course-detail', $registration->id) }}">
    │          Lanjutkan Belajar →
    │      </a>
    │  @else
    │      <a href="{{ route('checkout.show', $course->id) }}" class="btn-primary">
    │          Daftar Sekarang - Rp {{ $course->price }}
    │      </a>
    │  @endif
    │
    ├─ MATERIALS SECTION (hanya jika sudah daftar)
    │  @if ($isEnrolled && $materials->count() > 0)
    │      <div class="materials">
    │          <h2>Materi Pembelajaran</h2>
    │          @foreach ($materials as $material)
    │              <div class="material-item">
    │                  <h3>{{ $material->title }}</h3>
    │                  <p>{{ $material->description }}</p>
    │                  <a href="{{ $material->file_url }}">Download</a>
    │              </div>
    │          @endforeach
    │      </div>
    │  @endif
    │
    ├─ REVIEWS SECTION
    │  <div class="reviews">
    │      <h2>Reviews ({{ $reviews->count() }})</h2>
    │      @foreach ($reviews as $review)
    │          <div class="review-card">
    │              <p>⭐ {{ $review->rating }}/5</p>
    │              <p>{{ $review->comment }}</p>
    │              <small>- {{ $review->user->name }}</small>
    │          </div>
    │      @endforeach
    │  </div>
    │
    └─ Meta info (students enrolled, duration, dll)
    
11. BROWSER DISPLAY
    ↓
    ✓ Course detail page dengan semua informasi
    ✓ Jika sudah daftar: tombol "Lanjutkan Belajar" tampil
    ✓ Jika belum: tombol "Daftar Sekarang" tampil
```

---

### 💳 SKENARIO 4: USER CHECKOUT & MEMBAYAR COURSE

```
1. USER YANG BELUM DAFTAR KLIK "DAFTAR SEKARANG"
   ↓
   Browser: GET /checkout/123 (courseId)
   
2. ROUTING
   ↓
   Route::get('/{courseId}', [PaymentController::class, 'checkout'])
        ->middleware(['auth'])
        ->name('checkout.show');
   
3. MIDDLEWARE CHECK
   ↓
   auth middleware → User harus login
   
4. CONTROLLER ACTION
   ↓
   PaymentController@checkout($courseId)
   │
   ├─ Step 1: GET COURSE DATA
   │  $course = Course::findOrFail($courseId)
   │
   ├─ Step 2: CEK APAKAH USER SUDAH DAFTAR
   │  $alreadyEnrolled = auth()->user()
   │                           ->courses()
   │                           ->where('course_id', $courseId)
   │                           ->exists()
   │  
   │  if ($alreadyEnrolled) {
   │      abort(403, 'Anda sudah daftar kursus ini');
   │  }
   │
   ├─ Step 3: GET USER DATA
   │  $user = auth()->user()
   │
   ├─ Step 4: CEK VOUCHER (jika ada di request)
   │  $discount = 0
   │  if ($request->voucher_code) {
   │      $voucher = Voucher::where('code', $request->voucher_code)
   │                        ->where('is_active', true)
   │                        ->first()
   │      
   │      if ($voucher) {
   │          $discount = $course->price * ($voucher->discount_percent / 100)
   │      }
   │  }
   │
   ├─ Step 5: CALCULATE FINAL PRICE
   │  $finalPrice = $course->price - $discount
   │
   └─ Step 6: RETURN VIEW
      return view('payment.checkout', [
          'course' => $course,
          'user' => $user,
          'finalPrice' => $finalPrice,
          'discount' => $discount
      ]);

5. VIEW RENDERING
   ↓
   resources/views/payment/checkout.blade.php
   │
   ├─ LEFT SECTION: Course Summary
   │  ├─ Course thumbnail
   │  ├─ Course title
   │  ├─ Instructor name
   │  ├─ Course description (preview)
   │  └─ Benefits list
   │
   ├─ RIGHT SECTION: Payment Form
   │  ├─ Price breakdown:
   │  │  ├─ Original price: Rp {{ $course->price }}
   │  │  ├─ Discount: Rp {{ $discount }}
   │  │  └─ Total: Rp {{ $finalPrice }}
   │  │
   │  ├─ Voucher input:
   │  │  <input type="text" name="voucher_code" placeholder="Kode voucher">
   │  │  <button>Cek Voucher</button>
   │  │  (AJAX request: POST /checkout/voucher-check)
   │  │
   │  ├─ Payment method selector (akan dipilih di Midtrans)
   │  │
   │  └─ Terms & conditions checkbox
   │
   └─ Button "Lanjutkan Ke Pembayaran"
   
6. USER ISI VOUCHER CODE (OPTIONAL)
   ↓
   Misalnya: EARLYBIRD2024
   
7. USER KLIK TOMBOL CEKK VOUCHER (AJAX)
   ↓
   POST /checkout/voucher-check
   
8. CONTROLLER HANDLE VOUCHER CHECK
   ↓
   PaymentController@checkVoucher($request)
   │
   ├─ Get voucher:
   │  $voucher = Voucher::where('code', $request->code)
   │                    ->where('is_active', true)
   │                    ->where('expired_at', '>', now())
   │                    ->first()
   │
   ├─ If valid:
   │  return response()->json([
   │      'valid' => true,
   │      'discount_percent' => $voucher->discount_percent,
   │      'final_price' => $course->price - discount
   │  ])
   │
   └─ If invalid:
      return response()->json([
          'valid' => false,
          'message' => 'Kode voucher tidak valid'
      ]);

9. FRONTEND UPDATE PRICE (AJAX Response)
   ↓
   Vue 3 / JavaScript update final price di UI
   
10. USER REVIEW TOTAL & KLIK "LANJUTKAN KE PEMBAYARAN"
    ↓
    POST /checkout/process/123
    
11. CONTROLLER PROCESS PAYMENT
    ↓
    PaymentController@processPayment($request, $courseId)
    │
    ├─ Step 1: VALIDATE INPUT
    │  $request->validate([
    │      'voucher_code' => 'nullable|string',
    │      'agree_terms' => 'required|accepted'
    │  ])
    │
    ├─ Step 2: CREATE ORDER RECORD
    │  $order = Order::create([
    │      'user_id' => auth()->id(),
    │      'course_id' => $courseId,
    │      'voucher_code' => $request->voucher_code,
    │      'original_price' => $course->price,
    │      'discount' => $discount,
    │      'amount' => $finalPrice,
    │      'status' => 'pending',
    │      'order_date' => now()
    │  ])
    │
    ├─ Step 3: CALL MIDTRANS SERVICE
    │  $snapToken = $this->midtransService->createSnapToken($order)
    │  
    │  MidtransService akan:
    │  - Set order details
    │  - Set customer details
    │  - Call Midtrans API
    │  - Return snap token
    │
    └─ Step 4: RETURN SNAP TOKEN
       return view('payment.snap', [
           'snapToken' => $snapToken,
           'order' => $order
       ]);

12. VIEW RENDERING - SNAP PAGE
    ↓
    resources/views/payment/snap.blade.php
    │
    ├─ Load Midtrans Snap library:
    │  <script src="https://app.midtrans.com/snap/snap.js"></script>
    │
    ├─ JavaScript untuk trigger Snap UI:
    │  <script>
    │      var snapToken = '{{ $snapToken }}'
    │      
    │      snap.pay(snapToken, {
    │          onSuccess: function(result) {
    │              // Pembayaran berhasil
    │              fetch('/checkout/notification', {
    │                  method: 'POST',
    │                  body: JSON.stringify(result)
    │              })
    │          },
    │          onPending: function(result) {
    │              // Pending (tunggu)
    │          },
    │          onError: function(result) {
    │              // Error
    │          },
    │          onClose: function() {
    │              // User tutup modal
    │          }
    │      })
    │  </script>
    │
    └─ Loading UI sampai snap loaded
    
13. SNAP PAYMENT GATEWAY LOADED
    ↓
    User lihat payment options:
    ├─ Transfer Bank
    ├─ E-Wallet (GoPay, OVO, Dana)
    ├─ Kartu Kredit
    └─ Cicilan
    
14. USER PILIH PAYMENT METHOD & BAYAR
    ↓
    Misal: Transfer Bank BCA
    User dapat nomor virtual account
    
15. USER TRANSFER KE NOMOR VIRTUAL ACCOUNT
    ↓
    Midtrans detect transfer → Settlement
    
16. MIDTRANS KIRIM WEBHOOK NOTIFICATION
    ↓
    POST /checkout/notification
    │
    └─ Data:
       {
           "order_id": "123",
           "transaction_status": "settlement",
           "transaction_id": "mid-20241128-xyz",
           "gross_amount": "299000",
           "signature_key": "hash123..."
       }

17. CONTROLLER HANDLE NOTIFICATION
    ↓
    PaymentController@handleNotification($request)
    │
    ├─ Step 1: VERIFY SIGNATURE
    │  $isValid = $this->midtransService->verifyWebhookSignature(
    │      $request->order_id,
    │      $request->transaction_status,
    │      $request->gross_amount,
    │      $request->signature_key
    │  )
    │  
    │  if (!$isValid) {
    │      return response()->json(['error' => 'Invalid signature'], 403)
    │  }
    │
    ├─ Step 2: GET ORDER
    │  $order = Order::findOrFail($request->order_id)
    │
    ├─ Step 3: CHECK TRANSACTION STATUS
    │  if ($request->transaction_status === 'settlement' ||
    │      $request->transaction_status === 'capture') {
    │      
    │      // PEMBAYARAN BERHASIL
    │      
    │      ├─ UPDATE ORDER STATUS
    │      │  $order->update([
    │      │      'status' => 'paid',
    │      │      'transaction_id' => $request->transaction_id,
    │      │      'paid_at' => now()
    │      │  ])
    │      │
    │      ├─ CREATE COURSE REGISTRATION
    │      │  CourseRegistration::create([
    │      │      'user_id' => $order->user_id,
    │      │      'course_id' => $order->course_id,
    │      │      'enrolled_at' => now(),
    │      │      'progress' => 0
    │      │  ])
    │      │
    │      ├─ SEND EMAIL CONFIRMATION
    │      │  Mail::send('emails.payment-success', [
    │      │      'order' => $order,
    │      │      'course' => $order->course
    │      │  ], $order->user->email)
    │      │
    │      ├─ CREATE NOTIFICATION
    │      │  Notification::create([
    │      │      'user_id' => $order->user_id,
    │      │      'type' => 'payment_success',
    │      │      'message' => 'Pembayaran berhasil! Selamat belajar.',
    │      │      'link' => route('student.course-detail', ...)
    │      │  ])
    │      │
    │      ├─ DISPATCH EVENT (background job)
    │      │  UserEnrolledInCourse::dispatch($order->user, $order->course)
    │      │
    │      │  Listener akan:
    │      │  - Check achievements
    │      │  - Send welcome email
    │      │  - Update statistics
    │      │
    │      └─ AWARD ACHIEVEMENT (optional)
    │         $achievementService->checkCourseEnrolment($order->user)
    │
    │  } elseif ($request->transaction_status === 'pending') {
    │      // PEMBAYARAN PENDING
    │      $order->update(['status' => 'pending'])
    │
    │  } else if ($request->transaction_status === 'deny' ||
    │             $request->transaction_status === 'cancel' ||
    │             $request->transaction_status === 'expire') {
    │      // PEMBAYARAN GAGAL
    │      $order->update(['status' => 'failed'])
    │  }
    │
    └─ Return success response ke Midtrans
       return response()->json(['status' => 'ok']);

18. USER MENERIMA EMAIL CONFIRMATION
    ↓
    Subject: "Pembayaran Berhasil - Selamat belajar!"
    │
    ├─ Order details
    ├─ Course information
    ├─ Link ke course
    └─ Tips memulai belajar
    
19. USER BISA AKSES COURSE
    ↓
    ✓ Course muncul di "My Courses"
    ✓ Bisa lihat materials, assignments, quizzes
    ✓ Progress tracking dimulai
```

---

### 📖 SKENARIO 5: USER BELAJAR DAN SUBMIT ASSIGNMENT

```
1. USER KLIK COURSE DI "MY COURSES"
   ↓
   Browser: GET /student/course/42 (registrationId)
   
2. CONTROLLER & VIEW
   ↓
   StudentController@courseDetail($registrationId)
   │
   ├─ Get registration & course data
   ├─ Get all materials, assignments, quizzes
   ├─ Calculate current progress
   └─ Return course detail page
   
3. USER LIHAT COURSE CONTENT
   ↓
   resources/views/student/course-detail.blade.php
   │
   ├─ COURSE HEADER
   │  ├─ Course title
   │  ├─ Progress bar: 35% Completed
   │  ├─ Total students: 234
   │  └─ Instructor: John Doe
   │
   ├─ TAB MENU
   │  ├─ Materials (active)
   │  ├─ Assignments
   │  ├─ Quizzes
   │  ├─ Forum
   │  └─ Certificate (jika selesai)
   │
   ├─ MATERIALS SECTION
   │  @foreach ($materials as $material)
   │      <div class="material-card">
   │          📄 {{ $material->title }}
   │          {{ $material->description }}
   │          <a href="{{ $material->file_url }}">📥 Download</a>
   │      </div>
   │  @endforeach
   │
   └─ NAVIGATION
      Prev / Next material
      
4. USER DOWNLOAD MATERI
   ↓
   Misalnya: Modul 1 PDF
   
5. USER KLIK TAB "ASSIGNMENTS"
   ↓
   View assignments section
   │
   └─ Display list assignments:
      @foreach ($assignments as $assignment)
          <div class="assignment-card">
              <h3>{{ $assignment->title }}</h3>
              <p>{{ $assignment->description }}</p>
              <p>Deadline: {{ $assignment->due_date->format('d/m/Y') }}</p>
              
              @if (now() > $assignment->due_date)
                  <span class="badge-danger">Deadline Terlewat</span>
              @else
                  <span class="badge-warning">
                      Sisa: {{ $assignment->due_date->diffForHumans() }}
                  </span>
              @endif
              
              @if ($userSubmittedAssignment)
                  <span class="badge-success">✓ Submitted</span>
                  @if ($submission->grade)
                      <p>Nilai: {{ $submission->grade }}/100</p>
                  @endif
              @else
                  <a href="{{ route('student.assignment.submit.form', $assignment->id) }}">
                      Kerjakan Sekarang
                  </a>
              @endif
          </div>
      @endforeach
      
6. USER KLIK "KERJAKAN SEKARANG" UNTUK ASSIGNMENT
   ↓
   Browser: GET /student/assignments/15/submit (assignmentId)
   
7. CONTROLLER ACTION
   ↓
   StudentController@submitAssignmentForm($assignmentId)
   │
   ├─ Get assignment
   ├─ Check deadline
   ├─ Check previous submission (jika ada)
   └─ Return form view
   
8. VIEW RENDERING
   ↓
   resources/views/student/assignment-submit.blade.php
   │
   ├─ ASSIGNMENT DETAILS
   │  ├─ Title: {{ $assignment->title }}
   │  ├─ Description: {!! $assignment->description !!}
   │  ├─ Instructions: {!! $assignment->instructions !!}
   │  ├─ Due date: {{ $assignment->due_date }}
   │  └─ Max file size: 10MB
   │
   ├─ SUBMISSION FORM
   │  <form method="POST" enctype="multipart/form-data">
   │      <textarea name="answer" placeholder="Jawaban/Penjelasan">
   │          @if ($previousSubmission)
   │              {{ $previousSubmission->answer }}
   │          @endif
   │      </textarea>
   │      
   │      <input type="file" name="attachment" accept=".pdf,.doc,.docx,.zip">
   │      
   │      <div class="file-list">
   │          @if ($previousSubmission && $previousSubmission->attachment)
   │              <p>File sebelumnya: {{ $previousSubmission->attachment_name }}</p>
   │          @endif
   │      </div>
   │      
   │      <button type="submit">Submit Assignment</button>
   │  </form>
   │
   └─ SUBMISSION HISTORY
      @if ($previousSubmissions->count() > 0)
          <div class="history">
              @foreach ($previousSubmissions as $sub)
                  <p>
                      Submitted: {{ $sub->created_at }}
                      Status: {{ $sub->grade ? 'Graded: ' . $sub->grade : 'Pending' }}
                  </p>
              @endforeach
          </div>
      @endif
      
9. USER ISI JAWABAN & UPLOAD FILE
   ↓
   Misal:
   - Answer: "Jawaban saya untuk assignment ini..."
   - File: solution.pdf (2.5MB)
   
10. USER KLIK "SUBMIT ASSIGNMENT"
    ↓
    POST /student/assignments/15/submit
    
11. CONTROLLER PROCESS SUBMISSION
    ↓
    StudentController@submitAssignment($request, $assignmentId)
    │
    ├─ Step 1: VALIDATE
    │  $request->validate([
    │      'answer' => 'required|string',
    │      'attachment' => 'nullable|file|max:10240'
    │  ])
    │
    ├─ Step 2: CHECK DEADLINE
    │  if (now() > $assignment->due_date) {
    │      return back()->withErrors(['assignment' => 'Deadline terlewat']);
    │  }
    │
    ├─ Step 3: UPLOAD FILE (jika ada)
    │  $filePath = null
    │  if ($request->hasFile('attachment')) {
    │      $file = $request->file('attachment')
    │      $filePath = $file->store('submissions', 'public')
    │  }
    │
    ├─ Step 4: SAVE SUBMISSION
    │  $submission = AssignmentSubmission::create([
    │      'assignment_id' => $assignmentId,
    │      'user_id' => auth()->id(),
    │      'answer' => $request->answer,
    │      'attachment_path' => $filePath,
    │      'submitted_at' => now(),
    │      'status' => 'submitted'
    │  ])
    │
    ├─ Step 5: NOTIFY INSTRUCTOR
    │  Notification::create([
    │      'user_id' => $assignment->course->instructor_id,
    │      'type' => 'new_submission',
    │      'message' => auth()->user()->name . ' submitted ' . $assignment->title,
    │      'link' => route('instructor.submissions.detail', ...)
    │  ])
    │
    ├─ Step 6: SEND EMAIL
    │  Mail::send('emails.assignment-submitted', [...])
    │
    └─ Step 7: REDIRECT
       return redirect()->route('student.course-detail', ...)
              ->with('success', 'Assignment berhasil disubmit!');

12. INSTRUCTOR MELIHAT SUBMISSION
    ↓
    Browser: GET /instructor/assignments/15/submissions
    
13. INSTRUCTOR LIHAT SUBMISSION & GRADE
    ↓
    View detail: /instructor/assignments/15/submissions/123
    │
    ├─ Student name
    ├─ Submitted date
    ├─ Answer text
    ├─ Downloaded attachment
    └─ Grading form:
        <input type="number" name="grade" min="0" max="100">
        <textarea name="feedback">Feedback untuk student...</textarea>
        <button>Submit Grade</button>
        
14. INSTRUCTOR SUBMIT GRADE
    ↓
    PUT /instructor/submissions/123/grade
    
15. CONTROLLER UPDATE GRADE
    ↓
    InstructorController@gradeSubmission($submissionId)
    │
    ├─ Validate grade (0-100)
    ├─ Update submission with grade & feedback
    ├─ Update course progress
    └─ Notify student
    
16. STUDENT MENERIMA NOTIFICATION
    ↓
    "Assignment Anda telah di-grade! Score: 85/100"
    
17. STUDENT CEK GRADE
    ↓
    Buka assignment → Lihat grade & feedback dari instructor
    
18. STUDENT LIHAT PROGRESS UPDATE
    ↓
    Progress bar increment berdasarkan:
    - Materials completed
    - Assignments graded
    - Quizzes completed
```

---

### 🎯 SKENARIO 6: USER AMBIL QUIZ

```
1. USER KLIK TAB "QUIZZES" DI COURSE
   ↓
   View quizzes list
   │
   └─ Display quizzes:
      @foreach ($quizzes as $quiz)
          <div class="quiz-card">
              <h3>{{ $quiz->title }}</h3>
              <p>{{ $quiz->description }}</p>
              <p>Durasi: {{ $quiz->duration }} menit</p>
              <p>Total soal: {{ $quiz->questions()->count() }}</p>
              
              @if ($userCompletedQuiz)
                  <span class="badge-success">✓ Completed</span>
                  <p>Score: {{ $userQuizScore }}/100</p>
              @else
                  <button>Mulai Quiz</button>
              @endif
          </div>
      @endforeach
      
2. USER KLIK "MULAI QUIZ"
   ↓
   POST /student/course/42/quiz/7/start
   
3. CONTROLLER CREATE SUBMISSION
   ↓
   QuizController@start($courseId, $quizId)
   │
   ├─ Create quiz submission record:
   │  $submission = QuizSubmission::create([
   │      'quiz_id' => $quizId,
   │      'user_id' => auth()->id(),
   │      'started_at' => now(),
   │      'status' => 'in_progress'
   │  ])
   │
   └─ Redirect ke quiz start page
   
4. LOAD QUIZ PAGE
   ↓
   GET /student/course/42/quiz/7/submission/123
   
5. VIEW QUIZ INTERFACE
   ↓
   resources/views/student/quiz-attempt.blade.php
   │
   ├─ QUIZ HEADER
   │  ├─ Quiz title
   │  ├─ Timer: 30:00 (countdown)
   │  ├─ Progress: Question 1 of 10
   │  └─ Poin: 100 points total
   │
   ├─ QUESTION DISPLAY
   │  <div class="question">
   │      <h3>Soal 1: Apa itu Laravel?</h3>
   │      
   │      @if ($question->type === 'multiple_choice')
   │          <form>
   │              <input type="radio" name="answer" value="a"> A. PHP Framework
   │              <input type="radio" name="answer" value="b"> B. Database
   │              <input type="radio" name="answer" value="c"> C. Hosting Service
   │              <input type="radio" name="answer" value="d"> D. Testing Tool
   │          </form>
   │      @elseif ($question->type === 'essay')
   │          <textarea name="answer" placeholder="Jawab soal ini..."></textarea>
   │      @elseif ($question->type === 'true_false')
   │          <input type="radio" name="answer" value="true"> Benar
   │          <input type="radio" name="answer" value="false"> Salah
   │      @endif
   │  </div>
   │
   ├─ NAVIGATION
   │  <button>← Previous</button>
   │  <button>Next →</button>
   │
   ├─ QUESTION LIST SIDEBAR
   │  @foreach ($quiz->questions as $q)
   │      <button class="@if($q->answered) answered @endif">
   │          {{ $loop->iteration }}
   │      </button>
   │  @endforeach
   │
   └─ SUBMIT BUTTON
      <button type="button" data-action="submit">
          Selesaikan Quiz
      </button>
      
6. USER JAWAB PERTANYAAN 1
   ↓
   Misal: Pilih "A. PHP Framework"
   
7. USER KLIK "NEXT"
   ↓
   AJAX: SAVE ANSWER
   
   POST /student/course/42/quiz/7/submission/123/answer
   │
   └─ Save jawaban ke database
   
8. USER LANJUT KE SOAL BERIKUTNYA
   ↓
   (Proses berulang untuk setiap soal)
   
9. TIMER MENCAPAI 5 MENIT TERAKHIR
   ↓
   Alert: "Waktu habis dalam 5 menit!"
   
10. USER SELESAIKAN SEMUA SOAL & KLIK "SELESAIKAN QUIZ"
    ↓
    POST /student/course/42/quiz/7/submission/123/submit
    
11. CONTROLLER SUBMIT QUIZ
    ↓
    QuizController@submit($submissionId)
    │
    ├─ Step 1: MARK SUBMISSION AS COMPLETED
    │  $submission->update([
    │      'submitted_at' => now(),
    │      'status' => 'submitted'
    │  ])
    │
    ├─ Step 2: CALCULATE SCORE
    │  Get all answers dari submission
    │  Loop setiap answer:
    │  - Get correct answer dari question
    │  - Compare dengan user answer
    │  - Calculate points
    │
    │  $totalScore = 0
    │  foreach ($submission->answers as $answer) {
    │      if ($answer->answer === $answer->question->correct_answer) {
    │          $totalScore += $answer->question->points
    │      }
    │  }
    │
    ├─ Step 3: UPDATE SUBMISSION SCORE
    │  $submission->update([
    │      'score' => $totalScore,
    │      'passed' => $totalScore >= 70 // Pass score
    │  ])
    │
    ├─ Step 4: UPDATE COURSE PROGRESS
    │  Increment course progress:
    │  $registration->increment('progress', 5)
    │
    ├─ Step 5: CHECK ACHIEVEMENT
    │  if ($totalScore === 100) {
    │      AchievementService::award($user, 'perfect_quiz')
    │  }
    │
    ├─ Step 6: SEND NOTIFICATION
    │  Notification::create([
    │      'user_id' => auth()->id(),
    │      'message' => 'Quiz completed! Score: ' . $totalScore
    │  ])
    │
    └─ Step 7: REDIRECT
       return redirect()->route('student.quiz.result', ...)
   
12. USER LIHAT HASIL QUIZ
    ↓
    resources/views/student/quiz-result.blade.php
    │
    ├─ SCORE DISPLAY
    │  ├─ Total Score: 85/100
    │  ├─ Status: ✓ PASSED
    │  ├─ Percentage: 85%
    │  └─ Poin Earned: 850
    │
    ├─ ANSWER REVIEW
    │  @foreach ($submission->answers as $answer)
    │      <div class="answer-review">
    │          <p>Soal: {{ $answer->question->text }}</p>
    │          
    │          @if ($answer->is_correct)
    │              <p class="correct">✓ Benar</p>
    │          @else
    │              <p class="incorrect">✗ Salah</p>
    │              <p>Jawaban Anda: {{ $answer->answer }}</p>
    │              <p>Jawaban Benar: {{ $answer->question->correct_answer }}</p>
    │          @endif
    │      </div>
    │  @endforeach
    │
    └─ BUTTONS
       <a href="{{ route('student.course-detail', ...) }}">
           Kembali ke Course
       </a>
```

---

### 📊 SKENARIO 7: ADMIN VIEW ANALYTICS & MANAGE USERS

```
1. ADMIN LOGIN (sama seperti skenario user login)
   ↓
   POST /login → redirect /dashboard
   
2. ADMIN NAVIGATE KE ADMIN PANEL
   ↓
   Click navbar "Admin" → GET /admin/dashboard
   
3. MIDDLEWARE CHECK
   ↓
   'admin' middleware cek role === 'admin'
   
4. ADMIN DASHBOARD CONTROLLER
   ↓
   AdminController@dashboard()
   │
   ├─ Get statistics:
   │  $totalUsers = User::count()
   │  $totalCourses = Course::count()
   │  $totalRevenue = Order::where('status', 'paid')->sum('amount')
   │  $newUsers7Days = User::where('created_at', '>=', now()->subDays(7))->count()
   │
   ├─ Get charts data:
   │  $enrollmentChart = CourseRegistration::selectRaw('course_id, count(*) as total')
   │                    ->groupBy('course_id')
   │                    ->get()
   │
   ├─ Get recent activities:
   │  $recentOrders = Order::latest()->take(10)->get()
   │  $recentUsers = User::latest()->take(10)->get()
   │
   └─ Return dashboard view
   
5. ADMIN VIEW DASHBOARD
   ↓
   resources/views/admin/dashboard.blade.php
   │
   ├─ STATISTICS CARDS
   │  ├─ Total Users: {{ $totalUsers }}
   │  ├─ Total Courses: {{ $totalCourses }}
   │  ├─ Total Revenue: Rp {{ $totalRevenue }}
   │  └─ New Users (7 days): {{ $newUsers7Days }}
   │
   ├─ CHARTS
   │  ├─ Revenue Chart (last 30 days)
   │  ├─ Enrollment Chart (by course)
   │  ├─ User Growth Chart
   │  └─ Course Performance
   │
   ├─ RECENT ACTIVITIES
   │  ├─ Recent orders
   │  ├─ Recent sign-ups
   │  └─ Recent payments
   │
   └─ QUICK ACTIONS
      ├─ Add new course
      ├─ View users
      ├─ View courses
      └─ View financial reports
      
6. ADMIN KLIK "VIEW USERS"
   ↓
   GET /admin/users
   
7. ADMIN USERS CONTROLLER
   ↓
   AdminController@users()
   │
   ├─ Get users dengan pagination:
   │  $users = User::paginate(15)
   │
   ├─ Get counts:
   │  foreach ($users as $user) {
   │      $user->coursesEnrolled = $user->courses()->count()
   │      $user->totalSpent = $user->orders()
   │                             ->where('status', 'paid')
   │                             ->sum('amount')
   │  }
   │
   └─ Return view
   
8. VIEW USERS TABLE
   ↓
   resources/views/admin/users.blade.php
   │
   ├─ FILTERS
   │  <select name="role">
   │      <option>All Roles</option>
   │      <option>Student</option>
   │      <option>Instructor</option>
   │      <option>Admin</option>
   │  </select>
   │
   ├─ SEARCH
   │  <input type="search" placeholder="Search by name/email">
   │
   ├─ USERS TABLE
   │  <table>
   │      <thead>
   │          <tr>
   │              <th>Name</th>
   │              <th>Email</th>
   │              <th>Role</th>
   │              <th>Courses</th>
   │              <th>Total Spent</th>
   │              <th>Status</th>
   │              <th>Actions</th>
   │          </tr>
   │      </thead>
   │      <tbody>
   │          @foreach ($users as $user)
   │              <tr>
   │                  <td>{{ $user->name }}</td>
   │                  <td>{{ $user->email }}</td>
   │                  <td>
   │                      <select class="role-select" data-user="{{ $user->id }}">
   │                          <option value="student" @selected($user->role === 'student')>
   │                              Student
   │                          </option>
   │                          <option value="instructor" @selected($user->role === 'instructor')>
   │                              Instructor
   │                          </option>
   │                          <option value="admin" @selected($user->role === 'admin')>
   │                              Admin
   │                          </option>
   │                      </select>
   │                  </td>
   │                  <td>{{ $user->coursesEnrolled }}</td>
   │                  <td>Rp {{ $user->totalSpent }}</td>
   │                  <td>
   │                      @if ($user->email_verified_at)
   │                          <span class="badge-success">Verified</span>
   │                      @else
   │                          <span class="badge-danger">Not Verified</span>
   │                      @endif
   │                  </td>
   │                  <td>
   │                      <button>View Details</button>
   │                      <button>Delete</button>
   │                  </td>
   │              </tr>
   │          @endforeach
   │      </tbody>
   │  </table>
   │
   └─ PAGINATION
      {{ $users->links() }}
      
9. ADMIN UBAH ROLE USER
   ↓
   Change role dropdown → AJAX request
   
   PUT /admin/users/42/role
   
10. CONTROLLER UPDATE ROLE
    ↓
    AdminController@updateUserRole($userId, $request)
    │
    ├─ Validate:
    │  $request->validate([
    │      'role' => 'required|in:student,instructor,admin'
    │  ])
    │
    ├─ Update user:
    │  $user = User::findOrFail($userId)
    │  $user->update(['role' => $request->role])
    │
    ├─ Log activity:
    │  Log::info("User {$user->email} role changed to {$request->role}")
    │
    ├─ Notify user:
    │  Mail::send('emails.role-changed', [...], $user->email)
    │
    └─ Return success response
    
11. ADMIN KLIK "DELETE USER"
    ↓
    DELETE /admin/users/42
    
12. CONTROLLER DELETE USER
    ↓
    AdminController@deleteUser($userId)
    │
    ├─ Check if user is only admin:
    │  $adminCount = User::where('role', 'admin')->count()
    │  if ($adminCount === 1 && $user->role === 'admin') {
    │      abort(403, 'Cannot delete the only admin')
    │  }
    │
    ├─ Delete related data:
    │  - CourseRegistrations
    │  - Orders
    │  - Submissions
    │  - Notifications
    │
    ├─ Delete user:
    │  $user->delete() (cascade delete via foreign keys)
    │
    └─ Redirect with success
    
13. USER DELETED
    ↓
    ✓ User dan semua related data terhapus
```

---

## 📊 DIAGRAM ALUR KESELURUHAN

```
┌─────────────────────────────────────────────────────────────┐
│                      BROWSER USER                             │
│  User akses https://otakatikaacademy.com                    │
└─────────────────────────────────────────────────────────────┘
                        ↓
        Request ke Laravel Application Server
                        ↓
┌─────────────────────────────────────────────────────────────┐
│                   ROUTING (routes/web.php)                   │
│  Cek URL path → Match dengan route pattern                 │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│              MIDDLEWARE (Security Layer)                      │
│  ├─ Authentication ('auth')                                 │
│  ├─ Authorization ('admin', 'instructor')                   │
│  ├─ Email Verification ('verified')                         │
│  └─ Rate Limiting ('throttle')                              │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│              CONTROLLER (app/Http/Controllers/)              │
│  ├─ Get request data ($request)                             │
│  ├─ Validate input                                          │
│  ├─ Process business logic                                  │
│  └─ Interact with Model                                     │
└─────────────────────────────────────────────────────────────┘
                        ↓
        ┌──────────────────────────────────┐
        ↓                                  ↓
   ┌─────────────┐              ┌──────────────────┐
   │   MODEL     │              │    SERVICE       │
   │ (Eloquent)  │              │ (Business Logic) │
   │ Database    │              │ External APIs    │
   │ Queries     │              │ (Midtrans, etc)  │
   └─────────────┘              └──────────────────┘
        ↓                                  ↓
┌─────────────────────────────────────────────────────────────┐
│                 DATABASE (Oracle/SQL)                         │
│  └─ Execute queries, store/retrieve data                    │
└─────────────────────────────────────────────────────────────┘
                        ↓
                 Return data to Controller
                        ↓
┌─────────────────────────────────────────────────────────────┐
│              VIEW (resources/views/)                          │
│  ├─ Blade Template Engine                                   │
│  ├─ Interpolate data: {{ $variable }}                       │
│  ├─ Control structures: @if, @foreach, dll                  │
│  └─ Generate HTML                                           │
└─────────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│            FRONTEND ASSETS (CSS, JavaScript)                 │
│  ├─ Tailwind CSS (styling)                                  │
│  ├─ Vue 3 (interactivity)                                   │
│  └─ Vite (bundling)                                         │
└─────────────────────────────────────────────────────────────┘
                        ↓
        Response HTTP (HTML + CSS + JS)
                        ↓
┌─────────────────────────────────────────────────────────────┐
│                   BROWSER RENDER                              │
│  ├─ Parse HTML                                              │
│  ├─ Apply CSS styles                                        │
│  ├─ Execute JavaScript                                      │
│  └─ Display to user                                         │
└─────────────────────────────────────────────────────────────┘
```

Ini adalah alur complete dari satu request sampai response ditampilkan di browser!

```
1. USER KLIK TOMBOL "Beli Sekarang"
   ↓
   GET /checkout/123 (courseId)
   
2. CHECKOUT CONTROLLER
   ↓
   PaymentController@checkout($courseId)
   │
   ├─ Validasi:
   │  - User login?
   │  - Course exists?
   │  - User sudah daftar?
   │
   ├─ Get data:
   │  $course = Course::find($courseId)
   │  $user = auth()->user()
   │
   └─ Return checkout view
   
3. USER LIHAT FORM PEMBAYARAN
   ↓
   resources/views/payment/checkout.blade.php
   │
   ├─ Form dengan detail:
   │  - Course name & price
   │  - Voucher input (optional)
   │
   └─ Tombol "Bayar Sekarang"
   
4. USER KLIK "BAYAR SEKARANG"
   ↓
   POST /checkout/process/123
   
5. PAYMENT CONTROLLER - PROCESS
   ↓
   PaymentController@processPayment($courseId)
   │
   ├─ Validasi voucher (jika ada)
   │  $discount = VoucherService::validate($voucherCode)
   │
   ├─ Buat Order di database:
   │  Order::create([
   │      'user_id' => $user->id,
   │      'course_id' => $courseId,
   │      'amount' => $course->price - $discount,
   │      'status' => 'pending'
   │  ])
   │
   ├─ Panggil SERVICE - MidtransService:
   │  $snapToken = $midtransService->createSnapToken($order)
   │  ↓
   │  Koneksi ke Midtrans API
   │  ↓
   │  Midtrans generate token pembayaran
   │  ↓
   │  Return token
   │
   └─ Return snap token ke frontend
   
6. FRONTEND - LOAD MIDTRANS SNAP
   ↓
   Vue 3 / JavaScript:
   ```
   midtrans.snap.pay(snapToken, {
       onSuccess: function() { ...bayar sukses... },
       onPending: function() { ...pending... },
       onError: function() { ...error... }
   })
   ```
   
7. USER LIHAT PAYMENT GATEWAY (Midtrans)
   ↓
   Transfer bank / E-wallet / Kartu Kredit
   ↓
   USER MELAKUKAN PEMBAYARAN
   
8. MIDTRANS KIRIM WEBHOOK NOTIFICATION
   ↓
   POST /checkout/notification
   │
   └─ Data: order_id, status, signature
   
9. PAYMENT CONTROLLER - HANDLE NOTIFICATION
   ↓
   PaymentController@handleNotification($request)
   │
   ├─ Verifikasi signature:
   │  $isValid = $midtransService->verifyWebhookSignature(...)
   │  
   │  Jika signature tidak valid → REJECT
   │
   ├─ Cek status pembayaran:
   │  if ($status == 'settlement' || 'capture') {
   │      
   │      ├─ Update order status:
   │      │  Order::find($orderId)->update(['status' => 'paid'])
   │      │
   │      ├─ Daftarkan user ke course:
   │      │  CourseRegistration::create([
   │      │      'user_id' => $order->user_id,
   │      │      'course_id' => $order->course_id
   │      │  ])
   │      │
   │      ├─ Kirim email notification:
   │      │  Mail::send('emails.payment-success', [...])
   │      │
   │      └─ Award achievement (jika applicable)
   │         $achievementService->checkFirstCourse(...)
   │  }
   │
   └─ Return response ke Midtrans
   
10. USER MENERIMA EMAIL CONFIRMATION
    ↓
    "Pembayaran berhasil! Sekarang Anda terdaftar di course..."
    
11. USER BISA MENGAKSES COURSE
    ↓
    GET /student/course/123
    ↓
    StudentController@courseDetail($registrationId)
    ↓
    Lihat materials, assignments, quizzes, forum
```

---

## ✅ Best Practices

### 1. Model
```php
✓ DO:
- Gunakan Eloquent relationships
- Define fillable/hidden properties
- Tambahkan validasi business logic
- Gunakan query scopes untuk query kompleks

✗ DON'T:
- Raw SQL queries di model
- Business logic kompleks di model
- Fetch semua records tanpa limit
```

### 2. Controller
```php
✓ DO:
- Keep controller methods fokus dan kecil
- Gunakan dependency injection
- Return meaningful responses
- Validate input dengan FormRequest

✗ DON'T:
- Put complex logic di controller
- Direct DB queries (gunakan model)
- Terlalu banyak methods di 1 controller
```

### 3. Views
```php
✓ DO:
- Gunakan Blade syntax ({{ }}, @if, @foreach)
- Reuse components (@include, @component)
- Gunakan translation keys
- Keep views simple & presentational

✗ DON'T:
- Business logic di view
- Direct DB queries di view
- Hardcoded strings (gunakan translation)
```

### 4. Routes
```php
✓ DO:
- Group routes dengan middleware
- Gunakan named routes
- Use resource routing untuk CRUD
- Organize dengan prefix

✗ DON'T:
- Put logic di route closures
- Unprotected admin routes
- Ambiguous route names
```

### 5. Database
```php
✓ DO:
- Create migrations untuk schema changes
- Use foreign keys untuk relasi
- Seed data untuk testing
- Use migrations untuk version control

✗ DON'T:
- Manual SQL commands
- Skip migrations
- Delete production data
```

### 6. Services
```php
✓ DO:
- Extract shared logic ke services
- Use untuk external API calls
- Testable dan reusable
- Keep thin dan focused

✗ DON'T:
- Put everything di service
- Tight coupling dengan controller
- Business logic di model & service bersamaan
```

### 7. Security
```php
✓ DO:
- Always validate input
- Sanitize output
- Use CSRF tokens di forms
- Hash passwords
- Check authorization di middleware
- Escape user data di views
- Use prepared statements (Eloquent)

✗ DON'T:
- Trust user input
- Store plaintext passwords
- Skip validation
- Output user data tanpa escape
- Allow SQL injection risks
```

---

## 📁 Struktur Folder Project

```
OtakAtikAcademy/
├── app/
│   ├── Http/
│   │   ├── Controllers/        ← Business logic
│   │   ├── Middleware/         ← Security layers
│   │   └── Requests/           ← Form validation
│   ├── Models/                 ← Data models
│   ├── Services/               ← Shared business logic
│   ├── Events/                 ← Event dispatching
│   └── Listeners/              ← Event handlers
│
├── routes/
│   ├── web.php                 ← Web routes
│   └── api.php                 ← API routes
│
├── resources/
│   ├── views/                  ← Blade templates
│   │   ├── layouts/
│   │   ├── components/
│   │   ├── auth/
│   │   ├── courses/
│   │   ├── admin/
│   │   └── ...
│   ├── css/                    ← Tailwind CSS
│   ├── js/                     ← Vue 3 + JavaScript
│   └── lang/                   ← Multi-language
│       ├── en/
│       └── id/
│
├── database/
│   ├── migrations/             ← Schema definitions
│   ├── seeders/                ← Dummy data
│   └── factories/              ← Model factories
│
├── config/                     ← Configuration files
├── bootstrap/
│   └── app.php                 ← App bootstrapping
├── storage/                    ← Files & logs
├── tests/                      ← Unit & feature tests
│
└── package.json / composer.json
```

---

## 🎯 Kesimpulan

### Alur Data dalam OtakAtik Academy:

```
REQUEST dari Browser
    ↓
ROUTES (web.php) - Tentukan controller action
    ↓
MIDDLEWARE - Cek authentication & authorization
    ↓
CONTROLLER - Process logic, query data
    ↓
MODEL - Interact dengan database
    ↓
SERVICES - Complex business logic (optional)
    ↓
VIEW - Return HTML dengan data
    ↓
RESPONSE kembali ke Browser
```

Setiap layer punya tanggung jawab spesifik. Dengan memisahkan concerns ini, code jadi:
- **Modular** - Mudah dimodifikasi
- **Testable** - Mudah di-unit test
- **Maintainable** - Mudah di-maintain
- **Scalable** - Mudah di-scale

---

**Happy coding! 🚀**
