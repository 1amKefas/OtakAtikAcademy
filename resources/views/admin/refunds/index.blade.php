@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Refund Management</h1>
        <p class="text-gray-600 mt-2">Manage refund requests and processing</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total Refunds</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4">
            <div class="text-sm text-yellow-700">Pending</div>
            <div class="text-2xl font-bold text-yellow-900">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4">
            <div class="text-sm text-green-700">Approved</div>
            <div class="text-2xl font-bold text-green-900">{{ $stats['approved'] }}</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-4">
            <div class="text-sm text-red-700">Rejected</div>
            <div class="text-2xl font-bold text-red-900">{{ $stats['rejected'] }}</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4">
            <div class="text-sm text-blue-700">Total Refunded</div>
            <div class="text-2xl font-bold text-blue-900">Rp {{ number_format($stats['total_refunded'], 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.refunds.index') }}" 
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    All ({{ $stats['total'] }})
                </a>
                <a href="{{ route('admin.refunds.index', ['status' => 'pending']) }}" 
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('status') === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Pending ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('admin.refunds.index', ['status' => 'approved']) }}" 
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('status') === 'approved' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Approved ({{ $stats['approved'] }})
                </a>
                <a href="{{ route('admin.refunds.index', ['status' => 'rejected']) }}" 
                   class="px-6 py-3 border-b-2 font-medium text-sm {{ request('status') === 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Rejected ({{ $stats['rejected'] }})
                </a>
            </nav>
        </div>
    </div>

    {{-- Refunds Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($refunds->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-2">No refund requests found</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($refunds as $refund)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #{{ $refund->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $refund->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $refund->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $refund->registration->course->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format($refund->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $refund->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($refund->status === 'pending')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($refund->status === 'approved')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.refunds.show', $refund->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    View
                                </a>
                                
                                @if($refund->status === 'pending')
                                    <form action="{{ route('admin.refunds.approve', $refund->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to approve this refund?')"
                                          class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            Approve
                                        </button>
                                    </form>
                                    
                                    <button onclick="showRejectModal({{ $refund->id }})" 
                                            class="text-red-600 hover:text-red-900">
                                        Reject
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $refunds->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Refund Request</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" 
                              rows="4" 
                              required
                              minlength="10"
                              maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Explain why this refund is being rejected..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimum 10 characters</p>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reject Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal(refundId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/admin/refunds/${refundId}/reject`;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection