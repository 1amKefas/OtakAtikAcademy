<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered; // Penting untuk verifikasi email
use Laravel\Socialite\Facades\Socialite; // Penting untuk Google
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ... showLogin dan showRegister TETAP SAMA ...

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255', // Sesuaikan name field di view (name atau username)
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed', // Tambahkan confirmed jika ada field confirm password
            ]);

            Log::info('Registration attempt', ['email' => $validated['email']]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => false,      // Default User
                'is_instructor' => false, // Default User
            ]);

            // [PENTING] Kirim email verifikasi
            event(new Registered($user));

            // [UBAH] Jangan langsung login (Auth::login($user)).
            // Arahkan user ke halaman pemberitahuan untuk cek email.
            
            return redirect()->route('verification.notice')->with('success', 'Registrasi berhasil! Silakan cek email Anda (termasuk folder spam) untuk memverifikasi akun sebelum login.');
            
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

        // Cek kredensial
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // [PENTING] Cek apakah email sudah diverifikasi
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Email Anda belum diverifikasi. Silakan cek inbox email Anda.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            if ($user->is_admin) {
                return redirect()->intended('/admin/dashboard');
            }
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // ... logout TETAP SAMA ...

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
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah user dengan email ini sudah ada
            $user = User::where('email', $googleUser->getEmail())->first();

            if(!$user) {
                // Jika user baru, buat akun
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)), // Password acak
                    'email_verified_at' => now(), // Otomatis verifikasi karena dari Google
                    'profile_picture' => $googleUser->getAvatar(),
                ]);
            } else {
                // Jika user sudah ada, update google_id jika belum ada
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        // Jika email sama tapi belum verifikasi manual, kita anggap verified karena login via Google
                        'email_verified_at' => $user->email_verified_at ?? now(), 
                    ]);
                }
            }

            Auth::login($user);
            return redirect('/dashboard');

        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['error' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }
}