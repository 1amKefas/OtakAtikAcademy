<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'completion_date',
        'instructor_name',
        'instructor_title',
        'instructor_company',
        'instructor_signature_path',
        'verification_code',
        'is_downloaded',
        'downloaded_at'
    ];

    protected $casts = [
        'completion_date' => 'date',
        'downloaded_at' => 'datetime',
        'is_downloaded' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Generate unique certificate number
     */
    public static function generateCertificateNumber()
    {
        $prefix = strtoupper(substr(md5(time() . rand()), 0, 8));
        return $prefix;
    }

    /**
     * Generate unique verification code for QR
     */
    public static function generateVerificationCode()
    {
        return strtoupper(uniqid('CERT', true));
    }
}