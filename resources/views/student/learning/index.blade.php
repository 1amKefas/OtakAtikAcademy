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

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class', // Wajib biar dark mode jalan manual
            theme: { extend: {} }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Scrollbar Custom (Dark Mode Friendly) */
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 3px; }
        .dark .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #4b5563; }
        
        /* Active State Styling */
        .nav-item-active {
            background-color: #eff6ff; /* blue-50 */
            border-left: 4px solid #3b82f6; /* blue-500 */
            color: #1d4ed8; /* blue-700 */
        }
        /* Dark Mode Active State */
        .dark .nav-item-active {
            background-color: rgba(59, 130, 246, 0.15); /* blue-500 with opacity */
            border-left: 4px solid #60a5fa; /* blue-400 */
            color: #60a5fa; /* blue-400 */
        }

        .nav-item-inactive {
            border-left: 4px solid transparent;
            color: #4b5563; /* gray-600 */
        }
        .dark .nav-item-inactive {
            color: #9ca3af; /* gray-400 */
        }
        
        .locked { cursor: not-allowed; opacity: 0.6; }

        .progress-ring__circle {
            transition: stroke-dashoffset 0.35s;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 h-screen flex flex-col transition-colors duration-300"
      x-data="{ 
          theme: localStorage.getItem('theme') || 'system',
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

    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-6 shadow-sm z-20 flex-shrink-0 transition-colors">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.course-detail', $registration->id) }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition">
                <i class="fas fa-chevron-left mr-1"></i> Kembali
            </a>
            <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>
            <h1 class="text-lg font-bold text-gray-800 dark:text-white truncate max-w-md">{{ $course->title }}</h1>
        </div>
        
        <div class="flex items-center gap-4">
            
            <button @click="setTheme(theme === 'dark' ? 'light' : 'dark')" 
                    class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                    title="Ganti Tema">
                <i class="fas" :class="theme === 'dark' ? 'fa-sun' : 'fa-moon'"></i>
            </button>

            <div class="hidden md:flex items-center gap-3 w-64">
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $registration->progress }}% Selesai</span>
                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $registration->progress }}%"></div>
                </div>
                @if($registration->progress == 100)
                    <i class="fas fa-trophy text-yellow-500 text-lg animate-bounce"></i>
                @endif
            </div>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        
        <aside class="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden z-10 hidden md:flex transition-colors">
            <div class="p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm uppercase tracking-wide">Daftar Modul</h3>
            </div>
            
            <div class="flex-1 overflow-y-auto sidebar-scroll p-2 space-y-2">
                @foreach($course->modules as $module)
                <div x-data="{ open: true }" class="mb-2">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition text-left">
                        <span class="font-bold text-gray-800 dark:text-gray-200 text-sm">{{ $module->title }}</span>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 text-xs transition-transform" :class="{'rotate-180': !open}"></i>
                    </button>

                    <div x-show="open" class="mt-1 space-y-1 ml-2 pl-2 border-l-2 border-gray-200 dark:border-gray-700">
                        
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
                           class="flex items-center gap-3 p-3 rounded-md text-sm transition {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            
                            <div class="w-5 flex justify-center">
                                @if($isCompleted)
                                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                @elseif($isActive)
                                    <i class="fas fa-play-circle text-blue-600 dark:text-blue-400"></i>
                                @else
                                    <i class="far fa-circle text-gray-400 dark:text-gray-500 text-xs"></i>
                                @endif
                            </div>
                            
                            <span class="flex-1 truncate">{{ $mat->title }}</span>
                            
                            @if($mat->type == 'video') <i class="fas fa-video text-gray-400 dark:text-gray-500 text-xs"></i> @endif
                        </a>
                        @endforeach

                        @foreach($module->quizzes as $quiz)
                        @php $isActive = ($type == 'quiz' && $currentContent->id == $quiz->id); @endphp
                        <a href="{{ route('student.learning.content', [$course->id, 'quiz', $quiz->id]) }}" 
                           class="flex items-center gap-3 p-3 rounded-md text-sm transition {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <div class="w-5 flex justify-center">
                                <i class="fas fa-question-circle {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-purple-500 dark:text-purple-400' }}"></i>
                            </div>
                            <span class="flex-1 truncate">{{ $quiz->title }}</span>
                            <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 px-1.5 py-0.5 rounded">Quiz</span>
                        </a>
                        @endforeach

                    </div>
                </div>
                @endforeach
            </div>
        </aside>

        <main id="mainScrollContainer" class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6 lg:p-10 relative transition-colors">
            
            <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[80vh] flex flex-col transition-colors">
                
                <div class="p-6 md:p-8 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-10 transition-colors">
                    <div class="flex items-center gap-4">
                        @if($type == 'material')
                        <div class="relative w-10 h-10 flex items-center justify-center flex-shrink-0">
                            <svg class="progress-ring" width="40" height="40">
                                <circle class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="transparent" r="16" cx="20" cy="20"/>
                                <circle id="progressCircle" class="text-green-500 progress-ring__circle" stroke="currentColor" stroke-width="3" fill="transparent" r="16" cx="20" cy="20" stroke-dasharray="100 100" stroke-dashoffset="100"/>
                            </svg>
                            <div id="progressIcon" class="absolute text-[10px] font-bold text-gray-600 dark:text-gray-300">0%</div>
                        </div>
                        @endif

                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $currentContent->title }}</h2>
                        </div>
                    </div>
                </div>

                <div id="contentBody" class="p-6 md:p-8 flex-1">
                    
                    @if($type == 'material')
                        
                        @if($currentContent->type == 'video' && $currentContent->external_url)
                            <div class="aspect-w-16 aspect-h-9 mb-6 bg-black rounded-xl overflow-hidden shadow-lg">
                                <iframe src="{{ str_replace('watch?v=', 'embed/', $currentContent->external_url) }}" frameborder="0" allowfullscreen class="w-full h-full"></iframe>
                            </div>
                        @endif

                        @if($currentContent->description)
                            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                                {!! $currentContent->description !!}
                            </div>
                        @endif

                        @if($currentContent->file_path)
                            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center shadow-sm text-blue-600 dark:text-blue-400 text-xl">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 dark:text-gray-200">Materi Tambahan</h4>
                                        <p class="text-sm text-blue-600 dark:text-blue-400">{{ $currentContent->file_name ?? 'Document' }}</p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($currentContent->file_path) }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-md">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        @endif

                        <div class="h-20"></div>

                    @elseif($type == 'quiz')
                        <div class="text-center py-10">
                            <div class="w-24 h-24 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-clipboard-list text-4xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Quiz: {{ $currentContent->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">{{ $currentContent->description ?? 'Uji pemahaman Anda dengan mengerjakan kuis ini.' }}</p>
                            
                            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto mb-8 text-left">
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Durasi</p>
                                    <p class="font-bold text-gray-800 dark:text-gray-200">{{ $currentContent->duration_minutes }} Menit</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Passing Score</p>
                                    <p class="font-bold text-green-600 dark:text-green-400">{{ $currentContent->passing_score }}%</p>
                                </div>
                            </div>

                            <a href="{{ route('student.quiz.start', [$course->id, $currentContent->id]) }}" class="inline-block px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-lg shadow-lg hover:-translate-y-1 transition transform">
                                Mulai Quiz Sekarang
                            </a>
                        </div>
                    @endif

                </div>

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

                    $prevUrl = ($currentIndex > 0) 
                        ? route('student.learning.content', [$course->id, $flatList[$currentIndex-1]['type'], $flatList[$currentIndex-1]['id']]) 
                        : '#';
                        
                    $nextUrl = ($currentIndex < count($flatList) - 1)
                        ? route('student.learning.content', [$course->id, $flatList[$currentIndex+1]['type'], $flatList[$currentIndex+1]['id']])
                        : route('student.courses');
                @endphp

                <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center sticky bottom-0 z-10 transition-colors">
                    <a href="{{ $prevUrl }}" class="px-5 py-2.5 text-gray-600 dark:text-gray-300 font-medium hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition {{ $currentIndex <= 0 ? 'opacity-50 pointer-events-none' : '' }}">
                        <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
                    </a>
                    
                    @if($type == 'material')
                        <button id="btnNext" type="button" disabled 
                            class="px-6 py-2.5 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-bold rounded-lg cursor-not-allowed transition flex items-center gap-2">
                            <span>Selesai & Lanjut</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mt-8 text-center text-gray-400 dark:text-gray-500 text-sm">
                &copy; {{ date('Y') }} OtakAtik Academy Learning Platform
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

            if (@json($alreadyDone)) {
                unlockNextButton();
            }

            function unlockNextButton() {
                isCompleted = true;
                if(btnNext) {
                    btnNext.disabled = false;
                    btnNext.classList.remove('bg-gray-300', 'dark:bg-gray-700', 'text-gray-500', 'dark:text-gray-400', 'cursor-not-allowed');
                    btnNext.classList.add('bg-green-600', 'text-white', 'hover:bg-green-700', 'shadow-md');
                }
                
                if(progressCircle) {
                    progressCircle.style.strokeDashoffset = 0;
                    progressIcon.innerHTML = '<i class="fas fa-check text-green-600"></i>';
                }
            }

            if(scrollContainer && progressCircle) {
                scrollContainer.addEventListener('scroll', () => {
                    if (isCompleted) return;

                    const scrollTop = scrollContainer.scrollTop;
                    const scrollHeight = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                    
                    let scrollPercent = scrollHeight > 0 ? (scrollTop / scrollHeight) : 1;
                    if (scrollPercent > 1) scrollPercent = 1;

                    const offset = circumference - (scrollPercent * circumference);
                    progressCircle.style.strokeDashoffset = offset;
                    progressIcon.innerText = Math.round(scrollPercent * 100) + '%';

                    if (scrollHeight - scrollTop <= 10) {
                        unlockNextButton();
                        fetch(completeUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({})
                        });
                    }
                });
            }

            if(btnNext) {
                btnNext.addEventListener('click', () => {
                    if (!isCompleted) return;
                    window.location.href = nextUrl;
                });
            }
        });
    </script>
</body>
</html>