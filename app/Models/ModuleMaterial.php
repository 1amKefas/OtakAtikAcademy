<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleMaterial extends Model
{
    protected $table = 'module_materials';
    
    protected $fillable = [
        'module_id',
        'title',
        'description',
        'type',
        'file_url',
        'external_url',
        'duration_minutes',
        'sort_order'
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }
}
