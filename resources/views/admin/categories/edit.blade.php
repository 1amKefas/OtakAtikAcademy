@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Kategori: {{ $category->name }}</h2>
            <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori</label>
                        <input type="text" name="name" value="{{ $category->name }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
                        <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ $category->description }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Thumbnail Saat Ini</label>
                    <div class="relative group rounded-xl overflow-hidden border border-gray-200 shadow-sm mb-4 aspect-video bg-gray-100">
                        <img src="{{ $category->thumbnail_url }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <span class="text-white text-sm font-medium">Ganti gambar di bawah</span>
                        </div>
                    </div>
                    <input type="file" name="thumbnail" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
            </div>

            <div class="mb-8 bg-blue-50 rounded-xl p-6 border border-blue-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-list-check text-blue-600"></i> Kelola Kursus di Kategori Ini
                </h3>
                
                <div class="max-h-80 overflow-y-auto custom-scrollbar bg-white rounded-lg border border-gray-200 p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($courses as $course)
                        <label class="flex items-center p-3 border rounded-lg hover:border-blue-500 hover:bg-blue-50 cursor-pointer transition group {{ $category->courses->contains($course->id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="checkbox" name="courses[]" value="{{ $course->id }}" 
                                   {{ $category->courses->contains($course->id) ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                            <div class="ml-3 flex items-center gap-3 overflow-hidden w-full">
                                @if($course->image_url)
                                    <img src="{{ $course->image_url }}" class="w-10 h-10 rounded object-cover bg-gray-200 flex-shrink-0">
                                @else 
                                    <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500 flex-shrink-0">N/A</div>
                                @endif
                                <div class="min-w-0">
                                    <span class="block text-sm font-semibold text-gray-700 truncate">{{ $course->title }}</span>
                                    <span class="block text-xs text-gray-500">{{ $course->instructor->name ?? 'No Instructor' }}</span>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t pt-6">
                <a href="{{ route('categories.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fas fa-check mr-2"></i> Update Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection