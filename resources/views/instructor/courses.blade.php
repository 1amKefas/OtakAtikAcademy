<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - OtakAtik Instructor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Load External Script --}}
    <script src="{{ asset('js/instructor-courses.js') }}"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* [UPDATE] Samakan Sidebar dengan Admin (Dark Theme) */
        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
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
        <div class="sidebar w-64 text-white flex flex-col hidden md:flex">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Instructor</span></h1>
            </div>
            
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="/instructor/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors">
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
                    
                    <li class="pt-4 mt-4 border-t border-gray-700"></li>
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Other</p>

                    <li>
                        <a href="/course" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors">
                            <i class="fas fa-shopping-cart w-5"></i>
                            <span>Browse Courses</span>
                        </a>
                    </li>
                    <li>
                        <a href="/my-courses" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors">
                            <i class="fas fa-user-graduate w-5"></i>
                            <span>As Student</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center border border-blue-400">
                        <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Instructor</p>
                    </div>
                </div>
                <form action="/logout" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-gray-300 hover:bg-red-900/50 hover:text-red-300 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">My Assigned Courses</h1>
                        <p class="text-gray-600">Kelola konten dan materi untuk kursus Anda</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total: {{ $courses->count() }} courses</p>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($courses as $course)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300 group">
                        <div class="h-40 bg-gray-100 relative overflow-hidden">
                            @if($course->image_url)
                                <img src="{{ $course->image_url }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center">
                                    <i class="fas fa-book-open text-white/20 text-5xl"></i>
                                </div>
                            @endif
                            
                            <div class="absolute top-4 right-4 flex gap-2">
                                <span class="px-2 py-1 bg-white/90 backdrop-blur text-xs font-bold rounded-md shadow-sm text-gray-700">
                                    {{ ucfirst($course->type) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900 line-clamp-1 mb-1" title="{{ $course->title }}">{{ $course->title }}</h3>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1"><i class="fas fa-users"></i> {{ $course->registrations_count }} Siswa</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="flex items-center gap-1 {{ $course->is_active ? 'text-green-600' : 'text-red-500' }}">
                                        <i class="fas fa-circle text-[8px]"></i> {{ $course->is_active ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-6 py-3 border-t border-b border-gray-100 bg-gray-50/50 rounded-lg">
                                <div class="text-center">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Modules</p>
                                    <p class="font-bold text-gray-700 text-lg">{{ $course->modules_count ?? 0 }}</p>
                                </div>
                                <div class="text-center border-l border-gray-200">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Contents</p>
                                    <p class="font-bold text-gray-700 text-lg">{{ ($course->materials_count ?? 0) + ($course->assignments_count ?? 0) }}</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-3">
                                <a href="{{ route('instructor.courses.manage', $course->id) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg transition-all text-sm font-medium shadow-sm hover:shadow">
                                    <i class="fas fa-edit mr-2"></i> Edit Konten
                                </a>
                                
                                <a href="{{ route('instructor.courses.students', $course->id) }}" 
                                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 py-2.5 px-4 rounded-lg transition-all border border-gray-200" title="Lihat Siswa">
                                    <i class="fas fa-users"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="fas fa-folder-open text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Belum Ada Kursus</h3>
                        <p class="text-gray-500 mt-2">Anda belum ditugaskan ke kursus manapun oleh Admin.</p>
                    </div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center gap-3 animate-bounce">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

</body>
</html>