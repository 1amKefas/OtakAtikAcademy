<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'course_registration_id',  // ✅ Nama kolom yang benar
        'user_id',
        'course_id', 
        'amount',
        'reason',
        'bank_account',
        'status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'processed_at' // Tambahkan ini jika ada kolom processed_at
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ✅ Fix relasi - specify foreign key
    public function registration(): BelongsTo
    {
        return $this->belongsTo(CourseRegistration::class, 'course_registration_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Tambahkan relasi langsung ke course jika diperlukan
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}