@extends('layouts.app')

@section('title', 'Submit ' . $assignment->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.course-detail', $registration->id) }}" class="hover:opacity-80">
                    ← Kembali ke Kursus
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $assignment->title }}</h1>
            <p class="text-purple-100">{{ $course->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="grid grid-cols-3 gap-6">
            <!-- Assignment Details -->
            <div class="col-span-2">
                <!-- Assignment Info -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">📋 Detail Tugas</h3>
                    
                    @if($assignment->description)
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Deskripsi:</h4>
                        <p class="text-gray-600">{{ $assignment->description }}</p>
                    </div>
                    @endif

                    @if($assignment->instructions)
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Instruksi:</h4>
                        <div class="prose prose-sm text-gray-600">
                            {!! nl2br(e($assignment->instructions)) !!}
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Deadline:</p>
                            <p class="font-semibold text-lg">
                                @if($assignment->due_date > now())
                                    <span class="text-green-600">{{ $assignment->due_date->format('d M Y H:i') }}</span>
                                @else
                                    <span class="text-red-600">Sudah lewat</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status:</p>
                            @if($existingSubmission)
                                <p class="font-semibold text-lg">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                        ✓ Sudah Submit
                                    </span>
                                </p>
                                <p class="text-xs text-gray-600 mt-2">
                                    Tanggal: {{ $existingSubmission->submitted_at->format('d M Y H:i') }}
                                </p>
                            @else
                                <p class="font-semibold text-lg">
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                                        ⏱ Belum Submit
                                    </span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submission Form -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">📤 Submit Jawaban Anda</h3>

                    <form action="{{ route('student.assignment.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if($existingSubmission)
                        <input type="hidden" name="action" value="resubmit">
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-blue-800">
                                <strong>ℹ️ Catatan:</strong> Anda sudah pernah submit tugas ini. Jika Anda submit lagi, akan mengganti submission sebelumnya.
                            </p>
                        </div>
                        @endif

                        <!-- Text Submission -->
                        <div class="mb-6">
                            <label for="submission_text" class="block text-sm font-semibold text-gray-700 mb-2">
                                Jawaban (Text)
                                <span class="text-gray-500 font-normal text-xs">(opsional)</span>
                            </label>
                            <textarea 
                                id="submission_text" 
                                name="submission_text" 
                                rows="8"
                                placeholder="Tulis jawaban Anda di sini..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none @error('submission_text') border-red-500 @enderror"
                            >{{ old('submission_text', $existingSubmission->submission_text ?? '') }}</textarea>
                            @error('submission_text')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-600 mt-2">Maksimal 5000 karakter</p>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-6">
                            <label for="submission_file" class="block text-sm font-semibold text-gray-700 mb-2">
                                Upload File
                                <span class="text-gray-500 font-normal text-xs">(opsional)</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-purple-400 transition cursor-pointer" 
                                 onclick="document.getElementById('submission_file').click()">
                                <div class="text-gray-600">
                                    <p class="text-2xl mb-2">📎</p>
                                    <p class="font-semibold">Klik untuk upload atau drag & drop</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Format: PDF, DOC, DOCX, TXT, XLS, XLSX, PPT, PPTX, JPG, PNG
                                    </p>
                                    <p class="text-sm text-gray-500">Maksimal 10MB</p>
                                </div>
                                <input 
                                    type="file" 
                                    id="submission_file" 
                                    name="submission_file"
                                    class="hidden"
                                    accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                                    onchange="updateFileName(this)"
                                >
                            </div>
                            <p id="file-name" class="text-sm text-gray-600 mt-2"></p>
                            @error('submission_file')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @error('submission')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                                <p class="text-red-800">{{ $message }}</p>
                            </div>
                        @enderror

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                ✓ Submit Tugas
                            </button>
                            <a href="{{ route('student.course-detail', $registration->id) }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg text-center transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-1">
                <!-- Quick Info -->
                <div class="bg-purple-50 rounded-lg p-4 mb-4 border border-purple-200">
                    <h4 class="font-semibold text-purple-900 mb-3">💡 Tips</h4>
                    <ul class="text-sm text-purple-800 space-y-2">
                        <li>✓ Anda bisa submit text, file, atau keduanya</li>
                        <li>✓ File harus kurang dari 10MB</li>
                        <li>✓ Pastikan submit sebelum deadline</li>
                        <li>✓ Anda bisa submit ulang sebelum deadline</li>
                    </ul>
                </div>

                @if($existingSubmission && $existingSubmission->score !== null)
                <!-- Grading Info -->
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <h4 class="font-semibold text-green-900 mb-3">✓ Sudah Dinilai</h4>
                    <div class="text-center mb-3">
                        <p class="text-3xl font-bold text-green-600">{{ $existingSubmission->score }}</p>
                        <p class="text-sm text-green-700">dari 100</p>
                    </div>
                    @if($existingSubmission->feedback)
                    <div>
                        <p class="text-sm font-semibold text-green-900 mb-2">Feedback:</p>
                        <p class="text-sm text-green-800">{{ $existingSubmission->feedback }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = document.getElementById('file-name');
    if (input.files && input.files[0]) {
        fileName.textContent = '✓ File terpilih: ' + input.files[0].name;
    }
}
</script>
@endsection
