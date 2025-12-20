<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CourseAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'module_id',
        'title',
        'announcement_date',
        'announcement_time',
        'day_of_week',
        'type',
        'description',
        'zoom_link',
    ];

    protected $casts = [
        // Don't cast announcement_date as 'date' since we have separate announcement_time field
        // Casting to 'date' causes Carbon to format as Y-m-d H:i:s which breaks concatenation with announcement_time
    ];

    /**
     * Relasi ke Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relasi ke Module
     */
    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    /**
     * Accessor untuk mendapatkan hari dalam format Indonesia
     */
    public function getDayOfWeekAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Jika belum ada, generate dari tanggal
        if ($this->announcement_date) {
            return $this->generateDayOfWeek($this->announcement_date);
        }

        return null;
    }

    /**
     * Generate nama hari dari tanggal (Indonesia)
     */
    public static function generateDayOfWeek($date)
    {
        $hari = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
        ];

        $carbonDate = is_string($date) ? Carbon::parse($date) : $date;
        return $hari[$carbonDate->dayOfWeek];
    }

    /**
     * Mutator untuk set hari otomatis dari tanggal
     */
    public function setAnnouncementDateAttribute($value)
    {
        $this->attributes['announcement_date'] = $value;
        $this->attributes['day_of_week'] = self::generateDayOfWeek($value);
    }
}
