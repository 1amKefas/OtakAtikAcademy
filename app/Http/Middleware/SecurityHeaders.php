<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // --- Content Security Policy (CSP) ---
        // Aturan ini menentukan sumber mana yang diperbolehkan.
        // 'self' = domain sendiri.
        // 'unsafe-inline' = script/style di dalam HTML (kita masih butuh ini karena ada onclick="" dan Tailwind CDN).
        // 'unsafe-eval' = fungsi evaluasi JS (Alpine.js & beberapa library butuh ini).
        
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tiny.cloud; " .
               "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; " .
               "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " . // data: untuk gambar base64 (cropper.js)
               "frame-src 'self' https://www.youtube.com https://player.vimeo.com; " . // Izin embed video
               "connect-src 'self';";

        $response->headers->set('Content-Security-Policy', $csp);
        
        // --- Header Keamanan Tambahan (Best Practice OWASP) ---
        $response->headers->set('X-Content-Type-Options', 'nosniff'); // Mencegah browser menebak tipe file
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN'); // Mencegah Clickjacking (iframe dari domain lain)
        $response->headers->set('X-XSS-Protection', '1; mode=block'); // Proteksi XSS browser lama
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin'); // Privasi referrer

        return $response;
    }
}