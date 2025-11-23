@extends('layouts.app')

@section('title', isset($thread) ? 'Edit Topik' : 'Buat Topik Baru')

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.forum.index', $registration->id) }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ isset($thread) ? 'Edit Topik' : 'Buat Topik Baru' }}</h1>
            <p class="text-green-100">{{ $course->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-red-800 font-semibold mb-2">Terjadi Kesalahan:</p>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($thread) ? route('student.forum.update', $thread->id) : route('student.forum.store', $course->id) }}" 
                  method="POST">
                @csrf
                @if(isset($thread))
                    @method('PUT')
                @endif

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Topik</label>
                        <input type="text" name="title" required
                               value="{{ old('title', $thread->title ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Tulis judul topik yang jelas dan ringkas...">
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Isi Diskusi</label>
                        <textarea name="content" required rows="8"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                  placeholder="Jelaskan pertanyaan atau topik diskusi Anda secara detail...">{{ old('content', $thread->content ?? '') }}</textarea>
                        <p class="text-xs text-gray-600 mt-2">Semakin detail penjelasan Anda, semakin baik respons yang akan didapat</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                            {{ isset($thread) ? 'Update Topik' : 'Buat Topik' }}
                        </button>
                        <a href="{{ route('student.forum.index', $registration->id) }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tips -->
        <div class="mt-8 bg-blue-50 rounded-lg border border-blue-200 p-6">
            <h3 class="font-semibold text-blue-900 mb-3">Tips Membuat Topik Diskusi yang Baik</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li><strong>Judul jelas:</strong> Gunakan judul yang spesifik dan deskriptif</li>
                <li><strong>Detail lengkap:</strong> Jelaskan masalah atau pertanyaan Anda dengan jelas</li>
                <li><strong>Konteks:</strong> Berikan konteks tentang apa yang sudah Anda coba</li>
                <li><strong>Sopan:</strong> Gunakan bahasa yang sopan dan menghormati</li>
                <li><strong>Format:</strong> Gunakan paragraf untuk membuat mudah dibaca</li>
            </ul>
        </div>
    </div>
</div>
@endsection
