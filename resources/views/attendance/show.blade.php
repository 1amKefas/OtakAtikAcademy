@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header -->
        <div class="mb-6 border-b pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Mark Attendance</h1>
                    <p class="text-gray-600 mt-2">{{ $classSession->course->title }}</p>
                </div>
                <a href="/instructor/dashboard" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Session Details -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 bg-blue-50 p-4 rounded-lg">
            <div>
                <p class="text-sm text-gray-600">Session</p>
                <p class="text-lg font-semibold text-gray-800">{{ $classSession->title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Date & Time</p>
                <p class="text-lg font-semibold text-gray-800">
                    {{ $classSession->session_date->format('d M Y') }}
                    {{ $classSession->start_time ? '• ' . date('H:i', strtotime($classSession->start_time)) : '' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Type</p>
                <p class="text-lg font-semibold text-gray-800">
                    <span class="inline-block px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-sm">
                        {{ ucfirst($classSession->session_type) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Attendance Form -->
        <form action="/instructor/courses/{{ $classSession->course_id }}/class-sessions/{{ $classSession->id }}/attendance/mark" method="POST" class="space-y-4">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-gray-300">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Student Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Email</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $student->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->name) }}" 
                                             alt="{{ $student->name }}" class="w-8 h-8 rounded-full">
                                        <span class="font-medium text-gray-800">{{ $student->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $student->email }}</td>
                                <td class="px-4 py-3 text-center">
                                    <select name="attendance[{{ $student->id }}][status]" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="present" {{ optional($student->attendances()->where('class_session_id', $classSession->id)->first())->status === 'present' ? 'selected' : '' }}>
                                            <i class="fas fa-check"></i> Present
                                        </option>
                                        <option value="absent" {{ optional($student->attendances()->where('class_session_id', $classSession->id)->first())->status === 'absent' ? 'selected' : '' }}>
                                            <i class="fas fa-times"></i> Absent
                                        </option>
                                        <option value="late" {{ optional($student->attendances()->where('class_session_id', $classSession->id)->first())->status === 'late' ? 'selected' : '' }}>
                                            <i class="fas fa-clock"></i> Late
                                        </option>
                                        <option value="excused" {{ optional($student->attendances()->where('class_session_id', $classSession->id)->first())->status === 'excused' ? 'selected' : '' }}>
                                            <i class="fas fa-calendar-check"></i> Excused
                                        </option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="attendance[{{ $student->id }}][notes]" 
                                           placeholder="Optional notes" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                           value="{{ optional($student->attendances()->where('class_session_id', $classSession->id)->first())->notes ?? '' }}">
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b border-gray-200">
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    <i class="fas fa-users-slash text-2xl mb-2 block opacity-50"></i>
                                    No students enrolled in this course yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end mt-6 pt-4 border-t">
                <button type="reset" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save"></i> Save Attendance
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    body {
        background-color: #f9fafb;
    }
</style>
@endsection
