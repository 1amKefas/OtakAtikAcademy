<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // --- WHITELIST DOMAIN ---
        // Hapus CDN yang tidak perlu jika sudah di-lokalkan via NPM
        // TinyMCE masih butuh CDN
        $scripts = "https://cdn.tiny.cloud"; 
        $styles  = "https://fonts.googleapis.com";
        $fonts   = "https://fonts.gstatic.com";
        $images  = "https://ui-avatars.com https://www.svgrepo.com"; 

        // --- CONTENT SECURITY POLICY (STRICT MODE) ---
        // 1. default-src 'self': Blokir semua kecuali dari domain sendiri.
        // 2. script-src: Hapus 'unsafe-inline'. 'unsafe-eval' mungkin dibutuhkan AlpineJS (lihat catatan di bawah).
        // 3. style-src: Hapus 'unsafe-inline'.
        // 4. frame-ancestors 'self': Mencegah Clickjacking modern (Wajib buat ZAP).
        // 5. form-action 'self': Mencegah pembajakan formulir.

        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-eval' $scripts; " . // unsafe-eval dibiarkan utk AlpineJS
               "style-src 'self' 'unsafe-inline' $styles; " .                 // unsafe-inline DIHAPUS
               "font-src 'self' $fonts; " .
               "img-src 'self' data: $images; " .
               "frame-src 'self' https://www.youtube.com https://player.vimeo.com; " .
               "connect-src 'self' https://cdn.tiny.cloud; " . // TinyMCE butuh connect ke servernya
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'; " .
               "frame-ancestors 'self';";

        $response->headers->set('Content-Security-Policy', $csp);

        // --- SECURITY HEADERS LAINNYA ---
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=()');

        return $response;
    }
}