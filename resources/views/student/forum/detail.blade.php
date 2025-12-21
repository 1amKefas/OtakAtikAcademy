@extends('layouts.app')

@section('title', 'Topik Forum - ' . $forum->subject)

@section('content')
<div class="bg-white">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-8">
        {{-- Contoh jika ingin menambahkan link ke materi di header detail forum --}}
        <div class="mb-4 flex gap-4">
            <a href="{{ route('student.forum.index', $courseId) }}" class="hover:opacity-80">
                ← Kembali ke Daftar Forum
            </a>
            <span class="text-white/30">|</span>
            <a href="{{ route('student.course-detail', $registration->id) }}" class="hover:underline font-bold">
                Kembali ke Materi
            </a>
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
                    <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
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
                    <a href="{{ Storage::disk('public')->url($forum->video_path) }}" target="_blank" class="text-green-600 hover:text-green-800 font-medium">
                        📹 Lihat Video
                    </a>
                </div>
            @endif
        </div>

        <!-- Replies Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                {{ $forum->replies()->count() }} Balasan
            </h3>

            <div class="space-y-4">
                @forelse($forum->replies as $reply)
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                    <div class="flex items-start gap-4 mb-3">
                        @if($reply->user->profile_picture && Storage::disk('public')->exists($reply->user->profile_picture))
                            <img src="{{ Storage::disk('public')->url($reply->user->profile_picture) }}" alt="{{ $reply->user->name }}"
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

                    @if(auth()->id() === $reply->user_id)
                    <div class="flex gap-2 mt-3 pt-3 border-t border-gray-300">
                        <form action="{{ route('student.forum.delete-reply', [$courseId, $reply->id]) }}" method="POST" 
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">✍️ Berikan Balasan</h3>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-red-800 text-sm">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('student.forum.reply', [$courseId, $forum->id]) }}" method="POST">
                @csrf

                <textarea name="message" required rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="Tulis balasan Anda..."></textarea>

                <div class="flex gap-4 mt-4">
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        💬 Kirim Balasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
