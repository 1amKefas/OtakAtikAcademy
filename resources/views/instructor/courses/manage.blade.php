<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course - {{ $course->title }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Admin Sidebar Style */
        .sidebar { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
        
        /* Custom Scrollbar for Modal */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* TinyMCE Fixes */
        .tox-tinymce { border-radius: 0.5rem !important; border-color: #e2e8f0 !important; }
    </style>
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
                <a href="{{ route('course.show', $course->id) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                    <i class="fas fa-external-link-alt"></i> Preview Course
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
            
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Kurikulum & Konten</h3>
                    <p class="text-gray-500 text-sm mt-1">Susun modul, materi bacaan, video, dan kuis Anda di sini.</p>
                </div>
                <button @click="showModuleModal = true" class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition flex items-center gap-2 font-medium">
                    <i class="fas fa-plus-circle"></i> Tambah Modul Baru
                </button>
            </div>

            <div class="space-y-6 pb-20">
                @forelse($course->modules as $module)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ open: true }">
                    
                    <div class="p-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between group transition hover:bg-gray-100">
                        <div class="flex items-center gap-4 cursor-pointer flex-1" @click="open = !open">
                            <div class="text-gray-400 cursor-move hover:text-gray-600">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">{{ $module->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $module->materials->count() }} Materi • {{ $module->quizzes->count() }} Kuis
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition" title="Edit Nama Modul">
                                <i class="fas fa-pencil-alt"></i>
                            </button>

                            <form action="{{ route('instructor.course.module.delete', $module->id) }}" method="POST" onsubmit="return confirm('Hapus modul ini beserta isinya?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Hapus Modul">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>

                            <div class="ml-2 text-gray-400 transition-transform duration-300" :class="{'rotate-180': open}">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="bg-white p-2">
                        
                        <div class="space-y-1">
                            @forelse($module->materials as $material)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-transparent hover:border-blue-200 hover:bg-blue-50 group transition">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 truncate">{{ $material->title }}</p>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <span>Materi Pembelajaran</span>
                                            @if($material->file_path)
                                                <span class="text-gray-300">•</span>
                                                <span class="text-blue-500"><i class="fas fa-paperclip mr-1"></i>Lampiran</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                    <button class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-white rounded transition" title="Edit Konten">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('instructor.course.material.delete', $material->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini?');">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded transition" title="Hapus">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @empty
                                @if($module->quizzes->isEmpty() && $module->assignments->isEmpty())
                                    <div class="text-center py-8 text-gray-400 text-sm border-2 border-dashed border-gray-100 rounded-lg m-2">
                                        <i class="fas fa-inbox text-2xl mb-2 text-gray-300"></i>
                                        <p>Belum ada konten di modul ini.</p>
                                    </div>
                                @endif
                            @endforelse

                            @foreach($module->quizzes as $quiz)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-transparent hover:border-purple-200 hover:bg-purple-50 group transition mt-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $quiz->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $quiz->duration_minutes }} Menit • KKM: {{ $quiz->passing_score }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('instructor.quiz.edit', [$course->id, $quiz->id]) }}" class="px-3 py-1 bg-white border border-purple-200 text-purple-700 text-xs rounded hover:bg-purple-600 hover:text-white transition font-medium">
                                        <i class="fas fa-cog mr-1"></i> Kelola Soal
                                    </a>
                                    
                                    <form action="{{ route('instructor.course.module.quiz.delete', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" method="POST" onsubmit="return confirm('Hapus quiz ini?');">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded opacity-0 group-hover:opacity-100 transition"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            </div>
                            @endforeach

                            @foreach($module->quizzes as $quiz)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-transparent hover:border-purple-200 hover:bg-purple-50 group transition mt-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-question-circle"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $quiz->title }}</p>
                                        <p class="text-xs text-gray-500">Kuis • {{ $quiz->duration_minutes }} Menit</p>
                                    </div>
                                </div>
                                <div class="opacity-0 group-hover:opacity-100 transition">
                                    <a href="#" class="text-gray-400 hover:text-purple-600 p-2"><i class="fas fa-cog"></i></a>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-100 flex gap-3 px-2">
                            <button @click="openModal({{ $module->id }})" class="flex-1 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg border border-blue-200 transition flex items-center justify-center gap-2">
                                <i class="fas fa-plus-circle"></i> Tambah Materi
                            </button>
                            
                            <button @click="openQuizModal({{ $module->id }})" class="px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 text-sm font-medium rounded-lg border border-purple-200 transition flex items-center gap-2">
                                <i class="fas fa-question-circle"></i> Tambah Quiz
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
                    <p class="text-gray-500 mt-2 max-w-md mx-auto">Kursus ini masih kosong. Buat modul pertama Anda untuk mulai menambahkan materi pembelajaran.</p>
                    <button @click="showModuleModal = true" class="mt-6 text-blue-600 font-medium hover:text-blue-800 hover:underline">
                        + Buat Modul Pertama
                    </button>
                </div>
                @endforelse
            </div>

        </main>
    </div>

    <div x-show="showModuleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all" 
             @click.outside="showModuleModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <h3 class="text-xl font-bold text-gray-800 mb-4">Buat Modul Baru</h3>
            <form action="{{ route('instructor.course.module.store', $course->id) }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Modul / Bab</label>
                    <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="Contoh: Pengenalan Dasar HTML">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showModuleModal = false" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-lg transition">Simpan Modul</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showContentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-0 overflow-hidden flex flex-col max-h-[90vh]" 
             @click.outside="showContentModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Buat Materi Pembelajaran</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Isi konten materi dengan teks, gambar, atau video.</p>
                </div>
                <button @click="showContentModal = false" class="text-gray-400 hover:text-gray-600 transition p-2 hover:bg-gray-200 rounded-full"><i class="fas fa-times text-xl"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto custom-scrollbar bg-white">
                <form :action="`/instructor/courses/${courseId}/modules/${activeModuleId}/materials`" method="POST" enctype="multipart/form-data" id="contentForm">
                    @csrf
                    <input type="hidden" name="type" value="mixed">

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Materi</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium" placeholder="Contoh: Pengenalan HTML & CSS">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Konten Materi (Canvas)
                        </label>
                        <textarea id="richEditor" name="content"></textarea>
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <label class="block text-sm font-bold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-paperclip"></i> Lampiran File Eksternal (Opsional)
                        </label>
                        <p class="text-xs text-blue-600 mb-3">Upload PDF, Slide, atau Zip source code jika ada bahan yang perlu diunduh siswa.</p>
                        <input type="file" name="attachment" class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2.5 file:px-4
                            file:rounded-lg file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-600 file:text-white
                            hover:file:bg-blue-700
                            cursor-pointer
                        ">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showContentModal = false" class="px-6 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg transform hover:-translate-y-0.5 transition flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Materi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl z-[100] flex items-center gap-3 animate-bounce">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.fixed.bottom-6');
            if(alert) alert.remove();
        }, 4000);
    </script>
    @endif

    <script>
        const courseId = {{ $course->id }};

        document.addEventListener('alpine:init', () => {
            Alpine.data('contentManager', () => ({
                showModuleModal: false,
                showContentModal: false,
                
                // [ADD] State baru untuk Quiz
                showQuizModal: false, 
                activeModuleId: null,
                
                openModal(moduleId) {
                    this.activeModuleId = moduleId;
                    this.showContentModal = true;
                    
                    // Init TinyMCE with delay to ensure DOM is ready
                    setTimeout(() => {
                        if (tinymce.get('richEditor')) {
                            tinymce.get('richEditor').remove(); // Clear previous instance
                        }
                        
                        tinymce.init({
                            selector: '#richEditor',
                            height: 400,
                            menubar: false,
                            // [BARU] Tambahkan baris ini biar aman dari warning lisensi
                            license_key: 'gpl',
                            plugins: 'image media link lists table code preview wordcount',
                            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | code',
                            branding: false,
                            promotion: false,
                            // Enable Base64 Image Upload (Drag & Drop)
                            images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                                const reader = new FileReader();
                                reader.readAsDataURL(blobInfo.blob());
                                reader.onload = () => resolve(reader.result);
                                reader.onerror = error => reject(error);
                            })
                        });
                    }, 100);
                },
                
                // [ADD] Fungsi Buka Modal Quiz
                openQuizModal(moduleId) {
                    this.activeModuleId = moduleId;
                    this.showQuizModal = true;
                },

            }))
        });
    </script>
    <div x-show="showQuizModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 animate-fade-in" @click.outside="showQuizModal = false">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h3 class="text-xl font-bold text-gray-800">Buat Quiz Baru</h3>
                <button @click="showQuizModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <form :action="`/instructor/courses/${courseId}/modules/${activeModuleId}/quiz`" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Quiz</label>
                    <input type="text" name="title" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500 outline-none" placeholder="Contoh: Evaluasi Akhir Modul">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Instruksi / Deskripsi</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-purple-500 outline-none" placeholder="Kerjakan dengan teliti..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" value="30" min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 text-center font-bold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">KKM (0-100)</label>
                        <input type="number" name="passing_score" value="70" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 text-center font-bold text-gray-800">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showQuizModal = false" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-lg">Buat & Tambah Soal</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>