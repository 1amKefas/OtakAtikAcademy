<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - OtakAtik Academy</title>
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
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .students-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .courses-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .assignments-card {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <div class="sidebar w-64 text-white flex flex-col">
            <div class="p-6 border-b border-blue-500">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-300">Instructor</span></h1>
            </div>
            
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="/instructor/dashboard" class="flex items-center gap-3 px-4 py-3 bg-blue-500 rounded-lg text-white">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/instructor/courses" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white shadow-lg">
                            <i class="fas fa-chalkboard-teacher w-5"></i>
                            <span>Course Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="/instructor/courses" class="flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-blue-500 rounded-lg transition-colors">
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

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Instructor Dashboard</h1>
                        <p class="text-gray-600">Welcome back, {{ Auth::user()->name }}!</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Instructor Panel</p>
                            <p class="text-sm font-medium text-gray-800">{{ date('l, F j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="courses-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Total Courses</p>
                                <p class="text-3xl font-bold">{{ $stats['total_courses'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Courses you teach</p>
                            </div>
                            <i class="fas fa-book-open text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <div class="students-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Total Students</p>
                                <p class="text-3xl font-bold">{{ $stats['total_students'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Across all courses</p>
                            </div>
                            <i class="fas fa-users text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <div class="assignments-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Total Assignments</p>
                                <p class="text-3xl font-bold">{{ $stats['total_assignments'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Created assignments</p>
                            </div>
                            <i class="fas fa-tasks text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <div class="stats-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Active Courses</p>
                                <p class="text-3xl font-bold">{{ $stats['active_courses'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Currently teaching</p>
                            </div>
                            <i class="fas fa-chalkboard-teacher text-3xl opacity-80"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">My Courses</h3>
                            <a href="/instructor/courses" class="text-blue-500 hover:text-blue-600 text-sm font-medium">
                                View All →
                            </a>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($taughtCourses as $course)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-book text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $course->title }}</p>
                                        <p class="text-sm text-gray-600">{{ $course->registrations_count }} students • {{ ucfirst($course->type) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $course->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <a href="{{ route('instructor.courses.show', $course->id) }}" class="text-blue-500 hover:text-blue-700 text-sm block mt-1">
                                        Manage ›
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-book-open text-3xl mb-3 opacity-50"></i>
                                <p>No courses assigned yet</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Recent Registrations</h3>
                            <span class="text-blue-500 hover:text-blue-600 text-sm font-medium">
                                {{ $recentRegistrations->count() }} new
                            </span>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($recentRegistrations as $registration)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($registration->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $registration->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $registration->course->title }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm text-gray-500">{{ $registration->created_at->diffForHumans() }}</span>
                                    <p class="text-xs text-green-600 font-medium">Enrolled</p>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-users text-3xl mb-3 opacity-50"></i>
                                <p>No recent registrations</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <a href="/instructor/courses" class="p-4 bg-blue-50 rounded-lg text-center hover:bg-blue-100 transition-colors group">
                            <i class="fas fa-book text-blue-500 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <p class="font-medium text-blue-800">Manage Courses</p>
                        </a>
                        
                        <a href="/course" class="p-4 bg-green-50 rounded-lg text-center hover:bg-green-100 transition-colors group">
                            <i class="fas fa-plus text-green-500 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <p class="font-medium text-green-800">Browse Courses</p>
                        </a>
                        
                        <a href="/my-courses" class="p-4 bg-purple-50 rounded-lg text-center hover:bg-purple-100 transition-colors group">
                            <i class="fas fa-user-graduate text-purple-500 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <p class="font-medium text-purple-800">As Student</p>
                        </a>
                        
                        <a href="#" class="p-4 bg-orange-50 rounded-lg text-center hover:bg-orange-100 transition-colors group">
                            <i class="fas fa-chart-line text-orange-500 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <p class="font-medium text-orange-800">Analytics</p>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <script src="{{ asset('js/instructor-dashboard.js') }}"></script>

</body>
</html>