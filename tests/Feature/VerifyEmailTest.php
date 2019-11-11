<?php

namespace Tests\Feature;

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
    //  * A user cannot reset password without token
    //  *
    //  * @return void
    //  * @test
    //  */
    // public function user_cannot_resend_verification_without_token()
    // {
    //     $response = $this->postJson($this->resend_verification);
    //     $response
    //         ->assertStatus(422)
    //         ->assertJsonValidationErrors(['token']);
    // }

    // /**
    //  * A user cannot reset password without password
    //  *
    //  * @return void
    //  * @test
    //  */
    // public function user_cannot_resend_verification_without_password()
    // {
    //     $response = $this->postJson($this->resend_verification);
    //     $response
    //         ->assertStatus(422)
    //         ->assertJsonValidationErrors(['password']);
    // }

    // /**
    //  * A user cannot reset password with incorrect password match
    //  *
    //  * @return void
    //  * @test
    //  */
    // public function user_cannot_resend_verification_with_incorrect_password_match()
    // {
    //     $response = $this->postJson($this->resend_verification, ['password' => 'password', 'password_confirmation' => 'drowssap']);
    //     $response
    //         ->assertStatus(422)
    //         ->assertJsonValidationErrors(['password']);
    // }

    // /**
    //  * A user cannot reset password with invalid email
    //  *
    //  * @return void
    //  * @test
    //  */
    // public function user_cannot_resend_verification_with_invalid_email()
    // {
    //     $token = Password::createToken($this->user);
    //     $response = $this->postJson($this->resend_verification, ['email' => 'invalid@app.com', 'token' => $token, 'password' => 'password', 'password_confirmation' => 'password']);
    //     $response
    //         ->assertStatus(422)
    //         ->assertJson(['message' => true]);
    // }

    // /**
    //  * A user cannot reset password with invalid token
    //  *
    //  * @return void
    //  * @test
    //  */
    // public function user_cannot_resend_verification_with_invalid_token()
    // {
    //     $token = Password::createToken($this->user);
    //     $response = $this->postJson($this->resend_verification, ['email' => 'admin@blog.com', 'token' => Str::random(64), 'password' => 'password', 'password_confirmation' => 'password']);
    //     $response
    //         ->assertStatus(422)
    //         ->assertJson(['message' => true]);
    // }

    // /**
    //  * A user can reset password
    //  *
    //  * @return void
    //  * @test
    //  */
    // public function user_can_resend_verification()
    // {
    //     $token = Password::createToken($this->user);
    //     $response = $this->postJson($this->resend_verification, ['email' => 'admin@blog.com', 'token' => $token, 'password' => 'password', 'password_confirmation' => 'password']);
    //     $response
    //         ->assertOk()
    //         ->assertJson(['message' => true]);
    //     $this->assertTrue(Hash::check('password', $this->user->fresh()->password));
    // }
}
