@extends('layouts.app')

@section('title', 'Topik Forum - ' . $forum->subject)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-4">
                <a href="{{ route('instructor.courses.show', $course->id) }}" class="hover:opacity-80">
                    Kembali
                </a>
            </div>
            <h1 class="text-3xl font-bold mb-2">{{ $forum->subject }}</h1>
            <p class="text-purple-100">by {{ $forum->user->name }} • {{ $forum->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <!-- Original Post -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
            <div class="flex items-start gap-4 mb-4">
                @if($forum->user->profile_picture)
                    <img src="{{ Storage::url($forum->user->profile_picture) }}" alt="{{ $forum->user->name }}"
                         class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold">
                        {{ substr($forum->user->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">{{ $forum->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $forum->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="text-gray-700 whitespace-pre-wrap mb-4">
                {{ $forum->message }}
            </div>

            @if($forum->image_path)
                <div class="mt-4 mb-4">
                    <img src="{{ Storage::disk('public')->url($forum->image_path) }}" alt="Forum image" class="max-w-full h-auto rounded-lg max-h-96 object-contain">
                </div>
            @endif

            @if($forum->video_path)
                <div class="mt-4 mb-4">
                    <a href="{{ Storage::disk('public')->url($forum->video_path) }}" target="_blank" class="text-purple-600 hover:text-purple-800 font-medium">
                        📹 Lihat Video
                    </a>
                </div>
            @endif

            @if(Auth::id() === $forum->user_id || Auth::user()->is_instructor)
            <div class="flex gap-2 pt-4 border-t border-gray-200">
                <form action="{{ route('instructor.forum.destroy', [$course->id, $forum->id]) }}" method="POST" 
                      class="inline" onsubmit="return confirm('Hapus topik ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition">
                        Hapus
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Replies Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                {{ $forum->replies()->count() }} Balasan
            </h3>

            <div class="space-y-4">
                @forelse($forum->replies()->latest()->get() as $reply)
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                    <div class="flex items-start gap-4 mb-3">
                        @if($reply->user->profile_picture)
                            <img src="{{ Storage::url($reply->user->profile_picture) }}" alt="{{ $reply->user->name }}"
                                 class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                {{ substr($reply->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $reply->user->name }}</p>
                            <p class="text-xs text-gray-600">{{ $reply->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <p class="text-gray-700 whitespace-pre-wrap">{{ $reply->message }}</p>

                    @if(Auth::id() === $reply->user_id || Auth::user()->is_instructor)
                    <div class="flex gap-2 mt-3 pt-3 border-t border-gray-300">
                        <form action="{{ route('instructor.forum.reply.destroy', [$course->id, $forum->id, $reply->id]) }}" method="POST" 
                              class="inline" onsubmit="return confirm('Hapus balasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-gray-600 text-center py-8">Belum ada balasan. Jadilah yang pertama!</p>
                @endforelse
            </div>
        </div>

        <!-- Reply Form -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Berikan Balasan</h3>

            <form action="{{ route('instructor.forum.reply.store', [$course->id, $forum->id]) }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-red-800 text-sm">{{ $errors->first() }}</p>
                    </div>
                @endif

                <textarea name="content" required rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                          placeholder="Tulis balasan Anda..."></textarea>

                <div class="flex gap-4 mt-4">
                    <button type="submit" 
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        Kirim Balasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
