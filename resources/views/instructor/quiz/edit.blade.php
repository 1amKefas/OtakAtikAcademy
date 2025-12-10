@extends('layouts.app')

@section('title', 'Kelola Quiz: ' . $quiz->title)

@section('content')
{{-- Load CSS & JS Eksternal --}}
<link rel="stylesheet" href="{{ asset('css/instructor.css') }}">

{{-- [TAMBAHAN] Load TinyMCE (cdnjs) --}}
<script src="https://cdn.tiny.cloud/1/40wmpfbvzkycl0abvcvdpedgmg1a5pa6mu5yyv37jgk0thqo/tinymce/7/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

{{-- Script Custom Kita --}}
<script src="{{ asset('js/instructor-quiz-editor.js') }}"></script>

<div class="bg-gray-50 min-h-screen pb-20" 
     x-data="quizEditor({
         addUrl: '{{ route('instructor.quiz.question.add', [$course->id, $quiz->id]) }}',
         updateUrlBase: '{{ url('instructor/courses/' . $course->id . '/quiz/' . $quiz->id . '/questions') }}',
         maxScore: '{{ $quiz->passing_score_max ?? 100 }}', {{-- Asumsi lo mau set 100 default --}}
         questions: {{ $questions->toJson() }}
     })">
    
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex flex-col md:flex-row items-center justify-between sticky top-0 z-30 shadow-sm gap-4">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <a href="{{ route('instructor.courses.manage', $course->id) }}" class="text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $quiz->title }}</h1>
                <p class="text-xs text-gray-500">Total Soal: <span x-text="questionsList.length"></span></p>
            </div>
        </div>

        {{-- SCORE MONITORING BAR (Fitur Baru) --}}
        <div class="flex-1 w-full md:max-w-md px-4">
            <div class="flex justify-between text-xs font-bold uppercase tracking-wider mb-1">
                <span class="text-gray-500">Total Nilai</span>
                <span :class="scoreStatus.text">
                    <span x-text="projectedTotalScore"></span> / <span x-text="maxScore"></span>
                </span>
            </div>
            {{-- Bar Container --}}
            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                <div class="h-2.5 rounded-full score-bar-transition relative"
                     :class="scoreStatus.color"
                     :style="'width: ' + Math.min((projectedTotalScore / maxScore) * 100, 100) + '%'">
                     {{-- Efek Striped kalau over --}}
                     <div class="absolute inset-0 bg-white/20" x-show="projectedTotalScore > maxScore" style="background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent); background-size: 1rem 1rem;"></div>
                </div>
            </div>
            {{-- Alert Text --}}
            <p class="text-[10px] mt-1 font-medium text-right" :class="scoreStatus.text" x-text="scoreStatus.msg"></p>
        </div>

        <div class="flex items-center gap-3">
             <form action="{{ route('instructor.quiz.destroy', [$course->id, $quiz->id]) }}" method="POST" onsubmit="return confirm('Hapus Quiz ini beserta semua soalnya?');">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50" title="Hapus Quiz">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            <button @click="openAddMode()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Soal Baru
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        
        {{-- [BARU] FORM PENGATURAN QUIZ --}}
        <div class="bg-white p-6 rounded-xl border border-gray-200 mb-6 shadow-sm" x-data="{ openSettings: false }">
            <div class="flex justify-between items-center cursor-pointer" @click="openSettings = !openSettings">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Pengaturan Quiz</h3>
                        <p class="text-xs text-gray-500">Edit Judul, Deskripsi, Durasi & Passing Score</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-indigo-600 font-bold" x-show="!openSettings">Tampilkan</span>
                    <span class="text-xs text-gray-500 font-bold" x-show="openSettings">Sembunyikan</span>
                    <i class="fas" :class="openSettings ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </div>
            </div>

            <div x-show="openSettings" x-transition class="mt-6 pt-6 border-t border-gray-100">
                <form action="{{ route('instructor.quiz.update', [$course->id, $quiz->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Judul --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Quiz</label>
                            <input type="text" name="title" value="{{ old('title', $quiz->title) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi / Instruksi</label>
                            <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $quiz->description) }}</textarea>
                        </div>

                        {{-- Durasi --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Durasi Pengerjaan</label>
                            <div class="relative">
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="5" max="300" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Menit</span>
                                </div>
                            </div>
                        </div>

                        {{-- Passing Score --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Passing Score (KKM)</label>
                            <div class="relative">
                                <input type="number" name="passing_score" value="{{ old('passing_score', $quiz->passing_score) }}" min="0" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Point</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                         <button type="button" @click="openSettings = false" class="text-gray-500 hover:text-gray-700 font-medium text-sm px-4">Batal</button>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- INPUT SETTING MAX SCORE (Manual Input) --}}
        {{-- Ini dummy form action kalau mau update setting quiz, atau pake JS ajax --}}
        <div class="bg-white p-4 rounded-xl border border-gray-200 mb-6 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Target Nilai Maksimum</h3>
                    <p class="text-xs text-gray-500">Tentukan batas total nilai quiz ini.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Kita pake x-model.lazy biar gak render ulang tiap ketik --}}
                <input type="number" x-model.number="maxScore" class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-center font-bold text-gray-700 focus:ring-indigo-500">
                <span class="text-sm font-bold text-gray-500">Poin</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- KOLOM KIRI: DAFTAR SOAL --}}
            <div class="lg:col-span-4 space-y-4">
                <div class="flex items-center justify-between px-1">
                    <h2 class="font-bold text-gray-700">Daftar Soal</h2>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full"><span x-text="questionsList.length"></span> Item</span>
                </div>

                <div id="questions-list" class="questions-scroll-list space-y-3">
                    @forelse($questions as $index => $q)
                    {{-- Kita bind click event ke Alpine --}}
                    <div class="question-card bg-white p-4 rounded-xl border-l-4 shadow-sm hover:shadow-md transition cursor-pointer group relative border-gray-200 hover:border-indigo-300"
                         :class="{ 'question-card-active': currentQuestionId === {{ $q->id }} }"
                         @click="loadQuestion({{ $q }})"> {{-- Pass full object --}}
                        
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex items-start gap-3 overflow-hidden">
                                <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded min-w-[2rem] text-center">{{ $index + 1 }}</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug">
                                        {{ strip_tags($q->question) }}
                                    </p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full border bg-gray-50 border-gray-200 text-gray-600">
                                            {{ $q->question_type }}
                                        </span>
                                        <span class="text-[10px] font-bold" :class="{{ $q->points }} > 20 ? 'text-orange-500' : 'text-gray-400'">
                                            {{ $q->points }} Poin
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Delete Button (Stop Propagation) --}}
                            <form action="{{ route('instructor.quiz.question.delete', [$course->id, $quiz->id, $q->id]) }}" method="POST" onsubmit="return confirm('Hapus soal ini?');" 
                                  @click.stop>
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-red-500 transition p-1">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50">
                        <i class="far fa-folder-open text-3xl text-gray-300 mb-2"></i>
                        <p class="text-gray-400 text-sm">Belum ada soal.</p>
                        <button @click="openAddMode()" class="text-indigo-600 text-sm font-bold hover:underline mt-2">Buat Soal Pertama</button>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- KOLOM KANAN: EDITOR FORM --}}
            <div class="lg:col-span-8" x-ref="quizFormContainer">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden sticky top-24">
                    
                    {{-- Form Header --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center transition-colors duration-300"
                         :class="mode === 'edit' ? 'bg-indigo-50/50' : ''">
                        <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                            <span x-text="mode === 'add' ? '➕ Tambah Soal Baru' : '✏️ Edit Soal'"></span>
                        </h3>
                        <div class="flex items-center gap-3">
                             <span class="text-xs font-bold px-2 py-1 rounded bg-white border border-gray-200 text-gray-500" x-show="mode === 'edit'">ID: <span x-text="currentQuestionId"></span></span>
                             <button x-show="mode === 'edit'" @click="resetForm()" class="text-xs text-indigo-600 hover:underline">Batal Edit</button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <div class="p-6 md:p-8">
                        <form method="POST" :action="formAction">
                            @csrf
                            <input type="hidden" name="_method" :value="mode === 'edit' ? 'PUT' : 'POST'">
                            
                            {{-- Input Teks Soal --}}
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pertanyaan</label>
                                <textarea id="questionEditor" name="question" class="w-full"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                {{-- Tipe Soal --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Soal</label>
                                    <select name="question_type" x-model="questionType" @change="changeType($event.target.value)" 
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                                        <option value="pilihan ganda">Pilihan Ganda</option>
                                        <option value="multiple_select">Multiple Choice</option>
                                        <option value="true_false">Benar / Salah</option>
                                        <option value="essay">Essay</option>
                                    </select>
                                </div>

                                {{-- Bobot Nilai --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Bobot Poin</label>
                                    <div class="relative">
                                        <input type="number" name="points" x-model="points" min="1" max="100" 
                                               class="w-full px-4 py-3 pl-10 rounded-lg border focus:ring-2 transition font-bold"
                                               :class="projectedTotalScore > maxScore ? 'border-red-300 text-red-600 focus:ring-red-500' : 'border-gray-300 text-gray-800 focus:ring-indigo-500'">
                                        <i class="fas fa-star absolute left-3 top-3.5" :class="projectedTotalScore > maxScore ? 'text-red-400' : 'text-yellow-400'"></i>
                                    </div>
                                    <p class="text-[10px] mt-1 text-gray-400">Total poin saat ini + input ini: <span x-text="projectedTotalScore"></span></p>
                                </div>

                                {{-- Hidden Order --}}
                                <input type="hidden" name="order" x-model="order">
                            </div>

                            {{-- AREA JAWABAN DINAMIS --}}
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 mb-8">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Opsi Jawaban</h4>
                                
                                {{-- 1. Multiple Choice / Select --}}
                                <div x-show="['multiple_choice', 'multiple_select'].includes(questionType)">
                                    <div class="space-y-3">
                                        <template x-for="(opt, idx) in options" :key="idx">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded font-bold text-gray-500 shadow-sm" x-text="String.fromCharCode(65 + idx)"></div>
                                                <input type="text" name="options[]" x-model="options[idx]" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Jawaban...">
                                                <button type="button" @click="removeOption(idx)" class="text-gray-400 hover:text-red-500 p-2 transition"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </template>
                                    </div>
                                    <button type="button" @click="addOption()" class="mt-4 text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-2"><i class="fas fa-plus-circle"></i> Tambah</button>

                                    <div class="mt-6 pt-4 border-t border-gray-200">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban:</label>
                                        {{-- Radio --}}
                                        <div x-show="questionType === 'multiple_choice'" class="space-y-2">
                                            <template x-for="(opt, idx) in options" :key="'radio-'+idx">
                                                <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-100 transition w-full" x-show="opt.trim() !== ''">
                                                    <input type="radio" name="correct_answer" :value="idx" x-model="correctAnswer" class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                    <span class="text-sm text-gray-800 font-medium"><span class="font-bold text-indigo-600 mr-1" x-text="String.fromCharCode(65 + idx) + '.'"></span> <span x-text="opt"></span></span>
                                                </label>
                                            </template>
                                        </div>
                                        {{-- Checkbox --}}
                                        <div x-show="questionType === 'multiple_select'" class="space-y-2">
                                            <template x-for="(opt, idx) in options" :key="'check-'+idx">
                                                <label class="flex items-center gap-2 cursor-pointer p-2 rounded hover:bg-gray-100 transition w-full" x-show="opt.trim() !== ''">
                                                    <input type="checkbox" name="correct_answers[]" :value="idx" x-model="correctAnswersArray" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                                    <span class="text-sm text-gray-800 font-medium"><span class="font-bold text-indigo-600 mr-1" x-text="String.fromCharCode(65 + idx) + '.'"></span> <span x-text="opt"></span></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. True/False --}}
                                <div x-show="questionType === 'true_false'" class="flex gap-4">
                                    <label class="flex-1 cursor-pointer"><input type="radio" name="correct_answer" value="true" x-model="correctAnswer" class="peer sr-only"><div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 text-center transition"><span class="font-bold text-gray-600 peer-checked:text-green-700 text-lg">BENAR</span></div></label>
                                    <label class="flex-1 cursor-pointer"><input type="radio" name="correct_answer" value="false" x-model="correctAnswer" class="peer sr-only"><div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 text-center transition"><span class="font-bold text-gray-600 peer-checked:text-red-700 text-lg">SALAH</span></div></label>
                                </div>

                                {{-- 3. Essay --}}
                                <div x-show="questionType === 'essay'">
                                    <p class="text-sm text-gray-500 italic bg-white p-3 rounded border border-gray-200 mb-4">Soal Essay dinilai manual.</p>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Penjelasan (Opsional)</label>
                                    <textarea name="answer_explanation" x-model="essayExplanation" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                                </div>
                            </div>

                            {{-- Footer Buttons --}}
                            <div class="flex items-center gap-4">
                                <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition transform flex justify-center items-center gap-2">
                                    <i class="fas fa-save"></i> <span>Simpan Soal</span>
                                </button>
                                <button type="button" @click="resetForm()" class="px-6 py-3.5 border border-gray-300 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition" x-show="mode === 'edit'">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection