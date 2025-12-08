<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OtakAtik Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white">

    <div class="min-h-screen flex">
        
        <!-- Left Side: Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
            <div class="w-full max-w-md">
                
                <!-- Logo -->
                <div class="text-center mb-8">
                    <a href="/" class="inline-block">
                        <img src="/images/logo_OtakAtik.png" alt="OtakAtik Logo" class="h-12 mx-auto object-contain">
                    </a>
                </div>

                <!-- Form Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h1>
                    <p class="text-sm text-gray-500">Bergabunglah dengan komunitas pembelajar kami</p>
                </div>

                <!-- Alert Error -->
                @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium">Terjadi Kesalahan</p>
                            <ul class="list-disc list-inside text-xs text-red-600 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500 sm:text-sm transition-all"
                            placeholder="John Doe">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500 sm:text-sm transition-all"
                            placeholder="your@email.com">
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500 sm:text-sm transition-all"
                            placeholder="Min. 8 characters">
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-orange-500 sm:text-sm transition-all"
                            placeholder="Re-enter password">
                    </div>

                    <!-- Terms Checkbox (Optional but good for UI) -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded cursor-pointer">
                        </div>
                        <div class="ml-2 text-sm">
                            <label for="terms" class="font-medium text-gray-700 cursor-pointer">I agree to the <a href="#" class="text-orange-600 hover:text-orange-500">Terms</a> and <a href="#" class="text-orange-600 hover:text-orange-500">Privacy Policy</a></label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transform transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5">
                        Register Account
                    </button>


                    <div class="relative flex py-5 items-center">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">Or continue with</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>

                    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="h-5 w-5 mr-2" alt="Google">
                        Sign up with Google
                    </a>

                    <!-- Login Link -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:text-orange-500 transition-colors">
                                Sign in
                            </a>
                        </p>
                    </div>
                </form>
                
                <!-- Footer Copyright -->
                <div class="mt-10 text-center">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} Politeknik Negeri Jakarta. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side: Full Image (Hidden on Mobile) -->
        <div class="hidden lg:block w-1/2 relative">
            <!-- Pastikan file gambar ada di public/images/koala_registrasi.png -->
            <img src="/images/koala_registrasi.png" alt="Register Illustration" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
        </div>

    </div>

</body>
</html>