<!-- Navbar with Profile Dropdown and Notifications -->
<nav class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <!-- Logo (UPDATED: Image Only) -->
        <a href="/" class="flex items-center">
            <img src="/images/logo_OtakAtik.png" alt="OtakAtik Academy" class="h-12 w-auto object-contain">
        </a>
        
        <!-- Menu -->
        <div class="hidden md:flex items-center gap-8">
            <a href="/dashboard" class="text-gray-700 hover:text-orange-500 font-medium transition">{{ __('messages.home') }}</a>
            <a href="/course" class="text-gray-700 hover:text-orange-500 font-medium transition">{{ __('messages.courses') }}</a>
            <a href="/my-courses" class="text-gray-700 hover:text-orange-500 font-medium transition">{{ __('messages.my_courses') }}</a>
            <a href="/purchase-history" class="text-gray-700 hover:text-orange-500 font-medium transition">{{ __('messages.purchase_history') }}</a>
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
                        <h3 class="font-semibold text-gray-800">{{ __('messages.notifications') }}</h3>
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
                        <a href="{{ route('notifications.index') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">{{ __('messages.view_all') }}</a>
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
                <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl z-50 overflow-hidden border border-gray-100">
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <p class="font-semibold text-gray-900 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ Auth::user()->email ?? 'user@email.com' }}</p>
                            
                            <div class="mt-2 flex gap-1 flex-wrap">
                                @if(Auth::user()->is_admin)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">ADMIN</span>
                                @endif
                                @if(Auth::user()->is_instructor)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">INSTRUCTOR</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="py-2">
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-purple-700 hover:bg-purple-50 transition font-medium">
                                    <div class="w-5 text-center"><i class="fas fa-tachometer-alt"></i></div>
                                    <span>Admin Dashboard</span>
                                </a>
                                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-2 text-purple-700 hover:bg-purple-50 transition font-medium">
                                    <div class="w-5 text-center"><i class="fas fa-list"></i></div>
                                    <span>Categories</span>
                                </a>
                                <a href="{{ route('admin.financial') }}" class="flex items-center gap-3 px-4 py-2 text-purple-700 hover:bg-purple-50 transition font-medium">
                                    <div class="w-5 text-center"><i class="fas fa-chart-line"></i></div>
                                    <span>Financial</span>
                                </a>
                                <a href="{{ route('admin.refunds.index') }}" class="flex items-center gap-3 px-4 py-2 text-purple-700 hover:bg-purple-50 transition font-medium">
                                    <div class="w-5 text-center"><i class="fas fa-exchange-alt"></i></div>
                                    <span>Refunds</span>
                                </a>
                            @endif

                            @if(Auth::user()->is_instructor)
                                <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-blue-700 hover:bg-blue-50 transition font-medium">
                                    <div class="w-5 text-center"><i class="fas fa-chalkboard-teacher"></i></div>
                                    <span>Dashboard</span>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                            @endif

                            <a href="/profile" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <div class="w-5 text-center"><i class="fas fa-user text-gray-400"></i></div>
                                <span>{{ __('messages.profile') }}</span>
                            </a>
                            <a href="/my-courses" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <div class="w-5 text-center"><i class="fas fa-book text-gray-400"></i></div>
                                <span>{{ __('messages.my_courses') }}</span>
                            </a>
                            <a href="/purchase-history" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <div class="w-5 text-center"><i class="fas fa-history text-gray-400"></i></div>
                                <span>{{ __('messages.purchase_history') }}</span>
                            </a>
                            <a href="/achievements" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <div class="w-5 text-center"><i class="fas fa-trophy text-gray-400"></i></div>
                                <span>{{ __('messages.achievements') }}</span>
                            </a>
                            <a href="/settings" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <div class="w-5 text-center"><i class="fas fa-cog text-gray-400"></i></div>
                                <span>{{ __('messages.settings') }}</span>
                            </a>
                            <a href="/help" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                <div class="w-5 text-center"><i class="fas fa-question-circle text-gray-400"></i></div>
                                <span>{{ __('messages.help') }}</span>
                            </a>
                        </div>
                        
                        <div class="border-t border-gray-200 py-2 bg-gray-50">
                            <form action="/logout" method="POST" class="block">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 transition font-medium">
                                    <div class="w-5 text-center"><i class="fas fa-sign-out-alt"></i></div>
                                    <span>{{ __('messages.logout') }}</span>
                                </button>
                            </form>
                        </div>
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