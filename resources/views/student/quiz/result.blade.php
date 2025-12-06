<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Quiz: {{ $quiz->title }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen py-12">

    <div class="max-w-5xl mx-auto px-4">
        
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden text-center p-10 mb-8 border border-gray-100 relative">
            
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>

            @if($submission->score >= $quiz->passing_score)
                <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce shadow-lg shadow-green-100 border border-green-100">
                    <i class="fas fa-trophy text-5xl text-green-600"></i>
                </div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">Selamat! Anda Lulus</h1>
                <p class="text-gray-500 text-lg">Luar biasa! Anda telah menguasai materi ini.</p>
            @else
                <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-red-100 border border-red-100">
                    <i class="fas fa-times-circle text-5xl text-red-500"></i>
                </div>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">Belum Lulus</h1>
                <p class="text-gray-500 text-lg">Jangan menyerah, pelajari ulang materi dan coba lagi.</p>
            @endif

            <div class="mt-10 mb-10 relative inline-block">
                <svg class="w-56 h-56 transform -rotate-90 drop-shadow-xl">
                    <circle class="text-gray-100" stroke-width="12" stroke="currentColor" fill="transparent" r="90" cx="112" cy="112"/>
                    <circle class="{{ $submission->score >= $quiz->passing_score ? 'text-green-500' : 'text-red-500' }} transition-all duration-1000 ease-out" 
                            stroke-width="12" 
                            stroke-dasharray="565" 
                            stroke-dashoffset="{{ 565 - (565 * $submission->score) / 100 }}"
                            stroke-linecap="round" 
                            stroke="currentColor" fill="transparent" r="90" cx="112" cy="112"/>
                </svg>
                <div class="absolute top-0 left-0 w-full h-full flex flex-col items-center justify-center">
                    <div class="flex items-baseline">
                        <span class="text-6xl font-black {{ $submission->score >= $quiz->passing_score ? 'text-green-600' : 'text-red-600' }}">
                            {{ $submission->score }}
                        </span>
                        <span class="text-2xl font-bold text-gray-300 ml-1">/100</span>
                    </div>
                    <span class="text-sm text-gray-400 uppercase font-bold tracking-widest mt-1">Nilai Akhir</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('student.learning.index', $quiz->course_id) }}" 
                   class="px-8 py-3.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Materi
                </a>

                @if($submission->score < $quiz->passing_score)
                    <a href="{{ route('student.quiz.start', [$quiz->course_id, $quiz->id]) }}" 
                       class="px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <i class="fas fa-redo-alt"></i> Coba Lagi
                    </a>
                @endif
            </div>
        </div>

        @php
            $totalQuestions = $quiz->questions->count();
            $answeredCount = count($submission->answers ?? []);
            $correctCount = $submission->correct_answers_count;
            $wrongCount = $answeredCount - $correctCount;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Total Soal</p>
                <p class="text-2xl font-black text-gray-800">{{ $totalQuestions }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Dijawab</p>
                <p class="text-2xl font-black text-blue-600">{{ $answeredCount }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Benar</p>
                <p class="text-2xl font-black text-green-600">{{ $correctCount }}</p>
            </div>
            
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Salah</p>
                <p class="text-2xl font-black text-red-600">{{ $wrongCount }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Mulai</p>
                <p class="text-lg font-bold text-gray-800">
                    {{ $submission->started_at ? $submission->started_at->format('d M H:i') : '-' }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md transition">
                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Selesai</p>
                <p class="text-lg font-bold text-gray-800">
                    {{ $submission->submitted_at ? $submission->submitted_at->format('d M H:i') : '-' }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 backdrop-blur-sm">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shadow-sm">
                        <i class="fas fa-list-check"></i>
                    </div>
                    Review Jawaban Detail
                </h3>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($quiz->questions as $index => $question)
                    @php
                        $userAnswer = $submission->answers[$question->id] ?? null;
                        $isCorrect = false;
                        
                        // Logic Cek Jawaban
                        if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                            $isCorrect = (string)$userAnswer === (string)$question->correct_answer;
                        } elseif ($question->question_type === 'multiple_select') {
                            $correctArr = json_decode($question->correct_answer, true) ?? [];
                            $userArr = is_array($userAnswer) ? $userAnswer : [];
                            sort($correctArr); sort($userArr);
                            $isCorrect = ($correctArr == $userArr);
                        }
                        
                        $options = json_decode($question->options, true) ?? [];
                    @endphp

                    <div x-data="{ open: false }" class="group transition hover:bg-blue-50/30">
                        <button @click="open = !open" class="w-full px-8 py-5 flex items-start gap-5 text-left outline-none">
                            
                            <div class="flex-shrink-0 mt-1">
                                @if($question->question_type === 'essay')
                                    <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center border border-orange-200">
                                        <i class="fas fa-hourglass-half text-sm"></i>
                                    </div>
                                @elseif($isCorrect)
                                    <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center border border-green-200 shadow-sm">
                                        <i class="fas fa-check text-sm"></i>
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-500 flex items-center justify-center border border-red-200 shadow-sm">
                                        <i class="fas fa-times text-sm"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 line-clamp-2">
                                    <span class="font-bold text-gray-400 mr-2">#{{ $index + 1 }}</span> 
                                    {!! strip_tags($question->question) !!}
                                </p>
                                <p class="text-xs text-gray-500 mt-1.5 font-medium">
                                    @if($question->question_type === 'essay')
                                        <span class="text-orange-500">Menunggu Penilaian</span>
                                    @elseif($isCorrect)
                                        <span class="text-green-600">Jawaban Anda Benar</span>
                                    @else
                                        <span class="text-red-500">Jawaban Anda Salah</span>
                                    @endif
                                </p>
                            </div>

                            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-400 group-hover:border-blue-300 group-hover:text-blue-500 transition">
                                <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                            </div>
                        </button>

                        <div x-show="open" x-collapse class="bg-gray-50/80 border-t border-gray-100 px-8 py-6 pl-[5.5rem]">
                            
                            <div class="mb-6 p-4 bg-white border border-gray-200 rounded-xl shadow-sm relative">
                                <span class="absolute -left-2 top-4 w-1 h-8 bg-blue-500 rounded-r"></span>
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Pertanyaan</h4>
                                <div class="prose max-w-none text-gray-800 text-base leading-relaxed font-medium">
                                    {!! $question->question !!}
                                </div>
                            </div>

                            @if($question->question_type !== 'essay')
                                <div class="space-y-2">
                                    @foreach($options as $key => $opt)
                                        @php
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

                                        <div class="flex items-center gap-3 p-3 rounded-xl border text-sm transition-colors
                                            {{ $isKeyCorrect ? 'bg-green-50 border-green-200 shadow-sm' : '' }}
                                            {{ $isUserSelected && !$isKeyCorrect ? 'bg-red-50 border-red-200 shadow-sm' : 'bg-white border-gray-200' }}
                                        ">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold border
                                                {{ $isKeyCorrect ? 'bg-green-500 text-white border-green-500' : '' }}
                                                {{ $isUserSelected && !$isKeyCorrect ? 'bg-red-500 text-white border-red-500' : 'bg-gray-100 text-gray-500 border-gray-300' }}">
                                                {{ chr(65 + $key) }}
                                            </div>
                                            
                                            <span class="flex-1 font-medium {{ $isKeyCorrect ? 'text-green-800' : 'text-gray-600' }}">
                                                {{ $opt }}
                                            </span>

                                            @if($isKeyCorrect)
                                                <span class="flex items-center gap-1 text-[10px] font-bold uppercase text-green-700 bg-green-100 px-2 py-1 rounded-full">
                                                    <i class="fas fa-check"></i> Kunci
                                                </span>
                                            @endif
                                            @if($isUserSelected && !$isKeyCorrect)
                                                <span class="flex items-center gap-1 text-[10px] font-bold uppercase text-red-700 bg-red-100 px-2 py-1 rounded-full">
                                                    <i class="fas fa-times"></i> Jawabanmu
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm">
                                    <p class="text-xs font-bold text-gray-400 uppercase mb-2 tracking-wider">Jawaban Anda:</p>
                                    <p class="text-gray-800 whitespace-pre-line font-serif leading-relaxed">{{ $userAnswer ?? '-' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</body>
</html>