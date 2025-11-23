@extends('layouts.app')

@section('title', isset($quiz) ? 'Edit Quiz' : 'Buat Quiz Baru')

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.courses.show', $course->id) }}" class="hover:opacity-80">
                    Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ isset($quiz) ? 'Edit Quiz' : 'Buat Quiz Baru' }}</h1>
            <p class="text-indigo-100">{{ $course->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800 font-semibold mb-2">Terjadi Kesalahan:</p>
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Informasi Quiz</h3>

                    <form action="{{ isset($quiz) ? route('instructor.quiz.update', [$course->id, $quiz->id]) : route('instructor.quiz.store', $course->id) }}" 
                          method="POST" id="quizForm">
                        @csrf
                        @if(isset($quiz))
                            @method('PUT')
                        @endif

                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Quiz</label>
                                <input type="text" name="title" required
                                       value="{{ old('title', $quiz->title ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       placeholder="Contoh: Quiz Chapter 1 - Fundamental">
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                          placeholder="Jelaskan tujuan dan isi quiz ini...">{{ old('description', $quiz->description ?? '') }}</textarea>
                            </div>

                            <!-- Settings Grid -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Passing Score -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Skor Kelulusan (%)</label>
                                    <input type="number" name="passing_score" required min="0" max="100"
                                           value="{{ old('passing_score', $quiz->passing_score ?? 70) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <!-- Duration Minutes -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Durasi (Menit)</label>
                                    <input type="number" name="duration_minutes" required min="5" max="300"
                                           value="{{ old('duration_minutes', $quiz->duration_minutes ?? 30) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Attempts & Randomization -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Number of Attempts -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Percobaan</label>
                                    <input type="number" name="attempts_allowed" required min="1"
                                           value="{{ old('attempts_allowed', $quiz->attempts_allowed ?? 1) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <!-- Randomize Questions -->
                                <div class="flex items-end">
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                        <input type="checkbox" name="randomize_questions" value="1"
                                               {{ old('randomize_questions', $quiz->randomize_questions ?? false) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        Acak Urutan Soal
                                    </label>
                                </div>
                            </div>

                            <!-- Shuffle Answers -->
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <input type="checkbox" name="shuffle_answers" value="1"
                                           {{ old('shuffle_answers', $quiz->shuffle_answers ?? false) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    Acak Pilihan Jawaban
                                </label>
                            </div>

                            <!-- Availability Dates -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Available From -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tersedia Mulai (Opsional)</label>
                                    <input type="datetime-local" name="available_from"
                                           value="{{ old('available_from', isset($quiz) && $quiz->available_from ? $quiz->available_from->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <!-- Available Until -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tersedia Hingga (Opsional)</label>
                                    <input type="datetime-local" name="available_until"
                                           value="{{ old('available_until', isset($quiz) && $quiz->available_until ? $quiz->available_until->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Publish Quiz -->
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <input type="checkbox" name="is_published" value="1"
                                           {{ old('is_published', $quiz->is_published ?? false) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    Publikasikan Quiz (Siswa bisa mengerjakan)
                                </label>
                            </div>

                            <!-- Submit Button (save quiz info) -->
                            <div class="flex gap-4 pt-4 border-t border-gray-200">
                                <button type="submit" 
                                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                                    {{ isset($quiz) ? 'Update Quiz' : 'Buat Quiz' }}
                                </button>
                                <a href="{{ route('instructor.courses.show', $course->id) }}" 
                                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Questions Section (only for existing quiz) -->
                @if(isset($quiz))
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">Daftar Soal</h3>
                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $quiz->questions()->count() }} Soal
                        </span>
                    </div>

                    @if($quiz->questions()->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            @foreach($quiz->questions()->orderBy('order')->get() as $index => $question)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-indigo-300 transition">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600">Soal {{ $index + 1 }}</p>
                                        <p class="font-semibold text-gray-800">{{ Str::limit($question->question, 80) }}</p>
                                    </div>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                        {{ ucfirst($question->question_type) }}
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('instructor.quiz.question.edit', [$course->id, $quiz->id, $question->id]) }}" 
                                       class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('instructor.quiz.question.delete', [$course->id, $quiz->id, $question->id]) }}" method="POST" 
                                          class="inline" onsubmit="return confirm('Hapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-8">Belum ada soal. Tambahkan soal pertama Anda.</p>
                    @endif

                    <a href="{{ route('instructor.quiz.question.create', [$course->id, $quiz->id]) }}" 
                       class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg text-center block transition">
                        Tambah Soal
                    </a>
                </div>
                @endif
            </div>

            <!-- Sidebar Info -->
            <div class="col-span-1">
                <div class="sticky top-6">
                    <!-- Quiz Info -->
                    <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-4 mb-4">
                        <h4 class="font-semibold text-indigo-900 mb-3">Informasi Quiz</h4>
                        <div class="text-sm text-indigo-800 space-y-2">
                            <div>
                                <p class="text-xs text-indigo-600">Jenis Soal</p>
                                <p class="font-semibold">Pilihan Ganda, Benar/Salah, Essay</p>
                            </div>
                            <div>
                                <p class="text-xs text-indigo-600">Sistem Penilaian</p>
                                <p class="font-semibold">Otomatis (MC, T/F) + Manual (Essay)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timing Info -->
                    <div class="bg-purple-50 rounded-lg border border-purple-200 p-4">
                        <h4 class="font-semibold text-purple-900 mb-3">Tips Waktu</h4>
                        <ul class="text-xs text-purple-800 space-y-2">
                            <li>- Berikan waktu cukup untuk membaca dan berpikir</li>
                            <li>- Biasanya 1-2 menit per soal</li>
                            <li>- Minimal 10 menit untuk quiz singkat</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
