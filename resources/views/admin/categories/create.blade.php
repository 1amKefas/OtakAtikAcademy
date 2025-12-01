@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <h1 class="text-2xl font-bold mb-6">{{ __('Create Category') }}</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white rounded shadow p-6">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold mb-2">{{ __('Name') }}</label>
            <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="slug" class="block text-sm font-semibold mb-2">{{ __('Slug') }}</label>
            <input type="text" name="slug" id="slug" class="w-full border rounded px-3 py-2 @error('slug') border-red-500 @enderror" required>
            @error('slug') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-semibold mb-2">{{ __('Description') }}</label>
            <textarea name="description" id="description" class="w-full border rounded px-3 py-2" rows="3"></textarea>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                {{ __('Create') }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
