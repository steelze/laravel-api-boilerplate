<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use Illuminate\Support\Str;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public $reset_password = '/api/password/reset';

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'name' => 'Jeffery Way',
            'email' => 'admin@blog.com',
            'password' => Hash::make('123456'),
        ]);
        Notification::fake();
    }


    /**
     * A user cannot reset password without email
     *
     * @return void
     * @test
     */
    public function user_cannot_reset_password_without_email()
    {
        $response = $this->postJson($this->reset_password);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * A user cannot reset password without token
     *
     * @return void
     * @test
     */
    public function user_cannot_reset_password_without_token()
    {
        $response = $this->postJson($this->reset_password);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    }

    /**
     * A user cannot reset password without password
     *
     * @return void
     * @test
     */
    public function user_cannot_reset_password_without_password()
    {
        $response = $this->postJson($this->reset_password);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * A user cannot reset password with incorrect password match
     *
     * @return void
     * @test
     */
    public function user_cannot_reset_password_with_incorrect_password_match()
    {
        $response = $this->postJson($this->reset_password, ['password' => 'password', 'password_confirmation' => 'drowssap']);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * A user cannot reset password with invalid email
     *
     * @return void
     * @test
     */
    public function user_cannot_reset_password_with_invalid_email()
    {
        $token = Password::createToken($this->user);
        $response = $this->postJson($this->reset_password, ['email' => 'invalid@app.com', 'token' => $token, 'password' => 'password', 'password_confirmation' => 'password']);
        $response
            ->assertStatus(422)
            ->assertJson(['message' => true]);
    }

    /**
     * A user cannot reset password with invalid token
     *
     * @return void
     * @test
     */
    public function user_cannot_reset_password_with_invalid_token()
    {
        $token = Password::createToken($this->user);
        $response = $this->postJson($this->reset_password, ['email' => 'admin@blog.com', 'token' => Str::random(64), 'password' => 'password', 'password_confirmation' => 'password']);
        $response
            ->assertStatus(422)
            ->assertJson(['message' => true]);
    }

    /**
     * A user can reset password
     *
     * @return void
     * @test
     */
    public function user_can_reset_password()
    {
        $token = Password::createToken($this->user);
        $response = $this->postJson($this->reset_password, ['email' => 'admin@blog.com', 'token' => $token, 'password' => 'password', 'password_confirmation' => 'password']);
        $response
            ->assertOk()
            ->assertJson(['message' => true]);
        $this->assertTrue(Hash::check('password', $this->user->fresh()->password));
    }
}
