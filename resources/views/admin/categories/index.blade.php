@extends('layouts.admin')

@section('content')
{{-- Load Custom JS --}}
<script src="{{ asset('js/admin-categories.js') }}" defer></script>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Kategori</h1>
            <p class="text-gray-600">Kelola kategori kursus di sini.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>

    @if(session('success'))
    <div class="alert-dismissible bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-r relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Thumbnail</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Kursus</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden border border-gray-200">
                            @if($category->thumbnail)
                                <img src="{{ Storage::url($category->thumbnail) }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $category->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->slug }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $category->courses_count ?? 0 }} Kursus
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Tombol Edit via Modal (Pakai JS Listener) --}}
                            <button type="button" 
                                    class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition btn-edit-category"
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}"
                                    {{-- Tambah data lain jika perlu --}}
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            {{-- Link Edit Halaman Terpisah (Opsional/Alternatif) --}}
                            {{-- <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-pen"></i></a> --}}

                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua kursus di dalamnya akan kehilangan kategori.')" 
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada kategori yang dibuat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editCategoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative mx-auto p-5 border w-96 shadow-xl rounded-2xl bg-white transform transition-all">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-900">Edit Kategori</h3>
        </div>
        <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" name="name" id="edit_name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail (Opsional)</label>
                    <input type="file" name="thumbnail" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn-close-modal px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection