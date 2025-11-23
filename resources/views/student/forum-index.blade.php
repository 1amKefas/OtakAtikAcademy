@extends('layouts.app')

@section('title', 'Forum Diskusi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('student.course-detail', $courseId) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Kembali ke Kursus
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Forum Diskusi</h1>
            <p class="text-gray-600 mt-2">Diskusikan materi dan berbagi pengalaman dengan sesama peserta</p>
        </div>

        @if($forums->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <p class="text-gray-500 text-lg">Belum ada diskusi di forum ini</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($forums as $forum)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <!-- Forum Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <a href="{{ route('student.forum.detail', [$courseId, $forum->id]) }}" class="block hover:opacity-80">
                                        <div class="flex items-center gap-3 mb-3">
                                            @if($forum->is_pinned)
                                                <span class="inline-block bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">📌 Pinned</span>
                                            @endif
                                            <span class="text-sm text-gray-500">{{ $forum->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900">{{ $forum->subject }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">Oleh <span class="font-semibold">{{ $forum->user->name }}</span></p>
                                    </a>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('student.forum.detail', [$courseId, $forum->id]) }}" 
                                       class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold px-4 py-2 rounded-full hover:bg-blue-200 transition">
                                        {{ $forum->replies->count() }} Balasan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Forum Content -->
                        <div class="p-6 bg-gray-50">
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $forum->message }}</p>
                            
                            @if($forum->image_path)
                                <div class="mt-4">
                                    <img src="{{ Storage::disk('public')->url($forum->image_path) }}" 
                                         alt="Forum image" 
                                         class="max-w-full h-auto rounded-lg max-h-96 object-contain">
                                </div>
                            @endif

                            @if($forum->video_path)
                                <div class="mt-4">
                                    <a href="{{ Storage::disk('public')->url($forum->video_path) }}" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        📹 Lihat Video
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Replies -->
                        @if($forum->replies->count() > 0)
                            <div class="bg-white border-t border-gray-200">
                                <div class="p-6 space-y-4 bg-gray-50 max-h-64 overflow-y-auto">
                                    @foreach($forum->replies as $reply)
                                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                                            <div class="flex items-center gap-3 mb-2">
                                                @if($reply->user->profile_picture && Storage::disk('public')->exists($reply->user->profile_picture))
                                                    <img src="{{ Storage::disk('public')->url($reply->user->profile_picture) }}" 
                                                         alt="{{ $reply->user->name }}" 
                                                         class="w-8 h-8 rounded-full object-cover border-2 border-blue-500">
                                                @else
                                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-semibold text-gray-900">{{ $reply->user->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            <p class="text-gray-700 ml-11 text-sm leading-relaxed">{{ $reply->message }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
