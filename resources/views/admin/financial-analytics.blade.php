<!-- Recent Transactions -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($recentTransactions as $transaction)
                <tr>
                    <td class="px-4 py-3">
                        @if($transaction->type === 'payment')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Payment
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Refund
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $transaction->user->name }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($transaction->type === 'payment')
                            {{ $transaction->course->title }}
                        @else
                            {{ $transaction->registration->course->title }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-medium
                        {{ $transaction->type === 'payment' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $transaction->type === 'payment' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $transaction->created_at->format('d M Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">No transactions yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>