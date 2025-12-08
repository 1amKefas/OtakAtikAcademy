<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
                        <a href="/admin/courses" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white">
                            <i class="fas fa-book w-5"></i>
                            <span>Course Management</span>
                        </a>
                    </li>
                     <li>
                        <a href="/admin/courses/manage" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span>Tambah Course</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/financial" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Financial Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/refund" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-exchange-alt w-5"></i>
                            <span>Refund Management</span>
                        </a>
                    </li>
                    </li>  <li class="pt-4 mt-4 border-t border-gray-700"></li>

                    <li>
                        <a href="/" class="flex items-center gap-3 px-4 py-3 text-emerald-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors">
                            <i class="fas fa-home w-5"></i>
                            <span>Back to Home</span>
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
                        <h1 class="text-2xl font-bold text-gray-800">Course Management</h1>
                        <p class="text-gray-600">Manage all course registrations</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.courses.export') }}" 
                           class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total: {{ $courses->total() }} courses</p>
                            <p class="text-sm font-medium text-gray-800">{{ date('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Total Courses</p>
                                <p class="text-3xl font-bold mt-2">{{ $courses->total() }}</p>
                            </div>
                            <i class="fas fa-book-open text-2xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Pending</p>
                                <p class="text-3xl font-bold mt-2">{{ $courses->where('status', 'pending')->count() }}</p>
                            </div>
                            <i class="fas fa-clock text-2xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Paid</p>
                                <p class="text-3xl font-bold mt-2">{{ $courses->where('status', 'paid')->count() }}</p>
                            </div>
                            <i class="fas fa-check-circle text-2xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Cancelled</p>
                                <p class="text-3xl font-bold mt-2">{{ $courses->where('status', 'cancelled')->count() }}</p>
                            </div>
                            <i class="fas fa-times-circle text-2xl opacity-80"></i>
                        </div>
                    </div>
                </div>

                <!-- Courses Table -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personal Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($courses as $registration)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">{{ substr($registration->user->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $registration->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $registration->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <strong>{{ $registration->course->title }}</strong><br>
                                            <span class="text-gray-600">{{ $registration->course->type }}</span><br>
                                            <small class="text-gray-500">Instruktur: {{ $registration->course->instructor->name ?? 'Tidak tersedia' }}</small>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <strong>Name:</strong> {{ $registration->nama_lengkap }}<br>
                                            <strong>TTL:</strong> {{ $registration->ttl }}<br>
                                            <strong>Location:</strong> {{ $registration->tempat_tinggal }}<br>
                                            <strong>Gender:</strong> {{ $registration->gender }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($registration->discount_code)
                                            <div class="flex items-center gap-2">
                                                <span class="text-green-600">{{ $registration->formatted_final_price }}</span>
                                                <span class="text-xs text-gray-500 line-through">{{ $registration->formatted_price }}</span>
                                            </div>
                                            <div class="text-xs text-green-600">Diskon: {{ $registration->discount_code }}</div>
                                            @else
                                            <span>{{ $registration->formatted_price }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-{{ $registration->status }}">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                        @if($registration->status === 'paid' && $registration->progress > 0)
                                        <div class="text-xs text-gray-500 mt-1">Progress: {{ $registration->progress }}%</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $registration->created_at->format('M d, Y') }}<br>
                                        <small>{{ $registration->created_at->format('H:i') }}</small>
                                    </td>
                                    <!-- Di dalam loop foreach $courses as $registration -->
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    <div class="flex gap-2">
        <!-- GUNAKAN ROUTE YANG BENAR: admin.courses.status.update -->
        <form action="{{ route('admin.courses.status.update', $registration->id) }}" method="POST" class="inline">
            @csrf
            @method('PUT')
            <select name="status" onchange="this.form.submit()" 
                    class="text-xs border rounded p-1 
                           @if($registration->status == 'paid') bg-green-100 
                           @elseif($registration->status == 'pending') bg-yellow-100 
                           @else bg-red-100 @endif">
                <option value="pending" {{ $registration->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ $registration->status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ $registration->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>
        <form action="{{ route('course.destroy', $registration->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-900" 
                    onclick="return confirm('Delete this course registration?')">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-book-open text-4xl text-gray-300 mb-2"></i>
                                        <p>No course registrations found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($courses->hasPages())
                <div class="mt-6 bg-white px-6 py-3 rounded-lg shadow-sm">
                    {{ $courses->links() }}
                </div>
                @endif
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

</body>
</html>