<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course - {{ $course->title }}</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="course-id" content="{{ $course->id }}">

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <script src="{{ asset('js/instructor-manage.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/instructor.css') }}">
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden" x-data="contentManager">

    <aside class="sidebar w-64 text-white hidden md:flex flex-col flex-shrink-0">
        <div class="p-6 border-b border-gray-700">
            <h1 class="text-2xl font-bold">Instructor<span class="text-blue-400">Panel</span></h1>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>
            <a href="{{ route('instructor.courses') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-600 text-white rounded-lg shadow-lg">
                <i class="fas fa-chalkboard-teacher w-5"></i> Course Management
            </a>
        </nav>
        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center font-bold text-xs">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">Instructor</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm z-10">
            <div class="flex items-center gap-4">
                <a href="{{ route('instructor.courses') }}" class="text-gray-500 hover:text-gray-800 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <div class="h-6 w-px bg-gray-300"></div>
                <h2 class="text-lg font-bold text-gray-800 truncate max-w-md">{{ $course->title }}</h2>
                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-md font-medium border border-blue-100">
                    {{ ucfirst($course->type) }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.location.reload()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Selesai & Simpan
                </button>
                <a href="{{ route('instructor.courses.show', $course->id) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1 ml-2">
                    <i class="fas fa-external-link-alt"></i> Preview
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Kurikulum & Konten</h3>
                    <p class="text-gray-500 text-sm mt-1">Drag icon untuk mengubah urutan Modul atau Materi.</p>
                </div>
                <button @click="showModuleModal = true" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition flex items-center gap-2 font-medium">
                    <i class="fas fa-plus-circle"></i> Tambah Modul Baru
                </button>
            </div>

            <div id="modules-list" class="space-y-6 pb-20" data-course-id="{{ $course->id }}">
                @forelse($course->modules->sortBy('order') as $module)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden module-item transition hover:shadow-md" data-id="{{ $module->id }}" x-data="{ open: true }">
                    
                    <div class="p-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between group">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="text-gray-400 cursor-move hover:text-gray-600 handle p-2">
                                <i class="fas fa-grip-vertical text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">{{ $module->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $module->materials->count() + $module->quizzes->count() }} Item Konten
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button @click="openUpdateModuleModal({{ $module->id }}, '{{ addslashes($module->title) }}')" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <form action="{{ route('instructor.course.module.delete', $module->id) }}" method="POST" onsubmit="return confirm('Hapus modul ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            <button @click="open = !open" class="ml-2 p-2 rounded-full text-gray-500 hover:bg-gray-200 transition" :class="{'rotate-180': !open}">
                                <i class="fas fa-chevron-down text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="bg-white p-2">
                        @php
                            $mergedContents = collect();
                            foreach($module->materials as $m) { $m->type = 'material'; $mergedContents->push($m); }
                            foreach($module->quizzes as $q) { $q->type = 'quiz'; $mergedContents->push($q); }
                            $sortedContents = $mergedContents->sortBy('sort_order');
                        @endphp

                        <div class="space-y-1 contents-list mb-2" data-reorder-url="{{ route('instructor.modules.contents.reorder', ['course' => $course->id, 'module' => $module->id]) }}">
                            @forelse($sortedContents as $item)
                                <div class="flex items-center justify-between p-3 rounded-lg border border-transparent hover:border-blue-200 hover:bg-blue-50 group transition content-item" 
                                     data-id="{{ $item->id }}" data-type="{{ $item->type }}">
                                    
                                    <div class="flex items-center gap-3 overflow-hidden flex-1">
                                        <div class="text-gray-300 cursor-grab hover:text-gray-500 handle-content p-1">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $item->type == 'quiz' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' }}">
                                            <i class="fas {{ $item->type == 'quiz' ? 'fa-clipboard-check' : 'fa-book-open' }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 truncate">{{ $item->title }}</p>
                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                <span class="{{ $item->type == 'quiz' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} px-1.5 py-0.5 rounded text-[10px] font-bold">
                                                    {{ strtoupper($item->type) }}
                                                </span>
                                                @if($item->type == 'material' && $item->file_path)
                                                    <i class="fas fa-paperclip text-gray-400"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                        @if($item->type == 'quiz')
                                            <button @click="openEditQuizModal({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ addslashes($item->description) }}', {{ $item->duration_minutes }}, {{ $item->passing_score }})" 
                                                    class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-white rounded" title="Edit Pengaturan Quiz">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <a href="{{ route('instructor.quiz.edit', [$course->id, $item->id]) }}" class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-white rounded" title="Kelola Soal">
                                                <i class="fas fa-list-ol"></i>
                                            </a>
                                            <form action="{{ route('instructor.course.module.quiz.delete', ['courseId' => $course->id, 'quizId' => $item->id]) }}" method="POST" onsubmit="return confirm('Hapus quiz ini?');">
                                                @csrf @method('DELETE')
                                                <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded"><i class="fas fa-times"></i></button>
                                            </form>
                                        @else
                                            <button @click="openEditMaterialModal({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ $item->external_url }}', $el)"
                                                data-content="{{ base64_encode($item->description) }}"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-white rounded transition" title="Edit Konten">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ route('instructor.course.material.delete', $item->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini?');">
                                                @csrf @method('DELETE')
                                                <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded"><i class="fas fa-times"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-gray-400 text-sm border-2 border-dashed border-gray-100 rounded-lg m-2">
                                    <i class="fas fa-inbox text-xl mb-1"></i>
                                    <p>Belum ada konten.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-100 flex gap-3 px-2">
                            <button @click="openCreateMaterialModal('{{ route('instructor.course.material.store', [$course->id, $module->id]) }}')" class="flex-1 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg border border-blue-200 transition flex items-center justify-center gap-2">
                                <i class="fas fa-plus-circle"></i> Tambah Materi
                            </button>
                            <button @click="openCreateQuizModal('{{ route('instructor.course.module.quiz.store', [$course->id, $module->id]) }}')" class="px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 text-sm font-medium rounded-lg border border-purple-200 transition flex items-center gap-2">
                                <i class="fas fa-question-circle"></i> Quiz
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-500">
                        <i class="fas fa-layer-group text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Mulai Menyusun Kursus</h3>
                    <button @click="showModuleModal = true" class="mt-6 text-blue-600 font-medium hover:text-blue-800 hover:underline">
                        + Buat Modul Pertama
                    </button>
                </div>
                @endforelse
            </div>
        </main>
    </div>

    <div x-show="showModuleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4" x-text="moduleEditMode ? 'Edit Modul' : 'Buat Modul Baru'"></h3>
            <form :action="moduleFormAction" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="moduleEditMode ? 'PUT' : 'POST'">
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Modul</label>
                    <input type="text" name="title" x-model="moduleTitle" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showModuleModal = false" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg font-medium">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showContentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-0 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800" x-text="contentEditMode ? 'Edit Materi' : 'Buat Materi Baru'"></h3>
                <button @click="showContentModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-white">
                <form :action="contentFormAction" method="POST" enctype="multipart/form-data" id="contentForm">
                    @csrf
                    <input type="hidden" name="_method" :value="contentEditMode ? 'PUT' : 'POST'">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Materi</label>
                        <input type="text" name="title" x-model="contentTitle" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Konten Materi (Teks/Gambar/Video Embed)</label>
                        <textarea id="richEditor" name="content"></textarea>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">URL Video / Eksternal (YouTube/Vimeo Link)</label>
                        <input type="text" name="external_url" x-model="contentUrl" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: https://youtube.com/watch?v=...">
                        <p class="text-xs text-gray-500 mt-1">Masukkan link video jika ingin materi full-screen video player.</p>
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <label class="block text-sm font-bold text-blue-800 mb-2"><i class="fas fa-paperclip"></i> Lampiran File (Dokumen / Video MP4)</label>
                        <input type="file" name="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white cursor-pointer">
                        <p x-show="contentEditMode" class="text-xs text-gray-500 mt-2">*Biarkan kosong jika tidak ingin mengubah file.</p>
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, Word, PPT, TXT, JPG, PNG, MP4. Max: 50MB.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showContentModal = false" class="px-6 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg font-medium">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg">Simpan Materi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="showQuizModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 animate-fade-in">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h3 class="text-xl font-bold text-gray-800" x-text="quizEditMode ? 'Edit Pengaturan Quiz' : 'Buat Quiz Baru'"></h3>
                <button @click="showQuizModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <form :action="quizFormAction" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="quizEditMode ? 'PUT' : 'POST'">
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Quiz</label>
                    <input type="text" name="title" x-model="quizTitle" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Instruksi</label>
                    <textarea name="description" x-model="quizDesc" rows="2" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" x-model="quizDuration" min="1" class="w-full px-3 py-2 border rounded-lg text-center font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">KKM (0-100)</label>
                        <input type="number" name="passing_score" x-model="quizScore" min="0" max="100" class="w-full px-3 py-2 border rounded-lg text-center font-bold">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showQuizModal = false" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-lg" x-text="quizEditMode ? 'Update Pengaturan' : 'Buat Quiz'"></button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl z-[100] flex items-center gap-3 animate-bounce">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

</body>
</html>