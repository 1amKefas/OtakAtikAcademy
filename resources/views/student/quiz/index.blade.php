@extends('layouts.app')

@section('title', 'Quiz - ' . $course->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.course-detail', $registration->id) }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $course->title }}</h1>
            <p class="text-blue-100">Daftar Quiz Tersedia</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        @if(count($quizzes) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($quizzes as $quiz)
                <div class="bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition p-6">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ $quiz->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $quiz->questions()->count() }} Soal</p>
                        </div>
                        @php
                            // Hanya ambil submission yang sudah submitted (submitted_at not null)
                            $submission = $quiz->submissions()
                                ->where('user_id', auth()->id())
                                ->whereNotNull('submitted_at')
                                ->latest()
                                ->first();
                        @endphp
                        @if($submission)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                Sudah Dikerjakan
                            </span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                Belum Dikerjakan
                            </span>
                        @endif
                    </div>

                    <!-- Description -->
                    @if($quiz->description)
                    <p class="text-gray-700 mb-4 text-sm line-clamp-2">{{ $quiz->description }}</p>
                    @endif

                    <!-- Quiz Settings -->
                    <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm text-gray-700 space-y-1">
                        <div class="flex justify-between">
                            <span>Waktu:</span>
                            <strong>{{ $quiz->duration_minutes }} menit</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Skor Kelulusan:</span>
                            <strong>{{ $quiz->passing_score }}%</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Percobaan:</span>
                            <strong>{{ $quiz->attempts_allowed }} kali</strong>
                        </div>
                    </div>

                    <!-- Submission Info -->
                    @if($submission)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4 text-sm">
                        <div class="flex justify-between mb-2">
                            <span class="text-green-800">Skor Anda:</span>
                            <strong class="text-green-700">{{ $submission->score ?? 'Pending' }}/100</strong>
                        </div>
                        @if($submission->score)
                            @if($submission->score >= $quiz->passing_score)
                                <p class="text-green-700 font-semibold">LULUS</p>
                            @else
                                <p class="text-red-700 font-semibold">TIDAK LULUS</p>
                            @endif
                        @else
                            <p class="text-yellow-700">Sedang Dinilai</p>
                        @endif
                        @if($submission->submitted_at)
                        <p class="text-gray-600 text-xs mt-2">
                            {{ $submission->submitted_at->format('d M Y H:i') }}
                        </p>
                        @endif
                    </div>
                    @endif

                    <!-- Action Button -->
                    <div class="flex gap-2">
                        @if($submission)
                            <a href="{{ route('student.quiz.result', [$course->id, $quiz->id, $submission->id]) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-center transition">
                                Lihat Hasil
                            </a>
                            @if($quiz->submissions()->where('user_id', auth()->id())->count() < $quiz->attempts_allowed)
                            <a href="{{ route('student.quiz.start', [$course->id, $quiz->id]) }}" 
                               class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg text-center transition">
                                🔄 Ulang
                            </a>
                            @endif
                        @else
                            <a href="{{ route('student.quiz.start', [$course->id, $quiz->id]) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-center transition">
                                Mulai Quiz
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-12 text-center">
                <p class="text-3xl mb-4"></p>
                <p class="text-gray-600 text-lg">Belum ada quiz untuk course ini</p>
                <p class="text-gray-500 text-sm mt-2">Instruktur akan menambahkan quiz segera</p>
            </div>
        @endif
    </div>
</div>
@endsection
