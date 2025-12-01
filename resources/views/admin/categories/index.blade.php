@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">{{ __('Categories') }}</h1>
        <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ __('Add Category') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">{{ __('Name') }}</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">{{ __('Slug') }}</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">{{ __('Courses') }}</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium">{{ $category->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $category->slug }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded">
                                {{ $category->courses_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-800">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Confirm deletion?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            {{ __('No categories found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
