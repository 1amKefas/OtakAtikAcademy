<?php

namespace App\Events;

use App\Models\CourseMaterial;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaterialPosted
{
    use Dispatchable, SerializesModels;

    public CourseMaterial $material;

    public function __construct(CourseMaterial $material)
    {
        $this->material = $material;
    }
}
