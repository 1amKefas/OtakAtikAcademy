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
    
    <!-- TinyMCE Configuration -->
    <x-head.tinymce-config />
    
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js" crossorigin="anonymous"></script>
    
    <script defer src="{{ asset('js/instructor-manage-simple.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/instructor.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

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
                @if($course->type !== 'Full Online')
                <a href="{{ route('instructor.courses.classes.index', $course->id) }}" 
                   class="flex items-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition shadow-sm">
                   {{-- Icon Gedung Kelas --}}
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                   </svg>
                   Atur Kelas
                </a>
                @endif
                <a href="{{ route('instructor.courses.show', $course->id) }}" target="_blank" class="flex items-center bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Preview
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Kurikulum & Konten</h3>
                    <p class="text-gray-500 text-sm mt-1">Drag icon untuk mengubah urutan Modul atau Materi.</p>
                </div>
                <button onclick="openCreateModuleModal('{{ route('instructor.course.module.store', $course->id) }}')" 
                        class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition flex items-center gap-2 font-medium">
                    <i class="fas fa-plus-circle"></i> Tambah Modul Baru
                </button>
            </div>

            <div id="modules-list" class="space-y-6 pb-20" data-course-id="{{ $course->id }}">
                @forelse($course->modules->sortBy('order') as $module)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden module-item transition hover:shadow-md" data-id="{{ $module->id }}">
                    
                    <div class="p-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between group">
                        <div class="flex items-center gap-4 flex-1">
                            <div class="text-gray-400 cursor-move hover:text-gray-600 handle p-2">
                                <i class="fas fa-grip-vertical text-lg"></i>
                            </div>
                            <div class="flex items-center gap-3">
                                <h4 class="font-bold text-gray-800 text-lg">{{ $module->title }}</h4>
                                @if($module->title === 'Pemberitahuan & Event')
                                    <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs rounded-full font-bold">
                                        <i class="fas fa-bell mr-1"></i> ANNOUNCEMENT
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 ml-2">
                                @if($module->title === 'Pemberitahuan & Event')
                                    {{ $module->announcements->count() }} Pemberitahuan
                                @else
                                    {{ $module->materials->count() + $module->quizzes->count() }} Item Konten
                                @endif
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            @if($module->title !== 'Pemberitahuan & Event')
                            <button onclick="openCreateModuleModal('{{ route('instructor.course.module.store', $course->id) }}')" class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            @endif
                            <form action="{{ route('instructor.course.module.delete', $module->id) }}" method="POST" onsubmit="return confirm('Hapus modul ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            <button onclick="toggleModuleExpand({{ $module->id }})" class="ml-2 p-2 rounded-full text-gray-500 hover:bg-gray-200 transition">
                                <i class="fas fa-chevron-down text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div style="display: block;" x-collapse class="bg-white p-2">
                        {{-- Tampilkan Announcements untuk Module "Pemberitahuan & Event" --}}
                        @if($module->title === 'Pemberitahuan & Event')
                            <div class="space-y-3">
                                @forelse($module->announcements as $announcement)
                                    <div class="flex items-start justify-between p-4 rounded-lg border border-red-200 bg-gradient-to-r from-red-50 to-orange-50 hover:shadow-md group transition">
                                        <div class="flex items-start gap-4 flex-1">
                                            <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-red-600 to-orange-600 text-white shadow-md">
                                                <i class="fas fa-video text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-bold text-gray-900 text-base">{{ $announcement->title }}</p>
                                                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-700 mt-2">
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-calendar text-red-600"></i>
                                                        {{ $announcement->day_of_week }}, {{ date('d M Y', strtotime($announcement->announcement_date)) }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-clock text-red-600"></i>
                                                        {{ date('H:i', strtotime($announcement->announcement_time)) }} WIB
                                                    </span>
                                                </div>
                                                @if($announcement->description)
                                                    <p class="text-sm text-gray-600 mt-2">{{ $announcement->description }}</p>
                                                @endif
                                                @if($announcement->zoom_link)
                                                    <div class="mt-3 flex gap-2">
                                                        <a href="{{ $announcement->zoom_link }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition shadow-md">
                                                            <i class="fas fa-link"></i>
                                                            Buka Zoom
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition ml-4 flex-shrink-0">
                                            <button class="p-2 text-gray-400 hover:text-blue-600 hover:bg-white rounded" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ route('instructor.course.announcement.delete', $announcement->id) }}" method="POST" onsubmit="return confirm('Hapus pemberitahuan ini?');" style="display: inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-white rounded">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-400 border-2 border-dashed border-gray-200 rounded-lg">
                                        <i class="fas fa-calendar-times text-3xl mb-2 block"></i>
                                        <p class="font-medium">Belum ada sesi zoom</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <button onclick="openCreateModuleModal('{{ route('instructor.course.module.store', $course->id) }}')" 
                                        class="w-full py-2.5 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white text-sm font-bold rounded-lg transition flex items-center justify-center gap-2 shadow-md">
                                    <i class="fas fa-plus-circle"></i> Tambah Sesi Zoom
                                </button>
                            </div>
                        @else
                            {{-- Tampilkan Materi dan Quiz untuk Module Pembelajaran --}}
                        @php
                            $mergedContents = collect();
                            
                            // Masukkan Materi (Mapping 'order' ke 'urutan_tampil')
                            foreach($module->materials as $m) { 
                                $m->type = 'material'; 
                                $m->urutan_tampil = $m->order; // <--- KUNCINYA DISINI
                                $mergedContents->push($m); 
                            }
                            
                            // Masukkan Quiz (Mapping 'sort_order' ke 'urutan_tampil')
                            foreach($module->quizzes as $q) { 
                                $q->type = 'quiz'; 
                                $q->urutan_tampil = $q->sort_order; // <--- KUNCINYA DISINI
                                $mergedContents->push($q); 
                            }
                            
                            // Sort berdasarkan key yang sudah disamakan
                            $sortedContents = $mergedContents->sortBy('urutan_tampil');
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
                                            <button onclick="openEditQuizModal({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ addslashes($item->description) }}', {{ $item->duration_minutes }}, {{ $item->passing_score }})" 
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
                                            <button onclick="openEditMaterialModal({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ $item->external_url }}', this)"
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
                            <button onclick="openCreateMaterialModal('{{ route('instructor.course.material.store', [$course->id, $module->id]) }}')" class="flex-1 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg border border-blue-200 transition flex items-center justify-center gap-2">
                                <i class="fas fa-plus-circle"></i> Tambah Materi
                            </button>
                            <button onclick="openCreateQuizModal('{{ route('instructor.course.module.quiz.store', [$course->id, $module->id]) }}')" class="px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 text-sm font-medium rounded-lg border border-purple-200 transition flex items-center gap-2">
                                <i class="fas fa-question-circle"></i> Quiz
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-500">
                        <i class="fas fa-layer-group text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Mulai Menyusun Kursus</h3>
                    <button onclick="openCreateModuleModal('{{ route('instructor.course.module.store', $course->id) }}')" 
                            class="mt-6 text-blue-600 font-medium hover:text-blue-800 hover:underline">
                        + Buat Modul 
                    </button>
                </div>
                @endforelse
            </div>
        </main>
    </div>

    <div id="moduleModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Tambah Modul/Announcement</h3>
            
            <form id="moduleForm" method="POST">
                @csrf
                
                <!-- Pilihan Tipe -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <label class="block text-sm font-bold text-gray-700 mb-3">Tipe Konten</label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-blue-50">
                            <input type="radio" name="module_category" value="module" onchange="updateModuleType('module')" class="w-4 h-4 accent-blue-600" checked>
                            <div>
                                <span class="text-gray-700 font-medium block">📚 Modul Pembelajaran</span>
                                <span class="text-xs text-gray-500">Materi, Quiz, dan Tugas</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-red-50">
                            <input type="radio" name="module_category" value="announcement" onchange="updateModuleType('announcement')" class="w-4 h-4 accent-red-600">
                            <div>
                                <span class="text-gray-700 font-medium block">🎥 Announcement Zoom</span>
                                <span class="text-xs text-gray-500">Info Sesi Zoom/Pertemuan Hybrid</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Form Modul Biasa -->
                <div id="moduleLearningForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Modul</label>
                        <input type="text" name="title" id="moduleTitle" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Form Announcement Zoom -->
                <div id="moduleAnnouncementForm" class="space-y-4" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sesi/Event <span class="text-red-500">*</span></label>
                        <input type="text" name="announcement_title" id="announcementTitle" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500" placeholder="Contoh: Sesi Tanya Jawab AI & ML">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="announcement_date" id="announcementDate" onchange="updateAnnouncementDay()" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam <span class="text-red-500">*</span></label>
                            <input type="time" name="announcement_time" id="announcementTime" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hari (Auto-Generate)</label>
                        <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 font-medium" id="announcementDay">
                            Pilih tanggal terlebih dahulu
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link Zoom <span class="text-red-500">*</span></label>
                        <input type="url" name="zoom_link" id="zoomLink" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500" placeholder="https://zoom.us/j/...">
                        <p class="text-xs text-gray-500 mt-1">Paste link Zoom meeting di sini</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                        <textarea name="announcement_description" id="announcementDesc" rows="2" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500" placeholder="Topik diskusi, catatan penting, dst..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" onclick="closeModuleModal()" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg font-medium">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="contentModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-0 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">Buat Materi Baru</h3>
                <button onclick="closeContentModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar bg-white">
                <form id="contentForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Materi</label>
                        <input type="text" name="title" id="contentTitle" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Konten Materi (Teks/Gambar/Video Embed)</label>
                        <x-forms.tinymce-editor name="content" id="richEditor" />
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">URL Video / Eksternal (YouTube/Vimeo Link)</label>
                        <input type="text" name="external_url" id="contentUrl" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: https://youtube.com/watch?v=...">
                        <p class="text-xs text-gray-500 mt-1">Masukkan link video jika ingin materi full-screen video player.</p>
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <label class="block text-sm font-bold text-blue-800 mb-2"><i class="fas fa-paperclip"></i> Lampiran File (Dokumen / Video MP4)</label>
                        <input type="file" name="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, Word, PPT, TXT, JPG, PNG, MP4. Max: 50MB.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeContentModal()" class="px-6 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg font-medium">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg">Simpan Materi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="quizModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 animate-fade-in">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h3 class="text-xl font-bold text-gray-800">Buat Quiz Baru</h3>
                <button onclick="closeQuizModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <form id="quizForm">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Quiz</label>
                    <input type="text" name="title" id="quizTitle" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Instruksi</label>
                    <textarea name="description" id="quizDesc" rows="2" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" id="quizDuration" value="30" min="1" class="w-full px-3 py-2 border rounded-lg text-center font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">KKM (0-100)</label>
                        <input type="number" name="passing_score" id="quizScore" value="70" min="0" max="100" class="w-full px-3 py-2 border rounded-lg text-center font-bold">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeQuizModal()" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="button" onclick="submitQuizForm()" class="px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-lg">Buat Quiz</button>
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