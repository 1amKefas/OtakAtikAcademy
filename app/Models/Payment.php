<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'gross_amount',
        'payment_type',
        'transaction_status',
        'transaction_id',
        'transaction_time',
        'settlement_time',
        'status_code',
        'status_message',
        'payment_data'
    ];

    protected $casts = [
        'payment_data' => 'array',
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime'
    ];

    /**
     * Get the user that owns the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the payment
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get related registration
     */
    public function registration()
    {
        return $this->hasOne(CourseRegistration::class, 'order_id', 'order_id');
    }

    /**
     * Check if payment is successful
     */
    public function isPaid()
    {
        return in_array($this->transaction_status, ['capture', 'settlement']);
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return in_array($this->transaction_status, ['pending', 'authorize']);
    }

    /**
     * Check if payment is failed
     */
    public function isFailed()
    {
        return in_array($this->transaction_status, ['deny', 'cancel', 'expire']);
    }

    /**
     * Generate order ID
     */
    public static function generateOrderId()
    {
        return 'OTK-' . date('Ymd') . '-' . strtoupper(uniqid());
    }
}