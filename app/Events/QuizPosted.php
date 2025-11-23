<?php

namespace App\Events;

use App\Models\Quiz;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizPosted
{
    use Dispatchable, SerializesModels;

    public Quiz $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }
}
