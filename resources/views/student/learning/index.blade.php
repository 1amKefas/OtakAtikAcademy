<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentContent->title }} - {{ $course->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Scrollbar Custom */
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 3px; }
        
        /* Active State Styling */
        .nav-item-active {
            background-color: #eff6ff; /* blue-50 */
            border-left: 4px solid #3b82f6; /* blue-500 */
            color: #1d4ed8; /* blue-700 */
        }
        .nav-item-inactive {
            border-left: 4px solid transparent;
            color: #4b5563; /* gray-600 */
        }
        
        /* Lock Icon for Locked Content */
        .locked { cursor: not-allowed; opacity: 0.6; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col">

    <!-- TOP NAVBAR (Minimalis) -->
    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm z-20 flex-shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.course-detail', $registration->id) }}" class="text-gray-500 hover:text-gray-800 transition">
                <i class="fas fa-chevron-left mr-1"></i> Kembali ke beranda
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <h1 class="text-lg font-bold text-gray-800 truncate max-w-md">{{ $course->title }}</h1>
        </div>
        
        <div class="flex items-center gap-4">
            <!-- Progress Bar -->
            <div class="hidden md:flex items-center gap-3 w-64">
                <span class="text-xs font-semibold text-gray-600">{{ $registration->progress }}% Selesai</span>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $registration->progress }}%"></div>
                </div>
                @if($registration->progress == 100)
                    <i class="fas fa-trophy text-yellow-500 text-lg animate-bounce"></i>
                @endif
            </div>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        
        <!-- LEFT SIDEBAR (Curriculum Navigation) -->
        <aside class="w-80 bg-white border-r border-gray-200 flex flex-col overflow-hidden z-10 hidden md:flex">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wide">Daftar Modul</h3>
            </div>
            
            <div class="flex-1 overflow-y-auto sidebar-scroll p-2 space-y-2">
                @foreach($course->modules as $module)
                <div x-data="{ open: true }" class="mb-2">
                    <!-- Module Header -->
                    <button @click="open = !open" class="w-full flex items-center justify-between p-3 bg-gray-100 rounded-lg hover:bg-gray-200 transition text-left">
                        <span class="font-bold text-gray-800 text-sm">{{ $module->title }}</span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform" :class="{'rotate-180': !open}"></i>
                    </button>

                    <!-- Module Items -->
                    <div x-show="open" class="mt-1 space-y-1 ml-2 pl-2 border-l-2 border-gray-200">
                        
                        <!-- Materi -->
                        @foreach($module->materials as $mat)
                        @php
                            $isActive = ($type == 'material' && $currentContent->id == $mat->id);
                            // Logic Lock: Nanti diisi (sementara unlocked)
                            $isLocked = false; 
                        @endphp
                        <a href="{{ $isLocked ? '#' : route('student.learning.content', [$course->id, 'material', $mat->id]) }}" 
                           class="flex items-center gap-3 p-3 rounded-md text-sm transition {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-50' }} {{ $isLocked ? 'locked' : '' }}">
                            
                            <div class="w-5 flex justify-center">
                                @if($isLocked)
                                    <i class="fas fa-lock text-gray-400 text-xs"></i>
                                @elseif($isActive)
                                    <i class="fas fa-play-circle text-blue-600"></i>
                                @else
                                    <i class="far fa-circle text-gray-400 text-xs"></i> <!-- Check-circle kalau completed -->
                                @endif
                            </div>
                            
                            <span class="flex-1 truncate">{{ $mat->title }}</span>
                            
                            @if($mat->type == 'video') <i class="fas fa-video text-gray-400 text-xs"></i> @endif
                        </a>
                        @endforeach

                        <!-- Quiz -->
                        @foreach($module->quizzes as $quiz)
                        @php $isActive = ($type == 'quiz' && $currentContent->id == $quiz->id); @endphp
                        <a href="{{ route('student.learning.content', [$course->id, 'quiz', $quiz->id]) }}" 
                           class="flex items-center gap-3 p-3 rounded-md text-sm transition {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-50' }}">
                            <div class="w-5 flex justify-center">
                                <i class="fas fa-question-circle {{ $isActive ? 'text-blue-600' : 'text-purple-500' }}"></i>
                            </div>
                            <span class="flex-1 truncate">{{ $quiz->title }}</span>
                            <span class="text-xs bg-purple-100 text-purple-600 px-1.5 py-0.5 rounded">Quiz</span>
                        </a>
                        @endforeach

                    </div>
                </div>
                @endforeach
            </div>
        </aside>

        <!-- MAIN CONTENT AREA (Right Side) -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-10 relative">
            
            <!-- Content Card -->
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden min-h-[80vh] flex flex-col">
                
                <!-- Content Header -->
                <div class="p-6 md:p-8 border-b border-gray-100">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $currentContent->title }}</h2>
                    @if($type == 'material' && $currentContent->description)
                        <!-- Optional subtitle/desc -->
                    @endif
                </div>

                <!-- Content Body -->
                <div class="p-6 md:p-8 flex-1">
                    
                    @if($type == 'material')
                        <!-- Tampilan Materi -->
                        
                        <!-- 1. VIDEO -->
                        @if($currentContent->type == 'video' && $currentContent->external_url)
                            <div class="aspect-w-16 aspect-h-9 mb-6 bg-black rounded-xl overflow-hidden shadow-lg">
                                <iframe src="{{ str_replace('watch?v=', 'embed/', $currentContent->external_url) }}" frameborder="0" allowfullscreen class="w-full h-full"></iframe>
                            </div>
                        @endif

                        <!-- 2. TEXT / ARTIKEL -->
                        @if($currentContent->description)
                            <div class="prose max-w-none text-gray-700 leading-relaxed">
                                {!! $currentContent->description !!} <!-- Render HTML from TinyMCE -->
                            </div>
                        @endif

                        <!-- 3. FILE DOWNLOAD -->
                        @if($currentContent->file_path)
                            <div class="mt-8 p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm text-blue-600 text-xl">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">Materi Tambahan</h4>
                                        <p class="text-sm text-blue-600">{{ $currentContent->file_name ?? 'Document' }}</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($currentContent->file_path) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-md">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        @endif

                    @elseif($type == 'quiz')
                        <!-- Tampilan Quiz Intro -->
                        <div class="text-center py-10">
                            <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-clipboard-list text-4xl text-purple-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">Quiz: {{ $currentContent->title }}</h3>
                            <p class="text-gray-600 mb-8 max-w-md mx-auto">{{ $currentContent->description ?? 'Uji pemahaman Anda dengan mengerjakan kuis ini.' }}</p>
                            
                            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto mb-8 text-left">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-500 uppercase">Durasi</p>
                                    <p class="font-bold text-gray-800">{{ $currentContent->duration_minutes }} Menit</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-500 uppercase">Passing Score</p>
                                    <p class="font-bold text-green-600">{{ $currentContent->passing_score }}%</p>
                                </div>
                            </div>

                            <a href="{{ route('student.quiz.start', [$course->id, $currentContent->id]) }}" class="inline-block px-8 py-4 bg-purple-600 text-white rounded-xl font-bold text-lg shadow-lg hover:bg-purple-700 hover:-translate-y-1 transition transform">
                                Mulai Quiz Sekarang
                            </a>
                        </div>
                    @endif

                </div>

                <!-- Footer Actions (Next/Prev) -->
                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                    <button class="px-5 py-2.5 text-gray-600 font-medium hover:bg-gray-200 rounded-lg transition disabled:opacity-50" disabled>
                        <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
                    </button>
                    
                    <!-- Tombol Selesai & Lanjut -->
                    @if($type == 'material')
                    <form action="{{ route('student.learning.complete-material', [$course->id, $currentContent->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 hover:shadow-lg transition flex items-center gap-2">
                            Selesai & Lanjut <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Footer Copyright -->
            <div class="mt-8 text-center text-gray-400 text-sm">
                &copy; {{ date('Y') }} OtakAtik Academy Learning Platform
            </div>

        </main>
    </div>

</body>
</html>