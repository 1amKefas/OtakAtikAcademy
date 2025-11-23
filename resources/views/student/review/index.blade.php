@extends('layouts.app')

@section('title', 'Rating & Review - ' . $course->title)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('student.course-detail', $registration->id) }}" class="hover:opacity-80">
                    ← Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">Rating & Review</h1>
            <p class="text-amber-100">{{ $course->title }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <div class="grid grid-cols-3 gap-8">
            <!-- Reviews List -->
            <div class="col-span-2">
                <!-- Statistics -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Statistik Rating</h3>
                    
                    <div class="flex items-end gap-8 mb-8">
                        <!-- Average Rating -->
                        <div class="text-center">
                            <p class="text-5xl font-bold text-amber-600">{{ number_format($course->reviews()->avg('rating') ?? 0, 1) }}</p>
                            <div class="flex gap-1 mt-2 justify-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= ($course->reviews()->avg('rating') ?? 0))
                                        <span class="text-2xl">[*]</span>
                                    @else
                                        <span class="text-2xl text-gray-300">[ ]</span>
                                    @endif
                                @endfor
                            </div>
                            <p class="text-sm text-gray-600 mt-2">{{ $course->reviews()->count() }} Ulasan</p>
                        </div>

                        <!-- Rating Distribution -->
                        <div class="flex-1">
                            @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $course->reviews()->where('rating', $star)->count();
                                $total = $course->reviews()->count();
                                $percent = $total > 0 ? ($count / $total) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-semibold text-gray-600">{{ $star }} Stars</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 w-8">{{ $count }}</span>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- User's Review -->
                @php
                    $userReview = $course->reviews()->where('user_id', auth()->id())->first();
                @endphp
                @if($userReview)
                    <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-blue-900">Ulasan Anda</h4>
                            <a href="{{ route('student.review.edit', $userReview->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                Edit
                            </a>
                        </div>
                        <div class="flex gap-1 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $userReview->rating)
                                    <span class="text-2xl">[*]</span>
                                @else
                                    <span class="text-2xl text-gray-300">[ ]</span>
                                @endif
                            @endfor
                        </div>
                        @if($userReview->review_text)
                        <p class="text-gray-800">{{ $userReview->review_text }}</p>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Belum Ada Ulasan Anda</h4>
                        <p class="text-gray-600 mb-4">Bagikan pengalaman Anda mengikuti course ini untuk membantu siswa lain</p>
                        <a href="{{ route('student.review.create', $course->id) }}" 
                           class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            Beri Rating & Review
                        </a>
                    </div>
                @endif

                <!-- Other Reviews -->
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Ulasan Lainnya</h3>
                    
                    <div class="space-y-4">
                        @forelse($course->reviews()->where('user_id', '!=', auth()->id())->latest()->get() as $review)
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <!-- Reviewer Info -->
                            <div class="flex items-center gap-3 mb-3">
                                @if($review->user->profile_picture)
                                    <img src="{{ Storage::url($review->user->profile_picture) }}" alt="{{ $review->user->name }}"
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-amber-600 flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($review->user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">{{ $review->user->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div class="flex gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <span class="text-lg">[*]</span>
                                    @else
                                        <span class="text-lg text-gray-300">[ ]</span>
                                    @endif
                                @endfor
                            </div>

                            <!-- Review Text -->
                            @if($review->review_text)
                            <p class="text-gray-700 text-sm">{{ $review->review_text }}</p>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-600 text-center py-8">Belum ada ulasan lainnya</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-1">
                <div class="sticky top-6">
                    <!-- Course Info -->
                    <div class="bg-amber-50 rounded-lg border border-amber-200 p-4">
                        <h4 class="font-semibold text-amber-900 mb-3">📚 Informasi Course</h4>
                        <div class="text-sm text-amber-800 space-y-2">
                            <div class="flex justify-between">
                                <span>Siswa Terdaftar:</span>
                                <strong>{{ $course->registrations()->count() }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Total Rating:</span>
                                <strong>{{ $course->reviews()->count() }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Rata-rata Rating:</span>
                                <strong class="text-amber-600">{{ number_format($course->reviews()->avg('rating') ?? 0, 1) }}/5</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
