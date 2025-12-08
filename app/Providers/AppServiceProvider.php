<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Custom route macro for instructor routes
        Route::macro('instructor', function ($prefix = 'instructor') {
            return Route::prefix($prefix)
                ->middleware(['auth', 'instructor'])
                ->group(function () {
                    // Instructor routes will be defined here
                });
        });

        // [OPTIMASI] Gunakan View Composer spesifik, bukan '*' (all views)
        // Agar query tidak jalan berulang-ulang saat include sub-view
        View::composer(['layouts.app', 'layouts.admin', 'components.navbar'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // [OPTIMASI] Cache hitungan notifikasi selama 30 detik
                $cacheKey = 'pending_registrations_' . $user->id;
                
                $pendingRegistrationsCount = \Illuminate\Support\Facades\Cache::remember($cacheKey, 30, function () use ($user) {
                    if ($user->is_admin) {
                        return CourseRegistration::where('status', 'pending')->count();
                    }
                    
                    if ($user->is_instructor) {
                        return CourseRegistration::whereHas('course', function($q) use ($user) {
                            $q->where('instructor_id', $user->id);
                        })->where('status', 'pending')->count();
                    }
                    
                    return 0;
                });
                
                $view->with([
                    'currentUser' => $user,
                    'pendingRegistrationsCount' => $pendingRegistrationsCount,
                ]);
            }
        });

        // Share global stats for admin pages
        View::composer('admin.*', function ($view) {
            // [OPTIMASI] Cache statistik admin selama 5 menit
            $stats = \Illuminate\Support\Facades\Cache::remember('admin_global_stats', 300, function () {
                return [
                    'total_users' => User::count(),
                    'total_instructors' => User::where('is_instructor', true)->count(),
                    'total_courses' => Course::count(),
                    'active_courses' => Course::where('is_active', true)->count(),
                    'total_registrations' => CourseRegistration::count(),
                    'pending_registrations' => CourseRegistration::where('status', 'pending')->count(),
                ];
            });
            
            $view->with('globalStats', $stats);
        });

        // Share instructor stats for instructor pages
        View::composer('instructor.*', function ($view) {
            if (Auth::check() && Auth::user()->is_instructor) {
                $instructor = Auth::user();
                
                // [OPTIMASI] Gunakan query aggregate daripada load models
                $instructorStats = \Illuminate\Support\Facades\Cache::remember('instructor_stats_'.$instructor->id, 60, function () use ($instructor) {
                    $courses = Course::where('instructor_id', $instructor->id)->get();
                    
                    return [
                        'total_courses' => $courses->count(),
                        'total_students' => CourseRegistration::whereIn('course_id', $courses->pluck('id'))->where('status', 'paid')->count(),
                        'total_assignments' => \App\Models\CourseAssignment::whereIn('course_id', $courses->pluck('id'))->count(),
                        'active_courses' => $courses->where('is_active', true)->count(),
                    ];
                });
                
                $view->with('instructorStats', $instructorStats);
            }
        });

        // Custom validation rules
        \Illuminate\Support\Facades\Validator::extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^\+?[0-9\s\-\(\)]{10,}$/', $value);
        });

        \Illuminate\Support\Facades\Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
        });

        // Custom validation messages
        \Illuminate\Support\Facades\Validator::replacer('phone_number', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The '.$attribute.' must be a valid phone number.');
        });

        \Illuminate\Support\Facades\Validator::replacer('strong_password', function ($message, $attribute, $rule, $parameters) {
            return 'The password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
        });
    }
}