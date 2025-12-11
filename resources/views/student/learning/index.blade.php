<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $currentContent->title }} - {{ $course->title }}</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="{{ asset('js/learning-app.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/learning.css') }}">
    <style>
        /* Embed Responsive Video Support */
        .video-embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 0.75rem; background: #000; }
        .video-embed-container iframe, .video-embed-container object, .video-embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
    </style>
</head>

<body class="bg-gray-100 dark:bg-slate-900 text-gray-800 dark:text-gray-200 h-screen flex flex-col transition-colors duration-300 font-sans"
      x-data="layout">

    <header class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-4 md:px-8 shadow-sm z-30 flex-shrink-0 transition-colors fixed w-full top-0">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <a href="{{ route('student.course-detail', $registration->id) }}" class="group flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition">
                    <i class="fas fa-arrow-left text-sm"></i>
                </div>
                <span class="text-sm font-medium hidden sm:inline">Kembali</span>
            </a>
            
            <div class="h-6 w-px bg-gray-300 dark:bg-gray-700 hidden sm:block"></div>
            <h1 class="text-base md:text-lg font-bold text-gray-800 dark:text-white truncate max-w-[200px] md:max-w-md">{{ $course->title }}</h1>
        </div>
        
        <div class="flex items-center gap-3 md:gap-6">
            <button @click="setTheme(theme === 'dark' ? 'light' : 'dark')" 
                    class="w-9 h-9 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-gray-700"
                    title="Ganti Tema">
                <i class="fas transition-transform duration-500 hover:rotate-45" :class="theme === 'dark' ? 'fa-sun text-yellow-400' : 'fa-moon text-slate-400'"></i>
            </button>

            <div class="hidden md:flex flex-col items-end w-48">
                <div class="flex justify-between w-full text-[10px] uppercase font-bold tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                    <span>Progress</span>
                    <span>{{ $registration->progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $registration->progress }}%"></div>
                </div>
            </div>
        </div>
    </header>

    <div class="h-16"></div>

    <div class="flex-1 flex overflow-hidden relative">
        
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/50 z-30 md:hidden"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 w-72 md:w-80 bg-white dark:bg-slate-800 border-r border-gray-200 dark:border-gray-700 flex flex-col z-40 md:translate-x-0 transition-transform duration-300 shadow-2xl md:shadow-none pt-16 md:pt-0">
            <div class="p-5 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-white text-sm uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-list-ul text-blue-500"></i> Daftar Modul
                </h3>
            </div>
            
            <div class="flex-1 overflow-y-auto p-3 space-y-3">
                @foreach($course->modules as $index => $module)
                <div x-data="{ open: {{ ($currentContent->course_module_id == $module->id) ? 'true' : ($loop->first ? 'true' : 'false') }} }" class="bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 dark:hover:bg-slate-700 transition text-left group">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-md bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-gray-400 flex items-center justify-center text-xs font-bold group-hover:bg-blue-500 group-hover:text-white transition">{{ $index + 1 }}</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300 text-sm">{{ $module->title }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300" :class="{'rotate-180': !open}"></i>
                    </button>

                    <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700">
                        <div class="py-1">
                            {{-- MATERI --}}
                            @foreach($module->materials as $mat)
                            @php
                                $isActive = ($type == 'material' && $currentContent->id == $mat->id);
                                $itemKey = 'material_' . $mat->id;
                                $isCompleted = isset($completedMap[$itemKey]);
                                // Deteksi Video di Sidebar (Icon Play)
                                $matIsVideo = false;
                                if($mat->file_path && \Illuminate\Support\Str::contains($mat->mime_type ?? '', 'video')) $matIsVideo = true;
                                if(\Illuminate\Support\Str::contains($mat->description, '<iframe')) $matIsVideo = true;
                            @endphp
                            <a href="{{ route('student.learning.content', [$course->id, 'material', $mat->id]) }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm transition relative {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                                <div class="flex-shrink-0 w-5 text-center">
                                    @if($isCompleted)
                                        <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                    @elseif($isActive)
                                        <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse mx-auto"></div>
                                    @else
                                        <i class="far fa-circle text-gray-400 dark:text-slate-500 text-xs"></i>
                                    @endif
                                </div>
                                <span class="flex-1 truncate">{{ $mat->title }}</span>
                                @if($matIsVideo) 
                                    <i class="fas fa-play text-[10px] text-red-400"></i> 
                                @else
                                    <i class="fas fa-align-left text-[10px] text-gray-400"></i>
                                @endif
                            </a>
                            @endforeach

                            {{-- QUIZ --}}
                            @foreach($module->quizzes as $quiz)
                            @php 
                                $isActive = ($type == 'quiz' && $currentContent->id == $quiz->id);
                                $quizKey = 'quiz_' . $quiz->id;
                                $isQuizCompleted = isset($completedMap[$quizKey]);
                            @endphp
                            <a href="{{ route('student.learning.content', [$course->id, 'quiz', $quiz->id]) }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm transition {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                                <div class="flex-shrink-0 w-5 text-center">
                                    @if($isQuizCompleted)
                                        <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                    @else
                                        <i class="fas fa-question-circle {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-purple-500 dark:text-purple-400' }}"></i>
                                    @endif
                                </div>
                                <span class="flex-1 truncate">{{ $quiz->title }}</span>
                                <span class="text-[10px] font-bold bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-300 px-1.5 py-0.5 rounded">QUIZ</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </aside>

        <main id="mainScrollContainer" class="flex-1 overflow-y-auto bg-white dark:bg-slate-900 relative transition-colors scroll-smooth">
            
            <div class="w-full min-h-full flex flex-col">
                
                <div class="bg-white dark:bg-slate-800 shadow-none border-0 flex-1 flex flex-col transition-colors">
                    
                    {{-- Logic Deteksi Konten Video --}}
                    @php
                        $isVideoContent = false;
                        $hasVideoUpload = false;
                        
                        if ($type == 'material') {
                            // Cek File Upload Video
                            if ($currentContent->file_path && \Illuminate\Support\Str::contains($currentContent->mime_type ?? '', 'video')) {
                                $isVideoContent = true;
                                $hasVideoUpload = true;
                            } 
                            // Cek Embed Youtube di Deskripsi
                            elseif (\Illuminate\Support\Str::contains($currentContent->description, '<iframe')) {
                                $isVideoContent = true;
                            }
                        }
                    @endphp

                    <div class="px-6 py-5 md:px-10 md:py-6 border-b border-gray-100 dark:border-gray-700 bg-white/95 dark:bg-slate-800/95 backdrop-blur sticky top-0 z-20 transition-colors flex items-center justify-between gap-4">
                        <div>
                            {{-- [UPDATE] Label Badge Dinamis --}}
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 inline-block
                                {{ $isVideoContent ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 
                                   ($type == 'quiz' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400') }}">
                                @if($type == 'quiz')
                                    EVALUASI / UJIAN
                                @elseif($isVideoContent)
                                    <i class="fas fa-video mr-1"></i> VIDEO PEMBELAJARAN
                                @else
                                    <i class="fas fa-book-open mr-1"></i> MATERI BACAAN
                                @endif
                            </span>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white leading-tight">{{ $currentContent->title }}</h2>
                        </div>

                        {{-- Progress Circle hanya untuk Bacaan biasa, kalau Video pake logic tracking --}}
                        @if($type == 'material' && !$isVideoContent)
                        <div class="relative w-12 h-12 flex items-center justify-center flex-shrink-0" title="Scroll Progress">
                            <svg class="progress-ring" width="44" height="44">
                                <circle class="text-gray-100 dark:text-slate-700" stroke="currentColor" stroke-width="4" fill="transparent" r="18" cx="22" cy="22"/>
                                <circle id="progressCircle" class="text-green-500 progress-ring__circle" stroke="currentColor" stroke-width="4" stroke-linecap="round" fill="transparent" r="18" cx="22" cy="22" stroke-dasharray="100 100" stroke-dashoffset="100"/>
                            </svg>
                            <div id="progressIcon" class="absolute text-[10px] font-bold text-gray-600 dark:text-gray-300 transition-all">0%</div>
                        </div>
                        @endif
                    </div>

                    <div id="contentBody" class="p-6 md:p-10 flex-1">
                        {{-- [UPDATE] Container Width: Full jika video, Max-4xl jika teks --}}
                        <div class="w-full {{ $isVideoContent ? '' : 'max-w-4xl' }} mx-auto">
                            
                            @if($type == 'material')
                                
                                {{-- 1. VIDEO UPLOAD PLAYER (Tracking Supported) --}}
                                @if($hasVideoUpload)
                                    <div class="relative w-full overflow-hidden rounded-xl shadow-2xl bg-black mb-8 aspect-video">
                                        <video id="courseVideo" class="absolute top-0 left-0 w-full h-full" controls controlsList="nodownload">
                                            <source src="{{ asset('storage/' . $currentContent->file_path) }}" type="video/mp4">
                                            Browser Anda tidak support video html5.
                                        </video>
                                    </div>
                                @endif

                                {{-- 2. DESKRIPSI / TEXT CONTENT / YOUTUBE EMBED --}}
                                @if($currentContent->description)
                                    <div class="prose dark:prose-invert max-w-none text-lg leading-relaxed w-full {{ $isVideoContent && !$hasVideoUpload ? 'video-embed-wrapper' : '' }}">
                                        {!! $currentContent->description !!}
                                    </div>
                                    
                                    {{-- CSS Khusus biar Iframe Youtube Responsif & Full Width --}}
                                    @if($isVideoContent && !$hasVideoUpload)
                                        <style>
                                            .video-embed-wrapper iframe { width: 100%; aspect-ratio: 16/9; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
                                        </style>
                                    @endif
                                @endif

                                {{-- 3. FILE ATTACHMENT (Non-Video) --}}
                                @if($currentContent->file_path && !$hasVideoUpload)
                                    <div class="mt-10 p-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 group hover:border-blue-300 dark:hover:border-blue-600 transition w-full">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-white dark:bg-slate-700 rounded-lg flex items-center justify-center shadow-sm text-blue-600 dark:text-blue-400 text-2xl group-hover:scale-110 transition">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 dark:text-gray-200">Lampiran Materi</h4>
                                                <p class="text-sm text-blue-600 dark:text-blue-400 truncate max-w-[200px]">{{ $currentContent->file_name ?? 'Download File' }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ Storage::url($currentContent->file_path) }}" target="_blank" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                @endif
                                
                                @if(!$isVideoContent)
                                <div class="h-32 flex items-center justify-center">
                                    <p class="text-xs text-gray-400 uppercase tracking-widest animate-pulse">Scroll to complete</p>
                                </div>
                                @endif

                            @elseif($type == 'quiz')
                                <div class="flex flex-col items-center justify-center py-20 text-center w-full">
                                    <div class="w-28 h-28 bg-purple-50 dark:bg-purple-900/20 rounded-full flex items-center justify-center mb-6 animate-bounce-slow">
                                        <i class="fas fa-laptop-code text-5xl text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Quiz: {{ $currentContent->title }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-10 max-w-lg text-lg leading-relaxed">{{ $currentContent->description ?? 'Siap menguji pemahamanmu? Kerjakan kuis ini dengan teliti.' }}</p>
                                    
                                    <div class="flex flex-wrap justify-center gap-6 mb-10 w-full max-w-2xl">
                                        <div class="flex-1 min-w-[140px] bg-gray-50 dark:bg-slate-700/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-600">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold mb-1">Durasi</p>
                                            <p class="text-xl font-bold text-gray-800 dark:text-white flex items-center justify-center gap-2">
                                                <i class="far fa-clock text-blue-500"></i> {{ $currentContent->duration_minutes }}m
                                            </p>
                                        </div>
                                        <div class="flex-1 min-w-[140px] bg-gray-50 dark:bg-slate-700/50 p-5 rounded-2xl border border-gray-100 dark:border-gray-600">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold mb-1">Passing Score</p>
                                            <p class="text-xl font-bold text-green-600 dark:text-green-400 flex items-center justify-center gap-2">
                                                <i class="fas fa-check-circle"></i> {{ $currentContent->passing_score }}%
                                            </p>
                                        </div>
                                    </div>

                                    <a href="{{ route('student.quiz.start', [$course->id, $currentContent->id]) }}" class="group relative px-10 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-full font-bold text-lg shadow-xl hover:shadow-purple-500/50 transition-all transform hover:-translate-y-1">
                                        <span class="relative z-10 flex items-center gap-2">Mulai Quiz Sekarang <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i></span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        // Logic Navigasi Next/Prev
                        $flatList = [];
                        foreach($course->modules as $m) {
                            foreach($m->materials as $mat) { $flatList[] = ['type' => 'material', 'id' => $mat->id]; }
                            foreach($m->quizzes as $q) { $flatList[] = ['type' => 'quiz', 'id' => $q->id]; }
                        }
                        $currentIndex = -1;
                        foreach($flatList as $idx => $item) {
                            if($item['type'] == $type && $item['id'] == $currentContent->id) {
                                $currentIndex = $idx;
                                break;
                            }
                        }
                        $prevUrl = ($currentIndex > 0) ? route('student.learning.content', [$course->id, $flatList[$currentIndex-1]['type'], $flatList[$currentIndex-1]['id']]) : '#';
                        $nextUrl = ($currentIndex < count($flatList) - 1) ? route('student.learning.content', [$course->id, $flatList[$currentIndex+1]['type'], $flatList[$currentIndex+1]['id']]) : route('student.courses');
                        
                        // Cek apakah current content sudah selesai
                        $currentKey = $type . '_' . $currentContent->id;
                        $alreadyDone = isset($completedMap[$currentKey]);
                    @endphp

                    {{-- Data Element untuk JavaScript --}}
                    <div id="content-data" 
                         class="hidden"
                         data-next-url="{{ $nextUrl }}"
                         data-complete-url="{{ route('student.learning.complete-material', [$course->id, $currentContent->id ?? 0]) }}"
                         data-track-time-url="{{ route('student.course.track-time', $course->id) }}"
                         data-already-done="{{ $alreadyDone ? '1' : '0' }}"
                         data-is-video="{{ $hasVideoUpload ? '1' : '0' }}" 
                         data-is-video-content="{{ $isVideoContent ? '1' : '0' }}">
                    </div>

                    <div class="px-6 py-5 md:px-10 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50 backdrop-blur flex justify-between items-center sticky bottom-0 z-20">
                        <a href="{{ $prevUrl }}" class="px-6 py-3 text-gray-600 dark:text-gray-400 font-bold hover:bg-white dark:hover:bg-slate-700 rounded-xl border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition flex items-center gap-2 {{ $currentIndex <= 0 ? 'opacity-50 pointer-events-none' : '' }}">
                            <i class="fas fa-arrow-left"></i> <span>Sebelumnya</span>
                        </a>
                        
                        @if($type == 'material')
                            {{-- [UPDATE] Tombol Next default Disabled kalau Video --}}
                            <button id="btnNext" type="button" 
                                @if(!$alreadyDone && $isVideoContent) disabled @endif
                                class="px-8 py-3 font-bold rounded-xl transition-all flex items-center gap-3 shadow-none 
                                {{ (!$alreadyDone && $isVideoContent) ? 'bg-gray-300 dark:bg-slate-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg' }}">
                                
                                @if(!$alreadyDone && $isVideoContent)
                                    <i class="fas fa-lock"></i> <span>Tonton Sampai Habis</span>
                                @else
                                    <span>Lanjut Materi</span> <i class="fas fa-arrow-right"></i>
                                @endif
                            </button>
                        @endif
                    </div>
                </div>
                
            </div>
        </main>
    </div>
</body>
</html>