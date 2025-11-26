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
        // If user is authenticated, set locale from user preference
        if (auth()->check()) {
            $locale = auth()->user()->locale ?? 'en';
            App::setLocale($locale);
        } else {
            // Default to 'en' for guests
            App::setLocale('en');
        }

        return $next($request);
    }
}
