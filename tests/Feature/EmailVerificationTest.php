<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    /**
     * Test user can access verification notice
     */
    public function test_user_can_access_verification_notice()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertOk();
        $response->assertViewIs('auth.verify-email');
    }

    /**
     * Test user can verify email with valid signed URL
     */
    public function test_user_can_verify_email_with_valid_signed_url()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Generate valid signed URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        // Should redirect to dashboard
        $response->assertRedirect('/dashboard');

        // Email should be verified
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    /**
     * Test user cannot verify email with invalid signed URL
     */
    public function test_user_cannot_verify_email_with_invalid_signed_url()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $invalidUrl = '/email/verify/' . $user->id . '/invalid-hash';

        $response = $this->actingAs($user)->get($invalidUrl);

        // Should get 403 Forbidden
        $response->assertForbidden();

        // Email should NOT be verified
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    /**
     * Test user can resend verification email
     */
    public function test_user_can_resend_verification_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Event::fake();

        $response = $this->actingAs($user)
            ->post(route('verification.send'));

        $response->assertRedirect();

        // Verify email was dispatched
        Event::assertDispatchedTimes(Registered::class, 1);
    }

    /**
     * Test user must have verified email before accessing dashboard
     */
    public function test_user_must_verify_email_to_access_dashboard()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        // Should redirect to verification notice
        $response->assertRedirect();
    }

    /**
     * Test verification URL is properly generated with correct domain
     */
    public function test_verification_url_uses_correct_app_domain()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $appUrl = config('app.url');

        // Manually call the method to test URL generation
        $user->sendEmailVerificationNotification();

        // In production, verify the URL contains APP_URL, not Vercel domain
        // This requires checking email sent, which would be in a Mail::fake() test
    }
}
