<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Users Management - OtakAtik Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 bg-blue-600 rounded-lg text-white">
                            <i class="fas fa-users w-5"></i>
                            <span>Participants / Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/courses" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-book w-5"></i>
                            <span>List Courses</span>
                        </a>
                    </li>
                     <li>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white' : '' }}">
                            <i class="fas fa-tags w-5"></i>
                            <span>Categories</span>
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
                        <h1 class="text-2xl font-bold text-gray-800">Participants / Users</h1>
                        <p class="text-gray-600">Manage all registered users with detailed analytics</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total Users: {{ $userStats['total_users'] }}</p>
                            <p class="text-sm font-medium text-gray-800">{{ date('M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <div class="stats-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Total Users</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['total_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">All registered users</p>
                            </div>
                            <i class="fas fa-users text-3xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="admin-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Admin</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['admin_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Administrators</p>
                            </div>
                            <i class="fas fa-user-shield text-3xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="instructor-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Instructors</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['instructor_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Teachers</p>
                            </div>
                            <i class="fas fa-chalkboard-teacher text-3xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="user-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Regular Users</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['regular_users'] }}</p>
                                <p class="text-xs opacity-80 mt-2">Students</p>
                            </div>
                            <i class="fas fa-user text-3xl opacity-80"></i>
                        </div>
                    </div>
                    <div class="active-card rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Active This Month</p>
                                <p class="text-3xl font-bold mt-2">{{ $userStats['active_this_month'] }}</p>
                                <p class="text-xs opacity-80 mt-2">New registrations</p>
                            </div>
                            <i class="fas fa-chart-line text-3xl opacity-80"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Age Distribution</h3>
                        <div class="h-64">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Education Level</h3>
                        <div class="h-64">
                            <canvas id="educationChart"></canvas>
                        </div>
                    </div>

                    {{-- LOCATION CHART CARD --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6 relative">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800" id="locChartTitle">Top Provinces</h3>
                            {{-- Tombol Back --}}
                            <button id="btnResetLocation" class="hidden text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full transition font-bold items-center shadow-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </button>
                        </div>
                        <div class="h-64 relative">
                            <canvas id="locationChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-800">All Users</h3>
                        <div class="flex gap-2">
                            {{-- Search Form --}}
                            <form action="{{ route('admin.users') }}" method="GET" class="relative w-full md:w-64">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari nama atau email..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                            </form>
                            <button onclick="addUser()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                                <i class="fas fa-plus"></i> Add User
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Profile</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demographics</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role & Stats</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-lg">{{ $user->initial }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                                <div class="text-xs text-gray-400">Joined {{ $user->joinedDate }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="flex items-center gap-2 mb-1">
                                                <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                                <span>{{ $user->email }}</span>
                                            </div>
                                            @if($user->phone)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-phone text-gray-400 text-xs"></i>
                                                <span>{{ $user->phone }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 space-y-1">
                                            @if($user->age)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-birthday-cake text-blue-500 text-xs"></i>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $user->age }} tahun
                                                </span>
                                            </div>
                                            @endif
                                            @if($user->age_range)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-birthday-cake text-blue-500 text-xs"></i>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $user->age_range }}
                                                </span>
                                            </div>
                                            @endif
                                            @if($user->education_level)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-graduation-cap text-green-500 text-xs"></i>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $user->education_level }}
                                                </span>
                                            </div>
                                            @endif
                                            @if($user->location)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-map-marker-alt text-red-500 text-xs"></i>
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">
                                                    {{ $user->location }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <span class="role-badge 
                                                @if($user->is_admin) role-admin 
                                                @elseif($user->is_instructor) role-instructor 
                                                @else role-user @endif">
                                                @if($user->is_admin) Admin
                                                @elseif($user->is_instructor) Instructor
                                                @else User
                                                @endif
                                            </span>
                                            <div class="text-sm text-gray-600">
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-book text-purple-500 text-xs"></i>
                                                    <span>{{ $user->courseCount }} courses</span>
                                                </div>
                                                @if($user->is_instructor && $user->taughtCourses)
                                                <div class="flex items-center gap-1 mt-1">
                                                    <i class="fas fa-chalkboard-teacher text-blue-500 text-xs"></i>
                                                    <span>{{ $user->taughtCourses->count() }} courses taught</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            @if($user->id !== Auth::id())
                                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <select name="role" onchange="this.form.submit()" 
                                                        class="text-xs border rounded p-1 
                                                               @if($user->is_admin) bg-yellow-100 
                                                               @elseif($user->is_instructor) bg-blue-100 
                                                               @else bg-green-100 @endif">
                                                    <option value="user" {{ !$user->is_admin && !$user->is_instructor ? 'selected' : '' }}>User</option>
                                                    <option value="admin" {{ $user->is_admin ? 'selected' : '' }}>Admin</option>
                                                    <option value="instructor" {{ $user->is_instructor ? 'selected' : '' }}>Instructor</option>
                                                </select>
                                            </form>
                                            <button onclick="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="changePassword({{ $user->id }}, '{{ $user->name }}')" class="text-orange-600 hover:text-orange-900">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @else
                                            <span class="text-gray-400 text-xs">Current User</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                                        <p>No users found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($users->hasPages())
                <div class="bg-white px-6 py-3 rounded-lg shadow-sm">
                    {{ $users->links() }}
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">User Management</h3>
                        <div class="space-y-3">
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-user-plus"></i>
                                <span class="font-medium">Add New User</span>
                            </a>
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                                <i class="fas fa-file-export"></i>
                                <span class="font-medium">Export User Data</span>
                            </a>
                            <a href="#" class="w-full flex items-center gap-3 p-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-chart-bar"></i>
                                <span class="font-medium">User Analytics</span>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Demographic Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-2">
                                <span class="text-sm text-gray-600">Average Age:</span>
                                <span class="font-bold text-gray-800">28.5 years</span>
                            </div>
                            <div class="flex justify-between items-center p-2">
                                <span class="text-sm text-gray-600">Top Location:</span>
                                <span class="font-bold text-gray-800">Jakarta</span>
                            </div>
                            <div class="flex justify-between items-center p-2">
                                <span class="text-sm text-gray-600">Most Common Education:</span>
                                <span class="font-bold text-gray-800">Bachelor's Degree</span>
                            </div>
                            <div class="flex justify-between items-center p-2">
                                <span class="text-sm text-gray-600">Active Rate:</span>
                                <span class="font-bold text-green-600">85%</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">User Growth</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Monthly Growth</p>
                                        <p class="text-2xl font-bold text-green-600">+{{ $userStats['active_this_month'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-sm text-gray-600">User registration has increased by 15% compared to last month.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

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
        window.chartData = {
            age: {
                labels: {!! json_encode(array_column($ageDistribution, 'range')) !!},
                data: {!! json_encode(array_column($ageDistribution, 'count')) !!},
                colors: {!! json_encode(array_column($ageDistribution, 'color')) !!}
            },
            education: {
                labels: {!! json_encode(array_column($educationDistribution, 'level')) !!},
                data: {!! json_encode(array_column($educationDistribution, 'count')) !!},
                colors: {!! json_encode(array_column($educationDistribution, 'color')) !!}
            },
            // [FIX] Kirim FULL Object (bukan array_column)
            location: {!! json_encode($locationDistribution) !!} 
        };
    </script>

    <!-- Add User Modal -->
    <div id="addUserModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 flex items-center justify-between">
                <h2 class="text-2xl font-bold">Add New User</h2>
                <button onclick="closeAddUserModal()" class="text-white hover:bg-blue-800 p-2 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="addUserForm" method="POST" action="/admin/users/create" class="p-6 space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user"></i> Full Name *
                    </label>
                    <input type="text" name="name" placeholder="John Doe" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope"></i> Email *
                    </label>
                    <input type="email" name="email" placeholder="john@example.com" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock"></i> Password *
                    </label>
                    <input type="password" name="password" id="addUserPassword" placeholder="Enter password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Min 8 characters recommended</p>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag"></i> Role
                    </label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Phone (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="tel" name="phone" placeholder="+62 812 3456 7890"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date of Birth (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-birthday-cake"></i> Date of Birth
                    </label>
                    <input type="date" name="date_of_birth" id="addUserDateOfBirth"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Used for age distribution chart - needed to see data
                    </p>
                    <p id="ageDisplay" class="text-xs text-blue-600 mt-1"></p>
                </div>

                <!-- Province (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map"></i> Province
                    </label>
                    <input type="text" name="location" placeholder="e.g., Jakarta, West Java" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Will appear in location chart</p>
                </div>

                <!-- Address (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt"></i> Full Address
                    </label>
                    <textarea name="address" placeholder="Street address" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 justify-end pt-4 border-t">
                    <button type="button" onclick="closeAddUserModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-4 flex items-center justify-between">
                <h2 class="text-2xl font-bold">Edit User</h2>
                <button onclick="closeEditUserModal()" class="text-white hover:bg-purple-800 p-2 rounded-lg transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="editUserForm" method="POST" action="" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user"></i> Full Name *
                    </label>
                    <input type="text" id="editUserName" name="name" placeholder="John Doe" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope"></i> Email *
                    </label>
                    <input type="email" id="editUserEmail" name="email" placeholder="john@example.com" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Phone (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="tel" id="editUserPhone" name="phone" placeholder="+62 812 3456 7890"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Date of Birth (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-birthday-cake"></i> Date of Birth
                    </label>
                    <input type="date" id="editUserDateOfBirth" name="date_of_birth"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Province (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map"></i> Province/Location
                    </label>
                    <input type="text" id="editUserLocation" name="location" placeholder="e.g., Jakarta, West Java" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Will appear in location chart</p>
                </div>

                <!-- Address (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt"></i> Full Address
                    </label>
                    <textarea id="editUserAddress" name="address" placeholder="Street address" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <!-- Education Level (Optional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap"></i> Education Level
                    </label>
                    <select id="editUserEducationLevel" name="education_level" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Education Level</option>
                        <option value="high_school">High School</option>
                        <option value="bachelor">Bachelor's Degree</option>
                        <option value="master">Master's Degree</option>
                        <option value="phd">PhD</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 justify-end pt-4 border-t">
                    <button type="button" onclick="closeEditUserModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                        <i class="fas fa-save mr-2"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Calculate age when user enters date of birth
        document.getElementById('addUserDateOfBirth').addEventListener('change', function() {
            const dob = new Date(this.value);
            if (!isNaN(dob)) {
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                document.getElementById('ageDisplay').textContent = '✓ Age: ' + age + ' years';
            }
        });

        // Same for edit form
        if (document.getElementById('editUserDateOfBirth')) {
            document.getElementById('editUserDateOfBirth').addEventListener('change', function() {
                const dob = new Date(this.value);
                if (!isNaN(dob)) {
                    const today = new Date();
                    let age = today.getFullYear() - dob.getFullYear();
                    const monthDiff = today.getMonth() - dob.getMonth();
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                        age--;
                    }
                    if (!document.getElementById('ageDisplayEdit')) {
                        const span = document.createElement('p');
                        span.id = 'ageDisplayEdit';
                        span.className = 'text-xs text-blue-600 mt-1';
                        document.getElementById('editUserDateOfBirth').parentElement.appendChild(span);
                    }
                    document.getElementById('ageDisplayEdit').textContent = '✓ Age: ' + age + ' years';
                }
            });
        }

        function addUser() {
            document.getElementById('addUserModal').classList.remove('hidden');
            document.getElementById('addUserForm').reset();
            document.getElementById('ageDisplay').textContent = '';
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('addUserModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddUserModal();
            }
        });

        // Edit User Functions
        async function editUser(userId) {
            try {
                // Fetch user data
                const response = await fetch(`/admin/users/${userId}/edit`);
                if (!response.ok) {
                    throw new Error('Failed to fetch user data');
                }

                const userData = await response.json();

                // Fill form with user data
                document.getElementById('editUserName').value = userData.name || '';
                document.getElementById('editUserEmail').value = userData.email || '';
                document.getElementById('editUserPhone').value = userData.phone || '';
                document.getElementById('editUserDateOfBirth').value = userData.date_of_birth || '';
                document.getElementById('editUserLocation').value = userData.location || '';
                document.getElementById('editUserAddress').value = userData.address || '';
                document.getElementById('editUserEducationLevel').value = userData.education_level || '';

                // Set form action to update route
                document.getElementById('editUserForm').action = `/admin/users/${userId}`;

                // Open modal
                document.getElementById('editUserModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load user data. Please try again.');
            }
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.add('hidden');
            document.getElementById('editUserForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('editUserModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditUserModal();
            }
        });

        // Handle edit form submission
        document.getElementById('editUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const userId = this.action.split('/').pop();

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3';
                    notification.innerHTML = '<i class="fas fa-check-circle"></i><span class="font-medium">User updated successfully! Refreshing data...</span>';
                    document.body.appendChild(notification);
                    
                    // Close modal
                    closeEditUserModal();
                    
                    // Force full reload to update charts
                    setTimeout(() => {
                        window.location.href = window.location.href;
                    }, 1500);
                } else {
                    const error = await response.json();
                    alert('Error: ' + (error.message || 'Failed to update user'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to update user. Please try again.');
            }
        });

        // Delete User Function
        async function deleteUser(userId, userName) {
            if (!confirm(`Delete user ${userName}?`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-6 right-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3';
                    notification.innerHTML = '<i class="fas fa-check-circle"></i><span class="font-medium">User deleted successfully!</span>';
                    document.body.appendChild(notification);

                    // Remove row from table with fade effect
                    const row = document.querySelector(`button[onclick*="deleteUser(${userId}"]`).closest('tr');
                    if (row) {
                        row.style.opacity = '0.5';
                        setTimeout(() => {
                            row.remove();
                            // Reload page after 1 second to update stats
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        }, 500);
                    }
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete user'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to delete user. Please try again.');
            }
        }

        // Change Password Function
        let currentUserId = null;
        
        function changePassword(userId, userName) {
            currentUserId = userId;
            document.getElementById('changePasswordUserName').textContent = userName;
            document.getElementById('changePasswordModal').classList.remove('hidden');
            document.getElementById('newPasswordInput').value = '';
            document.getElementById('confirmPasswordInput').value = '';
        }

        function closeChangePasswordModal() {
            document.getElementById('changePasswordModal').classList.add('hidden');
            currentUserId = null;
        }

        async function saveNewPassword() {
            if (!currentUserId) return;

            const newPassword = document.getElementById('newPasswordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;

            if (!newPassword || !confirmPassword) {
                alert('Please fill in both password fields');
                return;
            }

            if (newPassword !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            if (newPassword.length < 6) {
                alert('Password must be at least 6 characters');
                return;
            }

            try {
                const response = await fetch(`/admin/users/${currentUserId}/password`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        password: newPassword,
                        password_confirmation: confirmPassword
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3';
                    notification.innerHTML = '<i class="fas fa-check-circle"></i><span class="font-medium">Password changed successfully!</span>';
                    document.body.appendChild(notification);

                    setTimeout(() => notification.remove(), 3000);
                    closeChangePasswordModal();
                } else {
                    alert('Error: ' + (data.message || 'Failed to change password'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to change password. Please try again.');
            }
        }
    </script>
    
    {{-- Change Password Modal --}}
    <div id="changePasswordModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-96">
            <div class="bg-blue-600 text-white p-6 rounded-t-lg flex items-center justify-between">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-key"></i> Change Password
                </h2>
                <button onclick="closeChangePasswordModal()" class="text-white hover:bg-blue-700 p-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User: <span id="changePasswordUserName" class="font-bold"></span></label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" id="newPasswordInput" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter new password">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="confirmPasswordInput" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Confirm new password">
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button onclick="closeChangePasswordModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button onclick="saveNewPassword()" class="flex-1 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Save Password
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Load JS External --}}
    <script src="{{ asset('js/admin-users.js') }}"></script>

</body>
</html>