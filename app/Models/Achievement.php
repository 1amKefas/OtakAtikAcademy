<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Achievement extends Model
{
    protected $fillable = [
        'slug',
        'name_en',
        'name_id',
        'description_en',
        'description_id',
        'icon',
        'color',
        'requirement_type',
        'requirement_value',
    ];

    /**
     * Get the name in current locale
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => app()->getLocale() === 'id' ? $this->name_id : $this->name_en,
        );
    }

    /**
     * Get the description in current locale
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => app()->getLocale() === 'id' ? $this->description_id : $this->description_en,
        );
    }

    /**
     * Users who earned this achievement
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withTimestamps()
            ->withPivot('earned_at');
    }
}
