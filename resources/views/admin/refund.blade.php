<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Management - OtakAtik Admin</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    {{-- Load External Custom Script --}}
    <script src="{{ asset('js/admin-refund.js') }}" defer></script>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <div class="sidebar w-64 text-white flex flex-col">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Admin</span></h1>
            </div>
            
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-line w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-users w-5"></i>
                            <span>Participants / Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/courses" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-book w-5"></i>
                            <span>Course Anaylitics</span>
                        </a>
                    </li>
                      <li>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white' : '' }}">
                            <i class="fas fa-tags w-5"></i>
                            <span>Kategori</span>
                        </a>
                    </li>
                      <li>
                        <a href="/admin/courses/manage" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span>Course Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/financial" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Financial Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/refund" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white">
                            <i class="fas fa-exchange-alt w-5"></i>
                            <span>Refund Management</span>
                        </a>
                    </li>
                    <li class="pt-4 mt-4 border-t border-gray-700"></li>
                    <li>
                        <a href="/" class="flex items-center gap-3 px-4 py-3 text-emerald-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                            <i class="fas fa-home w-5"></i>
                            <span>Back to Home</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Administrator</p>
                    </div>
                </div>
                <form action="/logout" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Refund Management</h1>
                        <p class="text-gray-600">Manage refund requests and processing</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total Refunds: {{ $refundStats['total_refunds'] }}</p>
                            <p class="text-sm font-medium text-gray-800">{{ date('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="refund-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Total Refunds</p>
                                <p class="text-3xl font-bold">{{ $refundStats['total_refunds'] }}</p>
                                <p class="text-xs opacity-80 mt-2">All time refunds</p>
                            </div>
                            <i class="fas fa-exchange-alt text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <div class="pending-refund-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Pending Refunds</p>
                                <p class="text-3xl font-bold">{{ $refundStats['pending_refunds'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Awaiting approval</p>
                            </div>
                            <i class="fas fa-clock text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <div class="processed-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Processed Refunds</p>
                                <p class="text-3xl font-bold">{{ $refundStats['processed_refunds'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Completed refunds</p>
                            </div>
                            <i class="fas fa-check-circle text-3xl opacity-80"></i>
                        </div>
                    </div>

                    <div class="rejected-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Rejected Refunds</p>
                                <p class="text-3xl font-bold">{{ $refundStats['rejected_refunds'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Denied requests</p>
                            </div>
                            <i class="fas fa-times-circle text-3xl opacity-80"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Refund Requests</h3>
                        <div class="flex gap-2">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Refund ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($refundRequests as $refund)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#REF-{{ $refund->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-xs">{{ substr($refund->user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $refund->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $refund->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $refund->registration->course->title ?? 'Course Deleted' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">Rp{{ number_format($refund->amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $refund->reason }}</div>
                                        @if($refund->description)
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($refund->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-{{ $refund->status }}">
                                            {{ ucfirst($refund->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $refund->created_at->format('M d, Y') }}<br>
                                        <small>{{ $refund->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            @if($refund->status == 'pending')
                                            <form action="{{ route('admin.refunds.approve', $refund->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Approve refund ini?')"
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900 btn-reject"
                                                    data-id="{{ $refund->id }}"
                                                    title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            
                                            @elseif($refund->status == 'approved')
                                            <span class="text-green-600 text-xs">
                                                <i class="fas fa-check-circle"></i> Approved
                                            </span>
                                            @else
                                            <span class="text-red-600 text-xs">
                                                <i class="fas fa-times-circle"></i> Rejected
                                            </span>
                                            @endif
                                            
                                            <a href="{{ route('admin.refunds.show', $refund->id) }}" 
                                               class="text-blue-600 hover:text-blue-900"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-2"></i>
                                        <p>No refund requests found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Refund Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-percentage text-blue-500"></i>
                                    <span class="text-sm font-medium text-blue-800">Refund Rate</span>
                                </div>
                                <span class="text-lg font-bold text-blue-800">{{ $refundStats['refund_rate'] }}%</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-money-bill-wave text-green-500"></i>
                                    <span class="text-sm font-medium text-green-800">Total Refund Amount</span>
                                </div>
                                <span class="text-lg font-bold text-green-800">Rp{{ number_format($refundStats['total_refund_amount'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-clock text-yellow-500"></i>
                                    <span class="text-sm font-medium text-yellow-800">Avg Processing Time</span>
                                </div>
                                <span class="text-lg font-bold text-yellow-800">{{ $refundStats['avg_processing_time'] }} days</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-cog"></i>
                                <span class="font-medium">Refund Policy Settings</span>
                            </a>
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                                <i class="fas fa-file-export"></i>
                                <span class="font-medium">Export Refund Report</span>
                            </a>
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-chart-bar"></i>
                                <span class="font-medium">View Refund Analytics</span>
                            </a>
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-bold text-gray-800 mb-2">Refund Tips</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Process refunds within 3-5 business days</li>
                                <li>• Contact users for additional information if needed</li>
                                <li>• Keep detailed records of all refund transactions</li>
                                <li>• Review refund policy regularly</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Reject Refund</h3>
                <button type="button" id="btnCloseModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" 
                              class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                              rows="4" 
                              required
                              minlength="10"
                              placeholder="Minimal 10 karakter..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Berikan alasan yang jelas untuk penolakan</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" 
                            id="btnCancelReject"
                            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-times-circle mr-1"></i> Reject Refund
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div id="successAlert" class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div id="errorAlert" class="fixed top-6 right-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

</body>
</html>