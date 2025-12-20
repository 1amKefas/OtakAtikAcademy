@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Header -->
        <div class="mb-6 border-b pb-4">
            <h1 class="text-3xl font-bold text-gray-800">Create Class Session</h1>
            <p class="text-gray-600 mt-2">{{ $course->title }}</p>
        </div>

        <form action="/instructor/courses/{{ $course->id }}/class-sessions" method="POST" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-heading"></i> Session Title *
                </label>
                <input type="text" name="title" id="title" placeholder="e.g., Introduction to Laravel"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                       value="{{ old('title') }}" required>
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea name="description" id="description" rows="3" placeholder="Describe what will be covered in this session"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Session Date -->
            <div>
                <label for="session_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar"></i> Session Date *
                </label>
                <input type="date" name="session_date" id="session_date"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('session_date') border-red-500 @enderror"
                       value="{{ old('session_date') }}" required>
                @error('session_date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock"></i> Start Time
                    </label>
                    <input type="time" name="start_time" id="start_time"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('start_time') }}">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-clock"></i> End Time
                    </label>
                    <input type="time" name="end_time" id="end_time"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('end_time') }}">
                </div>
            </div>

            <!-- Session Type -->
            <div>
                <label for="session_type" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-cubes"></i> Session Type *
                </label>
                <select name="session_type" id="session_type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('session_type') border-red-500 @enderror"
                        onchange="updateMeetingTypeOptions()" required>
                    <option value="">-- Select Type --</option>
                    <option value="online" {{ old('session_type') === 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ old('session_type') === 'offline' ? 'selected' : '' }}>Offline (Tatap Muka)</option>
                    <option value="hybrid" {{ old('session_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                </select>
                @error('session_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Meeting Type -->
            <div>
                <label for="meeting_type" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-video"></i> Meeting Type *
                </label>
                <select name="meeting_type" id="meeting_type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('meeting_type') border-red-500 @enderror"
                        onchange="updateZoomLinkVisibility()" required>
                    <option value="">-- Select Meeting Type --</option>
                    <option value="zoom" {{ old('meeting_type') === 'zoom' ? 'selected' : '' }}>Zoom</option>
                    <option value="tatap_muka" {{ old('meeting_type') === 'tatap_muka' ? 'selected' : '' }}>Tatap Muka</option>
                </select>
                @error('meeting_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Zoom Link (Conditional) -->
            <div id="zoomLinkDiv" class="hidden">
                <label for="zoom_link" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-link"></i> Zoom Meeting Link *
                </label>
                <input type="url" name="zoom_link" id="zoom_link" placeholder="https://zoom.us/j/..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ old('zoom_link') }}">
                @error('zoom_link')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location (For Offline) -->
            <div id="locationDiv" class="hidden">
                <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt"></i> Location *
                </label>
                <input type="text" name="location" id="location" placeholder="e.g., Building A, Hall 101"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ old('location') }}">
                @error('location')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Room Number (For Offline) -->
            <div id="roomNumberDiv" class="hidden">
                <label for="room_number" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-door-open"></i> Room Number
                </label>
                <input type="text" name="room_number" id="room_number" placeholder="e.g., 101"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ old('room_number') }}">
            </div>

            <!-- Offline Notes -->
            <div id="offlineNotesDiv" class="hidden">
                <label for="offline_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-sticky-note"></i> Offline Notes
                </label>
                <textarea name="offline_notes" id="offline_notes" rows="2" placeholder="Additional notes for offline session"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('offline_notes') }}</textarea>
            </div>

            <!-- Agenda -->
            <div>
                <label for="agenda" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-list"></i> Agenda
                </label>
                <textarea name="agenda" id="agenda" rows="3" placeholder="What will be discussed in this session?"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('agenda') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 justify-end pt-6 border-t">
                <a href="/instructor/courses/{{ $course->id }}/class-sessions" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save"></i> Create Session
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateZoomLinkVisibility() {
    const meetingType = document.getElementById('meeting_type').value;
    const zoomDiv = document.getElementById('zoomLinkDiv');
    const zoomInput = document.getElementById('zoom_link');
    
    if (meetingType === 'zoom') {
        zoomDiv.classList.remove('hidden');
        zoomInput.setAttribute('required', 'required');
    } else {
        zoomDiv.classList.add('hidden');
        zoomInput.removeAttribute('required');
        zoomInput.value = '';
    }
    updateOfflineFields();
}

function updateOfflineFields() {
    const meetingType = document.getElementById('meeting_type').value;
    const sessionType = document.getElementById('session_type').value;
    const locationDiv = document.getElementById('locationDiv');
    const roomNumberDiv = document.getElementById('roomNumberDiv');
    const offlineNotesDiv = document.getElementById('offlineNotesDiv');
    const locationInput = document.getElementById('location');
    
    if ((meetingType === 'tatap_muka' || sessionType === 'offline') && meetingType !== 'zoom') {
        locationDiv.classList.remove('hidden');
        roomNumberDiv.classList.remove('hidden');
        offlineNotesDiv.classList.remove('hidden');
        locationInput.setAttribute('required', 'required');
    } else {
        locationDiv.classList.add('hidden');
        roomNumberDiv.classList.add('hidden');
        offlineNotesDiv.classList.add('hidden');
        locationInput.removeAttribute('required');
        locationInput.value = '';
    }
}

function updateMeetingTypeOptions() {
    updateOfflineFields();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateZoomLinkVisibility();
    updateOfflineFields();
});
</script>

<style>
    body {
        background-color: #f9fafb;
    }
</style>
@endsection
