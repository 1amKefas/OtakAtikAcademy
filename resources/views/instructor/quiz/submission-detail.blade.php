@extends('layouts.app')

@section('title', 'Submission Detail - ' . $quiz->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.quiz.submissions', [$course->id, $quiz->id]) }}" class="hover:opacity-80 text-sm">
                    ← Kembali ke Submissions
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Detail Submission</h1>
            <p class="text-indigo-100">{{ $submission->user->name ?? 'Unknown' }} - {{ $quiz->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <!-- Student Info & Score -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Student Info -->
            <div class="md:col-span-2 bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Siswa</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        @if($submission->user && $submission->user->profile_picture && Storage::disk('public')->exists($submission->user->profile_picture))
                            <img src="{{ Storage::disk('public')->url($submission->user->profile_picture) }}" alt="{{ $submission->user->name }}" class="w-16 h-16 rounded-full">
                        @else
                            <div class="w-16 h-16 rounded-full bg-indigo-500 text-white flex items-center justify-center text-2xl font-bold">
                                {{ substr($submission->user->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Nama Siswa</p>
                            <p class="text-lg font-semibold text-gray-800">{{ $submission->user->name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-gray-800">{{ $submission->user->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Submission</p>
                        <p class="mt-2">
                            @if($submission->status === 'completed')
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i> Selesai
                                </span>
                            @elseif($submission->status === 'submitted')
                                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    <i class="fas fa-paper-plane mr-1"></i> Submitted
                                </span>
                            @else
                                <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Score Card -->
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg border border-indigo-200 p-6">
                <h3 class="text-lg font-semibold text-indigo-700 mb-6">Nilai</h3>
                <div class="text-center">
                    @if($submission->score !== null)
                        <div class="text-5xl font-bold text-indigo-600 mb-2">{{ $submission->score }}</div>
                        <p class="text-indigo-700 font-medium mb-4">/ 100</p>
                        @if($submission->score >= $quiz->passing_score)
                            <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold">
                                <i class="fas fa-check-circle mr-2"></i>LULUS
                            </div>
                        @else
                            <div class="inline-block bg-red-100 text-red-800 px-4 py-2 rounded-full font-semibold">
                                <i class="fas fa-times-circle mr-2"></i>TIDAK LULUS
                            </div>
                        @endif
                    @else
                        <p class="text-indigo-600 text-lg">-</p>
                        <p class="text-indigo-600 text-sm mt-2">Belum dinilai</p>
                    @endif
                </div>

                <!-- Time Info -->
                <div class="mt-6 pt-6 border-t border-indigo-200 space-y-3 text-sm">
                    @if($submission->submitted_at)
                        <div>
                            <p class="text-indigo-600">Waktu Submit</p>
                            <p class="text-indigo-800 font-medium">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                    @if($submission->time_spent)
                        <div>
                            <p class="text-indigo-600">Waktu Pengerjaan</p>
                            <p class="text-indigo-800 font-medium">{{ $submission->time_spent }} menit</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Questions & Answers -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">Pertanyaan & Jawaban</h3>
            <div class="space-y-6">
                @php
                    $correctCount = 0;
                    $totalQuestions = $quiz->questions->count();
                @endphp
                
                @if($submission->answers && count($submission->answers) > 0)
                    @foreach($quiz->questions as $question)
                        @php
                            $userAnswer = $submission->answers[$question->id] ?? null;
                            $correctAnswer = (string)$question->correct_answer;
                            $userAnswerStr = (string)$userAnswer;
                            $isCorrect = false;
                            
                            if ($question->question_type === 'multiple_choice' && $userAnswerStr === $correctAnswer) {
                                $isCorrect = true;
                                $correctCount++;
                            } elseif ($question->question_type === 'true_false') {
                                $userNorm = strtolower($userAnswerStr);
                                $correctNorm = strtolower($correctAnswer);
                                if ($userNorm === $correctNorm) {
                                    $isCorrect = true;
                                    $correctCount++;
                                }
                            }
                        @endphp
                        
                        <div class="border {{ $isCorrect ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50' }} rounded-lg p-6">
                            <!-- Question -->
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-1">Soal {{ $loop->iteration }}</p>
                                    <p class="text-lg font-semibold text-gray-800">{{ $question->question }}</p>
                                </div>
                                <div class="text-right ml-4">
                                    @if($isCorrect)
                                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            ✓ Benar
                                        </span>
                                    @else
                                        <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            ✗ Salah
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Options Display -->
                            @if($question->question_type === 'multiple_choice')
                                @php
                                    $options = is_array($question->options) ? $question->options : [$question->options];
                                @endphp
                                <div class="space-y-2 mb-4">
                                    @foreach($options as $idx => $option)
                                        <div class="p-3 rounded border-2 {{ $userAnswerStr === (string)$idx ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }} {{ $correctAnswer === (string)$idx ? 'border-green-500 bg-green-100' : '' }}">
                                            <p class="text-gray-800">
                                                <span class="font-semibold">{{ chr(65 + $idx) }}:</span> {{ $option }}
                                                @if($userAnswerStr === (string)$idx && $correctAnswer !== (string)$idx)
                                                    <span class="text-blue-600 ml-2">(Jawaban Siswa)</span>
                                                @endif
                                                @if($correctAnswer === (string)$idx)
                                                    <span class="text-green-600 ml-2">(Jawaban Benar)</span>
                                                @endif
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->question_type === 'true_false')
                                <div class="space-y-2 mb-4">
                                    <div class="p-3 rounded border-2 {{ $userAnswerStr === 'true' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }} {{ $correctAnswer === 'true' ? 'border-green-500 bg-green-100' : '' }}">
                                        <p class="text-gray-800">
                                            <span class="font-semibold">Benar</span>
                                            @if($userAnswerStr === 'true' && $correctAnswer !== 'true')
                                                <span class="text-blue-600 ml-2">(Jawaban Siswa)</span>
                                            @endif
                                            @if($correctAnswer === 'true')
                                                <span class="text-green-600 ml-2">(Jawaban Benar)</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="p-3 rounded border-2 {{ $userAnswerStr === 'false' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }} {{ $correctAnswer === 'false' ? 'border-green-500 bg-green-100' : '' }}">
                                        <p class="text-gray-800">
                                            <span class="font-semibold">Salah</span>
                                            @if($userAnswerStr === 'false' && $correctAnswer !== 'false')
                                                <span class="text-blue-600 ml-2">(Jawaban Siswa)</span>
                                            @endif
                                            @if($correctAnswer === 'false')
                                                <span class="text-green-600 ml-2">(Jawaban Benar)</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <!-- Summary -->
                            <div class="mt-4 pt-4 border-t border-gray-300">
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600">Jawaban Siswa</p>
                                        <p class="font-semibold text-gray-800">
                                            @if($question->question_type === 'multiple_choice')
                                                {{ $userAnswer !== null ? chr(65 + $userAnswer) . ': ' . ($question->options[$userAnswer] ?? 'Unknown') : 'Tidak dijawab' }}
                                            @elseif($question->question_type === 'true_false')
                                                {{ $userAnswer !== null ? (strtolower($userAnswer) === 'true' ? 'Benar' : 'Salah') : 'Tidak dijawab' }}
                                            @else
                                                {{ $userAnswer ?? 'Tidak dijawab' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Jawaban Benar</p>
                                        <p class="font-semibold text-green-600">
                                            @if($question->question_type === 'multiple_choice')
                                                {{ chr(65 + $correctAnswer) }}: {{ ($question->options[$correctAnswer] ?? 'Unknown') }}
                                            @elseif($question->question_type === 'true_false')
                                                {{ strtolower($correctAnswer) === 'true' ? 'Benar' : 'Salah' }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Status</p>
                                        <p class="font-semibold {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $isCorrect ? '✓ Benar' : '✗ Salah' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 block text-gray-300"></i>
                        <p>Tidak ada data jawaban</p>
                    </div>
                @endif
            </div>

            <!-- Score Summary -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-3 gap-6 text-center">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm">Total Soal</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalQuestions }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm">Jawaban Benar</p>
                        <p class="text-3xl font-bold text-green-600">{{ $correctCount }}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm">Skor</p>
                        <p class="text-3xl font-bold text-orange-600">{{ round(($correctCount / $totalQuestions) * 100) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
