<?php

namespace App\Events;

use App\Models\CourseAssignment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignmentPosted
{
    use Dispatchable, SerializesModels;

    public CourseAssignment $assignment;

    public function __construct(CourseAssignment $assignment)
    {
        $this->assignment = $assignment;
    }
}
