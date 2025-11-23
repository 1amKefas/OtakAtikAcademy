@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-4xl font-bold text-gray-800">
                    <i class="fas fa-bell text-orange-500 mr-3"></i>Notifications
                </h1>
                @if($notifications->total() > 0 && $notifications->where('read_at', null)->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        @if($notifications->count() > 0)
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition overflow-hidden border-l-4 {{ $notification->read_at ? 'border-l-gray-300' : 'border-l-orange-500' }}">
                        <div class="p-6 flex items-start justify-between">
                            <div class="flex items-start flex-1">
                                <!-- Icon -->
                                <div class="mr-4 mt-1">
                                    @switch($notification->type)
                                        @case('course_purchased')
                                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                            </div>
                                            @break
                                        @case('assignment_posted')
                                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-tasks text-blue-600 text-xl"></i>
                                            </div>
                                            @break
                                        @case('quiz_posted')
                                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                                <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                                            </div>
                                            @break
                                        @case('material_posted')
                                            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                                                <i class="fas fa-file-pdf text-amber-600 text-xl"></i>
                                            </div>
                                            @break
                                        @default
                                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                                <i class="fas fa-bell text-orange-600 text-xl"></i>
                                            </div>
                                    @endswitch
                                </div>

                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800">{{ $notification->title }}</h3>
                                            <p class="text-gray-600 mt-2">{{ $notification->message }}</p>
                                            
                                            @if($notification->course)
                                                <div class="mt-2 inline-block">
                                                    <span class="text-sm px-3 py-1 bg-orange-100 text-orange-700 rounded-full">
                                                        {{ $notification->course->title }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center text-sm text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="ml-4 flex flex-col items-end gap-2">
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm px-3 py-1 bg-orange-100 text-orange-600 rounded hover:bg-orange-200 transition">
                                            Mark as read
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs px-3 py-1 bg-gray-200 text-gray-600 rounded">Read</span>
                                @endif

                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 transition">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-12 text-center">
                <div class="mb-4">
                    <i class="fas fa-inbox text-6xl text-gray-300"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">No Notifications</h2>
                <p class="text-gray-600 mb-6">You're all caught up! Check back later for new notifications.</p>
                <a href="{{ route('dashboard') }}" class="inline-block px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                    Back to Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
