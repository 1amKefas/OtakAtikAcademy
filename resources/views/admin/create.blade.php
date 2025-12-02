@extends('layouts.app')

@section('title', 'Create New Course - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.courses.manage') }}" class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Courses
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">Create New Course</h1>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong class="font-bold">Oops! Ada kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="{{ route('admin.courses.create') }}" method="POST" enctype="multipart/form-data" id="createCourseForm">
                @csrf

                <!-- Course Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Course Title <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title" 
                        value="{{ old('title') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Web Development Bootcamp"
                        required
                    >
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Describe the course content, objectives, and outcomes..."
                        required
                    >{{ old('description') }}</textarea>
                </div>

                <!-- Course Type -->
                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Course Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="type" 
                        id="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                        <option value="">Select Type</option>
                        <option value="Full Online" {{ old('type') == 'Full Online' ? 'selected' : '' }}>Full Online</option>
                        <option value="Hybrid" {{ old('type') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                        <option value="Tatap Muka" {{ old('type') == 'Tatap Muka' ? 'selected' : '' }}>Tatap Muka</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Full Online tidak memerlukan instructor</p>
                </div>

                <!-- Instructor -->
                <div class="mb-6" id="instructor-field">
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Instructor <span class="text-red-500" id="instructor-required">*</span>
                    </label>
                    <select 
                        name="instructor_id" 
                        id="instructor_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Select Instructor</option>
                        @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                            {{ $instructor->name }} ({{ $instructor->email }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1" id="instructor-note">Required for Hybrid and Tatap Muka courses</p>
                </div>

                <!-- Price & Discount -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="price" 
                            id="price" 
                            value="{{ old('price', 0) }}"
                            min="0"
                            step="1000"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                    </div>
                    <div>
                        <label for="discount_percent" class="block text-sm font-medium text-gray-700 mb-2">
                            Discount (%)
                        </label>
                        <input 
                            type="number" 
                            name="discount_percent" 
                            id="discount_percent" 
                            value="{{ old('discount_percent', 0) }}"
                            min="0"
                            max="100"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                </div>

                <!-- Discount Code -->
                <div class="mb-6">
                    <label for="discount_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Discount Code (Optional)
                    </label>
                    <input 
                        type="text" 
                        name="discount_code" 
                        id="discount_code" 
                        value="{{ old('discount_code') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., PROMO2024"
                    >
                </div>

                <!-- Quota -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="min_quota" class="block text-sm font-medium text-gray-700 mb-2">
                            Min Quota <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="min_quota" 
                            id="min_quota" 
                            value="{{ old('min_quota', 5) }}"
                            min="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">Minimum students to start the course</p>
                    </div>
                    <div>
                        <label for="max_quota" class="block text-sm font-medium text-gray-700 mb-2">
                            Max Quota <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="max_quota" 
                            id="max_quota" 
                            value="{{ old('max_quota', 50) }}"
                            min="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">Maximum students allowed</p>
                    </div>
                </div>

                <!-- Duration -->
                <div class="mb-6">
                    <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">
                        Duration (Days) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="duration_days" 
                        id="duration_days" 
                        value="{{ old('duration_days', 30) }}"
                        min="1"
                        max="365"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <!-- Start & End Date -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date (Optional)
                        </label>
                        <input 
                            type="date" 
                            name="start_date" 
                            id="start_date" 
                            value="{{ old('start_date') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date (Optional)
                        </label>
                        <input 
                            type="date" 
                            name="end_date" 
                            id="end_date" 
                            value="{{ old('end_date') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Course Image (Optional)
                    </label>
                    <input 
                        type="file" 
                        name="image" 
                        id="image" 
                        accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <p class="text-xs text-gray-500 mt-1">Max 2MB (jpeg, png, jpg, gif)</p>
                </div>

                <!-- Is Active -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">
                            <strong>Active</strong> (Course will be visible to students immediately)
                        </span>
                    </label>
                </div>

                <!-- Categories -->
                <div class="mb-6">
                    <label for="categories" class="block text-sm font-medium text-gray-700 mb-2">
                        Categories <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="categories[]" 
                        id="categories"
                        multiple
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                        @php
                            $categories = \App\Models\Category::orderBy('sort_order')->get();
                            $oldCategories = old('categories', []);
                        @endphp
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, $oldCategories) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select one or more categories (Hold Ctrl/Cmd to select multiple)</p>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-4 pt-6 border-t">
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition flex items-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Course
                    </button>
                    <a 
                        href="{{ route('admin.courses.manage') }}"
                        class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Dynamic instructor field based on course type
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const instructorField = document.getElementById('instructor_id');
    const instructorRequired = document.getElementById('instructor-required');
    const instructorNote = document.getElementById('instructor-note');
    
    if (type === 'Full Online') {
        instructorField.removeAttribute('required');
        instructorRequired.style.display = 'none';
        instructorNote.textContent = 'Not required for Full Online courses';
        instructorNote.classList.add('text-blue-500');
        instructorField.value = '';
    } else {
        instructorField.setAttribute('required', 'required');
        instructorRequired.style.display = 'inline';
        instructorNote.textContent = 'Required for Hybrid and Tatap Muka courses';
        instructorNote.classList.remove('text-blue-500');
    }
});

// Quota validation
document.getElementById('createCourseForm').addEventListener('submit', function(e) {
    const minQuota = parseInt(document.getElementById('min_quota').value) || 0;
    const maxQuota = parseInt(document.getElementById('max_quota').value) || 0;
    
    if (maxQuota <= minQuota) {
        e.preventDefault();
        alert('Max quota must be greater than min quota!');
        return false;
    }
    
    // Validate instructor for Hybrid/Tatap Muka
    const type = document.getElementById('type').value;
    const instructorId = document.getElementById('instructor_id').value;
    
    if ((type === 'Hybrid' || type === 'Tatap Muka') && !instructorId) {
        e.preventDefault();
        alert('Please select an instructor for ' + type + ' course!');
        return false;
    }
});

// Set end date min value based on start date
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateField = document.getElementById('end_date');
    if (startDate) {
        endDateField.setAttribute('min', startDate);
    }
});

// Trigger initial state
document.getElementById('type').dispatchEvent(new Event('change'));
</script>
@endsection