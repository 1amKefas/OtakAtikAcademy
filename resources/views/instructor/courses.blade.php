<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - OtakAtik Instructor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
        }
        
        .type-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-online { background: #dbeafe; color: #1e40af; }
        .type-hybrid { background: #fef3c7; color: #d97706; }
        .type-offline { background: #d1fae5; color: #065f46; }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active { background: #d1fae5; color: #065f46; }
        .status-inactive { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-blue-500">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-300">Instructor</span></h1>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="/instructor/dashboard" class="flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-blue-500 rounded-lg transition-colors">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/instructor/courses" class="flex items-center gap-3 px-4 py-3 bg-blue-500 rounded-lg text-white">
                            <i class="fas fa-book w-5"></i>
                            <span>My Courses</span>
                        </a>
                    </li>
                    <li>
                        <a href="/course" class="flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-blue-500 rounded-lg transition-colors">
                            <i class="fas fa-shopping-cart w-5"></i>
                            <span>Browse Courses</span>
                        </a>
                    </li>
                    <li>
                        <a href="/my-courses" class="flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-blue-500 rounded-lg transition-colors">
                            <i class="fas fa-user-graduate w-5"></i>
                            <span>As Student</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- User Section -->
            <div class="p-4 border-t border-blue-500">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-300 rounded-full flex items-center justify-center">
                        <span class="text-blue-800 font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-blue-200 truncate">Instructor</p>
                    </div>
                </div>
                <form action="/logout" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-blue-200 hover:bg-blue-500 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">My Courses</h1>
                        <p class="text-gray-600">Manage and view your teaching courses</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total: {{ $courses->count() }} courses</p>
                        <p class="text-sm font-medium text-gray-800">{{ date('M j, Y') }}</p>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Courses Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                        <!-- Course Header -->
                        <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative p-4">
                            <div class="absolute top-4 right-4 flex gap-2">
                                <span class="type-badge type-{{ $course->type }}">
                                    {{ ucfirst($course->type) }}
                                </span>
                                <span class="status-badge status-{{ $course->is_active ? 'active' : 'inactive' }}">
                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="text-xl font-bold line-clamp-1">{{ $course->title }}</h3>
                                <p class="text-sm opacity-90">{{ $course->registrations_count }} students</p>
                            </div>
                        </div>
                        
                        <!-- Course Content -->
                        <div class="p-6">
                            <!-- Course Info -->
                            <div class="space-y-3 text-sm text-gray-600 mb-4">
                                <div class="flex items-center justify-between">
                                    <span>Kuota:</span>
                                    <span class="font-medium">{{ $course->current_enrollment }}/{{ $course->max_quota }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Harga:</span>
                                    <span class="font-medium text-green-600">{{ $course->formatted_final_price }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Materi:</span>
                                    <span class="font-medium">{{ $course->materials_count ?? 0 }} files</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Tugas:</span>
                                    <span class="font-medium">{{ $course->assignments_count ?? 0 }} assignments</span>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span>Enrollment Rate</span>
                                    <span class="font-medium">
                                        {{ $course->max_quota > 0 ? round(($course->current_enrollment / $course->max_quota) * 100) : 0 }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" 
                                         style="width: {{ $course->max_quota > 0 ? ($course->current_enrollment / $course->max_quota) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('instructor.courses.show', $course->id) }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 px-4 rounded-lg transition-all text-sm font-medium">
                                    <i class="fas fa-cog mr-1"></i> Kelola
                                </a>
                                <a href="{{ route('instructor.courses.students', $course->id) }}" 
                                   class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition-all text-sm">
                                    <i class="fas fa-users"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($courses->count() === 0)
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
                    <div class="max-w-md mx-auto">
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chalkboard-teacher text-white text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Belum Ada Course yang Diajar</h3>
                        <p class="text-gray-600 mb-8">Anda belum ditugaskan untuk mengajar course apapun.</p>
                        <div class="space-y-3">
                            <a href="/course" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold px-8 py-4 rounded-lg transition-all hover:scale-105 shadow-lg">
                                <i class="fas fa-shopping-cart mr-2"></i> Beli Course sebagai Student
                            </a>
                            <p class="text-sm text-gray-500">Hubungi admin untuk ditugaskan sebagai instruktur</p>
                        </div>
                    </div>
                </div>
                @endif
            </main>
        </div>
    </div>

    <!-- Success Message Handler -->
    @if(session('success'))
    <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.fixed.top-6');
            if(alert) alert.remove();
        }, 5000);
    </script>
    @endif

</body>
</html>