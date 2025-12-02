@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Buat Kategori Baru</h2>
            <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori</label>
                    <input type="text" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Web Development" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi kategori..."></textarea>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-2">Thumbnail / Cover</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition">
                    <i class="fas fa-image text-3xl text-gray-400 mb-2"></i>
                    <input type="file" name="thumbnail" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Max: 2MB.</p>
                </div>
            </div>

            <div class="mb-8 bg-blue-50 rounded-xl p-6 border border-blue-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-blue-600"></i> Masukkan Kursus ke Kategori Ini
                </h3>
                <p class="text-sm text-gray-600 mb-4">Pilih kursus yang ingin dikelompokkan ke dalam kategori ini:</p>
                
                <div class="max-h-60 overflow-y-auto custom-scrollbar bg-white rounded-lg border border-gray-200 p-4">
                    @if($courses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($courses as $course)
                            <label class="flex items-center p-3 border rounded-lg hover:border-blue-500 hover:bg-blue-50 cursor-pointer transition group">
                                <input type="checkbox" name="courses[]" value="{{ $course->id }}" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-gray-700 group-hover:text-blue-700">{{ $course->title }}</span>
                                    <span class="block text-xs text-gray-500">{{ $course->instructor->name ?? 'No Instructor' }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-400 italic py-4">Belum ada kursus yang tersedia.</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t pt-6">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection