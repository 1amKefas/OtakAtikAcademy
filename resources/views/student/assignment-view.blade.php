@extends('layouts.app')

@section('title', 'Lihat Submission - ' . $assignment->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.course-detail', $submission->assignment->course->registrations()->where('user_id', auth()->id())->first()->id ?? '#') }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $assignment->title }}</h1>
            <p class="text-purple-100">Submission Anda</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="grid grid-cols-3 gap-6">
            <!-- Submission Content -->
            <div class="col-span-2">
                <!-- Submission Info -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Submitted:</p>
                            <p class="font-semibold">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status:</p>
                            @if($submission->grade !== null)
                                <p><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">✓ Sudah Dinilai</span></p>
                            @else
                                <p><span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">⏳ Menunggu Penilaian</span></p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submission Text -->
                @if($submission->submission_text)
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="font-semibold text-lg mb-4">📝 Jawaban Anda</h3>
                    <div class="bg-gray-50 rounded-lg p-4 whitespace-pre-wrap text-gray-700">
                        {{ $submission->submission_text }}
                    </div>
                </div>
                @endif

                <!-- Submission File -->
                @if($submission->file_path)
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="font-semibold text-lg mb-4">📎 File Submission</h3>
                    <div class="flex items-center gap-4 bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl">📄</div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ basename($submission->file_path) }}</p>
                            <p class="text-sm text-gray-600">Uploaded: {{ $submission->submitted_at->format('d M Y H:i') }}</p>
                        </div>
                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">
                            ⬇️ Download
                        </a>
                    </div>
                </div>
                @endif

                <!-- Feedback/Grading -->
                @if($submission->grade !== null)
                <div class="bg-green-50 rounded-lg border border-green-200 p-6">
                    <h3 class="font-semibold text-lg mb-4">✓ Penilaian</h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white rounded-lg p-4 text-center">
                            <p class="text-gray-600 text-sm">Skor Anda</p>
                            <p class="text-4xl font-bold text-green-600">{{ $submission->grade }}</p>
                            <p class="text-sm text-gray-600">dari 100</p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-gray-600 text-sm mb-2">Graded by:</p>
                            <p class="font-semibold">{{ $assignment->course->instructor->name ?? 'Instruktur' }}</p>
                            <p class="text-sm text-gray-600">{{ $submission->graded_at?->format('d M Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($submission->feedback)
                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">📝 Feedback Instruktur:</h4>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $submission->feedback }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-span-1">
                <!-- Assignment Details -->
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200 mb-4">
                    <h4 class="font-semibold text-purple-900 mb-3">📋 Detail Tugas</h4>
                    <div class="text-sm text-purple-800 space-y-2">
                        <div>
                            <p class="text-xs text-purple-600">Deadline:</p>
                            <p class="font-semibold">{{ $assignment->due_date->format('d M Y H:i') }}</p>
                        </div>
                        @if($assignment->due_date > now())
                            <div class="bg-green-100 text-green-700 p-2 rounded text-xs">
                                ✓ Masih dalam batas waktu
                            </div>
                        @else
                            <div class="bg-red-100 text-red-700 p-2 rounded text-xs">
                                ✗ Sudah melewati deadline
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <a href="{{ route('student.assignment.submit.form', $assignment->id) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-center transition block mb-2">
                    📝 Edit Submission
                </a>
                <a href="javascript:history.back()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg text-center transition block">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
