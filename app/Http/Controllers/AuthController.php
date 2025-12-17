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

/**
 * Class AuthController
 * * Bertanggung jawab menangani semua proses autentikasi dan otorisasi pengguna.
 * Fitur utama: Registrasi Manual, Login/Logout, Verifikasi Email, dan OAuth Google.
 */
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
    /**
     * Menangani proses registrasi pengguna baru (Manual).
     * * Alur:
     * 1. Validasi input (Nama, Email, Password).
     * 2. Hash password untuk keamanan data.
     * 3. Set default role sebagai Student (bukan admin/instruktur).
     * 4. Kirim email verifikasi untuk validasi akun.
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
            event(new \Illuminate\Auth\Events\Registered($user));

            

            // Redirect langsung ke halaman Verify Email membawa session email
            return redirect()->route('registration.success')->with('email_registered', $user->email);
            
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
            
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            
            \Log::info('Google User Retrieved', ['email' => $email, 'google_id' => $googleId]);
            
            // Check if user exists with this google_id (most reliable)
            $user = User::where('google_id', $googleId)->first();
            
            if ($user) {
                \Log::info('User found by google_id', ['user_id' => $user->id]);
            } else {
                // If not found by google_id, create NEW user for this Google account
                // DO NOT link to manual signup - keep them separate!
                \Log::info('Creating new user from Google OAuth', ['email' => $email]);
                
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(), // Google email is pre-verified
                    'profile_picture' => $googleUser->getAvatar(),
                    'is_admin' => false,
                    'is_instructor' => false,
                ]);
                
                \Log::info('New Google user created', ['user_id' => $user->id, 'email' => $email]);
            }

            // Login user
            Auth::login($user);
            \Log::info('User logged in via Google', ['user_id' => $user->id]);
            return redirect('/dashboard');

        } catch (\Exception $e) {
            \Log::error('Google Login Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return redirect('/login')->withErrors(['error' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }

    /**
     * Verify email from signed URL
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Check if hash matches
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, __('Invalid verification hash'));
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect('/dashboard')->with('success', __('Email already verified!'));
        }

        // Mark as verified
        $user->markEmailAsVerified();

        // Auto-login if not authenticated
        if (!auth()->check()) {
            auth()->loginUsingId($user->id);
        }

        return redirect('/dashboard')->with('success', __('Email verified successfully! Welcome to OtakAtik Academy.'));
    }
}