<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Gym;
use App\Mail\AdminOtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected $gym;
    protected $admin;
    protected $superAdmin;
    protected $trainer;
    protected $member;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable Stripe during tests
        config(['services.stripe.secret' => null]);

        $this->gym = Gym::create([
            'name' => 'OTP Test Gym',
            'email' => 'otp.gym@gmail.com',
            'subscription_status' => 'active',
        ]);

        $this->admin = User::create([
            'name' => 'Gym Admin',
            'email' => 'admin@gymglow.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'gym_id' => $this->gym->id,
            'status' => 'active',
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gymglow.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'gym_id' => $this->gym->id,
            'status' => 'active',
        ]);

        $this->trainer = User::create([
            'name' => 'Trainer Joe',
            'email' => 'trainer@gymglow.com',
            'password' => Hash::make('password'),
            'role' => 'trainer',
            'gym_id' => $this->gym->id,
            'status' => 'active',
        ]);

        $this->member = User::create([
            'name' => 'Member Alice',
            'email' => 'member@gymglow.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'gym_id' => $this->gym->id,
            'status' => 'active',
        ]);
    }

    /**
     * Test admin login redirects to OTP verification page and dispatches email.
     */
    public function test_admin_login_redirects_to_otp_page_and_sends_email(): void
    {
        Mail::fake();

        $response = $this->post('/login', [
            'email' => 'admin@gymglow.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('auth.otp.verify'));
        $this->assertGuest(); // Not fully logged in yet
        $this->assertEquals($this->admin->id, session('2fa_user_id'));

        Mail::assertSent(AdminOtpMail::class, function ($mail) {
            return $mail->hasTo('admin@gymglow.com') && $mail->otp >= 100000 && $mail->otp <= 999999;
        });
    }

    /**
     * Test super admin login redirects to OTP verification page and dispatches email.
     */
    public function test_super_admin_login_redirects_to_otp_page_and_sends_email(): void
    {
        Mail::fake();

        $response = $this->post('/login', [
            'email' => 'superadmin@gymglow.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('auth.otp.verify'));
        $this->assertGuest();
        $this->assertEquals($this->superAdmin->id, session('2fa_user_id'));

        Mail::assertSent(AdminOtpMail::class, function ($mail) {
            return $mail->hasTo('superadmin@gymglow.com');
        });
    }

    /**
     * Test successful OTP verification fully logs in the admin.
     */
    public function test_successful_otp_verification(): void
    {
        Mail::fake();

        $this->post('/login', [
            'email' => 'admin@gymglow.com',
            'password' => 'password',
        ]);

        $plainOtp = null;
        Mail::assertSent(AdminOtpMail::class, function ($mail) use (&$plainOtp) {
            $plainOtp = $mail->otp;
            return true;
        });

        $this->assertNotNull($plainOtp);

        $response = $this->post(route('auth.otp.verify.post'), [
            'otp' => (string) $plainOtp,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->admin);

        // Verify fields are cleared
        $this->admin->refresh();
        $this->assertNull($this->admin->otp_code);
        $this->assertNull($this->admin->otp_expires_at);
        $this->assertEquals(0, $this->admin->otp_attempts);
    }

    /**
     * Test invalid OTP verification increment attempts.
     */
    public function test_failed_otp_verification(): void
    {
        Mail::fake();

        $this->post('/login', [
            'email' => 'admin@gymglow.com',
            'password' => 'password',
        ]);

        $response = $this->post(route('auth.otp.verify.post'), [
            'otp' => '000000', // incorrect OTP
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();

        $this->admin->refresh();
        $this->assertEquals(1, $this->admin->otp_attempts);
    }

    /**
     * Test OTP attempts limit blocks and redirects back to login page.
     */
    public function test_otp_attempts_limit_blocks_admin(): void
    {
        Mail::fake();

        $this->post('/login', [
            'email' => 'admin@gymglow.com',
            'password' => 'password',
        ]);

        // Submit incorrect OTP 3 times
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post(route('auth.otp.verify.post'), [
                'otp' => '000000',
            ]);
        }

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Too many incorrect attempts. Please log in again.');
        $this->assertGuest();

        // Check columns are cleared
        $this->admin->refresh();
        $this->assertNull($this->admin->otp_code);
        $this->assertEquals(0, $this->admin->otp_attempts);
        $this->assertFalse(session()->has('2fa_user_id'));
    }

    /**
     * Test expired OTP fails verification.
     */
    public function test_expired_otp_verification(): void
    {
        Mail::fake();

        $this->post('/login', [
            'email' => 'admin@gymglow.com',
            'password' => 'password',
        ]);

        $plainOtp = null;
        Mail::assertSent(AdminOtpMail::class, function ($mail) use (&$plainOtp) {
            $plainOtp = $mail->otp;
            return true;
        });

        // Travel 6 minutes into the future to expire the OTP
        $this->travel(6)->minutes();

        $response = $this->post(route('auth.otp.verify.post'), [
            'otp' => $plainOtp,
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();
    }

    /**
     * Test OTP resend throttling and regeneration.
     */
    public function test_resend_otp_throttling_and_regeneration(): void
    {
        Mail::fake();

        $this->post('/login', [
            'email' => 'admin@gymglow.com',
            'password' => 'password',
        ]);

        // Attempt immediate resend (less than 60s)
        $response = $this->post(route('auth.otp.resend'));
        $response->assertSessionHasErrors('resend');

        // Simulate cooldown lapse by traveling 65 seconds into the future
        $this->travel(65)->seconds();

        // Try resending again
        $response = $this->post(route('auth.otp.resend'));
        $response->assertSessionHas('success');

        Mail::assertSent(AdminOtpMail::class, 2); // 2 emails sent total
    }

    /**
     * Test trainers bypass 2FA and login directly.
     */
    public function test_trainer_bypasses_2fa(): void
    {
        $response = $this->post('/login', [
            'email' => 'trainer@gymglow.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->trainer);
    }

    /**
     * Test members bypass 2FA and login directly.
     */
    public function test_member_bypasses_2fa(): void
    {
        $response = $this->post('/login', [
            'email' => 'member@gymglow.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->member);
    }

    /**
     * Test guest cannot access verification route.
     */
    public function test_guest_cannot_access_otp_verification_route(): void
    {
        $response = $this->get(route('auth.otp.verify'));
        $response->assertRedirect(route('login'));
    }
}
