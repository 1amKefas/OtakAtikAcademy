<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Try to get locale from authenticated user's database preference
        if (auth()->check()) {
            try {
                $locale = auth()->user()->locale ?? session('locale', 'en');
            } catch (\Exception $e) {
                // If column doesn't exist yet, fall back to session
                $locale = session('locale', 'en');
            }
            App::setLocale($locale);
        } else {
            // Default to 'en' for guests, check session
            $locale = session('locale', 'en');
            App::setLocale($locale);
        }

        return $next($request);
    }
}
