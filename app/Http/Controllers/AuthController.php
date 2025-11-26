<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show register page
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration (Manual)
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            Log::info('Registration attempt', ['email' => $validated['email']]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => false,
                'is_instructor' => false,
            ]);

            // Kirim email verifikasi
            event(new Registered($user));

            Log::info('User created successfully, waiting for verification', ['user_id' => $user->id]);

            // Redirect ke halaman pemberitahuan cek email
            return redirect()->route('verification.notice')->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk memverifikasi akun.');
            
        } catch (\Exception $e) {
            Log::error('Registration failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek apakah email sudah diverifikasi
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Email Anda belum diverifikasi. Silakan cek inbox email Anda.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            // Redirect sesuai role
            if ($user->is_admin) {
                return redirect()->intended('/admin/dashboard')->with('success', '👋 Welcome back, Admin ' . $user->name . '!');
            }
            
            return redirect()->intended('/dashboard')->with('success', ' Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        $userName = Auth::user()?->name ?? 'User';
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', '👋 Goodbye, ' . $userName . '! You have been logged out successfully.');
    }

    /**
     * Redirect ke Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            \Log::info('Google Callback - Starting');
            
            // Mengambil user dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            \Log::info('Google User Retrieved', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'id' => $googleUser->getId(),
            ]);
            
            // Cari user berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            if(!$user) {
                \Log::info('Creating new user from Google');
                // Jika user belum ada, buat baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)), // Password random
                    'email_verified_at' => now(), // Otomatis verified
                    'profile_picture' => $googleUser->getAvatar(),
                    'is_admin' => false,
                    'is_instructor' => false,
                ]);
                \Log::info('New user created', ['user_id' => $user->id, 'email' => $user->email]);
            } else {
                \Log::info('Existing user found', ['user_id' => $user->id]);
                // Jika user sudah ada, update google_id dan set verified
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);
                    \Log::info('User updated with google_id');
                }
            }

            // Login user
            Auth::login($user);
            \Log::info('User logged in successfully', ['user_id' => $user->id]);
            return redirect('/dashboard');

        } catch (\Exception $e) {
            \Log::error('Google Login Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect('/login')->withErrors(['error' => 'Gagal login dengan Google. Silakan coba lagi. Error: ' . $e->getMessage()]);
        }
    }
}