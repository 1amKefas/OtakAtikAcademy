<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Hapus sementara SoftDeletes sampai migration selesai
// use Illuminate\Database\Eloquent\SoftDeletes;

class CourseRegistration extends Model
{
    use HasFactory; // Hapus: , SoftDeletes

    protected $table = 'course_registrations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'nama_lengkap',
        'ttl',
        'tempat_tinggal',
        'gender',
        'price',
        'final_price',
        'discount_code',
        'payment_method',
        'status',
        'progress',
        'total_learning_seconds',
        'paid_at',
        'enrolled_at'
    ];

    // pastikan Eloquent me-cast harga sebagai integer
    protected $casts = [
        'price' => 'integer',
        'final_price' => 'integer',
        'paid_at' => 'datetime',
        'enrolled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the course registration.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Pengguna Dihapus',
            'email' => 'N/A'
        ]);
    }

    /**
     * Get the course that owns the registration.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the refund for this registration.
     */
    public function refund()
    {
        return $this->hasOne(Refund::class, 'course_registration_id');
    }

    /**
     * Get formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get formatted final price attribute.
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        return 'Rp' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Get status badge class attribute.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'paid' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get progress percentage attribute.
     */
    public function getProgressPercentageAttribute(): string
    {
        return number_format($this->progress, 1) . '%';
    }

    /**
     * Check if course is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->progress >= 100;
    }

    /**
     * Scope approved registrations.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope pending registrations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope cancelled registrations.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope by user ID.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by course ID.
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Check if registration is active.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'paid' && $this->progress < 100;
    }

    /**
     * Check if registration is completed.
     */
    public function getIsFullyCompletedAttribute(): bool
    {
        return $this->status === 'paid' && $this->progress >= 100;
    }

    /**
     * Get days since enrolled.
     */
    public function getDaysSinceEnrolledAttribute(): ?int
    {
        return $this->enrolled_at ? $this->enrolled_at->diffInDays(now()) : null;
    }

    /**
     * Get discount amount.
     */
    public function getDiscountAmountAttribute(): float
    {
        return $this->price - $this->final_price;
    }

    /**
     * Get formatted discount amount.
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return 'Rp' . number_format($this->discount_amount, 0, ',', '.');
    }

    // Method For Course Class
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }
}