<?php

namespace Tests\Feature;

use App\Notifications\EmailVerificationNotification;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public $register_url = '/api/register';

    /**
     * Cannot register user with no data passed
     *
     * @test
     * @return void
     */
    public function user_cannot_register_without_data()
    {
        $response = $this->postJson($this->register_url);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Cannot register user with empty name passed
     *
     * @test
     * @return void
     */
    public function user_cannot_register_with_empty_name()
    {
        $response = $this->postJson($this->register_url, ['name' => '', 'email' => 'user@app.com', 'password' => '12345678', 'password_confirmation' => '12345678', ]);
       $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['email', 'password']);
    }

    /**
     * Cannot register user with empty email passed
     *
     * @test
     * @return void
     */
    public function user_cannot_register_with_empty_email()
    {
        $response = $this->postJson($this->register_url, ['name' => 'Jeffery Way', 'email' => '', 'password' => '12345678', 'password_confirmation' => '12345678', ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonMissingValidationErrors(['name', 'password']);
    }

    /**
     * Cannot register user with empty password passed
     *
     * @test
     * @return void
     */
    public function user_cannot_register_with_empty_password()
    {
        $response = $this->postJson($this->register_url, ['name' => 'Jeffery Way', 'email' => 'user@app.com', 'password' => '',]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJsonMissingValidationErrors(['name', 'email']);
    }

    /**
     * Cannot register user with incorrect password match
     *
     * @test
     * @return void
     */
    public function user_cannot_register_with_incorrect_password_match()
    {
        $response = $this->postJson($this->register_url, ['name' => 'Jeffery Way', 'email' => 'user@app.com', 'password' => '12345678', 'password_confirmation' => '87654321', ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJsonMissingValidationErrors(['name', 'email']);
    }

    /**
     * Register user with correct details
     *
     * @test
     * @return void
     */
    public function user_can_register()
    {
        Notification::fake();
        $response = $this->postJson($this->register_url, ['name' => 'Jeffery Way', 'email' => 'user@app.com', 'password' => '12345678', 'password_confirmation' => '12345678', ]);
        $response->assertCreated();
        $user = User::where('email', 'user@app.com')->first();
        Notification::assertSentTo($user, EmailVerificationNotification::class);
    }
}
