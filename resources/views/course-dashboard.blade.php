<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .course-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <div class="sidebar w-64 min-h-screen p-6">
            <!-- Logo -->
            <div class="mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">O</span>
                    </div>
                    <span class="text-xl font-bold text-gray-800">OtakAtik</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="space-y-2">
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-home w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/course-dashboard" class="flex items-center gap-3 px-4 py-3 bg-orange-500 text-white rounded-lg">
                    <i class="fas fa-book w-5"></i>
                    <span>My Courses</span>
                </a>
                <a href="/profile" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-user w-5"></i>
                    <span>Profile</span>
                </a>
                <a href="/settings" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-cog w-5"></i>
                    <span>Settings</span>
                </a>
            </nav>
            
            <!-- User Section -->
            <div class="absolute bottom-6 left-6 right-6">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form action="/logout" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">HI, {{ strtoupper(Auth::user()->name) }}</h1>
                <p class="text-white/80 text-lg">WELCOME BACK!</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="glass-card rounded-2xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Courses</p>
                                    <p class="text-3xl font-bold mt-2">{{ $courses->count() }}</p>
                                </div>
                                <i class="fas fa-book-open text-2xl opacity-80"></i>
                            </div>
                        </div>
                        
                        <div class="glass-card rounded-2xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Active Courses</p>
                                    <p class="text-3xl font-bold mt-2">{{ $courses->where('status', 'paid')->count() }}</p>
                                </div>
                                <i class="fas fa-play-circle text-2xl opacity-80"></i>
                            </div>
                        </div>
                        
                        <div class="glass-card rounded-2xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Pending</p>
                                    <p class="text-3xl font-bold mt-2">{{ $courses->where('status', 'pending')->count() }}</p>
                                </div>
                                <i class="fas fa-clock text-2xl opacity-80"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Border Control Section -->
                    <div class="course-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Border Control</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                <span class="text-gray-700 font-medium">Saved No</span>
                                <span class="text-gray-900 font-bold">-</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                <span class="text-gray-700 font-medium">Overload</span>
                                <span class="text-gray-900 font-bold">-</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                <span class="text-gray-700 font-medium">Pending to the</span>
                                <span class="text-gray-900 font-bold">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Obscurity Section -->
                    <div class="course-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Obscurity</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <p class="text-sm font-medium text-blue-800">Decade UI/UX</p>
                                </div>
                                <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                                    <p class="text-sm font-medium text-green-800">Temperature UI/UX</p>
                                </div>
                                <div class="p-4 bg-gray-100 rounded-xl">
                                    <p class="text-sm text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-4 bg-purple-50 rounded-xl border border-purple-200">
                                    <p class="text-sm font-medium text-purple-800">All We have Soliton Decade</p>
                                </div>
                                <div class="p-4 bg-gray-100 rounded-xl">
                                    <p class="text-sm text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                                    <p class="text-sm font-medium text-yellow-800">Temperature Track</p>
                                </div>
                                <div class="p-4 bg-gray-100 rounded-xl">
                                    <p class="text-sm text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-4 bg-red-50 rounded-xl border border-red-200">
                                    <p class="text-sm font-medium text-red-800">Height Resolved</p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <p class="text-sm font-medium text-blue-800">Decade UI/UX</p>
                                </div>
                                <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                                    <p class="text-sm font-medium text-green-800">Temperature UI/UX</p>
                                </div>
                                <div class="p-4 bg-gray-100 rounded-xl">
                                    <p class="text-sm text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-4 bg-orange-50 rounded-xl border border-orange-200">
                                    <p class="text-sm font-medium text-orange-800">Material 1</p>
                                </div>
                                <div class="p-4 bg-gray-100 rounded-xl">
                                    <p class="text-sm text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-200">
                                    <p class="text-sm font-medium text-indigo-800">Material 2</p>
                                </div>
                                <div class="p-4 bg-pink-50 rounded-xl border border-pink-200">
                                    <p class="text-sm font-medium text-pink-800">Targets</p>
                                </div>
                                <div class="p-4 bg-gray-100 rounded-xl">
                                    <p class="text-sm text-gray-600 text-center">...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Extra Section -->
                    <div class="course-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Total Extra Decade UI/UX</h3>
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-8 text-center text-white">
                            <p class="text-lg font-semibold">Advanced UI/UX Design Mastery</p>
                            <p class="text-sm opacity-90 mt-2">Complete course package with expert mentorship</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <!-- My Courses Section -->
                    <div class="course-card rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800">My Courses</h3>
                            <a href="/course" class="text-orange-500 hover:text-orange-600 font-medium text-sm">
                                View All →
                            </a>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($courses->take(3) as $course)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-800">{{ $course->course }}</h4>
                                    <span class="status-badge status-{{ $course->status }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $course->sub_course1 }} - {{ $course->sub_course2 }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $course->kelas }}</span>
                                    <span class="font-semibold">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-book-open text-3xl mb-3 opacity-50"></i>
                                <p>No courses registered yet</p>
                                <a href="/course" class="text-orange-500 hover:text-orange-600 font-medium text-sm mt-2 inline-block">
                                    Register Now →
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Second Obscurity Section -->
                    <div class="course-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Obscurity</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <p class="text-xs font-medium text-blue-800">Decade UI/UX</p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs font-medium text-green-800">Temperature UI/UX</p>
                                </div>
                                <div class="p-3 bg-gray-100 rounded-lg">
                                    <p class="text-xs text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-3 bg-orange-50 rounded-lg">
                                    <p class="text-xs font-medium text-orange-800">Material 1</p>
                                </div>
                                <div class="p-3 bg-gray-100 rounded-lg">
                                    <p class="text-xs text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-3 bg-indigo-50 rounded-lg">
                                    <p class="text-xs font-medium text-indigo-800">Material 2</p>
                                </div>
                                <div class="p-3 bg-pink-50 rounded-lg">
                                    <p class="text-xs font-medium text-pink-800">Targets</p>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <p class="text-xs font-medium text-blue-800">Decade UI/UX</p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs font-medium text-green-800">Toggle Properties UI/UX</p>
                                </div>
                                <div class="p-3 bg-gray-100 rounded-lg">
                                    <p class="text-xs text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-3 bg-orange-50 rounded-lg">
                                    <p class="text-xs font-medium text-orange-800">Material 1</p>
                                </div>
                                <div class="p-3 bg-gray-100 rounded-lg">
                                    <p class="text-xs text-gray-600 text-center">...</p>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs font-medium text-purple-800">Vendor Foot</p>
                                </div>
                                <div class="p-3 bg-gray-100 rounded-lg">
                                    <p class="text-xs text-gray-600 text-center">...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="course-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="/course" class="flex items-center gap-3 p-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors">
                                <i class="fas fa-plus w-5"></i>
                                <span class="font-medium">Register New Course</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-download w-5"></i>
                                <span class="font-medium">Download Materials</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                                <i class="fas fa-calendar w-5"></i>
                                <span class="font-medium">View Schedule</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
    <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-fade-in flex items-center gap-3 max-w-md">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const notif = document.querySelector('.fixed.top-6');
            if(notif) notif.remove();
        }, 5000);
    </script>
    @endif
</body>
</html>