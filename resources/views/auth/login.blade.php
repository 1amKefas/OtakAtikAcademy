<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OtakAtik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        .animate-slide-down {
            animation: slideDown 0.4s ease-out;
        }
        .chalkboard-bg {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            position: relative;
        }
        .chalkboard-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                repeating-linear-gradient(0deg, rgba(255,255,255,0.03) 0px, transparent 1px, transparent 2px, rgba(255,255,255,0.03) 3px),
                repeating-linear-gradient(90deg, rgba(255,255,255,0.03) 0px, transparent 1px, transparent 2px, rgba(255,255,255,0.03) 3px);
            opacity: 0.5;
        }
        .math-text {
            position: absolute;
            color: rgba(255,255,255,0.15);
            font-family: 'Courier New', monospace;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-500 to-orange-600 min-h-screen flex items-center justify-center p-4">
    
    <!-- Success/Error Notifications -->
    @if(session('success'))
        <div class="fixed top-6 right-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-slide-down flex items-center gap-3 max-w-md">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        <script>
            setTimeout(() => {
                const notif = document.querySelector('.fixed.top-6');
                if(notif) {
                    notif.style.opacity = '0';
                    notif.style.transform = 'translateY(-20px)';
                    notif.style.transition = 'all 0.3s';
                    setTimeout(() => notif.remove(), 300);
                }
            }, 4000);
        </script>
    @endif

    @if($errors->any())
        <div class="fixed top-6 right-6 bg-red-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-slide-down max-w-md">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const notif = document.querySelector('.fixed.top-6');
                if(notif) {
                    notif.style.opacity = '0';
                    notif.style.transform = 'translateY(-20px)';
                    notif.style.transition = 'all 0.3s';
                    setTimeout(() => notif.remove(), 300);
                }
            }, 5000);
        </script>
    @endif

    <div class="max-w-6xl w-full animate-fade-in">
        <div class="bg-white shadow-2xl overflow-hidden flex relative rounded-3xl" style="min-height: 520px;">
            
            <!-- Left Side - Koala Section -->
            <div class="w-[42%] chalkboard-bg rounded-tl-3xl rounded-bl-3xl relative overflow-hidden">
                <!-- Math formulas -->
                <div class="math-text" style="top: 15%; left: 10%; font-size: 14px; transform: rotate(-10deg);">2+2=4</div>
                <div class="math-text" style="top: 25%; right: 15%; font-size: 16px; transform: rotate(8deg);">πr²</div>
                <div class="math-text" style="top: 40%; left: 20%; font-size: 12px;">a²+b²=c²</div>
                <div class="math-text" style="bottom: 30%; right: 20%; font-size: 18px;">∑</div>
                <div class="math-text" style="top: 60%; left: 15%; font-size: 14px; transform: rotate(-5deg);">x=√y</div>
                
                <div class="relative h-full flex flex-col items-center pt-8 px-8 z-10">
                    <!-- Exclamation marks -->
                    <div class="flex gap-2 items-end mb-4">
                        <div class="text-white text-5xl font-bold drop-shadow-lg">!</div>
                        <div class="text-white text-6xl font-bold drop-shadow-lg">!</div>
                    </div>
                    
                    <!-- Text -->
                    <h3 class="text-white text-3xl font-bold mb-2 drop-shadow-lg">Hello, Welcome!</h3>
                    <p class="text-white text-sm mb-6 opacity-90">Don't Have an Account yet?</p>
                    
                    <!-- Sign Up Button -->
                    <a href="/register" 
                       class="inline-block bg-white text-teal-700 font-bold px-10 py-3 rounded-xl hover:bg-gray-100 hover:scale-105 transition-all duration-300 shadow-lg mb-8">
                        Sign Up
                    </a>
                    
                    <!-- Koala Character -->
                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-full" style="height: 280px;">
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-56 h-56 bg-gray-500 rounded-full">
                            <div class="absolute -top-12 -left-14 w-24 h-28 bg-gray-500 rounded-full">
                                <div class="absolute top-2 left-2 w-20 h-24 bg-gray-300 rounded-full"></div>
                            </div>
                            <div class="absolute -top-12 -right-14 w-24 h-28 bg-gray-500 rounded-full">
                                <div class="absolute top-2 right-2 w-20 h-24 bg-gray-300 rounded-full"></div>
                            </div>
                            <div class="absolute top-16 left-11 w-11 h-13 bg-black rounded-full">
                                <div class="absolute top-2 left-2 w-4 h-4 bg-white rounded-full"></div>
                            </div>
                            <div class="absolute top-16 right-11 w-11 h-13 bg-black rounded-full">
                                <div class="absolute top-2 left-2 w-4 h-4 bg-white rounded-full"></div>
                            </div>
                            <div class="absolute top-30 left-1/2 transform -translate-x-1/2 w-13 h-10 bg-black rounded-full">
                                <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <div class="absolute top-40 left-1/2 transform -translate-x-1/2 w-10 h-5 bg-black rounded-b-full"></div>
                            <div class="absolute top-40 left-1/2 transform -translate-x-1/2 w-8 h-4 bg-pink-300 rounded-full"></div>
                            <div class="absolute top-26 left-4 w-11 h-9 bg-pink-400 rounded-full opacity-75"></div>
                            <div class="absolute top-26 right-4 w-11 h-9 bg-pink-400 rounded-full opacity-75"></div>
                            <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 w-64 h-24 bg-gray-600 rounded-t-full"></div>
                        </div>
                        <div class="absolute bottom-2 left-1/4 w-14 h-14 bg-gray-600 rounded-full">
                            <div class="absolute top-3 left-3 w-3 h-3 bg-gray-700 rounded-full"></div>
                            <div class="absolute top-3 right-3 w-3 h-3 bg-gray-700 rounded-full"></div>
                            <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gray-700 rounded-full"></div>
                        </div>
                        <div class="absolute bottom-2 right-1/4 w-14 h-14 bg-gray-600 rounded-full">
                            <div class="absolute top-3 left-3 w-3 h-3 bg-gray-700 rounded-full"></div>
                            <div class="absolute top-3 right-3 w-3 h-3 bg-gray-700 rounded-full"></div>
                            <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gray-700 rounded-full"></div>
                        </div>
                    </div>
                </div>
                <div class="absolute top-0 right-0 w-32 h-full bg-white" style="border-top-left-radius: 100%; border-bottom-left-radius: 100%;"></div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="w-[58%] px-20 py-16 bg-white rounded-3xl flex flex-col justify-center">
                <h2 class="text-5xl font-bold mb-12 text-gray-800">Login</h2>
                
                <form action="/login" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                            class="w-full px-6 py-5 bg-gray-100 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('email') ring-2 ring-red-500 @enderror" 
                            required />
                    </div>
                    <div>
                        <input type="password" name="password" placeholder="Password" 
                            class="w-full px-6 py-5 bg-gray-100 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all" 
                            required />
                    </div>
                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-5 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 mt-6">
                        Sign In
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>
</html>