<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz: {{ $quiz->title }}</title>
    
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { 
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { slate: { 850: '#1e293b', 900: '#0f172a' } }
                } 
            },
            plugins: [ require('@tailwindcss/typography') ]
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background-color: #475569; }
        
        .custom-radio:checked + div { border-color: #3b82f6; background-color: #eff6ff; }
        .dark .custom-radio:checked + div { border-color: #60a5fa; background-color: rgba(59, 130, 246, 0.15); }
    </style>
</head>

<body class="bg-gray-100 dark:bg-slate-900 text-gray-800 dark:text-gray-200 min-h-screen flex flex-col font-sans transition-colors duration-300"
      x-data="quizTimer({{ $quiz->duration_minutes * 60 }})">

    <header class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 h-20 fixed w-full top-0 z-40 shadow-sm flex items-center justify-between px-4 md:px-8 transition-colors">
        
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 shadow-sm">
                <i class="fas fa-stopwatch text-xl"></i>
            </div>
            <div>
                <h1 class="text-sm md:text-lg font-bold text-gray-900 dark:text-white truncate max-w-[150px] md:max-w-md">
                    {{ $quiz->title }}
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Sisa Waktu: <span class="font-mono font-bold text-red-500 text-sm" x-text="formattedTime">00:00:00</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden md:flex flex-col items-end mr-2">
                <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                    Progress
                </span>
                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full rounded-full transition-all duration-500" 
                         style="width: 0%" 
                         x-bind:style="'width: ' + progress + '%'"></div>
                </div>
            </div>

            <button @click="confirmSubmit()" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-purple-500/30 transition transform hover:-translate-y-0.5 text-sm flex items-center gap-2">
                <i class="fas fa-paper-plane"></i> <span class="hidden sm:inline">Kumpulkan</span>
            </button>
        </div>
    </header>

    <div class="h-24"></div>

    <main class="flex-1 max-w-3xl mx-auto w-full px-4 pb-20">
        
        <form id="quizForm" action="{{ route('student.quiz.submit', [$quiz->course_id, $quiz->id, $submission->id]) }}" method="POST">
            @csrf
            
            <div class="space-y-8">
                @foreach($quiz->questions as $index => $q)
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all question-card hover:shadow-xl"
                     id="q_{{ $q->id }}"
                     x-data="{ answered: false }">
                    
                    <div class="px-6 py-4 bg-gray-50/80 dark:bg-slate-800/80 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center backdrop-blur-sm">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Soal No. {{ $index + 1 }}
                        </span>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-md bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-gray-300">
                            {{ $q->points }} Poin
                        </span>
                    </div>

                    <div class="p-6 md:p-8">
                        <div class="prose dark:prose-invert max-w-none text-lg text-gray-800 dark:text-gray-200 mb-8 leading-relaxed">
                            {!! $q->question_text !!}
                        </div>

                        <div class="space-y-3">
                            
                            @if($q->question_type === 'multiple_choice')
                                @foreach(json_decode($q->options) as $key => $option)
                                <label class="block relative cursor-pointer group">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" 
                                           class="custom-radio sr-only"
                                           @change="answered = true; updateProgress()">
                                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 bg-white dark:bg-slate-700/30 transition-all flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-500 flex items-center justify-center text-sm font-bold text-gray-500 dark:text-gray-400 group-hover:border-blue-500 group-hover:text-blue-500 transition">
                                            {{ chr(65 + $key) }}
                                        </div>
                                        <span class="text-base text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">{{ $option }}</span>
                                    </div>
                                </label>
                                @endforeach
                            
                            @elseif($q->question_type === 'true_false')
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="true" class="custom-radio sr-only" @change="answered = true; updateProgress()">
                                        <div class="p-5 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-green-500 bg-white dark:bg-slate-700/30 text-center transition-all">
                                            <span class="font-bold text-green-600 dark:text-green-400 text-lg block mb-1">BENAR</span>
                                            <span class="text-xs text-gray-400">True</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="false" class="custom-radio sr-only" @change="answered = true; updateProgress()">
                                        <div class="p-5 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-red-500 bg-white dark:bg-slate-700/30 text-center transition-all">
                                            <span class="font-bold text-red-600 dark:text-red-400 text-lg block mb-1">SALAH</span>
                                            <span class="text-xs text-gray-400">False</span>
                                        </div>
                                    </label>
                                </div>

                            @elseif($q->question_type === 'essay')
                                <textarea name="answers[{{ $q->id }}]" rows="5" 
                                          class="w-full px-5 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-slate-900/50 text-gray-800 dark:text-gray-200 focus:ring-0 focus:border-purple-500 transition placeholder-gray-400 text-base"
                                          placeholder="Tulis jawaban Anda secara lengkap di sini..."
                                          @input="answered = $el.value.length > 0; updateProgress()"></textarea>

                            @elseif($q->question_type === 'multiple_select')
                                <div class="space-y-2">
                                    @foreach(json_decode($q->options) as $key => $option)
                                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700/50 cursor-pointer transition group">
                                        <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $key }}" 
                                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                               @change="updateProgress()"> 
                                        <span class="ml-3 text-gray-700 dark:text-gray-300 font-medium group-hover:text-gray-900 dark:group-hover:text-white">{{ $option }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-16 text-center pb-12">
                <button type="button" @click="confirmSubmit()" class="group relative inline-flex items-center gap-3 px-12 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-lg rounded-full shadow-xl hover:shadow-purple-500/40 transition-all transform hover:-translate-y-1">
                    <span>Kumpulkan Jawaban</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
                    Pastikan Anda telah memeriksa semua jawaban sebelum mengumpulkan.
                </p>
            </div>
            
        </form>
    </main>

    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display: none;">
        
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-gray-200 dark:border-gray-700"
             @click.outside="showConfirmModal = false">
            
            <div class="bg-gradient-to-br from-purple-600 to-indigo-600 p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-white/10 backdrop-blur-sm"></div>
                <div class="relative z-10">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner ring-4 ring-white/10">
                        <i class="fas fa-clipboard-check text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Kumpulkan Jawaban?</h3>
                    <p class="text-purple-100 text-sm mt-2">Cek kembali sebelum Anda menyelesaikannya.</p>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-4 mb-8 text-center">
                    <div class="bg-gray-50 dark:bg-slate-700/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold mb-1">Total Soal</p>
                        <p class="text-2xl font-extrabold text-gray-800 dark:text-white">{{ $quiz->questions->count() }}</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-2xl border border-purple-100 dark:border-purple-800/50">
                        <p class="text-xs text-purple-600 dark:text-purple-300 uppercase font-bold mb-1">Terjawab</p>
                        <p class="text-2xl font-extrabold text-purple-600 dark:text-purple-400" x-text="answeredCount"></p>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button @click="finalSubmit()" class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <span>Ya, Kumpulkan Sekarang</span>
                        <i class="fas fa-check-circle"></i>
                    </button>
                    <button @click="showConfirmModal = false" class="w-full py-4 text-gray-500 dark:text-gray-400 font-bold hover:text-gray-800 dark:hover:text-white transition">
                        Batal, Saya Ingin Cek Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function quizTimer(duration) {
            return {
                timeLeft: duration,
                formattedTime: '00:00:00',
                progress: 0,
                answeredCount: 0,
                showConfirmModal: false,
                
                init() {
                    this.startTimer();
                    this.updateProgress();
                    window.onbeforeunload = () => "Waktu berjalan. Yakin keluar?";
                },

                startTimer() {
                    const timer = setInterval(() => {
                        if (this.timeLeft <= 0) {
                            clearInterval(timer);
                            this.finalSubmit(true);
                        } else {
                            this.timeLeft--;
                            this.formatTime();
                        }
                    }, 1000);
                },

                formatTime() {
                    const h = Math.floor(this.timeLeft / 3600);
                    const m = Math.floor((this.timeLeft % 3600) / 60);
                    const s = this.timeLeft % 60;
                    this.formattedTime = (h < 10 ? "0"+h : h) + ":" + (m < 10 ? "0"+m : m) + ":" + (s < 10 ? "0"+s : s);
                },

                updateProgress() {
                    const total = {{ $quiz->questions->count() }};
                    let answered = 0;
                    document.querySelectorAll('.question-card').forEach(card => {
                        const r = card.querySelectorAll('input[type="radio"]:checked').length;
                        const c = card.querySelectorAll('input[type="checkbox"]:checked').length;
                        const t = Array.from(card.querySelectorAll('textarea')).filter(el => el.value.trim().length > 0).length;
                        if (r > 0 || c > 0 || t > 0) answered++;
                    });
                    this.answeredCount = answered;
                    this.progress = Math.round((answered / total) * 100);
                },

                confirmSubmit() {
                    this.updateProgress();
                    this.showConfirmModal = true;
                },

                finalSubmit(force = false) {
                    window.onbeforeunload = null;
                    document.getElementById('quizForm').submit();
                }
            }
        }

        function updateProgress() {
            document.querySelector('[x-data]').__x.$data.updateProgress();
        }
    </script>
</body>
</html>