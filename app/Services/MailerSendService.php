<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Exception;

class MailerSendService
{
    private $fromEmail;
    private $fromName;

    public function __construct()
    {
        $this->fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@otakatik-academy.com');
        $this->fromName = env('MAIL_FROM_NAME', 'OtakAtik Academy');
    }

    /**
     * Send email using Laravel Mail (SMTP configured)
     */
    public function sendEmail(string $recipientEmail, string $recipientName, string $subject, string $htmlContent, ?string $textContent = null)
    {
        try {
            Mail::send([], [], function (Message $message) use ($recipientEmail, $recipientName, $subject, $htmlContent) {
                $message->from($this->fromEmail, $this->fromName)
                    ->to($recipientEmail, $recipientName)
                    ->subject($subject)
                    ->html($htmlContent);
            });

            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail(string $email, string $name, string $verificationUrl)
    {
        $subject = 'Verify Your Email Address';
        
        $htmlContent = view('emails.verify-email', [
            'name' => $name,
            'verificationUrl' => $verificationUrl
        ])->render();

        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email, string $name, string $resetUrl)
    {
        $subject = 'Reset Your Password';
        
        $htmlContent = view('emails.reset-password', [
            'name' => $name,
            'resetUrl' => $resetUrl
        ])->render();

        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail(string $email, string $name)
    {
        $subject = 'Welcome to OtakAtik Academy';
        
        $htmlContent = view('emails.welcome', [
            'name' => $name
        ])->render();

        return $this->sendEmail($email, $name, $subject, $htmlContent);
    }
}
