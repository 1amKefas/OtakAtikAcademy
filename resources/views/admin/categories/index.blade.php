@extends('layouts.admin') 

@section('title', 'Manajemen Kategori') {{-- Judul di Header --}}

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('Manajemen Kategori') }}</h1>
            <p class="text-sm text-gray-500">Kelola kategori kursus untuk OtakAtik Academy</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-md">
            <i class="fas fa-plus"></i> {{ __('Tambah Kategori') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b border-gray-200">
                    <th class="px-6 py-4 font-bold">{{ __('Nama Kategori') }}</th>
                    <th class="px-6 py-4 font-bold">{{ __('Slug') }}</th>
                    <th class="px-6 py-4 font-bold text-center">{{ __('Total Kursus') }}</th>
                    <th class="px-6 py-4 font-bold text-center">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-blue-50 transition duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                                    @if($category->thumbnail)
                                        <img src="{{ Storage::url($category->thumbnail) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-tags text-gray-400"></i>
                                    @endif
                                </div>
                                <span class="font-semibold text-gray-800">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ $category->slug }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $category->courses_count ?? 0 }} Kursus
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua kursus di dalamnya mungkin akan kehilangan kategori.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-folder-open text-5xl mb-3 opacity-30"></i>
                                <p class="text-lg font-medium">{{ __('Belum ada kategori ditemukan') }}</p>
                                <p class="text-sm">Silakan buat kategori baru untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="mt-6 px-6 pb-6">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection