@extends('layouts.app')

@section('title', 'Hasil Quiz - ' . $quiz->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.quiz.index', $course->id) }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Hasil Quiz</h1>
            <p class="text-blue-100">{{ $quiz->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        @php
            // Calculate correct answers from submission (moved to top for use in all sections)
            $correctCount = 0;
            if ($submission->answers) {
                foreach ($quiz->questions as $question) {
                    $userAnswer = $submission->answers[$question->id] ?? null;
                    $correctAnswer = (string)$question->correct_answer;
                    $userAnswerStr = (string)$userAnswer;
                    
                    if ($question->question_type === 'multiple_choice' && $userAnswerStr === $correctAnswer) {
                        $correctCount++;
                    } elseif ($question->question_type === 'true_false') {
                        $userNorm = strtolower($userAnswerStr);
                        $correctNorm = strtolower($correctAnswer);
                        if ($userNorm === $correctNorm) {
                            $correctCount++;
                        }
                    }
                }
            }
        @endphp
        <div class="grid grid-cols-3 gap-8">
            <!-- Sidebar Stats -->
            <div class="col-span-1">
                <div class="sticky top-6 space-y-4">
                    <!-- Score Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg border border-blue-200 p-6 text-center">
                        <p class="text-sm text-gray-600 mb-2">Skor Anda</p>
                        @php
                            // Calculate score from correct answers
                            $score = ($correctCount / $quiz->questions()->count()) * 100;
                            $score = round($score, 2);
                        @endphp
                        <p class="text-5xl font-bold text-blue-600">{{ $score }}</p>
                        <p class="text-sm text-gray-600">dari 100</p>
                        
                        @if($score >= $quiz->passing_score)
                            <div class="mt-4 bg-green-100 text-green-800 rounded-lg py-2 font-bold">
                                LULUS
                            </div>
                        @else
                            <div class="mt-4 bg-red-100 text-red-800 rounded-lg py-2 font-bold">
                                TIDAK LULUS
                            </div>
                        @endif
                    </div>

                    <!-- Quiz Info -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Informasi Quiz</h4>
                        <div class="text-sm text-gray-700 space-y-2">
                            <div class="flex justify-between">
                                <span>Total Soal:</span>
                                <strong>{{ $quiz->questions()->count() }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Benar:</span>
                                <strong class="text-green-600">{{ $correctCount }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Salah:</span>
                                <strong class="text-red-600">{{ ($quiz->questions()->count() - $correctCount) }}</strong>
                            </div>
                            <div class="flex justify-between border-t border-gray-300 pt-2 mt-2">
                                <span>Nilai Minimal Lulus:</span>
                                <strong>{{ $quiz->passing_score }}%</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Timing -->
                    <div class="bg-purple-50 rounded-lg border border-purple-200 p-4">
                        <h4 class="font-semibold text-purple-800 mb-3">Waktu</h4>
                        <div class="text-sm text-purple-700 space-y-2">
                            <div>
                                <p class="text-xs">Mulai:</p>
                                <p class="font-semibold">{{ $submission->created_at->format('H:i:s') }}</p>
                            </div>
                            <div>
                                <p class="text-xs">Selesai:</p>
                                <p class="font-semibold">{{ $submission->submitted_at->format('H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-span-2">
                <!-- Performance Bar -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Performa</h3>
                    <div class="space-y-3">
                        <!-- Correct -->
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700">Jawaban Benar</span>
                                <span class="font-bold text-green-600">{{ $correctCount }}/{{ $quiz->questions()->count() }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full" 
                                     style="width: {{ (($correctCount / $quiz->questions()->count()) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Review -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Ulasan Soal</h3>
                    
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @foreach($quiz->questions()->orderBy('order')->get() as $index => $question)
                        @php
                            $userAnswer = $submission->answers[$question->id] ?? null;
                            $isCorrect = false;
                            
                            if($question->question_type !== 'essay') {
                                $correctAnswer = $question->correct_answer;
                                $isCorrect = $userAnswer == $correctAnswer;
                            }
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 {{ $isCorrect ? 'bg-green-50' : 'bg-red-50' }}">
                            <!-- Question Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-600 mb-1">Soal {{ $index + 1 }}</p>
                                    <p class="text-gray-800 font-medium">{{ $question->question_text }}</p>
                                </div>
                                @if($question->question_type === 'essay')
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">Essay</span>
                                @elseif($isCorrect)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Benar</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Salah</span>
                                @endif
                            </div>

                            <!-- Answer Display -->
                            @if($question->question_type === 'multiple_choice')
                                <div class="bg-white rounded p-3 text-sm">
                                    @php
                                        // Handle both string JSON and array formats
                                        $options = is_string($question->options) 
                                            ? json_decode($question->options, true) 
                                            : $question->options;
                                        $options = $options ?? [];
                                        $correctAnswer = $question->correct_answer;
                                    @endphp
                                    
                                    <p class="text-gray-700 mb-2"><strong>Jawaban Anda:</strong></p>
                                    <p class="text-gray-800 mb-2">{{ $options[$userAnswer] ?? 'Tidak dijawab' }}</p>
                                    
                                    @if(!$isCorrect)
                                        <p class="text-green-700 mt-2"><strong>Jawaban Benar:</strong></p>
                                        <p class="text-green-800">{{ $options[$correctAnswer] ?? 'N/A' }}</p>
                                    @endif
                                </div>
                            
                            @elseif($question->question_type === 'true_false')
                                <div class="bg-white rounded p-3 text-sm">
                                    <p class="text-gray-700 mb-2"><strong>Jawaban Anda:</strong></p>
                                    <p class="text-gray-800 mb-2">
                                        {{ $userAnswer === 'true' ? 'Benar' : ($userAnswer === 'false' ? 'Salah' : 'Tidak dijawab') }}
                                    </p>
                                    
                                    @if(!$isCorrect)
                                        <p class="text-green-700 mt-2"><strong>Jawaban Benar:</strong></p>
                                        <p class="text-green-800">
                                            {{ $question->correct_answer === 'true' ? 'Benar' : 'Salah' }}
                                        </p>
                                    @endif
                                </div>
                            
                            @elseif($question->question_type === 'essay')
                                <div class="bg-white rounded p-3 text-sm">
                                    <p class="text-gray-700 mb-2"><strong>Jawaban Anda:</strong></p>
                                    <p class="text-gray-800 whitespace-pre-wrap mb-3">{{ $userAnswer ?? 'Tidak dijawab' }}</p>
                                    
                                    @if($submission->essay_feedback && $submission->essay_feedback[$question->id] ?? null)
                                        <p class="text-blue-700 mt-2"><strong>Feedback Instruktur:</strong></p>
                                        <p class="text-blue-800">{{ $submission->essay_feedback[$question->id] }}</p>
                                    @else
                                        <p class="text-yellow-700 italic">Menunggu feedback dari instruktur...</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 mt-6">
                    <a href="{{ route('student.quiz.index', $submission->quiz->course->registrations()->where('user_id', auth()->id())->first()->id ?? '#') }}" 
                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition">
                        ← Kembali ke Daftar Quiz
                    </a>
                    
                    @if($quiz->submissions()->where('user_id', auth()->id())->count() < $quiz->attempts_allowed)
                    <a href="{{ route('student.quiz.start', $quiz->id) }}" 
                       class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition">
                        🔄 Coba Lagi
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
