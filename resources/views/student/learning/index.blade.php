<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        /* [BARU] Scroll Progress Circle */
        .progress-ring__circle {
            transition: stroke-dashoffset 0.35s;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col">

    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm z-20 flex-shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.course-detail', $registration->id) }}" class="text-gray-500 hover:text-gray-800 transition">
                <i class="fas fa-chevron-left mr-1"></i> Kembali ke beranda
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <h1 class="text-lg font-bold text-gray-800 truncate max-w-md">{{ $course->title }}</h1>
        </div>
        
        <div class="flex items-center gap-4">
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
        
        <aside class="w-80 bg-white border-r border-gray-200 flex flex-col overflow-hidden z-10 hidden md:flex">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wide">Daftar Modul</h3>
            </div>
            
            <div class="flex-1 overflow-y-auto sidebar-scroll p-2 space-y-2">
                @foreach($course->modules as $module)
                <div x-data="{ open: true }" class="mb-2">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-3 bg-gray-100 rounded-lg hover:bg-gray-200 transition text-left">
                        <span class="font-bold text-gray-800 text-sm">{{ $module->title }}</span>
                        <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform" :class="{'rotate-180': !open}"></i>
                    </button>

                    <div x-show="open" class="mt-1 space-y-1 ml-2 pl-2 border-l-2 border-gray-200">
                        
                        @foreach($module->materials as $mat)
                        @php
                            $isActive = ($type == 'material' && $currentContent->id == $mat->id);
                            // Cek status completed dari DB (optional, bisa ditambah nanti)
                            $isCompleted = \App\Models\CourseProgress::where('user_id', Auth::id())
                                ->where('content_id', $mat->id)
                                ->where('content_type', 'material')
                                ->where('is_completed', true)
                                ->exists();
                        @endphp
                        <a href="{{ route('student.learning.content', [$course->id, 'material', $mat->id]) }}" 
                           class="flex items-center gap-3 p-3 rounded-md text-sm transition {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-50' }}">
                            
                            <div class="w-5 flex justify-center">
                                @if($isCompleted)
                                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                @elseif($isActive)
                                    <i class="fas fa-play-circle text-blue-600"></i>
                                @else
                                    <i class="far fa-circle text-gray-400 text-xs"></i>
                                @endif
                            </div>
                            
                            <span class="flex-1 truncate">{{ $mat->title }}</span>
                            
                            @if($mat->type == 'video') <i class="fas fa-video text-gray-400 text-xs"></i> @endif
                        </a>
                        @endforeach

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

        <main id="mainScrollContainer" class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-10 relative">
            
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden min-h-[80vh] flex flex-col">
                
                <div class="p-6 md:p-8 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                    <div class="flex items-center gap-4">
                        @if($type == 'material')
                        <div class="relative w-10 h-10 flex items-center justify-center flex-shrink-0">
                            <svg class="progress-ring" width="40" height="40">
                                <circle class="text-gray-200" stroke="currentColor" stroke-width="3" fill="transparent" r="16" cx="20" cy="20"/>
                                <circle id="progressCircle" class="text-green-500 progress-ring__circle" stroke="currentColor" stroke-width="3" fill="transparent" r="16" cx="20" cy="20" stroke-dasharray="100 100" stroke-dashoffset="100"/>
                            </svg>
                            <div id="progressIcon" class="absolute text-[10px] font-bold text-gray-600">0%</div>
                        </div>
                        @endif

                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900">{{ $currentContent->title }}</h2>
                            @if($type == 'material' && $currentContent->description)
                                @endif
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
                            <div class="prose max-w-none text-gray-700 leading-relaxed text-lg">
                                {!! $currentContent->description !!}
                            </div>
                        @endif

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

                        <div class="h-20"></div>

                    @elseif($type == 'quiz')
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

                @php
                    // Ratakan semua konten jadi satu array urut
                    $flatList = [];
                    foreach($course->modules as $m) {
                        foreach($m->materials as $mat) { $flatList[] = ['type' => 'material', 'id' => $mat->id]; }
                        foreach($m->quizzes as $q) { $flatList[] = ['type' => 'quiz', 'id' => $q->id]; }
                    }

                    // Cari index item saat ini
                    $currentIndex = -1;
                    foreach($flatList as $idx => $item) {
                        if($item['type'] == $type && $item['id'] == $currentContent->id) {
                            $currentIndex = $idx;
                            break;
                        }
                    }

                    // Tentukan URL Previous & Next
                    $prevUrl = ($currentIndex > 0) 
                        ? route('student.learning.content', [$course->id, $flatList[$currentIndex-1]['type'], $flatList[$currentIndex-1]['id']]) 
                        : '#';
                        
                    $nextUrl = ($currentIndex < count($flatList) - 1)
                        ? route('student.learning.content', [$course->id, $flatList[$currentIndex+1]['type'], $flatList[$currentIndex+1]['id']])
                        : route('student.courses'); // Kalau habis, balik ke list course
                @endphp

                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-between items-center sticky bottom-0 z-10">
                    <a href="{{ $prevUrl }}" class="px-5 py-2.5 text-gray-600 font-medium hover:bg-gray-200 rounded-lg transition {{ $currentIndex <= 0 ? 'opacity-50 pointer-events-none' : '' }}">
                        <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
                    </a>
                    
                    @if($type == 'material')
                        <button id="btnNext" type="button" disabled 
                            class="px-6 py-2.5 bg-gray-300 text-gray-500 font-bold rounded-lg cursor-not-allowed transition flex items-center gap-2">
                            <span>Selesai & Lanjut</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mt-8 text-center text-gray-400 text-sm">
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
            
            // Config Circle
            const radius = progressCircle.r.baseVal.value;
            const circumference = radius * 2 * Math.PI;
            progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
            progressCircle.style.strokeDashoffset = circumference;

            let isCompleted = false;
            
            // URL Target (Dari PHP)
            const nextUrl = "{{ $nextUrl }}";
            const completeUrl = "{{ route('student.learning.complete-material', [$course->id, $currentContent->id ?? 0]) }}";

            // Cek apakah user ini SEBELUMNYA sudah menyelesaikan materi ini?
            // (Kita inject status dari PHP biar ga perlu scroll ulang kalau balik lagi)
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

            // Fungsi Unlock
            function unlockNextButton() {
                isCompleted = true;
                btnNext.disabled = false;
                btnNext.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                btnNext.classList.add('bg-green-600', 'text-white', 'hover:bg-green-700', 'shadow-md');
                
                // Update Visual Circle jadi Centang
                progressCircle.style.strokeDashoffset = 0;
                progressIcon.innerHTML = '<i class="fas fa-check text-green-600"></i>';
            }

            // Event Scroll Listener
            scrollContainer.addEventListener('scroll', () => {
                if (isCompleted) return; // Kalau udah, ga usah hitung lagi

                const scrollTop = scrollContainer.scrollTop;
                const scrollHeight = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                
                // Hitung Persentase
                let scrollPercent = scrollHeight > 0 ? (scrollTop / scrollHeight) : 1;
                if (scrollPercent > 1) scrollPercent = 1;

                // Update Circle
                const offset = circumference - (scrollPercent * circumference);
                progressCircle.style.strokeDashoffset = offset;
                progressIcon.innerText = Math.round(scrollPercent * 100) + '%';

                // Jika sudah mentok bawah (toleransi 10px)
                if (scrollHeight - scrollTop <= 10) {
                    unlockNextButton();
                    
                    // Kirim AJAX untuk simpan progress tanpa reload
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

            // Handle Klik Tombol Next
            btnNext.addEventListener('click', () => {
                if (!isCompleted) return;
                window.location.href = nextUrl;
            });
        });
    </script>

</body>
</html>