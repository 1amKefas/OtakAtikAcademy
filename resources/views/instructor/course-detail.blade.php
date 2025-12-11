<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $currentContent->title }} - {{ $course->title }}</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    
    <script src="{{ asset('js/preview-course.js') }}"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

        .nav-item-active {
            background: linear-gradient(to right, #eff6ff, #ffffff);
            border-left: 4px solid #3b82f6;
            color: #1d4ed8;
        }
        .nav-item-inactive {
            border-left: 4px solid transparent;
            color: #64748b;
        }
        
        .prose { max-width: 100% !important; color: #374151; }
        .aspect-w-16 { position: relative; padding-bottom: 56.25%; }
        .aspect-w-16 iframe { position: absolute; width: 100%; height: 100%; top: 0; left: 0; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 h-screen flex flex-col font-sans overflow-hidden" x-data="previewCourse">

    <header class="bg-indigo-900 text-white shadow-md h-16 flex items-center justify-between px-4 md:px-6 z-30 flex-shrink-0 relative">
        <div class="flex items-center gap-4">
            <button @click="toggleSidebar()" class="md:hidden text-white hover:text-gray-200">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <a href="{{ route('instructor.courses.manage', $course->id) }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition text-sm font-medium border border-white/10">
                <i class="fas fa-edit"></i>
                <span class="hidden sm:inline">Kembali ke Editor</span>
            </a>
            
            <div class="h-6 w-px bg-white/20 hidden sm:block"></div>
            
            <div class="flex items-center gap-2">
                <span class="bg-yellow-400 text-yellow-900 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Preview Mode</span>
                <h1 class="text-sm md:text-base font-bold truncate max-w-[200px] md:max-w-md opacity-90">{{ $course->title }}</h1>
            </div>
        </div>
        
        <div class="hidden md:block text-xs text-indigo-200">
            *Tampilan ini sama dengan yang dilihat siswa
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden relative">
        
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-30 md:hidden" style="display: none;"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:static inset-y-0 left-0 w-72 md:w-80 bg-white border-r border-gray-200 flex flex-col z-40 md:translate-x-0 transition-transform duration-300 shadow-2xl md:shadow-none">
            <div class="p-5 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-list-ul text-blue-500"></i> Daftar Modul
                </h3>
            </div>
            
            <div class="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar">
                @foreach($course->modules as $index => $module)
                <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                    <button @click="open = !open" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 transition text-left group bg-gray-50/50">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-md bg-white border border-gray-200 text-gray-500 flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                            <span class="font-bold text-gray-700 text-sm">{{ $module->title }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300" :class="{'rotate-180': !open}"></i>
                    </button>

                    <div x-show="open" x-collapse class="border-t border-gray-100">
                        <div class="py-1">
                            @foreach($module->materials as $mat)
                            @php $isActive = ($currentType == 'material' && $currentContent->id == $mat->id); @endphp
                            <a href="{{ route('instructor.courses.show', [$course->id, 'material', $mat->id]) }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm transition relative {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-50' }}">
                                <div class="flex-shrink-0 w-5 text-center">
                                    <i class="far fa-circle text-gray-400 text-xs"></i>
                                </div>
                                <span class="flex-1 truncate">{{ $mat->title }}</span>
                                <i class="{{ $mat->type == 'video' ? 'fas fa-play' : 'fas fa-align-left' }} text-[10px] text-gray-400"></i> 
                            </a>
                            @endforeach

                            @foreach($module->quizzes as $quiz)
                            @php $isActive = ($currentType == 'quiz' && $currentContent->id == $quiz->id); @endphp
                            <a href="{{ route('instructor.courses.show', [$course->id, 'quiz', $quiz->id]) }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm transition {{ $isActive ? 'nav-item-active' : 'nav-item-inactive hover:bg-gray-50' }}">
                                <div class="flex-shrink-0 w-5 text-center">
                                    <i class="fas fa-question-circle {{ $isActive ? 'text-blue-600' : 'text-purple-500' }}"></i>
                                </div>
                                <span class="flex-1 truncate">{{ $quiz->title }}</span>
                                <span class="text-[10px] font-bold bg-purple-100 text-purple-600 px-1.5 py-0.5 rounded">QUIZ</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto bg-white relative scroll-smooth">
            <div class="w-full min-h-full flex flex-col">
                <div class="bg-white shadow-none border-0 flex-1 flex flex-col">
                    
                    <div class="px-6 py-5 md:px-10 md:py-6 border-b border-gray-100 bg-white/95 backdrop-blur sticky top-0 z-20 flex items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1 block">
                                {{ $currentType == 'material' ? 'Materi Pembelajaran' : 'Evaluasi' }}
                            </span>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 leading-tight">{{ $currentContent->title }}</h2>
                        </div>
                    </div>

                    <div class="p-6 md:p-10 flex-1">
                        <div class="w-full">
                            @if($currentType == 'material')
                                @if(isset($currentContent->external_url) && $currentContent->external_url)
                                    <div class="aspect-w-16 aspect-h-9 mb-8 bg-black rounded-xl overflow-hidden shadow-lg ring-1 ring-gray-900/5 w-full">
                                        <iframe src="{{ str_replace('watch?v=', 'embed/', $currentContent->external_url) }}" frameborder="0" allowfullscreen class="w-full h-full"></iframe>
                                    </div>
                                @endif

                                @if($currentContent->description)
                                    <div class="prose max-w-none text-lg leading-relaxed w-full mb-8">
                                        {!! $currentContent->description !!}
                                    </div>
                                @endif

                                @if($currentContent->file_path)
                                    <div class="mt-8 p-5 bg-blue-50 border border-blue-100 rounded-xl flex items-center gap-4 w-full">
                                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm text-blue-600 text-2xl">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-800">Lampiran Materi</h4>
                                            <p class="text-sm text-blue-600 truncate">{{ $currentContent->file_name ?? 'Download File' }}</p>
                                        </div>
                                        <a href="{{ Storage::url($currentContent->file_path) }}" target="_blank" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-md transition">
                                            Download
                                        </a>
                                    </div>
                                @endif

                            @elseif($currentType == 'quiz')
                                <div class="flex flex-col items-center justify-center py-20 text-center w-full bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                    <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                                        <i class="fas fa-laptop-code text-5xl text-purple-600"></i>
                                    </div>
                                    <h3 class="text-3xl font-bold text-gray-900 mb-3">{{ $currentContent->title }}</h3>
                                    <p class="text-gray-500 mb-8 max-w-lg">{{ $currentContent->description ?? 'Deskripsi quiz tidak tersedia.' }}</p>
                                    
                                    <div class="flex gap-4 mb-8">
                                        <span class="px-4 py-2 bg-white border rounded-lg shadow-sm font-bold text-gray-700">
                                            <i class="far fa-clock text-blue-500 mr-2"></i> {{ $currentContent->duration_minutes }} Menit
                                        </span>
                                        <span class="px-4 py-2 bg-white border rounded-lg shadow-sm font-bold text-gray-700">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Pass: {{ $currentContent->passing_score }}%
                                        </span>
                                    </div>
                                    
                                    {{-- [MODIFIKASI] Tombol Demo untuk Instruktur --}}
                                    <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-200 mb-6 max-w-md">
                                        <p class="text-xs text-yellow-800 font-bold mb-2"><i class="fas fa-info-circle"></i> INFO INSTRUKTUR</p>
                                        <p class="text-sm text-yellow-700">Anda dapat mencoba mengerjakan quiz ini sebagai simulasi (Demo Mode). Hasil tidak akan mempengaruhi nilai siswa lain.</p>
                                    </div>

                                    <a href="{{ route('student.quiz.start', [$course->id, $currentContent->id]) }}" 
                                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-full shadow-lg hover:shadow-indigo-200 transition transform hover:-translate-y-0.5 flex items-center gap-2 mx-auto w-fit">
                                        <i class="fas fa-play"></i> Mulai Demo Quiz
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        // Logic Navigasi Next/Prev
                        $currentIndex = -1;
                        foreach($flatList as $idx => $item) {
                            if($item['type'] == $currentType && $item['data']->id == $currentContent->id) {
                                $currentIndex = $idx;
                                break;
                            }
                        }
                        $prevUrl = ($currentIndex > 0) ? route('instructor.courses.show', [$course->id, $flatList[$currentIndex-1]['type'], $flatList[$currentIndex-1]['data']->id]) : '#';
                        $nextUrl = ($currentIndex < count($flatList) - 1) ? route('instructor.courses.show', [$course->id, $flatList[$currentIndex+1]['type'], $flatList[$currentIndex+1]['data']->id]) : '#';
                    @endphp

                    <div class="px-6 py-5 md:px-10 border-t border-gray-100 bg-gray-50/50 backdrop-blur flex justify-between items-center sticky bottom-0 z-20">
                        <a href="{{ $prevUrl }}" class="px-6 py-3 text-gray-600 font-bold hover:bg-white rounded-xl border border-transparent hover:border-gray-200 transition flex items-center gap-2 {{ $currentIndex <= 0 ? 'opacity-50 pointer-events-none' : '' }}">
                            <i class="fas fa-arrow-left"></i> <span>Sebelumnya</span>
                        </a>
                        
                        <a href="{{ $nextUrl }}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition flex items-center gap-2 shadow-lg shadow-indigo-200 {{ $currentIndex >= count($flatList) - 1 ? 'opacity-50 pointer-events-none' : '' }}">
                            <span>Selanjutnya</span> <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>