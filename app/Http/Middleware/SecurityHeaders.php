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

        // --- CONTENT SECURITY POLICY (PERMISSIVE MODE) ---
        // Kita izinkan 'https:' secara umum dulu untuk mengatasi masalah hosting/CDN/subdomain.
        // 'data:' kita izinkan di font dan img karena sering dipakai library modern.
        
        $csp = "default-src 'self' https: data: blob:; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; " . 
               "style-src 'self' 'unsafe-inline' https:; " . 
               "font-src 'self' data: https:; " .
               "img-src 'self' data: https: blob:; " .
               "connect-src 'self' https:; " . 
               "frame-src 'self' https:; " .
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