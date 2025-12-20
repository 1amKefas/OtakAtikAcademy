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
        // Untuk development, allow localhost Vite dev server + websocket untuk HMR
        
        $viteDevServer = config('app.env') === 'local' ? 'http://127.0.0.1:5174 http://127.0.0.1:5173 ws://127.0.0.1:5174 ws://127.0.0.1:5173' : '';
        
        $csp = "default-src 'self' https: data: blob: $viteDevServer; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: $viteDevServer; " . 
               "style-src 'self' 'unsafe-inline' https: $viteDevServer; " . 
               "font-src 'self' data: https:; " .
               "img-src 'self' data: https: blob:; " .
               "connect-src 'self' https: ws: wss: $viteDevServer; " . 
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