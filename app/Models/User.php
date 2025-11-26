<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id', // Google OAuth ID
        'is_admin',
        'is_instructor',
        'age_range',
        'education_level',
        'education_name',
        'location',
        'phone',
        'date_of_birth',
        'profile_picture',
        'bio',
        'expertise',
        'notify_assignment_posted',
        'notify_deadline_reminder',
        'notify_quiz_posted',
        'notify_material_posted',
        'notify_forum_reply',
        'notify_submission_graded',
        'profile_visibility',
        'show_achievements',
        'allow_direct_messages',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_instructor' => 'boolean',
            'date_of_birth' => 'date',
            'deleted_at' => 'datetime',
            'notify_assignment_posted' => 'boolean',
            'notify_deadline_reminder' => 'boolean',
            'notify_quiz_posted' => 'boolean',
            'notify_material_posted' => 'boolean',
            'notify_forum_reply' => 'boolean',
            'notify_submission_graded' => 'boolean',
            'show_achievements' => 'boolean',
            'allow_direct_messages' => 'boolean',
        ];
    }

    /**
     * Get the course registrations for the user.
     */
    public function courseRegistrations()
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Get the courses taught by the user (if instructor)
     */
    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get assignment submissions for the user
     */
    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Get notifications for the user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check if user is instructor
     */
    public function isInstructor(): bool
    {
        return $this->is_instructor;
    }

    /**
     * Get user's initial for avatar
     */
    public function getInitialAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Get user's age from date_of_birth
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->diffInYears(now());
    }

    /**
     * Get formatted created date
     */
    public function getJoinedDateAttribute(): string
    {
        return $this->created_at->format('M d, Y');
    }

    protected $appends = [
        'initial',
        'age',
        'joined_date',
        'status_badge_class',
        'status_text',
        'course_count',
        'enrolled_courses',
        'pending_registrations'
    ];
    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->is_admin) {
            return 'bg-purple-100 text-purple-800';
        } elseif ($this->is_instructor) {
            return 'bg-blue-100 text-blue-800';
        } else {
            return 'bg-green-100 text-green-800';
        }
    }

    /**
     * Get user's status text
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->is_admin) {
            return 'Admin';
        } elseif ($this->is_instructor) {
            return 'Instructor';
        } else {
            return 'User';
        }
    }

    /**
     * Get user's course count
     */
    public function getCourseCountAttribute(): int
    {
        return $this->courseRegistrations->count();
    }

    /**
     * Get user's enrolled courses (approved)
     */
    public function getEnrolledCoursesAttribute()
    {
        return $this->courseRegistrations()->where('status', 'paid')->with('course')->get();
    }

    /**
     * Get user's pending course registrations
     */
    public function getPendingRegistrationsAttribute()
    {
        return $this->courseRegistrations()->where('status', 'pending')->with('course')->get();
    }

    /**
     * Scope filter by age range
     */
    public function scopeByAgeRange($query, $range)
    {
        return $query->where('age_range', $range);
    }

    /**
     * Scope filter by education level
     */
    public function scopeByEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }

    /**
     * Scope filter by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', '%'.$location.'%');
    }

    /**
     * Scope instructors only
     */
    public function scopeInstructors($query)
    {
        return $query->where('is_instructor', true);
    }

    /**
     * Send the email verification notification.
     * Custom implementation untuk mengirim verification link ke correct domain
     */
    public function sendEmailVerificationNotification()
    {
        // Determine the correct base URL for verification link
        // Priority: APP_URL (custom domain) > VERCEL_URL (Vercel preview) > APP_URL fallback
        $baseUrl = rtrim(config('app.url'), '/');
        
        // If APP_URL is still localhost atau example.com di production, use Vercel URL
        if ((env('APP_ENV') === 'production' || env('APP_ENV') === 'staging') 
            && (strpos($baseUrl, 'localhost') !== false || strpos($baseUrl, 'example.com') !== false)) {
            if (env('VERCEL_URL')) {
                $baseUrl = 'https://' . env('VERCEL_URL');
            }
        }

        // Generate verification URL dengan proper base URL
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            \Illuminate\Support\Carbon::now()->addMinutes(60), // 60 minutes expiration
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->email),
            ]
        );
        
        // Replace base URL to ensure consistency
        $verificationUrl = preg_replace(
            '~^https?://[^/]+~',
            $baseUrl,
            $verificationUrl
        );

        \Illuminate\Support\Facades\Mail::send([], [], function (\Illuminate\Mail\Message $message) use ($verificationUrl) {
            $message->from(env('MAIL_FROM_ADDRESS', 'noreply@otakatik-academy.com'), env('MAIL_FROM_NAME', 'OtakAtik Academy'))
                ->to($this->email, $this->name)
                ->subject('Verify Your Email Address')
                ->html(view('emails.verify-email', ['name' => $this->name, 'verificationUrl' => $verificationUrl])->render());
        });
    }
}