<?php

namespace Tests\Feature;

use App\Notifications\EmailVerificationNotification;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    public $resend_verification = '/api/email/resend';

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'name' => 'Jeffery Way',
            'email' => 'admin@blog.com',
            'password' => Hash::make('123456'),
        ]);
    }


    /**
     * Unauthenticated user cannot resend email verification
     *
     * @return void
     * @test
     */
    public function unauthenticated_user_cannot_resend_email_verification()
    {
        $response = $this->getJson($this->resend_verification);
        $response
            ->assertStatus(401)
            ->assertJson(['message' => true]);
    }

    /**
     * Authenticated user can request resend email verification
     *
     * @return void
     * @test
     */
    public function authenticated_user_can_request_resend_email_verification()
    {
        Notification::fake();
        $token = JWTAuth::fromUser($this->user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson($this->resend_verification);
        $response
            ->assertOk()
            ->assertJson(['message' => true]);
    }

    // /**
    //  * User can verify email
    //  *
    //  * @return void
    //  * @test
    //  */
    // public function user_can_verify_email()
    // {
    //     Notification::fake();
    //     $notification = new EmailVerificationNotification();
    //     $uri = str_ireplace('http://localhost', '', $notification->verificationUrl($this->user));
    //     $response = $this->followingRedirects()
    //         ->get($uri);
    //         // ->assertStatus(200);
    // }
}
