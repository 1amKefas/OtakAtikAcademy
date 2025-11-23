@extends('layouts.app')

@section('title', 'Assignment Submissions - ' . $assignment->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.courses.show', $course->id) }}" class="hover:opacity-80">
                    Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Lihat Submissions</h1>
            <p class="text-indigo-100">{{ $assignment->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-6 py-8">
        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-sm text-gray-600 mb-1">Total Siswa</p>
                <p class="text-2xl font-bold text-gray-800">{{ $course->registrations()->count() }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-sm text-gray-600 mb-1">Sudah Submit</p>
                <p class="text-2xl font-bold text-green-600">{{ $submissions->count() }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-sm text-gray-600 mb-1">Belum Submit</p>
                <p class="text-2xl font-bold text-red-600">{{ $course->registrations()->count() - $submissions->count() }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-sm text-gray-600 mb-1">Sudah Dinilai</p>
                <p class="text-2xl font-bold text-blue-600">{{ $submissions->whereNotNull('grade')->count() }}</p>
            </div>
        </div>

        <!-- Assignment Info -->
        <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-6 mb-6">
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-indigo-600 mb-1">Deadline</p>
                    <p class="font-semibold text-indigo-900">{{ $assignment->due_date->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-indigo-600 mb-1">Deskripsi</p>
                    <p class="text-indigo-900">{{ Str::limit($assignment->description, 100) }}</p>
                </div>
                <div>
                    <p class="text-sm text-indigo-600 mb-1">Tipe Submission</p>
                    <p class="font-semibold text-indigo-900">Text + File</p>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Siswa</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Submitted</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-800">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-800">Nilai</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-800">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Submitted -->
                        @forelse($submissions as $submission)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $submission->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $submission->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $submission->submitted_at->format('d M H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($submission->grade !== null)
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        Dinilai
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($submission->grade !== null)
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="text-lg font-bold {{ $submission->grade >= 70 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $submission->grade }}
                                        </span>
                                        <span class="text-xs text-gray-600">/100</span>
                                    </div>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('instructor.submissions.detail', [$assignment->id, $submission->id]) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold transition">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-600">
                                Belum ada submission
                            </td>
                        </tr>
                        @endforelse

                        <!-- Not Submitted -->
                        @foreach($course->registrations()->whereNotIn('user_id', $submissions->pluck('user_id'))->get() as $notSubmitted)
                        <tr class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $notSubmitted->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $notSubmitted->user->email }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">-</td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    Belum Submit
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">-</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-gray-500 text-xs">-</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Export/Print -->
        <div class="mt-6 flex gap-4">
            <a href="{{ route('instructor.courses.show', $course->id) }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                Kembali
            </a>
            <button onclick="window.print()" 
                    class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition font-medium">
                Print
            </button>
        </div>
    </div>
</div>
@endsection
