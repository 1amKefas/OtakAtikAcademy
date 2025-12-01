@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ __('Financial Dashboard') }}</h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded shadow p-6">
            <p class="text-gray-600 text-sm">{{ __('Total Revenue') }}</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded shadow p-6">
            <p class="text-gray-600 text-sm">{{ __('Refunded') }}</p>
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($summary['total_refunded'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded shadow p-6">
            <p class="text-gray-600 text-sm">{{ __('Net Revenue') }}</p>
            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($summary['net_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded shadow p-6">
            <p class="text-gray-600 text-sm">{{ __('Total Orders') }}</p>
            <p class="text-2xl font-bold">{{ $summary['total_orders'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top 10 Courses by Revenue -->
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">{{ __('Top 10 Courses by Revenue') }}</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">{{ __('Course') }}</th>
                            <th class="text-right py-2">{{ __('Sales') }}</th>
                            <th class="text-right py-2">{{ __('Revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCourses as $course)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3">{{ substr($course->title, 0, 20) }}...</td>
                                <td class="text-right">{{ $course->enrollment_count ?? 0 }}</td>
                                <td class="text-right font-semibold">Rp {{ number_format($course->revenue ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray-500">{{ __('No data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route('admin.financial.analytics') }}" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">
                {{ __('View Full Analytics') }} →
            </a>
        </div>

        <!-- Pending Refunds -->
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">{{ __('Pending Refunds') }}</h2>
            <div class="text-4xl font-bold text-yellow-600 mb-4">{{ $pendingRefunds ?? 0 }}</div>
            <a href="{{ route('admin.financial.refunds') }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                {{ __('View Refunds') }}
            </a>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.financial.orders') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 p-4 rounded text-center">
            {{ __('All Orders') }}
        </a>
        <a href="{{ route('admin.financial.revenue') }}" class="bg-green-100 hover:bg-green-200 text-green-800 p-4 rounded text-center">
            {{ __('Revenue Report') }}
        </a>
        <a href="{{ route('admin.financial.analytics') }}" class="bg-purple-100 hover:bg-purple-200 text-purple-800 p-4 rounded text-center">
            {{ __('Advanced Analytics') }}
        </a>
    </div>
</div>
@endsection
