@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 py-12 transition-colors">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden text-center p-8 mb-8 border border-gray-100 dark:border-gray-700">
            
            @if($submission->score >= $quiz->passing_score)
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <i class="fas fa-trophy text-4xl text-green-600 dark:text-green-400"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Selamat! Anda Lulus</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Hasil kerja keras Anda memuaskan.</p>
            @else
                <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times-circle text-4xl text-red-600 dark:text-red-400"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Belum Lulus</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Tetap semangat, silakan coba lagi.</p>
            @endif

            <div class="mt-6">
                <span class="text-5xl font-extrabold {{ $submission->score >= $quiz->passing_score ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $submission->score }}
                </span>
                <span class="text-gray-400 text-lg">/ 100</span>
            </div>

            <div class="mt-8 flex justify-center gap-3">
                <a href="{{ route('student.learning.index', $quiz->course_id) }}" 
                   class="px-6 py-2.5 bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl hover:bg-gray-300 dark:hover:bg-slate-600 transition text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Materi
                </a>
                @if($submission->score < $quiz->passing_score)
                    <a href="{{ route('student.quiz.start', [$quiz->course_id, $quiz->id]) }}" 
                       class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg transition transform hover:-translate-y-0.5 text-sm">
                        <i class="fas fa-redo mr-2"></i> Coba Lagi
                    </a>
                @endif
            </div>
        </div>

        @php
            $totalQuestions = $quiz->questions->count();
            $answeredCount = count($submission->answers ?? []);
            $correctCount = $submission->correct_answers_count;
            $wrongCount = $answeredCount - $correctCount; // Asumsi: Terjawab - Benar = Salah (utk PG)
            $unansweredCount = $totalQuestions - $answeredCount;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold">Total Soal</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalQuestions }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border-l-4 border-purple-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold">Terjawab</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $answeredCount }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border-l-4 border-green-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold">Jawaban Benar</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $correctCount }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border-l-4 border-red-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold">Jawaban Salah</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $wrongCount }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold"><i class="far fa-clock mr-1"></i> Mulai</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $submission->created_at->format('H:i:s') }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold"><i class="fas fa-flag-checkered mr-1"></i> Selesai</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">{{ $submission->submitted_at ? $submission->submitted_at->format('H:i:s') : '-' }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 dark:bg-slate-900/50 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-list-check text-blue-500"></i> Review Jawaban
                </h3>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($quiz->questions as $index => $question)
                    @php
                        // Ambil Jawaban User
                        $userAnswer = $submission->answers[$question->id] ?? null;
                        $isCorrect = false;
                        
                        // Logic Cek Kebenaran (Sederhana)
                        if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                            $isCorrect = (string)$userAnswer === (string)$question->correct_answer;
                        } elseif ($question->question_type === 'multiple_select') {
                            // Bandingkan array
                            $correctArr = json_decode($question->correct_answer, true) ?? [];
                            $userArr = is_array($userAnswer) ? $userAnswer : [];
                            sort($correctArr);
                            sort($userArr);
                            $isCorrect = ($correctArr == $userArr);
                        }
                        
                        // Decode Options
                        $options = json_decode($question->options, true) ?? [];
                    @endphp

                    <div x-data="{ open: false }" class="group">
                        <button @click="open = !open" class="w-full px-6 py-4 flex items-start gap-4 text-left hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                            
                            <div class="mt-0.5 flex-shrink-0">
                                @if($question->question_type === 'essay')
                                    <span class="text-orange-500" title="Menunggu Penilaian"><i class="fas fa-hourglass-half"></i></span>
                                @elseif($isCorrect)
                                    <span class="text-green-500" title="Benar"><i class="fas fa-check-circle"></i></span>
                                @else
                                    <span class="text-red-500" title="Salah"><i class="fas fa-times-circle"></i></span>
                                @endif
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 line-clamp-2">
                                    <span class="font-bold mr-1">{{ $index + 1 }}.</span> {!! strip_tags($question->question_text) !!}
                                </p>
                                <div class="flex gap-3 mt-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Jawabanmu: 
                                        <span class="font-medium {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                            @if(is_array($userAnswer))
                                                {{ implode(', ', array_map(fn($k) => chr(65+$k), $userAnswer)) }}
                                            @elseif($question->question_type == 'true_false')
                                                {{ $userAnswer == 'true' ? 'Benar' : 'Salah' }}
                                            @elseif($question->question_type == 'essay')
                                                (Essay)
                                            @else
                                                {{ isset($userAnswer) ? chr(65 + $userAnswer) : '-' }}
                                            @endif
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open" x-collapse class="bg-gray-50 dark:bg-slate-900/50 px-6 py-4 border-t border-gray-100 dark:border-gray-700 text-sm">
                            
                            <div class="mb-4 prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 text-sm">
                                {!! $question->question_text !!}
                            </div>

                            @if($question->question_type !== 'essay')
                            <div class="space-y-2 mb-4">
                                @foreach($options as $key => $opt)
                                    @php
                                        // Cek status opsi ini
                                        $isUserSelected = false;
                                        if (is_array($userAnswer)) $isUserSelected = in_array($key, $userAnswer);
                                        else $isUserSelected = ((string)$userAnswer === (string)$key);

                                        $isKeyCorrect = false;
                                        if ($question->question_type == 'multiple_select') {
                                            $correctArr = json_decode($question->correct_answer, true) ?? [];
                                            $isKeyCorrect = in_array($key, $correctArr);
                                        } elseif ($question->question_type == 'multiple_choice') {
                                            $isKeyCorrect = ((string)$question->correct_answer === (string)$key);
                                        }
                                    @endphp

                                    <div class="flex items-center gap-3 p-3 rounded-lg border 
                                        {{ $isKeyCorrect ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : '' }}
                                        {{ $isUserSelected && !$isKeyCorrect ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' : 'border-gray-200 dark:border-gray-700' }}
                                    ">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold border
                                            {{ $isKeyCorrect ? 'bg-green-500 border-green-500 text-white' : '' }}
                                            {{ $isUserSelected && !$isKeyCorrect ? 'bg-red-500 border-red-500 text-white' : 'bg-white dark:bg-slate-800 border-gray-300 dark:border-gray-600 text-gray-500' }}
                                        ">
                                            {{ chr(65 + $key) }}
                                        </div>
                                        <span class="flex-1 {{ $isKeyCorrect ? 'text-green-800 dark:text-green-300 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $opt }}
                                        </span>
                                        
                                        @if($isKeyCorrect)
                                            <span class="text-xs font-bold text-green-600 dark:text-green-400"><i class="fas fa-check"></i> Kunci</span>
                                        @endif
                                        @if($isUserSelected && !$isKeyCorrect)
                                            <span class="text-xs font-bold text-red-600 dark:text-red-400"><i class="fas fa-times"></i> Jawabanmu</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @else
                                <div class="p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-1">Jawaban Anda:</p>
                                    <p class="text-gray-800 dark:text-gray-200">{{ $userAnswer ?? '-' }}</p>
                                </div>
                            @endif

                            </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection