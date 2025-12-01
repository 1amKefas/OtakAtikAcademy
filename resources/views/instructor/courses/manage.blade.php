<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course - {{ $course->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden" x-data="{ 
    showModuleModal: false, 
    showContentModal: false,
    activeModuleId: null,
    contentType: 'file' 
}">

    <aside class="sidebar w-64 text-white hidden md:flex flex-col flex-shrink-0">
        <div class="p-6 border-b border-gray-700">
            <h1 class="text-2xl font-bold">Instructor<span class="text-blue-400">Panel</span></h1>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>
            <a href="{{ route('instructor.courses') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-600 text-white rounded-lg">
                <i class="fas fa-chalkboard-teacher w-5"></i> My Courses
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('instructor.courses') }}" class="text-gray-500 hover:text-gray-800"><i class="fas fa-arrow-left"></i> Kembali</a>
                <div class="h-6 w-px bg-gray-300"></div>
                <h2 class="text-lg font-bold text-gray-800 truncate">{{ $course->title }}</h2>
            </div>
            <a href="{{ route('course.show', $course->id) }}" target="_blank" class="text-sm text-blue-600 hover:underline">Preview</a>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Kurikulum</h3>
                    <p class="text-gray-500 text-sm">Susun modul dan materi pembelajaran.</p>
                </div>
                <button @click="showModuleModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Tambah Modul
                </button>
            </div>

            <div class="space-y-6">
                @forelse($course->modules as $module)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ open: true }">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-grip-vertical text-gray-400"></i>
                            <h4 class="font-bold text-gray-800 text-lg">{{ $module->title }}</h4>
                        </div>
                        <div class="flex items-center gap-2" @click.stop>
                            <div class="relative" x-data="{ dOpen: false }">
                                <button @click="dOpen = !dOpen" class="px-3 py-1 bg-white border rounded text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-plus text-blue-500"></i> Konten
                                </button>
                                <div x-show="dOpen" @click.outside="dOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-20 py-1" style="display:none;">
                                    <button @click="showContentModal = true; activeModuleId = {{ $module->id }}; contentType = 'video'; dOpen = false" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 flex gap-2"><i class="fas fa-play w-5"></i> Video</button>
                                    <button @click="showContentModal = true; activeModuleId = {{ $module->id }}; contentType = 'file'; dOpen = false" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 flex gap-2"><i class="fas fa-file w-5"></i> File</button>
                                    <button @click="showContentModal = true; activeModuleId = {{ $module->id }}; contentType = 'text'; dOpen = false" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 flex gap-2"><i class="fas fa-align-left w-5"></i> Teks</button>
                                </div>
                            </div>
                            
                            <form action="{{ route('instructor.course.module.delete', $module->id) }}" method="POST" onsubmit="return confirm('Hapus modul ini?');">
                                @csrf @method('DELETE')
                                <button class="p-2 text-gray-400 hover:text-red-500"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>

                    <div x-show="open" class="p-2 space-y-1 bg-white">
                        @forelse($module->materials as $material)
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-200">
                            <div class="flex items-center gap-3">
                                <i class="fas {{ $material->type == 'video' ? 'fa-play text-red-500' : 'fa-file text-blue-500' }}"></i>
                                <span class="text-sm font-medium text-gray-700">{{ $material->title }}</span>
                            </div>
                            <form action="{{ route('instructor.course.material.delete', $material->id) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-400 text-sm italic">Belum ada konten.</div>
                        @endforelse
                    </div>
                </div>
                @empty
                <div class="text-center py-12 border-2 border-dashed rounded-xl">
                    <i class="fas fa-layer-group text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">Belum ada modul. Silakan tambah modul baru.</p>
                </div>
                @endforelse
            </div>
        </main>
    </div>

    <div x-show="showModuleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl w-full max-w-md p-6" @click.outside="showModuleModal = false">
            <h3 class="text-xl font-bold mb-4">Tambah Modul</h3>
            <form action="{{ route('instructor.course.module.store', $course->id) }}" method="POST">
                @csrf
                <input type="text" name="title" class="w-full border rounded p-2 mb-4" placeholder="Judul Modul" required>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showModuleModal = false" class="px-4 py-2 rounded hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showContentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-xl w-full max-w-lg p-6" @click.outside="showContentModal = false">
            <h3 class="text-xl font-bold mb-4">Tambah Konten: <span x-text="contentType"></span></h3>
            <form :action="`/instructor/courses/{{ $course->id }}/modules/${activeModuleId}/materials`" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" :value="contentType">
                <input type="text" name="title" class="w-full border rounded p-2 mb-4" placeholder="Judul Materi" required>
                
                <div x-show="contentType === 'file'" class="mb-4">
                    <input type="file" name="file" class="w-full text-sm">
                </div>
                <div x-show="['video', 'link'].includes(contentType)" class="mb-4">
                    <input type="text" name="content_url" class="w-full border rounded p-2" placeholder="https://...">
                </div>
                <div x-show="contentType === 'text'" class="mb-4">
                    <textarea name="text_content" class="w-full border rounded p-2" rows="4"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showContentModal = false" class="px-4 py-2 rounded hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>