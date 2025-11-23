<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Management - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
        
        .refund-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .pending-refund-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .processed-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .rejected-card {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #dc2626; }
        .status-processing { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold text-white">OtakAtik<span class="text-blue-400">Admin</span></h1>
            </div>
            
            <!-- Navigation -->
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
                </ul>
            </nav>
            
            <!-- User Section -->
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

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
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

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Refund Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Refunds -->
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

                    <!-- Pending Refunds -->
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

                    <!-- Processed Refunds -->
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

                    <!-- Rejected Refunds -->
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

                <!-- Refund Requests Table -->
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
                                        <div class="text-sm text-gray-900">{{ $refund->course->course }}</div>
                                        <div class="text-xs text-gray-500">{{ $refund->course->sub_course1 }}</div>
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
                                            <button onclick="processRefund({{ $refund->id }}, 'approved')" 
                                                    class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="processRefund({{ $refund->id }}, 'rejected')" 
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @elseif($refund->status == 'approved')
                                            <span class="text-green-600 text-xs">Approved</span>
                                            @else
                                            <span class="text-red-600 text-xs">Rejected</span>
                                            @endif
                                            <button class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </button>
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
                    <!-- Refund Statistics -->
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

                    <!-- Quick Actions -->
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

                        <!-- Refund Tips -->
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

    <!-- Success Message Handler -->
    @if(session('success'))
    <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.fixed.top-6');
            if(alert) alert.remove();
        }, 5000);
    </script>
    @endif

    <script>
        function processRefund(refundId, action) {
            if (confirm(`Are you sure you want to ${action} this refund request?`)) {
                // In a real application, you would make an AJAX request here
                alert(`Refund #${refundId} ${action} successfully!`);
                // Reload the page to see the changes
                location.reload();
            }
        }

        // Sample refund processing simulation
        document.addEventListener('DOMContentLoaded', function() {
            const pendingBadges = document.querySelectorAll('.status-badge.status-pending');
            pendingBadges.forEach(badge => {
                badge.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const refundId = row.querySelector('td:first-child').textContent.replace('#REF-', '');
                    console.log('Processing refund:', refundId);
                });
            });
        });
    </script>

</body>
</html>