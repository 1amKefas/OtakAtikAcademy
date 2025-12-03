<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $attributes = [
        'discount_percent' => 0,
        'current_enrollment' => 0,
        'is_active' => true,
    ];

    protected $fillable = [
        'title',
        'description',
        'type',
        'instructor_id',
        'price',
        'discount_code',
        'discount_percent',
        'min_quota',
        'max_quota',
        'current_enrollment',
        'is_active',
        'image_url',
        'duration_days',
        'start_date',
        'end_date',
        'is_rescheduled',
        'rescheduled_start_date',
        'reschedule_reason',
        'quota_not_met'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'min_quota' => 'integer',
        'max_quota' => 'integer',
        'current_enrollment' => 'integer',
        'is_active' => 'boolean',
        'duration_days' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_rescheduled' => 'boolean',
        'rescheduled_start_date' => 'date',
        'quota_not_met' => 'boolean'
    ];

    protected $appends = [
        'display_type',
        'formatted_price',
        'formatted_final_price',
        'final_price',
        'requires_instructor',
        'has_available_slots'
    ];

    /**
     * Get the modules for the course.
     */
    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    /**
     * Relasi Asisten Instruktur (Instruktur Tambahan)
     * FIXED: Menggunakan 'instructor_id' sebagai foreign key pivot, bukan 'user_id'
     */
    public function assistants()
    {
        return $this->belongsToMany(
            User::class, 
            'course_instructor', 
            'course_id', 
            'instructor_id' // <--- KUNCI PERBAIKAN ERROR SQL
        );
    }

    /**
     * Helper untuk cek apakah user adalah instruktur (baik utama maupun asisten)
     */
    public function hasInstructor(User $user)
    {
        return $this->instructor_id === $user->id || $this->assistants->contains($user->id);
    }

    /**
     * Many-to-Many: Course punya banyak instructors (Legacy/Existing relation)
     */
    public function instructors()
    {
        return $this->belongsToMany(
            User::class,
            'course_instructor',
            'course_id',
            'instructor_id'
        )->withPivot('active_duration_days', 'zoom_link', 'zoom_start_time', 'notes');
    }

    /**
     * Many-to-Many: Course punya banyak categories
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_course');
    }

    /**
     * One-to-Many: Course punya banyak ratings
     */
    public function ratings()
    {
        return $this->hasMany(CourseRating::class);
    }

    /**
     * One-to-Many: Course punya banyak certificates
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get display type attribute for user-friendly display
     */
    public function getDisplayTypeAttribute()
    {
        $mapping = [
            'online' => 'Full Online',
            'hybrid' => 'Hybrid',
            'offline' => 'Tatap Muka'
        ];
        
        return $mapping[$this->type] ?? $this->type;
    }

    /**
     * Get the instructor for the course
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id')->withDefault([
            'name' => 'Tidak tersedia',
            'email' => 'N/A'
        ]);
    }

    /**
     * Get the course registrations
     */
    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Get active registrations (paid)
     */
    public function activeRegistrations()
    {
        return $this->hasMany(CourseRegistration::class)->where('status', 'paid');
    }

    /**
     * Get pending registrations
     */
    public function pendingRegistrations()
    {
        return $this->hasMany(CourseRegistration::class)->where('status', 'pending');
    }

    /**
     * Get cancelled registrations
     */
    public function cancelledRegistrations()
    {
        return $this->hasMany(CourseRegistration::class)->where('status', 'cancelled');
    }

    /**
     * Get materials for the course
     */
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class)->orderBy('order');
    }

    /**
     * Get published materials
     */
    public function publishedMaterials()
    {
        return $this->hasMany(CourseMaterial::class)->where('is_published', true)->orderBy('order');
    }

    /**
     * Get assignments for the course
     */
    public function assignments()
    {
        return $this->hasMany(CourseAssignment::class)->orderBy('due_date');
    }

    /**
     * Get published assignments
     */
    public function publishedAssignments()
    {
        return $this->hasMany(CourseAssignment::class)->where('is_published', true)->orderBy('due_date');
    }

    /**
     * Check if course has available slots
     */
    public function hasAvailableSlots()
    {
        return $this->current_enrollment < $this->max_quota;
    }

    /**
     * Get available slots count
     */
    public function getAvailableSlotsAttribute()
    {
        return $this->max_quota - $this->current_enrollment;
    }

    /**
     * Get final price after discount
     */
    public function getFinalPriceAttribute()
    {
        if ($this->discount_percent > 0) {
            return $this->price * (1 - ($this->discount_percent / 100));
        }
        return $this->price;
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmountAttribute()
    {
        if ($this->discount_percent > 0) {
            return $this->price * ($this->discount_percent / 100);
        }
        return 0;
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAmountAttribute()
    {
        return 'Rp' . number_format($this->discount_amount, 0, ',', '.');
    }

    /**
     * Get formatted final price
     */
    public function getFormattedFinalPriceAttribute()
    {
        return 'Rp' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Get formatted original price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Check if course has discount
     */
    public function getHasDiscountAttribute()
    {
        return $this->discount_percent > 0;
    }

    /**
     * Check if course requires instructor
     */
    public function getRequiresInstructorAttribute()
    {
        return in_array($this->type, ['hybrid', 'offline']);
    }

    /**
     * Get enrollment percentage
     */
    public function getEnrollmentPercentageAttribute()
    {
        if ($this->max_quota === 0) return 0;
        return ($this->current_enrollment / $this->max_quota) * 100;
    }

    /**
     * Check if course is full
     */
    public function getIsFullAttribute()
    {
        return $this->current_enrollment >= $this->max_quota;
    }

    /**
     * Check if course is popular (more than 50% full)
     */
    public function getIsPopularAttribute()
    {
        return $this->enrollment_percentage >= 50;
    }

    /**
     * Get has_available_slots attribute for view
     */
    public function getHasAvailableSlotsAttribute()
    {
        return $this->hasAvailableSlots();
    }

    /**
     * Scope active courses - HANYA UNTUK USER
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope inactive courses
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope popular courses (more than 50% enrollment)
     */
    public function scopePopular($query)
    {
        return $query->whereRaw('current_enrollment >= max_quota * 0.5');
    }

    /**
     * Scope courses with available slots
     */
    public function scopeWithAvailableSlots($query)
    {
        return $query->whereRaw('current_enrollment < max_quota');
    }

    /**
     * Scope courses with discount
     */
    public function scopeWithDiscount($query)
    {
        return $query->where('discount_percent', '>', 0);
    }

    /**
     * Scope courses by instructor
     */
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope courses that need instructor
     */
    public function scopeRequiresInstructor($query)
    {
        return $query->whereIn('type', ['hybrid', 'offline']);
    }

    /**
     * Increment enrollment count
     */
    public function incrementEnrollment()
    {
        $this->increment('current_enrollment');
    }

    /**
     * Decrement enrollment count
     */
    public function decrementEnrollment()
    {
        $this->decrement('current_enrollment');
    }

    /**
     * Check if user is enrolled in this course
     */
    public function isEnrolledByUser($userId)
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->exists();
    }

    /**
     * Get user registration for this course
     */
    public function getUserRegistration($userId)
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get quizzes for this course
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get forum topics for this course
     */
    public function forums()
    {
        return $this->hasMany(CourseForum::class)->orderBy('created_at', 'desc');
    }

    public function getCheckoutUrlAttribute()
    {
        return route('checkout.show', $this->id);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Set default instructor_id for online courses
        static::saving(function ($course) {
            // Kita hapus logic yang memaksa instructor_id = null untuk online
            // Karena sekarang admin boleh assign instruktur untuk Full Online jika mau
            /*
            if ($course->type === 'online') {
                $course->instructor_id = null;
            }
            */
        });

        // Validate max_quota > min_quota
        static::saving(function ($course) {
            if ($course->max_quota <= $course->min_quota) {
                // throw new \Exception('Kuota maksimal harus lebih besar dari kuota minimal');
                // Sebaiknya biarkan validator controller yang handle exception, return true/false disini
            }
        });
    }
}