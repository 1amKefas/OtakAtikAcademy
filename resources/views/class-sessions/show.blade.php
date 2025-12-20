@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header -->
        <div class="mb-6 border-b pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $classSession->title }}</h1>
                    <p class="text-gray-600 mt-2">{{ $course->title }}</p>
                </div>
                <a href="/instructor/courses/{{ $course->id }}/class-sessions" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-arrow-left"></i> Back to Sessions
                </a>
            </div>
        </div>

        <!-- Session Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <p class="text-sm text-gray-600 font-medium">Date & Time</p>
                <p class="text-lg font-semibold text-gray-800 mt-2">
                    {{ $classSession->session_date->format('d M Y') }}
                    @if($classSession->start_time)
                        <br><span class="text-sm text-gray-600">{{ date('H:i', strtotime($classSession->start_time)) }} - {{ date('H:i', strtotime($classSession->end_time)) }}</span>
                    @endif
                </p>
            </div>

            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <p class="text-sm text-gray-600 font-medium">Session Type</p>
                <p class="mt-2">
                    <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                        {{ $classSession->session_type === 'online' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $classSession->session_type === 'offline' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $classSession->session_type === 'hybrid' ? 'bg-green-100 text-green-800' : '' }}
                    ">
                        <i class="fas fa-cubes"></i> {{ ucfirst($classSession->session_type) }}
                    </span>
                </p>
            </div>

            @if($classSession->meeting_type === 'zoom' && $classSession->zoom_link)
                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                    <p class="text-sm text-gray-600 font-medium">Zoom Meeting</p>
                    <p class="mt-2">
                        <a href="{{ $classSession->zoom_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-2">
                            <i class="fas fa-video"></i> Join Zoom Meeting
                        </a>
                    </p>
                </div>
            @endif

            @if($classSession->session_type === 'offline' && $classSession->location)
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <p class="text-sm text-gray-600 font-medium">Location</p>
                    <p class="text-lg font-semibold text-gray-800 mt-2">{{ $classSession->location }}</p>
                    @if($classSession->room_number)
                        <p class="text-sm text-gray-600 mt-1">Room {{ $classSession->room_number }}</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Description -->
        @if($classSession->description)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-600 font-medium mb-2">Description</p>
                <p class="text-gray-800">{{ $classSession->description }}</p>
            </div>
        @endif

        <!-- Agenda -->
        @if($classSession->agenda)
            <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                <p class="text-sm text-gray-600 font-medium mb-2">Agenda</p>
                <p class="text-gray-800 whitespace-pre-wrap">{{ $classSession->agenda }}</p>
            </div>
        @endif

        <!-- Offline Notes -->
        @if($classSession->session_type === 'offline' && $classSession->offline_notes)
            <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-sm text-gray-600 font-medium mb-2">Offline Session Notes</p>
                <p class="text-gray-800 whitespace-pre-wrap">{{ $classSession->offline_notes }}</p>
            </div>
        @endif

        <!-- Attendance Section -->
        <div class="mb-6 border-t pt-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Attendance</h2>
                <a href="/instructor/courses/{{ $course->id }}/class-sessions/{{ $classSession->id }}/attendance" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-check-square"></i> Mark Attendance
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-gray-300">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Student</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $attendance->user->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($attendance->user->name) }}" 
                                             alt="{{ $attendance->user->name }}" class="w-8 h-8 rounded-full">
                                        <span class="font-medium text-gray-800">{{ $attendance->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($attendance->status === 'present')
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check"></i> Present
                                        </span>
                                    @elseif($attendance->status === 'absent')
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                            <i class="fas fa-times"></i> Absent
                                        </span>
                                    @elseif($attendance->status === 'late')
                                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                            <i class="fas fa-clock"></i> Late
                                        </span>
                                    @elseif($attendance->status === 'excused')
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                            <i class="fas fa-calendar-check"></i> Excused
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr class="border-b border-gray-200">
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                                    <i class="fas fa-inbox text-2xl mb-2 block opacity-50"></i>
                                    No attendance marked yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 justify-end pt-6 border-t">
            <a href="/instructor/courses/{{ $course->id }}/class-sessions/{{ $classSession->id }}/edit" class="px-4 py-2 border border-blue-300 rounded-lg text-blue-600 hover:bg-blue-50 font-medium transition-colors">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="/instructor/courses/{{ $course->id }}/class-sessions/{{ $classSession->id }}" method="POST" class="inline" onclick="return confirm('Delete this session?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 border border-red-300 rounded-lg text-red-600 hover:bg-red-50 font-medium transition-colors">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f9fafb;
    }
</style>
@endsection
