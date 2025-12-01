@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">{{ $course->title }}</h1>
            <p class="text-gray-600">{{ __('Modules') }}</p>
        </div>
        <a href="{{ route('instructor.modules.create', $course) }}" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ __('Add Module') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($modules as $module)
            <div class="bg-white rounded shadow p-4 hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">{{ $module->title }}</h3>
                        <p class="text-gray-600 text-sm">{{ $module->description }}</p>
                        <div class="mt-2 flex gap-2">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                {{ $module->materials_count ?? 0 }} {{ __('materials') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('instructor.modules.show', [$course, $module]) }}" class="text-blue-600">{{ __('View') }}</a>
                        <a href="{{ route('instructor.modules.edit', [$course, $module]) }}" class="text-orange-600">{{ __('Edit') }}</a>
                        <form action="{{ route('instructor.modules.destroy', [$course, $module]) }}" method="POST" class="inline" onsubmit="return confirm()">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-100 rounded p-8 text-center">
                <p class="text-gray-600">{{ __('No modules yet') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
