@extends('layouts.app')

@section('title', 'Submission Detail - ' . $assignment->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.assignments.submissions', $assignment->id) }}" class="hover:opacity-80 text-sm">
                    ← Kembali ke Submissions
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Detail Submission</h1>
            <p class="text-indigo-100">{{ $submission->user->name ?? 'Unknown' }} - {{ $assignment->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start">
                <i class="fas fa-check-circle text-green-600 mr-3 mt-0.5"></i>
                <div>
                    <p class="text-green-800 font-semibold">Sukses!</p>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Student Info -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Siswa</h3>
                    <div class="flex items-center gap-4">
                        @if($submission->user && $submission->user->profile_picture && Storage::disk('public')->exists($submission->user->profile_picture))
                            <img src="{{ Storage::disk('public')->url($submission->user->profile_picture) }}" alt="{{ $submission->user->name }}" class="w-16 h-16 rounded-full">
                        @else
                            <div class="w-16 h-16 rounded-full bg-indigo-500 text-white flex items-center justify-center text-2xl font-bold">
                                {{ substr($submission->user->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-lg font-semibold text-gray-800">{{ $submission->user->name ?? 'Unknown' }}</p>
                            <p class="text-gray-600">{{ $submission->user->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Submission Details -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Detail Pengumpulan</h3>
                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Status</p>
                            <p class="mt-1">
                                @if($submission->status === 'submitted')
                                    <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        <i class="fas fa-paper-plane mr-1"></i> Submitted
                                    </span>
                                @elseif($submission->status === 'graded')
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i> Dinilai
                                    </span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                    </span>
                                @endif
                            </p>
                        </div>

                        <!-- Submitted Date -->
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Tanggal Submit</p>
                            <p class="mt-1 text-gray-800">
                                @if($submission->submitted_at)
                                    {{ $submission->submitted_at->format('d M Y H:i') }}
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </p>
                        </div>

                        <!-- Submission File/Link -->
                        <div>
                            <p class="text-sm text-gray-600 font-medium">File/Link Pengumpulan</p>
                            <div class="mt-2">
                                @if($submission->file_path)
                                    <a href="{{ Storage::disk('public')->url($submission->file_path) }}" 
                                       target="_blank"
                                       class="inline-block text-indigo-600 hover:text-indigo-700 font-medium">
                                        <i class="fas fa-download mr-1"></i> Download File
                                    </a>
                                @elseif($submission->submission_link)
                                    <a href="{{ $submission->submission_link }}" 
                                       target="_blank"
                                       class="inline-block text-indigo-600 hover:text-indigo-700 font-medium">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Link
                                    </a>
                                @else
                                    <p class="text-gray-500">-</p>
                                @endif
                            </div>
                        </div>

                        <!-- Comments/Notes -->
                        @if($submission->notes)
                            <div>
                                <p class="text-sm text-gray-600 font-medium">Catatan Siswa</p>
                                <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-4 text-gray-800">
                                    {{ $submission->notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Grading Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 sticky top-20">
                    <h3 class="text-xl font-bold text-white mb-6">✍️ Beri Nilai</h3>

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <p class="text-red-800 font-semibold text-sm mb-2">Ada Kesalahan:</p>
                            <ul class="text-xs text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Current Score Display (Always Show) -->
                    @if($submission->grade !== null)
                        <div class="bg-white bg-opacity-10 rounded-lg p-4 backdrop-blur mb-6 border border-white border-opacity-20">
                            <p class="text-xs text-indigo-100 font-semibold uppercase tracking-wide">Nilai Saat Ini</p>
                            <p class="text-5xl font-bold text-white mt-2">{{ $submission->grade }}</p>
                            <p class="text-sm text-indigo-100 mt-1">dari {{ $assignment->max_points ?? 100 }}</p>
                            @if($submission->graded_at)
                                <p class="text-xs text-indigo-200 mt-3">
                                    📅 {{ $submission->graded_at->format('d M Y H:i') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('instructor.submissions.grade', $submission->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Score Input -->
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-white mb-2">
                                Ubah Nilai (0 - {{ $assignment->max_points ?? 100 }})
                            </label>
                            <input type="number" 
                                   name="score" 
                                   min="0" 
                                   max="{{ $assignment->max_points ?? 100 }}"
                                   value="{{ old('score', '') }}"
                                   class="w-full px-4 py-3 border-2 border-white bg-white text-gray-800 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-300 font-bold text-lg"
                                   placeholder="Masukkan nilai baru...">
                        </div>

                        <!-- Feedback Textarea -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-white mb-2">
                                💭 Feedback
                            </label>
                            <textarea name="feedback"
                                      rows="4"
                                      class="w-full px-4 py-3 border-2 border-white bg-white text-gray-800 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-300"
                                      placeholder="Berikan feedback untuk siswa...">{{ old('feedback', $submission->feedback ?? '') }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-yellow-400 hover:bg-yellow-500 text-indigo-700 font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 shadow-md">
                            ✓ Simpan Nilai
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
