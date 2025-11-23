<!-- Navbar with Profile Dropdown and Notifications -->
<nav class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">O</span>
            </div>
            <span class="text-xl font-bold text-gray-800">OtakAtik</span>
        </div>
        
        <!-- Menu -->
        <div class="hidden md:flex items-center gap-8">
            <a href="/dashboard" class="text-gray-700 hover:text-orange-500 font-medium transition">About Us</a>
            <a href="/course" class="text-gray-700 hover:text-orange-500 font-medium transition">Our Course</a>
            <a href="/my-courses" class="text-gray-700 hover:text-orange-500 font-medium transition">My Courses</a>
            <a href="/purchase-history" class="text-gray-700 hover:text-orange-500 font-medium transition">History</a>
        </div>
        
        <!-- Right Section: Notifications and Profile -->
        <div class="flex items-center gap-6">
            <!-- Notification Bell -->
            <div class="relative">
                @php
                    $unreadNotifications = collect();
                    $unreadCount = 0;
                    if (Auth::check()) {
                        $unreadNotifications = Auth::user()->notifications()
                            ->whereNull('read_at')
                            ->orderBy('created_at', 'desc')
                            ->get();
                        $unreadCount = $unreadNotifications->count();
                    }
                @endphp
                <button id="notificationBtn" class="relative text-gray-600 hover:text-orange-500 transition text-xl">
                    <i class="fas fa-bell"></i>
                    @if($unreadCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                    @endif
                </button>
                <!-- Notification Dropdown -->
                @if(Auth::check())
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($unreadNotifications->take(5) as $notification)
                            <div class="p-4 hover:bg-gray-50 cursor-pointer transition" onclick="markAsRead({{ $notification->id }})" data-notification-id="{{ $notification->id }}">
                                <div class="flex items-start gap-3">
                                    @if($notification->type === 'course_purchased')
                                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-graduation-cap text-orange-600"></i>
                                        </div>
                                    @elseif($notification->type === 'assignment_posted')
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-clipboard-list text-blue-600"></i>
                                        </div>
                                    @elseif($notification->type === 'assignment_deadline_changed')
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-hourglass-end text-red-600"></i>
                                        </div>
                                    @elseif($notification->type === 'quiz_posted')
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-question-circle text-purple-600"></i>
                                        </div>
                                    @elseif($notification->type === 'material_posted')
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-book text-green-600"></i>
                                        </div>
                                    @elseif($notification->type === 'forum_reply')
                                        <div class="w-10 h-10 bg-cyan-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-comments text-cyan-600"></i>
                                        </div>
                                    @elseif($notification->type === 'submission_graded')
                                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-check-circle text-teal-600"></i>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-bell text-gray-600"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500">
                                <p class="text-sm">No new notifications</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-3 border-t border-gray-200 text-center">
                        <a href="{{ route('notifications.index') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">View All Notifications</a>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Profile Dropdown -->
            @auth
            <div class="relative">
                <button id="profileBtn" class="flex items-center gap-3 hover:bg-gray-100 rounded-full p-1 transition">
                    <!-- Circular Profile Avatar -->
                    @if(Auth::user()->profile_picture && Storage::disk('public')->exists(Auth::user()->profile_picture))
                        <img src="{{ Storage::url(Auth::user()->profile_picture) }}" alt="{{ Auth::user()->name }}" 
                             class="w-10 h-10 rounded-full object-cover border-2 border-orange-500">
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </button>
                
                <!-- Profile Dropdown Menu -->
                <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50">
                    <!-- Header with name -->
                    <div class="p-4 border-b border-gray-200">
                        <p class="font-semibold text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->email ?? 'user@email.com' }}</p>
                    </div>
                    
                    <!-- Menu Items -->
                    <div class="py-2">
                        <a href="/profile" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <span>My Profile</span>
                        </a>
                        <a href="/purchase-history" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <span>Purchase History</span>
                        </a>
                        <a href="/settings" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <span>Settings</span>
                        </a>
                        <a href="/achievements" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <span>Achievements</span>
                        </a>
                        <a href="/help" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                            <span>Help Center</span>
                        </a>
                    </div>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200 py-2">
                        <form action="/logout" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 transition">
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth
            
            <!-- Guest Login Button -->
            @guest
            <div class="flex gap-3">
                <a href="{{ route('login') }}" class="px-4 py-2 text-orange-600 hover:bg-orange-50 rounded-lg transition font-medium">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-600 text-white hover:bg-orange-700 rounded-lg transition font-medium">
                    Daftar
                </a>
            </div>
            @endguest
        </div>
    </div>
</nav>

<!-- Dropdown Toggle Scripts -->
<script>
    // Mark notification as read
    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Remove notification from dropdown without reloading
                  const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                  if (notificationElement) {
                      notificationElement.style.opacity = '0.5';
                      notificationElement.style.textDecoration = 'line-through';
                  }
                  // Update badge count
                  const badge = document.querySelector('#notificationBtn span');
                  if (badge) {
                      let count = parseInt(badge.textContent);
                      if (count > 1) {
                          badge.textContent = count - 1;
                      } else {
                          badge.remove();
                      }
                  }
              }
          }).catch(error => console.error('Error:', error));
    }
    
    // Profile Dropdown
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    
    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('hidden');
        notificationDropdown.classList.add('hidden');
    });
    
    // Notification Dropdown
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    notificationBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
        profileDropdown.classList.add('hidden');
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        profileDropdown.classList.add('hidden');
        notificationDropdown.classList.add('hidden');
    });
</script>
