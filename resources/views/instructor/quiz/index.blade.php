@extends('layouts.app')

@section('title', 'Quiz - ' . $course->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.courses.show', $course->id) }}" class="hover:opacity-80 text-sm">
                    ← Kembali ke Course
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Quiz</h1>
            <p class="text-indigo-100">{{ $course->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
                <i class="fas fa-check-circle text-green-600 mr-3 mt-0.5"></i>
                <div>
                    <p class="text-green-800 font-semibold">Sukses!</p>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Add Quiz Button -->
        <div class="mb-8">
            <a href="{{ route('instructor.quiz.create', $course->id) }}" 
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition">
                <i class="fas fa-plus"></i> Buat Quiz Baru
            </a>
        </div>

        @if($quizzes->count() > 0)
            <!-- Quizzes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($quizzes as $quiz)
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white p-6">
                            <h3 class="text-lg font-semibold mb-2">{{ $quiz->title }}</h3>
                            <p class="text-indigo-100 text-sm">{{ Str::limit($quiz->description, 100) }}</p>
                        </div>

                        <!-- Content -->
                        <div class="p-6 space-y-4">
                            <!-- Quiz Stats -->
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-gray-600 text-sm">Soal</p>
                                    <p class="text-2xl font-bold text-indigo-600">{{ $quiz->questions()->count() }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Durasi</p>
                                    <p class="text-2xl font-bold text-indigo-600">{{ $quiz->duration_minutes }}m</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Kelulusan</p>
                                    <p class="text-2xl font-bold text-indigo-600">{{ $quiz->passing_score }}%</p>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="pt-4 border-t border-gray-200">
                                @if($quiz->is_published)
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i> Dipublikasikan
                                    </span>
                                @else
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-pencil mr-1"></i> Draft
                                    </span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-4 border-t border-gray-200">
                                <a href="{{ route('instructor.quiz.edit', [$course->id, $quiz->id]) }}" 
                                   class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg text-center text-sm font-medium transition">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('instructor.quiz.destroy', [$course->id, $quiz->id]) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus quiz ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-gray-50 rounded-lg border border-gray-200">
                <i class="fas fa-quiz text-gray-300 text-5xl mb-4 block"></i>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Quiz</h3>
                <p class="text-gray-600 mb-6">Mulai dengan membuat quiz pertama untuk course ini</p>
                <a href="{{ route('instructor.quiz.create', $course->id) }}" 
                   class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-medium transition">
                    <i class="fas fa-plus mr-2"></i> Buat Quiz Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
