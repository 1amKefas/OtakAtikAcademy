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
        
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Custom route macro for instructor routes
        Route::macro('instructor', function ($prefix = 'instructor') {
            return Route::prefix($prefix)
                ->middleware(['auth', 'instructor'])
                ->group(function () {
                    // Instructor routes will be defined here
                });
        });

        // Share common data with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Share user notifications count
                $pendingRegistrationsCount = 0;
                
                if ($user->is_admin) {
                    $pendingRegistrationsCount = CourseRegistration::where('status', 'pending')->count();
                }
                
                if ($user->is_instructor) {
                    $instructorCourses = $user->taughtCourses()->pluck('id');
                    $pendingRegistrationsCount = CourseRegistration::whereIn('course_id', $instructorCourses)
                        ->where('status', 'pending')
                        ->count();
                }
                
                $view->with([
                    'currentUser' => $user,
                    'pendingRegistrationsCount' => $pendingRegistrationsCount,
                ]);
            }
        });

        // Share global stats for admin pages
        View::composer('admin.*', function ($view) {
            $stats = [
                'total_users' => User::count(),
                'total_instructors' => User::where('is_instructor', true)->count(),
                'total_courses' => Course::count(),
                'active_courses' => Course::where('is_active', true)->count(),
                'total_registrations' => CourseRegistration::count(),
                'pending_registrations' => CourseRegistration::where('status', 'pending')->count(),
            ];
            
            $view->with('globalStats', $stats);
        });

        // Share instructor stats for instructor pages
        View::composer('instructor.*', function ($view) {
            if (Auth::check() && Auth::user()->is_instructor) {
                $instructor = Auth::user();
                $taughtCourses = $instructor->taughtCourses()->withCount(['registrations' => function($query) {
                    $query->where('status', 'paid');
                }])->get();

                $totalStudents = $taughtCourses->sum('registrations_count');
                $totalAssignments = 0;

                foreach ($taughtCourses as $course) {
                    $totalAssignments += $course->assignments()->count();
                }

                $instructorStats = [
                    'total_courses' => $taughtCourses->count(),
                    'total_students' => $totalStudents,
                    'total_assignments' => $totalAssignments,
                    'active_courses' => $taughtCourses->where('is_active', true)->count(),
                ];
                
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