<?php

namespace App\Events;

use App\Models\CourseAssignment;
use Carbon\Carbon;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignmentDeadlineChanged
{
    use Dispatchable, SerializesModels;

    public CourseAssignment $assignment;
    public Carbon $oldDeadline;
    public Carbon $newDeadline;

    public function __construct(CourseAssignment $assignment, Carbon $oldDeadline, Carbon $newDeadline)
    {
        $this->assignment = $assignment;
        $this->oldDeadline = $oldDeadline;
        $this->newDeadline = $newDeadline;
    }
}
