<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DEMO: {{ $quiz->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    
    <script src="{{ asset('js/quiz-app.js') }}"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .no-select { user-select: none; -webkit-user-select: none; }
    </style>
</head>

@php
    $savedAnswers = $submission->answers ?? [];
@endphp

<body class="bg-gray-100 text-gray-800 h-screen flex flex-col font-sans overflow-hidden border-t-8 border-yellow-400"
      x-data="quizApp({{ $timeRemaining }}, {{ $quiz->questions->count() }})">

    <header class="bg-white/90 backdrop-blur-md border-b border-gray-200 h-16 fixed w-full top-0 z-50 shadow-sm flex items-center justify-between px-6 mt-1">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 px-3 py-1 bg-yellow-100 border border-yellow-300 rounded-lg text-yellow-800 font-bold text-xs uppercase tracking-wider">
                <i class="fas fa-robot"></i> Demo Mode
            </div>
            <div>
                <h1 class="text-sm font-bold text-gray-900 truncate max-w-[200px]">{{ $quiz->title }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Simulasi Waktu</p>
                <span class="font-mono font-bold text-gray-600 text-sm" x-text="formattedTime">00:00:00</span>
            </div>
            
            <form action="{{ route('instructor.courses.show', [$course->id, 'quiz', $quiz->id]) }}" method="GET">
                <button type="submit" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl transition-all text-sm flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">Keluar Demo</span>
                </button>
            </form>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden relative pt-16">
        
        <aside class="w-72 bg-white border-r border-gray-200 flex flex-col z-40 hidden md:flex shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
            <div class="p-5 border-b border-gray-200 bg-gray-50/50">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-wider mb-1 flex items-center gap-2">
                    <i class="fas fa-th text-blue-500"></i> Navigasi Soal
                </h3>
            </div>
            
            <div class="flex-1 overflow-y-auto p-5 custom-scrollbar">
                <div class="grid grid-cols-4 gap-3">
                    @foreach($quiz->questions as $index => $q)
                    @php $isDone = isset($savedAnswers[$q->id]); @endphp
                    <button @click="currentIndex = {{ $index }}; scrollToTop()" 
                            class="w-10 h-10 rounded-lg text-sm font-bold transition-all border-2 relative flex items-center justify-center"
                            :class="{
                                'bg-blue-600 border-blue-600 text-white shadow-md scale-110 z-10': currentIndex === {{ $index }},
                                'bg-green-600 border-green-600 text-white hover:bg-green-700': isAnswered({{ $q->id }}) && currentIndex !== {{ $index }},
                                'bg-white border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300': !isAnswered({{ $q->id }}) && currentIndex !== {{ $index }}
                            }">
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto bg-gray-50 relative scroll-smooth" id="questionContainer">
            {{-- Form Dummy untuk Demo --}}
            <div class="min-h-full flex flex-col">
                
                <div class="px-6 py-5 md:px-10 md:py-6 border-b border-gray-100 bg-white sticky top-0 z-20 flex justify-between items-center shadow-sm">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-1 block">
                            Pertanyaan <span x-text="currentIndex + 1"></span> <span class="text-gray-400 font-normal">/ {{ $quiz->questions->count() }}</span>
                        </span>
                    </div>
                </div>

                <div class="flex-1 relative overflow-hidden min-h-[400px]"> 
                    @foreach($quiz->questions as $index => $q)
                    <div x-show="currentIndex === {{ $index }}" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-10"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="absolute inset-0 w-full h-full p-6 md:p-10 overflow-y-auto custom-scrollbar"> 
                        
                        <div class="max-w-4xl mx-auto pb-20">
                            <div class="prose max-w-none text-xl text-gray-800 mb-10 leading-relaxed font-medium">
                                <span class="text-sm font-bold text-gray-400 block mb-2">Bobot: {{ $q->points }} Poin</span>
                                {!! $q->question !!}
                            </div>

                            <div class="space-y-4 max-w-3xl">
                                @if($q->question_type === 'multiple_choice')
                                    {{-- [FIX] Logic Decode Aman untuk String/Array --}}
                                    @php
                                        $options = $q->options;
                                        if (is_string($options)) {
                                            $options = json_decode($options, true) ?? [];
                                        }
                                        if (!is_array($options)) $options = [];
                                    @endphp

                                    @foreach($options as $key => $option)
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" 
                                               class="custom-radio sr-only peer"
                                               @if(isset($savedAnswers[$q->id]) && (string)$savedAnswers[$q->id] === (string)$key) checked @endif
                                               @change="markAnswered({{ $q->id }})">
                                        
                                        <div class="w-full p-5 rounded-xl border-2 bg-white transition-all flex items-center gap-5 group-hover:shadow-sm
                                                    border-gray-200 hover:border-blue-400 
                                                    peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-md">
                                            <div class="w-10 h-10 flex-shrink-0 rounded-lg border-2 border-gray-300 flex items-center justify-center text-sm font-bold text-gray-500 
                                                        group-hover:border-blue-500 group-hover:text-blue-500 transition bg-gray-50
                                                        peer-checked:border-blue-600 peer-checked:text-blue-600 peer-checked:bg-white">
                                                {{ chr(65 + $key) }}
                                            </div>
                                            <span class="text-lg text-gray-700 font-medium peer-checked:text-gray-900">{{ $option }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                
                                @elseif($q->question_type === 'true_false')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="true" class="custom-radio sr-only peer" 
                                                   @if(isset($savedAnswers[$q->id]) && (string)$savedAnswers[$q->id] === 'true') checked @endif
                                                   @change="markAnswered({{ $q->id }})">
                                            <div class="p-8 rounded-xl border-2 bg-white text-center transition-all h-full flex flex-col justify-center hover:shadow-md
                                                        border-gray-200 hover:border-green-500
                                                        peer-checked:border-green-600 peer-checked:bg-green-50 peer-checked:shadow-md">
                                                <span class="font-bold text-green-600 text-2xl block mb-1">BENAR</span>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="false" class="custom-radio sr-only peer" 
                                                   @if(isset($savedAnswers[$q->id]) && (string)$savedAnswers[$q->id] === 'false') checked @endif
                                                   @change="markAnswered({{ $q->id }})">
                                            <div class="p-8 rounded-xl border-2 bg-white text-center transition-all h-full flex flex-col justify-center hover:shadow-md
                                                        border-gray-200 hover:border-red-500
                                                        peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:shadow-md">
                                                <span class="font-bold text-red-600 text-2xl block mb-1">SALAH</span>
                                            </div>
                                        </label>
                                    </div>

                                @elseif($q->question_type === 'essay')
                                    <textarea name="answers[{{ $q->id }}]" rows="8" 
                                              class="w-full px-6 py-5 border-2 border-gray-200 rounded-xl bg-white text-gray-800 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition placeholder-gray-400 text-lg shadow-sm outline-none"
                                              placeholder="Ketikan jawaban Anda..."
                                              @input="checkTextarea({{ $q->id }}, $el)">{{ $savedAnswers[$q->id] ?? '' }}</textarea>

                                @elseif($q->question_type === 'multiple_select')
                                    {{-- [FIX] Logic Decode Aman --}}
                                    @php
                                        $options = $q->options;
                                        if (is_string($options)) {
                                            $options = json_decode($options, true) ?? [];
                                        }
                                        if (!is_array($options)) $options = [];
                                    @endphp

                                    <div class="space-y-3">
                                        @foreach($options as $key => $option)
                                        <label class="flex items-center p-0 cursor-pointer transition group bg-white hover:shadow-sm">
                                            @php 
                                                $isChecked = false;
                                                if(isset($savedAnswers[$q->id]) && is_array($savedAnswers[$q->id])) {
                                                    $isChecked = in_array((string)$key, $savedAnswers[$q->id]);
                                                }
                                            @endphp
                                            <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $key }}" 
                                                   class="custom-checkbox sr-only peer"
                                                   @if($isChecked) checked @endif
                                                   @change="checkCheckbox({{ $q->id }})"> 
                                            
                                            <div class="w-full p-5 border-2 rounded-xl flex items-center
                                                        border-gray-200 hover:border-blue-400
                                                        peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                                <div class="w-6 h-6 border-2 border-gray-300 rounded flex items-center justify-center mr-4 
                                                            peer-checked:bg-blue-600 peer-checked:border-blue-600 transition">
                                                    <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                                </div>
                                                <span class="text-gray-700 text-lg font-medium group-hover:text-blue-600 transition peer-checked:text-gray-900">{{ $option }}</span>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="px-6 py-6 md:px-10 border-t border-gray-100 bg-white flex justify-between items-center z-20 relative shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                    <button type="button" @click="prev()" :class="currentIndex > 0 ? 'text-gray-600 hover:bg-gray-100 border-gray-200' : 'text-gray-300 cursor-not-allowed'" class="px-6 py-3 font-bold rounded-xl border border-transparent transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> <span>Sebelumnya</span>
                    </button>

                    <button type="button" @click="next()" x-show="currentIndex < totalQuestions - 1" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>

                    <div x-show="currentIndex === totalQuestions - 1" class="text-sm text-gray-500 italic px-4">
                        (Ini hanya demo preview)
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>