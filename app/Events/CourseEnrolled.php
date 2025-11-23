<?php

namespace App\Events;

use App\Models\CourseRegistration;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseEnrolled
{
    use Dispatchable, SerializesModels;

    public CourseRegistration $registration;

    public function __construct(CourseRegistration $registration)
    {
        $this->registration = $registration;
    }
}
