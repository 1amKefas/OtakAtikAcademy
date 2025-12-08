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

        // --- DAFTAR DOMAIN DIPERBOLEHKAN (WHITELIST) ---
        // Kita spesifikan domain satu per satu supaya tidak pakai Wildcard (*)
        $allowedScripts = "https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tiny.cloud";
        $allowedStyles  = "https://cdnjs.cloudflare.com https://fonts.googleapis.com https://cdn.jsdelivr.net";
        $allowedFonts   = "https://fonts.gstatic.com https://cdnjs.cloudflare.com";
        $allowedImages  = "https://ui-avatars.com https://www.svgrepo.com"; // Tambah domain gambar external lain jika ada

        // --- CONTENT SECURITY POLICY (CSP) ---
        // default-src 'self': Memblokir semuanya kecuali dari domain sendiri, kecuali di-override.
        // unsafe-inline: Masih kita perlukan SEMENTARA sampai kita bersihkan onclick dan style tag.
        // unsafe-eval: Diperlukan oleh Alpine.js (standar).
        
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' $allowedScripts; " .
               "style-src 'self' 'unsafe-inline' $allowedStyles; " .
               "font-src 'self' $allowedFonts; " .
               "img-src 'self' data: $allowedImages; " . // data: untuk gambar base64 (cropper)
               "frame-src 'self' https://www.youtube.com https://player.vimeo.com; " .
               "connect-src 'self'; " . 
               "object-src 'none'; " . // Blokir plugin kayak Flash/Java
               "base-uri 'self';";     // Mencegah injeksi base tag

        $response->headers->set('Content-Security-Policy', $csp);
        
        // --- Header Keamanan Lainnya ---
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()'); // Batasi fitur browser

        return $response;
    }
}