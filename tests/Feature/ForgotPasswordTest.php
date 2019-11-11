<?php

namespace Tests\Feature;

use App\Notifications\ResetPasswordNotification;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public $forgot_password = '/api/password/email';

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
     * A user cannot request password reset link without email
     *
     * @return void
     * @test
     */
    public function user_cannot_request_reset_password_link_without_email()
    {
        $response = $this->postJson($this->forgot_password);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * A user cannot request password reset link with invalid email
     *
     * @return void
     * @test
     */
    public function user_cannot_request_reset_password_link_with_invalid_email()
    {
        $response = $this->postJson($this->forgot_password, ['email' => 'invalid@app.com']);
        $response
            ->assertStatus(422)
            ->assertJson(['message' => true]);
    }

    /**
     * A user can request password reset link
     *
     * @return void
     * @test
     */
    public function user_can_request_reset_password_link()
    {
        $response = $this->postJson($this->forgot_password, ['email' => 'admin@blog.com']);
        $response
            ->assertOk()
            ->assertJson(['message' => true]);
        Notification::assertSentTo($this->user, ResetPasswordNotification::class);
    }
}
