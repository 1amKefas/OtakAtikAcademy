@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-gray-50 min-h-screen pb-20" x-data="{ showCreateModal: false }">
    
    <div class="bg-white border-b border-gray-200 pt-24 pb-8">
        <div class="max-w-5xl mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    {{-- Cari bagian link Kembali ke Materi --}}
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                        {{-- UBAH: dari $course->id menjadi $registration->id --}}
                        <a href="{{ route('student.course-detail', $registration->id) }}" class="hover:text-blue-600 transition">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Materi
                        </a>
                        <span>/</span>
                        <span class="text-gray-800 font-medium">Forum Diskusi</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $course->title }}</h1>
                    <p class="text-gray-600 mt-1">Diskusikan materi, tanyakan kendala, dan berbagi ilmu di sini.</p>
                </div>
                
                <button @click="showCreateModal = true; initEditor()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-blue-200 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Mulai Diskusi Baru
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 mt-8">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-gray-800">
                {{ $forums->total() }} Diskusi
            </h2>
            
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-sort"></i>
                    Urutkan: {{ $sort == 'oldest' ? 'Terlama' : 'Terbaru' }}
                </button>
                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-10" style="display: none;">
                    <a href="{{ route('student.forum.index', ['courseId' => $course->id, 'sort' => 'latest']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sort == 'latest' ? 'font-bold text-blue-600' : '' }}">
                        Terbaru
                    </a>
                    <a href="{{ route('student.forum.index', ['courseId' => $course->id, 'sort' => 'oldest']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sort == 'oldest' ? 'font-bold text-blue-600' : '' }}">
                        Terlama
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($forums as $forum)
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition group relative">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        @if($forum->user->profile_picture)
                            <img src="{{ Storage::url($forum->user->profile_picture) }}" class="w-12 h-12 rounded-full object-cover border border-gray-100">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                {{ substr($forum->user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition">
                                    <a href="{{ route('student.forum.detail', [$course->id, $forum->id]) }}" class="focus:outline-none">
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                        {{ $forum->title }}
                                    </a>
                                </h3>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="font-medium text-gray-900">{{ $forum->user->name }}</span>
                                    @if($forum->user->is_instructor)
                                        <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Instructor</span>
                                    @endif
                                    <span>•</span>
                                    <span>{{ $forum->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 text-gray-500">
                                <i class="far fa-comment-alt"></i>
                                <span class="font-bold text-sm">{{ $forum->replies->count() }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-gray-600 text-sm line-clamp-2">
                            {{ Str::limit(strip_tags($forum->message), 160) }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-200">
                <div class="w-20 h-20 bg-blue-50 text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Belum ada diskusi</h3>
                <p class="text-gray-500 mt-2">Jadilah yang pertama memulai diskusi di materi ini!</p>
                <button @click="showCreateModal = true; initEditor()" class="mt-6 text-blue-600 font-medium hover:underline">
                    Mulai Diskusi Sekarang
                </button>
            </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $forums->appends(['sort' => $sort])->links() }}
        </div>
    </div>

    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden flex flex-col max-h-[90vh]" @click.outside="showCreateModal = false">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">Buat Diskusi Baru</h3>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto">
                <form action="{{ route('student.forum.store', $course->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Diskusi</label>
                        <input type="text" name="subject" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="Contoh: Bagaimana cara install Laravel di Windows?">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pertanyaan / Pesan</label>
                        <textarea id="forumEditor" name="message"></textarea>
                        <p class="text-xs text-gray-500 mt-2">*Anda bisa menyisipkan gambar dengan cara drag & drop ke dalam editor.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showCreateModal = false" class="px-6 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg transition transform hover:-translate-y-0.5">Kirim Diskusi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function initEditor() {
            // Delay sedikit biar modal render dulu
            setTimeout(() => {
                if (tinymce.get('forumEditor')) {
                    tinymce.get('forumEditor').remove();
                }
                
                tinymce.init({
                    selector: '#forumEditor',
                    height: 300,
                    menubar: false,
                    license_key: 'gpl', // Agar tidak error API key
                    placeholder: 'Tulis pertanyaanmu di sini...',
                    plugins: 'image link lists code preview wordcount',
                    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
                    branding: false,
                    promotion: false,
                    // Logic Upload Gambar (Base64 Drag-Drop)
                    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.readAsDataURL(blobInfo.blob());
                        reader.onload = () => resolve(reader.result);
                        reader.onerror = error => reject(error);
                    })
                });
            }, 100);
        }
    </script>
</div>
@endsection