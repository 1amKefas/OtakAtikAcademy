<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\CourseEnrolled;
use App\Events\AssignmentPosted;
use App\Events\AssignmentDeadlineChanged;
use App\Events\QuizPosted;
use App\Events\MaterialPosted;
use App\Listeners\CreateCourseEnrolledNotification;
use App\Listeners\NotifyAssignmentPosted;
use App\Listeners\NotifyAssignmentDeadlineChanged;
use App\Listeners\NotifyQuizPosted;
use App\Listeners\NotifyMaterialPosted;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CourseEnrolled::class => [
            CreateCourseEnrolledNotification::class,
        ],
        AssignmentPosted::class => [
            NotifyAssignmentPosted::class,
        ],
        AssignmentDeadlineChanged::class => [
            NotifyAssignmentDeadlineChanged::class,
        ],
        QuizPosted::class => [
            NotifyQuizPosted::class,
        ],
        MaterialPosted::class => [
            NotifyMaterialPosted::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}