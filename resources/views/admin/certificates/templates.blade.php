@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">{{ __('Certificate Templates') }}</h1>
        <a href="{{ route('admin.certificates.templates.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ __('Create Template') }}
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
                    <th class="px-6 py-3 text-left">{{ __('Name') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('Issuer') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ $template->name }}</td>
                        <td class="px-6 py-4">{{ $template->issuer_name }}</td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('admin.certificates.templates.edit', $template) }}" class="text-blue-600">{{ __('Edit') }}</a>
                            <form action="{{ route('admin.certificates.templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Confirm?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">{{ __('No templates') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
