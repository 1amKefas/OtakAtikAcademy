@extends('layouts.app')

@section('title', isset($review) ? 'Edit Review' : 'Beri Rating & Review')

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.review.index', $course->id) }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ isset($review) ? 'Edit Review' : 'Beri Rating & Review' }}</h1>
            <p class="text-amber-100">{{ $course->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="bg-white rounded-lg border border-gray-200 p-8">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-red-800 font-semibold mb-2">Terjadi Kesalahan:</p>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($review) ? route('student.review.update', $review->id) : route('student.review.store') }}" 
                  method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                @if(isset($review))
                    @method('PUT')
                @endif

                <div class="space-y-8">
                    <!-- Rating Selection -->
                    <div>
                        <label class="block text-lg font-semibold text-gray-800 mb-4">Berapa Rating Anda untuk Course Ini?</label>
                        
                        <div id="ratingContainer" class="flex gap-4">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer group">
                                <input type="radio" name="rating" value="{{ $i }}" 
                                       {{ old('rating', $review->rating ?? 0) == $i ? 'checked' : '' }}
                                       class="sr-only"
                                       onchange="updateRatingDisplay()">
                                <span class="text-5xl transition group-hover:scale-110" id="star-{{ $i }}">
                                    {{ old('rating', $review->rating ?? 0) >= $i ? '[*]' : '[ ]' }}
                                </span>
                            </label>
                            @endfor
                        </div>

                        <div id="ratingLabel" class="mt-3 text-sm font-semibold text-gray-700">
                            @php
                                $ratings = [
                                    0 => '-- Pilih Rating --',
                                    1 => 'Sangat Buruk - Tidak Merekomendasikan',
                                    2 => 'Buruk - Ada Banyak Masalah',
                                    3 => 'Sedang - Cukup Baik',
                                    4 => 'Bagus - Sangat Merekomendasikan',
                                    5 => 'Sempurna - Tidak Ada Bandingannya!'
                                ];
                            @endphp
                            <span id="ratingText">{{ $ratings[old('rating', $review->rating ?? 0)] }}</span>
                        </div>
                    </div>

                    <!-- Review Text -->
                    <div>
                        <label class="block text-lg font-semibold text-gray-800 mb-3">Detail Review Anda</label>
                        <p class="text-sm text-gray-600 mb-3">Bagikan pengalaman dan pendapat Anda tentang course ini</p>
                        
                        <textarea name="review_text" rows="6"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                  placeholder="Apa yang Anda sukai tentang course ini?&#10;Apa yang bisa ditingkatkan?&#10;Apakah materi sudah jelas dan mudah dipahami?&#10;Bagaimana kualitas instruktur?">{{ old('review_text', $review->review_text ?? '') }}</textarea>
                        <p class="text-xs text-gray-600 mt-2">
                            <span id="charCount">0</span>/500 karakter
                        </p>
                    </div>

                    <!-- Guidelines -->
                    <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Tips Menulis Review yang Baik</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>Jujur dan spesifik tentang pengalaman Anda</li>
                            <li>Sebutkan aspek positif dan area untuk perbaikan</li>
                            <li>Hindari komentar yang menyerang atau tidak pantas</li>
                            <li>Tulislah dengan jelas dan mudah dipahami</li>
                        </ul>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-6 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                            {{ isset($review) ? 'Update Review' : 'Kirim Review' }}
                        </button>
                        <a href="{{ route('student.review.index', $course->id) }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const ratings = {
        0: '-- Pilih Rating --',
        1: '😞 Sangat Buruk - Tidak Merekomendasikan',
        2: '😐 Buruk - Ada Banyak Masalah',
        3: '😊 Sedang - Cukup Baik',
        4: '😄 Bagus - Sangat Merekomendasikan',
        5: '😍 Sempurna - Tidak Ada Bandingannya!'
    };

    function updateRatingDisplay() {
        const rating = document.querySelector('input[name="rating"]:checked')?.value || 0;
        document.getElementById('ratingText').textContent = ratings[rating];

        // Update star display
        for (let i = 1; i <= 5; i++) {
            const star = document.getElementById('star-' + i);
            if (i <= rating) {
                star.textContent = '⭐';
            } else {
                star.textContent = '☆';
            }
        }
    }

    // Character counter
    const textarea = document.querySelector('textarea[name="review_text"]');
    if (textarea) {
        textarea.addEventListener('input', (e) => {
            document.getElementById('charCount').textContent = e.target.value.length;
        });
        
        // Initialize count
        document.getElementById('charCount').textContent = textarea.value.length;
    }

    // Initialize on page load
    updateRatingDisplay();
</script>
@endsection
