<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz: {{ $quiz->title }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
        
        .custom-radio:checked + div { border-color: #3b82f6; background-color: #eff6ff; }
        
        /* Animasi Slide */
        .slide-enter { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 h-screen flex flex-col font-sans overflow-hidden"
      x-data="quizApp({{ $quiz->duration_minutes * 60 }}, {{ $quiz->questions->count() }})">

    <header class="bg-white/90 backdrop-blur-md border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm z-30 flex-shrink-0 relative">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                <i class="fas fa-stopwatch text-xl"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-gray-900 truncate max-w-[200px]">{{ $quiz->title }}</h1>
                <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-red-500 text-sm" x-text="formattedTime">00:00:00</span>
                    <span class="text-[10px] text-gray-400">tersisa</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] font-bold text-gray-500 uppercase">Terjawab</p>
                <p class="text-sm font-bold text-blue-600">
                    <span x-text="answeredCount">0</span> / {{ $quiz->questions->count() }}
                </p>
            </div>
            
            <button @click="confirmSubmit()" 
                    :disabled="answeredCount < totalQuestions"
                    :class="answeredCount < totalQuestions ? 'bg-gray-300 cursor-not-allowed text-gray-500' : 'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white shadow-lg transform hover:-translate-y-0.5'"
                    class="px-6 py-2.5 font-bold rounded-xl transition-all text-sm flex items-center gap-2">
                <i class="fas" :class="answeredCount < totalQuestions ? 'fa-lock' : 'fa-paper-plane'"></i>
                <span class="hidden sm:inline">Kumpulkan</span>
            </button>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden relative">
        
        <aside class="w-72 bg-white border-r border-gray-200 flex flex-col z-20 hidden md:flex">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-wider mb-1">Navigasi Soal</h3>
                <p class="text-[10px] text-gray-500">Klik nomor untuk pindah</p>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-4 gap-3">
                    @foreach($quiz->questions as $index => $q)
                    <button @click="currentIndex = {{ $index }}" 
                            class="w-10 h-10 rounded-lg text-sm font-bold transition-all border-2 relative"
                            :class="{
                                'bg-blue-600 border-blue-600 text-white shadow-md scale-110': currentIndex === {{ $index }},
                                'bg-green-600 border-green-600 text-white': isAnswered({{ $q->id }}) && currentIndex !== {{ $index }},
                                'bg-gray-100 border-transparent text-gray-600 hover:bg-gray-200': !isAnswered({{ $q->id }}) && currentIndex !== {{ $index }}
                            }">
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-200 text-[10px] text-gray-400 space-y-2 bg-gray-50">
                <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-blue-600"></div> Sedang Dikerjakan</div>
                <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-green-600"></div> Sudah Dijawab</div>
                <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-gray-200"></div> Belum Dijawab</div>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 relative scroll-smooth" id="questionContainer">
            <form id="quizForm" action="{{ route('student.quiz.submit', [$quiz->course_id, $quiz->id, $submission->id]) }}" method="POST" class="h-full max-w-3xl mx-auto flex flex-col">
                @csrf
                
                <div class="flex-1">
                    @foreach($quiz->questions as $index => $q)
                    <div x-show="currentIndex === {{ $index }}" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-10"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="min-h-full flex flex-col">
                        
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden flex-1 flex flex-col">
                            
                            <div class="px-8 py-6 border-b border-gray-100 bg-white sticky top-0 z-10 flex justify-between items-center">
                                <span class="text-sm font-bold uppercase tracking-wider text-blue-600">
                                    Pertanyaan {{ $index + 1 }} <span class="text-gray-400">/ {{ $quiz->questions->count() }}</span>
                                </span>
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                                    {{ $q->points }} Poin
                                </span>
                            </div>

                            <div class="p-8 flex-1 overflow-y-auto custom-scrollbar">
                                <div class="prose max-w-none text-xl text-gray-800 mb-8 leading-relaxed font-medium">
                                    {!! $q->question !!}
                                </div>

                                <div class="space-y-3 max-w-2xl">
                                    @if($q->question_type === 'multiple_choice')
                                        @foreach(json_decode($q->options) as $key => $option)
                                        <label class="flex items-center group cursor-pointer">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" 
                                                   class="custom-radio sr-only"
                                                   @change="markAnswered({{ $q->id }})">
                                            <div class="w-full p-4 rounded-xl border-2 border-gray-200 hover:border-blue-400 bg-gray-50 transition-all flex items-center gap-4 group-hover:bg-white">
                                                <div class="w-10 h-10 rounded-full border-2 border-gray-300 flex items-center justify-center text-sm font-bold text-gray-500 group-hover:border-blue-500 group-hover:text-blue-500 transition bg-white">
                                                    {{ chr(65 + $key) }}
                                                </div>
                                                <span class="text-base text-gray-700 font-medium">{{ $option }}</span>
                                            </div>
                                        </label>
                                        @endforeach
                                    
                                    @elseif($q->question_type === 'true_false')
                                        <div class="grid grid-cols-2 gap-4">
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="answers[{{ $q->id }}]" value="true" class="custom-radio sr-only" @change="markAnswered({{ $q->id }})">
                                                <div class="p-6 rounded-xl border-2 border-gray-200 hover:border-green-500 bg-white text-center transition-all h-full flex flex-col justify-center">
                                                    <span class="font-bold text-green-600 text-xl block mb-1">BENAR</span>
                                                    <span class="text-xs text-gray-400 uppercase tracking-widest">True</span>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="answers[{{ $q->id }}]" value="false" class="custom-radio sr-only" @change="markAnswered({{ $q->id }})">
                                                <div class="p-6 rounded-xl border-2 border-gray-200 hover:border-red-500 bg-white text-center transition-all h-full flex flex-col justify-center">
                                                    <span class="font-bold text-red-600 text-xl block mb-1">SALAH</span>
                                                    <span class="text-xs text-gray-400 uppercase tracking-widest">False</span>
                                                </div>
                                            </label>
                                        </div>

                                    @elseif($q->question_type === 'essay')
                                        <textarea name="answers[{{ $q->id }}]" rows="6" 
                                                  class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-800 focus:ring-0 focus:border-blue-500 transition placeholder-gray-400 text-base shadow-sm"
                                                  placeholder="Ketikan jawaban Anda di sini..."
                                                  @input="checkTextarea({{ $q->id }}, $el)"></textarea>

                                    @elseif($q->question_type === 'multiple_select')
                                        <div class="space-y-2">
                                            @foreach(json_decode($q->options) as $key => $option)
                                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:bg-white cursor-pointer transition group bg-gray-50">
                                                <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $key }}" 
                                                       class="w-6 h-6 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                       @change="checkCheckbox({{ $q->id }})"> 
                                                <span class="ml-4 text-gray-700 font-medium group-hover:text-blue-600 transition">{{ $option }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                                <button type="button" 
                                        @click="prev()" 
                                        x-show="currentIndex > 0"
                                        class="px-6 py-3 text-gray-600 font-bold hover:bg-gray-200 rounded-xl transition flex items-center gap-2">
                                    <i class="fas fa-arrow-left"></i> Sebelumnya
                                </button>
                                <div x-show="currentIndex === 0"></div> <button type="button" 
                                        @click="next()" 
                                        x-show="currentIndex < totalQuestions - 1"
                                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition flex items-center gap-2">
                                    Selanjutnya <i class="fas fa-arrow-right"></i>
                                </button>

                                <div x-show="currentIndex === totalQuestions - 1">
                                    <button type="button" 
                                            @click="confirmSubmit()"
                                            :disabled="answeredCount < totalQuestions"
                                            :class="answeredCount < totalQuestions ? 'bg-gray-300 cursor-not-allowed text-gray-500' : 'bg-gradient-to-r from-green-600 to-emerald-600 text-white shadow-lg hover:shadow-green-500/30 transform hover:-translate-y-0.5'"
                                            class="px-8 py-3 font-bold rounded-xl transition-all flex items-center gap-2">
                                        <span>Selesai & Kumpulkan</span> <i class="fas fa-check-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </form>
        </main>
    </div>

    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" style="display: none;">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-gray-200" @click.outside="showConfirmModal = false">
            <div class="bg-gradient-to-br from-purple-600 to-indigo-600 p-8 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i class="fas fa-clipboard-check text-3xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Semua Terjawab!</h3>
                <p class="text-purple-100 text-sm mt-1">Yakin ingin mengumpulkan sekarang?</p>
            </div>
            <div class="p-8">
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 mb-6 text-center">
                    <p class="text-sm text-gray-500">Anda telah menjawab <strong class="text-blue-600">{{ $quiz->questions->count() }}</strong> dari <strong class="text-gray-800">{{ $quiz->questions->count() }}</strong> soal.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <button @click="finalSubmit()" class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                        Ya, Kumpulkan!
                    </button>
                    <button @click="showConfirmModal = false" class="w-full py-4 text-gray-500 font-bold hover:text-gray-800 transition">
                        Batal, Cek Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function quizApp(duration, total) {
            return {
                timeLeft: duration,
                formattedTime: '00:00:00',
                progress: 0,
                totalQuestions: total,
                answeredCount: 0,
                currentIndex: 0,
                showConfirmModal: false,
                answers: {}, // Object untuk track ID soal yg sudah dijawab

                init() {
                    this.startTimer();
                    window.onbeforeunload = () => "Waktu berjalan. Yakin keluar?";
                },

                startTimer() {
                    const timer = setInterval(() => {
                        if (this.timeLeft <= 0) {
                            clearInterval(timer);
                            // FORCE SUBMIT saat waktu habis (Bypass validasi)
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

                // Update status terjawab
                markAnswered(questionId) {
                    this.answers[questionId] = true;
                    this.recalcProgress();
                },
                
                checkTextarea(questionId, el) {
                    if(el.value.trim().length > 0) {
                        this.answers[questionId] = true;
                    } else {
                        delete this.answers[questionId];
                    }
                    this.recalcProgress();
                },

                checkCheckbox(questionId) {
                    // Cari input checkbox dengan name yg sama
                    const checked = document.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`).length > 0;
                    if(checked) this.answers[questionId] = true;
                    else delete this.answers[questionId];
                    this.recalcProgress();
                },

                recalcProgress() {
                    this.answeredCount = Object.keys(this.answers).length;
                    this.progress = Math.round((this.answeredCount / this.totalQuestions) * 100);
                },

                isAnswered(questionId) {
                    return !!this.answers[questionId];
                },

                next() {
                    if (this.currentIndex < this.totalQuestions - 1) {
                        this.currentIndex++;
                        // Scroll to top of question container
                        document.getElementById('questionContainer').scrollTop = 0;
                    }
                },

                prev() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                },

                confirmSubmit() {
                    // Double check (walaupun tombol disabled)
                    if (this.answeredCount < this.totalQuestions) {
                        alert("Masih ada soal yang belum dijawab!");
                        return;
                    }
                    this.showConfirmModal = true;
                },

                finalSubmit(force = false) {
                    // Kalau force (waktu habis), abaikan validasi answeredCount
                    window.onbeforeunload = null;
                    document.getElementById('quizForm').submit();
                }
            }
        }
    </script>
</body>
</html>