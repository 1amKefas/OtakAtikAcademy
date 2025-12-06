@extends('layouts.app')

@section('title', isset($question) ? 'Edit Soal' : 'Tambah Soal')

@section('content')
<div class="bg-white">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.quiz.edit', [$course->id, $quiz->id]) }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ isset($question) ? '✏️ Edit Soal' : '➕ Tambah Soal Baru' }}</h1>
            <p class="text-indigo-100">{{ $quiz->title }}</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 py-8">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800 font-semibold mb-2">❌ Terjadi Kesalahan:</p>
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($question) ? route('instructor.quiz.question.update', [$course->id, $quiz->id, $question->id]) : route('instructor.quiz.question.add', [$course->id, $quiz->id]) }}" 
              method="POST" id="questionForm">
            @csrf
            @if(isset($question))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📝 Teks Soal</h3>
                    <textarea name="question" required rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                              placeholder="Tulis soal Anda di sini...">{{ old('question', $question->question ?? '') }}</textarea>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">🎯 Tipe Soal</h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-indigo-50 cursor-pointer transition"
                               onclick="changeQuestionType('multiple_choice')">
                            <input type="radio" name="question_type" value="multiple_choice" 
                                   {{ old('question_type', $question->question_type ?? 'multiple_choice') === 'multiple_choice' ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3">
                                <p class="font-semibold text-gray-800">Pilihan Ganda</p>
                                <p class="text-sm text-gray-600">Siswa memilih satu jawaban dari beberapa pilihan</p>
                            </span>
                        </label>

                        <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-indigo-50 cursor-pointer transition"
                               onclick="changeQuestionType('multiple_select')">
                            <input type="radio" name="question_type" value="multiple_select" 
                                   {{ old('question_type', $question->question_type ?? '') === 'multiple_select' ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3">
                                <p class="font-semibold text-gray-800">Multiple Choice</p>
                                <p class="text-sm text-gray-600">Siswa bisa memilih lebih dari satu jawaban benar</p>
                            </span>
                        </label>

                        <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-indigo-50 cursor-pointer transition"
                               onclick="changeQuestionType('true_false')">
                            <input type="radio" name="question_type" value="true_false" 
                                   {{ old('question_type', $question->question_type ?? '') === 'true_false' ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3">
                                <p class="font-semibold text-gray-800">Benar/Salah</p>
                                <p class="text-sm text-gray-600">Siswa memilih Benar atau Salah</p>
                            </span>
                        </label>

                        <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-indigo-50 cursor-pointer transition"
                               onclick="changeQuestionType('essay')">
                            <input type="radio" name="question_type" value="essay" 
                                   {{ old('question_type', $question->question_type ?? '') === 'essay' ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3">
                                <p class="font-semibold text-gray-800">Essay</p>
                                <p class="text-sm text-gray-600">Siswa menulis jawaban (dinilai manual oleh instruktur)</p>
                            </span>
                        </label>
                    </div>
                </div>

                <div id="optionsSection" class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">🔤 Pilihan Jawaban</h3>
                    <div id="optionsContainer" class="space-y-3">
                        @php
                            $options = old('options', isset($question) ? ($question->options ?? []) : []);
                        @endphp
                        @forelse($options as $index => $option)
                        <div class="flex items-center gap-3 optionItem">
                            <span class="text-sm font-bold text-gray-600 w-6">{{ chr(65 + $index) }}</span>
                            <input type="text" name="options[]" value="{{ $option }}"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Masukkan pilihan...">
                            <button type="button" onclick="removeOption(this)" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition">
                                🗑️
                            </button>
                        </div>
                        @empty
                        <div class="flex items-center gap-3 optionItem">
                            <span class="text-sm font-bold text-gray-600 w-6">A</span>
                            <input type="text" name="options[]"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Masukkan pilihan...">
                            <button type="button" onclick="removeOption(this)" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition">
                                🗑️
                            </button>
                        </div>
                        <div class="flex items-center gap-3 optionItem">
                            <span class="text-sm font-bold text-gray-600 w-6">B</span>
                            <input type="text" name="options[]"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Masukkan pilihan...">
                            <button type="button" onclick="removeOption(this)" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition">
                                🗑️
                            </button>
                        </div>
                        @endforelse
                    </div>
                    <button type="button" onclick="addOption()" 
                            class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        + Tambah Pilihan
                    </button>
                </div>

                <div id="correctAnswerSection" class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">✓ Jawaban Benar</h3>
                    
                    <div id="mcCorrectAnswer" class="space-y-3">
                        <p class="text-sm text-gray-600 mb-3">Pilih SATU jawaban yang benar:</p>
                        <div id="correctAnswerOptions" class="space-y-2">
                            </div>
                    </div>

                    <div id="msCorrectAnswer" class="space-y-3 hidden">
                        <p class="text-sm text-gray-600 mb-3">Centang SEMUA jawaban yang benar:</p>
                        <div id="correctAnswerCheckboxes" class="space-y-2">
                            </div>
                    </div>

                    <div id="tfCorrectAnswer" class="space-y-3 hidden">
                        <p class="text-sm text-gray-600 mb-3">Jawaban yang benar adalah:</p>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="correct_answer" value="true" 
                                       {{ old('correct_answer', $question->correct_answer ?? '') === 'true' ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                <span class="ml-2 text-gray-800">✓ Benar</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="correct_answer" value="false" 
                                       {{ old('correct_answer', $question->correct_answer ?? '') === 'false' ? 'checked' : '' }}
                                       class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                <span class="ml-2 text-gray-800">✗ Salah</span>
                            </label>
                        </div>
                    </div>

                    <div id="essayCorrectAnswer" class="hidden bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-blue-800 text-sm">
                            ℹ️ Essay questions don't have a predefined correct answer. <br>
                            Students' responses will be reviewed manually by the instructor.
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">🔢 Urutan Soal</h3>
                    <input type="number" name="order" min="1"
                           value="{{ old('order', $question->order ?? ($quiz->questions()->count() + 1)) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Urutan soal">
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">⚖️ Bobot Nilai</h3>
                    <input type="number" name="points" min="1" max="100"
                           value="{{ old('points', $question->points ?? 10) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Nilai untuk soal ini">
                </div>

                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        ✓ {{ isset($question) ? 'Update Soal' : 'Tambah Soal' }}
                    </button>
                    <a href="{{ route('instructor.quiz.edit', [$course->id, $quiz->id]) }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        ✕ Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function changeQuestionType(type) {
        const mcSection = document.getElementById('mcCorrectAnswer');
        const msSection = document.getElementById('msCorrectAnswer'); // Baru
        const tfSection = document.getElementById('tfCorrectAnswer');
        const essaySection = document.getElementById('essayCorrectAnswer');
        const optionsSection = document.getElementById('optionsSection');

        // Hide all sections
        mcSection.classList.add('hidden');
        msSection.classList.add('hidden');
        tfSection.classList.add('hidden');
        essaySection.classList.add('hidden');
        optionsSection.classList.add('hidden');

        // Show relevant sections
        if (type === 'multiple_choice') {
            mcSection.classList.remove('hidden');
            optionsSection.classList.remove('hidden');
        } else if (type === 'multiple_select') { // Baru
            msSection.classList.remove('hidden');
            optionsSection.classList.remove('hidden');
        } else if (type === 'true_false') {
            tfSection.classList.remove('hidden');
        } else if (type === 'essay') {
            essaySection.classList.remove('hidden');
        }
    }

    function addOption() {
        const container = document.getElementById('optionsContainer');
        const itemCount = container.children.length;
        const letter = String.fromCharCode(65 + itemCount);
        
        const html = `
            <div class="flex items-center gap-3 optionItem">
                <span class="text-sm font-bold text-gray-600 w-6">${letter}</span>
                <input type="text" name="options[]"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Masukkan pilihan...">
                <button type="button" onclick="removeOption(this)" 
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition">
                    🗑️
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        updateCorrectAnswerOptions();
    }

    function removeOption(btn) {
        btn.closest('.optionItem').remove();
        updateCorrectAnswerOptions();
    }

    function updateCorrectAnswerOptions() {
        const options = Array.from(document.querySelectorAll('input[name="options[]"]')).map(el => el.value);
        const container = document.getElementById('correctAnswerOptions');
        const containerMulti = document.getElementById('correctAnswerCheckboxes'); // Baru
        
        if (options.length === 0) {
            container.innerHTML = '<p class="text-gray-600 text-sm italic">Tambahkan pilihan terlebih dahulu</p>';
            containerMulti.innerHTML = '<p class="text-gray-600 text-sm italic">Tambahkan pilihan terlebih dahulu</p>';
            return;
        }

        // Generate Radio Buttons (Untuk Single Choice)
        let htmlRadio = '';
        // Generate Checkboxes (Untuk Multiple Select)
        let htmlCheckbox = '';

        options.forEach((option, index) => {
            if (option) {
                // Radio
                htmlRadio += `
                    <label class="flex items-center">
                        <input type="radio" name="correct_answer" value="${index}" 
                               class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-800">${String.fromCharCode(65 + index)}: ${option}</span>
                    </label>
                `;

                // Checkbox
                htmlCheckbox += `
                    <label class="flex items-center">
                        <input type="checkbox" name="correct_answers[]" value="${index}" 
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-gray-800">${String.fromCharCode(65 + index)}: ${option}</span>
                    </label>
                `;
            }
        });
        
        container.innerHTML = htmlRadio;
        containerMulti.innerHTML = htmlCheckbox;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        const checkedType = document.querySelector('input[name="question_type"]:checked');
        const type = checkedType ? checkedType.value : 'multiple_choice';
        changeQuestionType(type);
        
        // Initial populate
        updateCorrectAnswerOptions();

        // Add event listeners to existing option inputs
        document.querySelectorAll('input[name="options[]"]').forEach(input => {
            input.addEventListener('input', updateCorrectAnswerOptions);
        });
    });
    
    // Update listener when adding new option
    const originalAddOption = window.addOption;
    window.addOption = function() {
        originalAddOption();
        // Add listener to newly added input
        const latestInput = document.querySelector('#optionsContainer input[name="options[]"]:last-of-type');
        if (latestInput) {
            latestInput.addEventListener('input', updateCorrectAnswerOptions);
        }
    };
</script>
@endsection