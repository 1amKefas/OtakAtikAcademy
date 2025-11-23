@extends('layouts.app')

@section('title', 'Quiz: ' . $quiz->title)

@section('content')
<div class="bg-white">
    <!-- Quiz Container -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header with Timer -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $quiz->title }}</h1>
                        <p class="text-gray-600">{{ $quiz->questions()->count() }} Soal</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-1">Sisa Waktu</p>
                        <div id="timer" class="text-4xl font-bold text-blue-600">{{ $quiz->duration_minutes }}:00</div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all" style="width: 0%"></div>
                </div>
                <p id="progressText" class="text-sm text-gray-600 mt-2">Soal 1 dari {{ $quiz->questions()->count() }}</p>
            </div>

            <!-- Questions Form -->
            <form action="{{ route('student.quiz.submit', [$course->id, $quiz->id, $submission->id]) }}" method="POST" id="quizForm">
                @csrf

                @foreach($quiz->questions()->orderBy('order')->get() as $index => $question)
                <div class="question-container bg-white rounded-lg shadow-lg p-8 mb-6" data-question="{{ $index + 1 }}">
                    <!-- Question Header -->
                    <div class="mb-6">
                        <p class="text-sm font-semibold text-blue-600 mb-2">Soal {{ $index + 1 }} dari {{ $quiz->questions()->count() }}</p>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $question->question }}</h2>
                    </div>

                    <!-- Question Type Specific -->
                    @if($question->question_type === 'multiple_choice')
                        <!-- Multiple Choice -->
                        <div class="space-y-3" id="answers-{{ $question->id }}">
                            @php
                                // Handle both string JSON and array formats
                                $options = is_string($question->options) 
                                    ? json_decode($question->options, true) 
                                    : $question->options;
                                $options = $options ?? [];
                            @endphp
                            @foreach($options as $optionIndex => $option)
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 cursor-pointer transition">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optionIndex }}" 
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                       required>
                                <span class="ml-3 text-gray-800">{{ $option }}</span>
                            </label>
                            @endforeach
                        </div>

                    @elseif($question->question_type === 'true_false')
                        <!-- True/False -->
                        <div class="grid grid-cols-2 gap-4" id="answers-{{ $question->id }}">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-green-50 hover:border-green-400 cursor-pointer transition">
                                <input type="radio" name="answers[{{ $question->id }}]" value="true" 
                                       class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                                       required>
                                <span class="ml-3 text-gray-800 font-semibold">Benar</span>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-red-50 hover:border-red-400 cursor-pointer transition">
                                <input type="radio" name="answers[{{ $question->id }}]" value="false" 
                                       class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                                       required>
                                <span class="ml-3 text-gray-800 font-semibold">Salah</span>
                            </label>
                        </div>

                    @elseif($question->question_type === 'essay')
                        <!-- Essay -->
                        <div id="answers-{{ $question->id }}">
                            <textarea name="answers[{{ $question->id }}]" 
                                      rows="5"
                                      maxlength="2000"
                                      placeholder="Tulis jawaban Anda di sini... (maksimal 2000 karakter)"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      required></textarea>
                            <p class="text-xs text-gray-600 mt-2">
                                <span id="charCount-{{ $question->id }}">0</span>/2000 karakter
                            </p>
                        </div>
                    @endif
                </div>
                @endforeach

                <!-- Navigation & Submit -->
                <div class="bg-white rounded-lg shadow-lg p-6 flex gap-4">
                    <button type="button" onclick="previousQuestion()" id="prevBtn" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium hidden">
                        ← Sebelumnya
                    </button>
                    <button type="button" onclick="nextQuestion()" id="nextBtn" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        Berikutnya →
                    </button>
                    <button type="submit" id="submitBtn" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition hidden"
                            onclick="return submitQuiz()">
                        Selesai & Kirim
                    </button>
                </div>
            </form>

            <!-- Loading Modal -->
            <div id="loadingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-8 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-800 font-medium">Mengoreksi jawaban Anda...</p>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-8 max-w-sm mx-auto">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">✓ Konfirmasi Pengumpulan</h3>
                    <p class="text-gray-600 mb-6">Apakah Anda yakin ingin mengumpulkan jawaban quiz ini? Jawaban yang sudah dikumpulkan tidak dapat diubah.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="cancelSubmit()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">
                            Batal
                        </button>
                        <button type="button" onclick="confirmSubmit()" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            Kumpulkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentQuestion = 0;
    const totalQuestions = document.querySelectorAll('.question-container').length;
    const timeLimit = {{ $quiz->duration_minutes }} * 60; // Convert to seconds
    let timeRemaining = timeLimit;

    // Show first question
    showQuestion(0);

    // Timer
    const timerInterval = setInterval(() => {
        timeRemaining--;
        updateTimer();

        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            autoSubmit();
        }
    }, 1000);

    function updateTimer() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        document.getElementById('timer').textContent = 
            minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        
        if (timeRemaining <= 300) { // 5 minutes
            document.getElementById('timer').classList.add('text-red-600');
            document.getElementById('timer').classList.remove('text-blue-600');
        }
    }

    function showQuestion(index) {
        // Hide all questions
        document.querySelectorAll('.question-container').forEach(el => {
            el.classList.add('hidden');
        });

        // Show current question
        document.querySelectorAll('.question-container')[index].classList.remove('hidden');

        // Update progress
        const progress = ((index + 1) / totalQuestions) * 100;
        document.getElementById('progressBar').style.width = progress + '%';
        document.getElementById('progressText').textContent = 
            'Soal ' + (index + 1) + ' dari ' + totalQuestions;

        // Update button states
        document.getElementById('prevBtn').classList.toggle('hidden', index === 0);
        document.getElementById('nextBtn').classList.toggle('hidden', index === totalQuestions - 1);
        document.getElementById('submitBtn').classList.toggle('hidden', index !== totalQuestions - 1);
    }

    function nextQuestion() {
        if (currentQuestion < totalQuestions - 1) {
            currentQuestion++;
            showQuestion(currentQuestion);
            window.scrollTo(0, 0);
        }
    }

    function previousQuestion() {
        if (currentQuestion > 0) {
            currentQuestion--;
            showQuestion(currentQuestion);
            window.scrollTo(0, 0);
        }
    }

    let isSubmitting = false;

    function submitQuiz() {
        // Show confirmation modal instead of browser confirm
        document.getElementById('confirmModal').classList.remove('hidden');
        return false;
    }

    function cancelSubmit() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    function confirmSubmit() {
        document.getElementById('confirmModal').classList.add('hidden');
        
        const form = document.getElementById('quizForm');
        const formData = new FormData(form);
        
        // Show loading modal
        document.getElementById('loadingModal').classList.remove('hidden');
        clearInterval(timerInterval);
        isSubmitting = true; // Disable beforeunload

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingModal').classList.add('hidden');
            
            if (data.success) {
                // Redirect to result page
                window.location.href = '{{ route("student.quiz.result", [$course->id, $quiz->id, $submission->id]) }}';
            } else {
                alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                isSubmitting = false;
            }
        })
        .catch(error => {
            document.getElementById('loadingModal').classList.add('hidden');
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim jawaban');
            isSubmitting = false;
        });
    }

    function autoSubmit() {
        const form = document.getElementById('quizForm');
        const formData = new FormData(form);
        
        document.getElementById('loadingModal').classList.remove('hidden');
        isSubmitting = true; // Disable beforeunload
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("student.quiz.result", [$course->id, $quiz->id, $submission->id]) }}';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Character counter for essay questions
    document.querySelectorAll('textarea').forEach(textarea => {
        textarea.addEventListener('input', (e) => {
            const id = e.target.name.match(/\d+/)[0];
            document.getElementById('charCount-' + id).textContent = e.target.value.length;
        });
    });

    // Prevent accidental navigation away (only if not submitting)
    window.addEventListener('beforeunload', (e) => {
        if (!isSubmitting) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
@endsection
