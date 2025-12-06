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
            }
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
            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                <i class="fas fa-stopwatch text-xl"></i>
            </div>
            <div>
                <h1 class="text-sm md:text-lg font-bold text-gray-900 dark:text-white truncate max-w-[150px] md:max-w-md">{{ $quiz->title }}</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Sisa Waktu: <span class="font-mono font-bold text-red-500 text-sm" x-text="formattedTime">00:00:00</span></p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden md:flex flex-col items-end mr-4">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Progress</span>
                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-500" style="width: 0%" x-bind:style="'width: ' + progress + '%'"></div>
                </div>
            </div>
            <button @click="confirmSubmit()" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5 text-sm flex items-center gap-2">
                <i class="fas fa-paper-plane"></i> <span class="hidden sm:inline">Kumpulkan</span>
            </button>
        </div>
    </header>

    <div class="h-24"></div>

    <main class="flex-1 max-w-4xl mx-auto w-full px-4 pb-20">
        
        <form id="quizForm" action="{{ route('student.quiz.submit', [$quiz->course_id, $quiz->id, $submission->id]) }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                @foreach($quiz->questions as $index => $q)
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors question-card" id="q_{{ $q->id }}">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Soal No. {{ $index + 1 }}</span>
                        <span class="text-xs font-bold px-2 py-1 rounded bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-gray-300">{{ $q->points }} Poin</span>
                    </div>

                    <div class="p-6 md:p-8">
                        <div class="prose dark:prose-invert max-w-none text-lg text-gray-800 dark:text-gray-200 mb-6">{!! $q->question_text !!}</div>

                        <div class="space-y-3">
                            @if($q->question_type === 'multiple_choice')
                                @foreach(json_decode($q->options) as $key => $option)
                                <label class="block relative cursor-pointer group">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" class="custom-radio sr-only" @change="updateProgress()">
                                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 bg-white dark:bg-slate-700/30 transition-all flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-500 flex items-center justify-center text-sm font-bold text-gray-500 dark:text-gray-400 group-hover:border-blue-500 group-hover:text-blue-500 transition">{{ chr(65 + $key) }}</div>
                                        <span class="text-base text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                    </div>
                                </label>
                                @endforeach
                            @elseif($q->question_type === 'true_false')
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="true" class="custom-radio sr-only" @change="updateProgress()">
                                        <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-green-400 bg-white dark:bg-slate-700/30 text-center transition"><span class="font-bold text-green-600 dark:text-green-400">BENAR</span></div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="false" class="custom-radio sr-only" @change="updateProgress()">
                                        <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-red-400 bg-white dark:bg-slate-700/30 text-center transition"><span class="font-bold text-red-600 dark:text-red-400">SALAH</span></div>
                                    </label>
                                </div>
                            @elseif($q->question_type === 'essay')
                                <textarea name="answers[{{ $q->id }}]" rows="4" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-slate-800 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" placeholder="Tulis jawaban Anda di sini..." @input="updateProgress()"></textarea>
                            @elseif($q->question_type === 'multiple_select')
                                <div class="space-y-2">
                                    @foreach(json_decode($q->options) as $key => $option)
                                    <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700/50 cursor-pointer transition">
                                        <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $key }}" class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500" @change="updateProgress()"> 
                                        <span class="ml-3 text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12 text-center pb-12">
                <button type="button" @click="confirmSubmit()" class="px-10 py-4 bg-purple-600 hover:bg-purple-700 text-white font-bold text-lg rounded-full shadow-xl hover:shadow-purple-500/40 transition transform hover:-translate-y-1 w-full md:w-auto">
                    Kumpulkan Jawaban
                </button>
            </div>
        </form>
    </main>

    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all"
             @click.outside="showConfirmModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i class="fas fa-clipboard-check text-3xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Kumpulkan Jawaban?</h3>
                <p class="text-purple-100 text-sm mt-1">Pastikan Anda sudah yakin dengan semua jawaban.</p>
            </div>

            <div class="p-6">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800/50 rounded-xl p-4 flex gap-3 mb-6">
                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-500 mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-yellow-800 dark:text-yellow-400 text-sm">Peringatan</h4>
                        <p class="text-xs text-yellow-700 dark:text-yellow-500/80 mt-1">
                            Setelah dikumpulkan, Anda <span class="font-bold">tidak dapat mengubah</span> jawaban lagi. Nilai akan langsung keluar (kecuali soal Essay).
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 text-center">
                    <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Total Soal</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $quiz->questions->count() }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Terjawab</p>
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400" x-text="answeredCount + ' Soal'"></p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="showConfirmModal = false" class="flex-1 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                        Periksa Lagi
                    </button>
                    <button @click="finalSubmit()" class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-lg transition">
                        Ya, Kumpulkan!
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
                showConfirmModal: false, // State Modal
                
                init() {
                    this.startTimer();
                    this.updateProgress();
                    window.onbeforeunload = function() { return "Waktu berjalan. Yakin keluar?"; };
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
                    this.formattedTime = 
                        (h < 10 ? "0" + h : h) + ":" + (m < 10 ? "0" + m : m) + ":" + (s < 10 ? "0" + s : s);
                },

                // Hitung progress & update state answeredCount untuk modal
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

                // Buka Modal Konfirmasi
                confirmSubmit() {
                    this.updateProgress(); // Pastikan hitungan update
                    this.showConfirmModal = true;
                },

                // Submit Beneran
                finalSubmit(force = false) {
                    window.onbeforeunload = null;
                    document.getElementById('quizForm').submit();
                }
            }
        }

        // Helper global untuk trigger update dari input change
        function updateProgress() {
            document.querySelector('[x-data]').__x.$data.updateProgress();
        }
    </script>
</body>
</html>