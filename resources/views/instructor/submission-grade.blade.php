@extends('layouts.app')

@section('title', 'Grade Submission - ' . $submission->assignment->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.submissions', $submission->assignment->id) }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">📝 Grade Submission</h1>
            <p class="text-indigo-100">{{ $submission->user->name }} - {{ $submission->assignment->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="grid grid-cols-3 gap-8">
            <!-- Submission Content -->
            <div class="col-span-2">
                <!-- Student Info -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            @if($submission->user->profile_picture)
                                <img src="{{ Storage::url($submission->user->profile_picture) }}" alt="{{ $submission->user->name }}"
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($submission->user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800">{{ $submission->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $submission->user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Submitted</p>
                            <p class="font-semibold">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Submission Text -->
                @if($submission->submission_text)
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">📝 Jawaban Teks</h3>
                    <div class="bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap">
                        {{ $submission->submission_text }}
                    </div>
                </div>
                @endif

                <!-- Submission File -->
                @if($submission->file_path)
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">📎 File Submission</h3>
                    <div class="flex items-center gap-4 bg-blue-50 p-4 rounded-lg">
                        <div class="text-3xl">📄</div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ basename($submission->file_path) }}</p>
                            <p class="text-sm text-gray-600">Uploaded: {{ $submission->submitted_at->format('d M Y H:i') }}</p>
                        </div>
                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">
                            ⬇️ Download
                        </a>
                    </div>
                </div>
                @endif

                <!-- Grading Form -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="font-semibold text-lg text-gray-800 mb-6">✍️ Nilai & Feedback</h3>

                    <form action="{{ route('instructor.submissions.grade', $submission->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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

                        <!-- Score -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nilai (0-100)</label>
                            <input type="number" name="score" required min="0" max="100"
                                   value="{{ old('score', $submission->grade) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg"
                                   placeholder="Masukkan nilai...">
                            <p class="text-sm text-gray-600 mt-2">
                                @if(old('score', $submission->grade) >= 70)
                                    ✓ <span class="text-green-600 font-semibold">LULUS</span>
                                @elseif(old('score', $submission->grade) >= 0)
                                    ✗ <span class="text-red-600 font-semibold">TIDAK LULUS</span>
                                @endif
                            </p>
                        </div>

                        <!-- Feedback -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Feedback & Catatan</label>
                            <textarea name="feedback" rows="5"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                      placeholder="Berikan feedback lengkap untuk siswa...">{{ old('feedback', $submission->feedback) }}</textarea>
                            <p class="text-xs text-gray-600 mt-1">💡 Berikan feedback yang konstruktif dan membangun</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-4 pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                                ✓ Simpan Nilai & Feedback
                            </button>
                            <a href="{{ route('instructor.submissions', $submission->assignment->id) }}" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                ✕ Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-1">
                <div class="sticky top-6">
                    <!-- Assignment Info -->
                    <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-4 mb-4">
                        <h4 class="font-semibold text-indigo-900 mb-3">📋 Informasi Tugas</h4>
                        <div class="text-sm text-indigo-800 space-y-3">
                            <div>
                                <p class="text-xs text-indigo-600">Deadline</p>
                                <p class="font-semibold">{{ $submission->assignment->due_date->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-indigo-600">Submitted</p>
                                <p class="font-semibold">{{ $submission->submitted_at->format('d M Y H:i') }}</p>
                            </div>
                            @if($submission->submitted_at > $submission->assignment->due_date)
                                <div class="bg-red-100 rounded p-2">
                                    <p class="text-red-800 text-xs font-semibold">⚠️ TERLAMBAT</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">📊 Status Saat Ini</h4>
                        @if($submission->grade !== null)
                            <div class="text-center mb-4">
                                <p class="text-3xl font-bold {{ $submission->grade >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $submission->grade }}
                                </p>
                                <p class="text-xs text-gray-600">Nilai saat ini</p>
                            </div>
                            <div class="text-center">
                                @if($submission->grade >= 70)
                                    <p class="text-green-700 font-semibold">✓ LULUS</p>
                                @else
                                    <p class="text-red-700 font-semibold">✗ TIDAK LULUS</p>
                                @endif
                            </div>
                        @else
                            <p class="text-center text-gray-600 text-sm">Belum dinilai</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
