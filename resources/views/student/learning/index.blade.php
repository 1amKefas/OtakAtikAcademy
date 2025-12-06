<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $currentContent->title }} - {{ $course->title }}</title>
    
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { 
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 
                        slate: { 
                            850: '#1e293b', 
                            900: '#0f172a' 
                        } 
                    }
                } 
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Scrollbar Halus */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background-color: #475569; }
        ::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

        /* Nav Item Active Styling */
        .nav-item-active {
            background: linear-gradient(to right, #eff6ff, #ffffff);
            border-left: 4px solid #3b82f6;
            color: #1d4ed8;
        }
        .dark .nav-item-active {
            background: linear-gradient(to right, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0));
            border-left: 4px solid #60a5fa;
            color: #60a5fa;
        }

        .nav-item-inactive {
            border-left: 4px solid transparent;
            color: #64748b;
        }
        .dark .nav-item-inactive {
            color: #94a3b8;
        }
        
        .locked { cursor: not-allowed; opacity: 0.5; filter: grayscale(100%); }

        .progress-ring__circle {
            transition: stroke-dashoffset 0.35s ease-out;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
        
        /* Dark Mode Typography & Width Fix */
        .prose { 
            max-width: 100% !important; /* [FIXED] Stretch Text Full Width */
            color: #374151; 
        }
        .dark .prose { color: #cbd5e1; }
        .dark .prose h1, .dark .prose h2, .dark .prose h3, .dark .prose h4, .dark .prose strong { color: #f1f5f9; }
        .dark .prose a { color: #60a5fa; }
        .dark .prose code { color: #e2e8f0; background-color: #1e293b; }
        
        /* Video aspect ratio container */
        .aspect-w-16 { position: relative; padding-bottom: 56.25%; }
        .aspect-w-16 iframe { position: absolute; width: 100%; height: 100%; top: 0; left: 0; }
    </style>
</head>

<body class="bg-gray-100 dark:bg-slate-900 text-gray-800 dark:text-gray-200 h-screen flex flex-col transition-colors duration-300 font-sans"
      x-data="{ 
          theme: localStorage.getItem('theme') || 'system',
          sidebarOpen: false,
          setTheme(val) {
              this.theme = val;
              localStorage.setItem('theme', val);
              if (val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }"
      x-init="$watch('theme', val => setTheme(val))">

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
                <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 dark:hover:bg-slate-700 transition text-left group">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-md bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-gray-400 flex items-center justify-center text-xs font-bold group-hover:bg-blue-500 group-hover:text-white transition">{{ $index + 1 }}</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300 text-sm">{{ $module->title }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300" :class="{'rotate-180': !open}"></i>
                    </button>

                    <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700">
                        <div class="py-1">
                            @foreach($module->materials as $mat)
                            @php
                                $isActive = ($type == 'material' && $currentContent->id == $mat->id);
                                $isCompleted = \App\Models\CourseProgress::where('user_id', Auth::id())
                                    ->where('content_id', $mat->id)
                                    ->where('content_type', 'material')
                                    ->where('is_completed', true)
                                    ->exists();
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
                                @if($mat->type == 'video') 
                                    <i class="fas fa-play text-[10px] text-gray-400"></i> 
                                @else
                                    <i class="fas fa-align-left text-[10px] text-gray-400"></i>
                                @endif
                            </a>
                            @endforeach

                            @foreach($module->quizzes as $quiz)
                            @php $isActive = ($type == 'quiz' && $currentContent->id == $quiz->id); @endphp
                            <a href="{{ route('student.learning.content', [$course->id, 'quiz', $quiz->id]) }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm transition {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                                <div class="flex-shrink-0 w-5 text-center">
                                    <i class="fas fa-question-circle {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-purple-500 dark:text-purple-400' }}"></i>
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
                    
                    <div class="px-6 py-5 md:px-10 md:py-6 border-b border-gray-100 dark:border-gray-700 bg-white/95 dark:bg-slate-800/95 backdrop-blur sticky top-0 z-20 transition-colors flex items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1 block">
                                {{ $type == 'material' ? 'Materi Pembelajaran' : 'Evaluasi' }}
                            </span>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white leading-tight">{{ $currentContent->title }}</h2>
                        </div>

                        @if($type == 'material')
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
                        <div class="w-full"> @if($type == 'material')
                                @if($currentContent->type == 'video' && $currentContent->external_url)
                                    <div class="aspect-w-16 aspect-h-9 mb-8 bg-black rounded-xl overflow-hidden shadow-lg ring-1 ring-gray-900/5 w-full">
                                        <iframe src="{{ str_replace('watch?v=', 'embed/', $currentContent->external_url) }}" frameborder="0" allowfullscreen class="w-full h-full"></iframe>
                                    </div>
                                @endif

                                @if($currentContent->description)
                                    <div class="prose dark:prose-invert max-w-none text-lg leading-relaxed w-full">
                                        {!! $currentContent->description !!}
                                    </div>
                                @endif

                                @if($currentContent->file_path)
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
                                
                                <div class="h-32 flex items-center justify-center">
                                    <p class="text-xs text-gray-400 uppercase tracking-widest animate-pulse">Scroll to complete</p>
                                </div>

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

                    <div class="px-6 py-5 md:px-10 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-slate-800/50 backdrop-blur flex justify-between items-center sticky bottom-0 z-20">
                        @php
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
                        @endphp

                        <a href="{{ $prevUrl }}" class="px-6 py-3 text-gray-600 dark:text-gray-400 font-bold hover:bg-white dark:hover:bg-slate-700 rounded-xl border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition flex items-center gap-2 {{ $currentIndex <= 0 ? 'opacity-50 pointer-events-none' : '' }}">
                            <i class="fas fa-arrow-left"></i> <span>Sebelumnya</span>
                        </a>
                        
                        @if($type == 'material')
                            <button id="btnNext" type="button" disabled 
                                class="px-8 py-3 bg-gray-300 dark:bg-slate-700 text-gray-500 dark:text-gray-400 font-bold rounded-xl cursor-not-allowed transition-all flex items-center gap-3 shadow-none">
                                <span>Lanjut Materi</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
                
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const scrollContainer = document.getElementById('mainScrollContainer');
            const btnNext = document.getElementById('btnNext');
            const progressCircle = document.getElementById('progressCircle');
            const progressIcon = document.getElementById('progressIcon');
            
            const radius = progressCircle ? progressCircle.r.baseVal.value : 0;
            const circumference = radius * 2 * Math.PI;
            
            if(progressCircle) {
                progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
                progressCircle.style.strokeDashoffset = circumference;
            }

            let isCompleted = false;
            const nextUrl = "{{ $nextUrl }}";
            const completeUrl = "{{ route('student.learning.complete-material', [$course->id, $currentContent->id ?? 0]) }}";

            @php
                $alreadyDone = \App\Models\CourseProgress::where('user_id', Auth::id())
                    ->where('content_id', $currentContent->id ?? 0)
                    ->where('content_type', 'material')
                    ->where('is_completed', true)
                    ->exists();
            @endphp

            if (@json($alreadyDone)) unlockNextButton();

            function unlockNextButton() {
                isCompleted = true;
                if(btnNext) {
                    btnNext.disabled = false;
                    btnNext.classList.remove('bg-gray-300', 'dark:bg-slate-700', 'text-gray-500', 'dark:text-gray-400', 'cursor-not-allowed', 'shadow-none');
                    btnNext.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'hover:shadow-lg', 'hover:shadow-blue-500/30', 'transform', 'hover:-translate-y-0.5');
                    btnNext.innerHTML = `<span>Selesai & Lanjut</span> <i class="fas fa-check-circle animate-pulse"></i>`;
                }
                if(progressCircle) {
                    progressCircle.style.strokeDashoffset = 0;
                    progressIcon.innerHTML = '<i class="fas fa-check text-green-500 text-xl"></i>';
                }
            }

            if(scrollContainer && progressCircle) {
                scrollContainer.addEventListener('scroll', () => {
                    if (isCompleted) return;
                    const scrollTop = scrollContainer.scrollTop;
                    const scrollHeight = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                    let percent = scrollHeight > 0 ? (scrollTop / scrollHeight) : 1;
                    if (percent > 1) percent = 1;
                    const offset = circumference - (percent * circumference);
                    progressCircle.style.strokeDashoffset = offset;
                    progressIcon.innerText = Math.round(percent * 100) + '%';
                    if (scrollHeight - scrollTop <= 50) {
                        unlockNextButton();
                        fetch(completeUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }, body: JSON.stringify({}) });
                    }
                });
            }

            if(btnNext) {
                btnNext.addEventListener('click', () => { if(isCompleted) window.location.href = nextUrl; });
            }
        });
    </script>
</body>
</html>