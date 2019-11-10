<?php

namespace Tests\Feature;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
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
    public function it_will_not_register_a_user_with_no_data_passed()
    {
        $response = $this->postJson($this->register_url);
        $response
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Cannot register user with empty name passed
     *
     * @test
     * @return void
     */
    public function it_will_not_register_a_user_with_empty_name_passed()
    {
        $response = $this->postJson($this->register_url, ['name' => '', 'email' => 'user@app.com', 'password' => '12345678', 'password_confirmation' => '12345678', ]);
        $response
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJsonMissingValidationErrors(['email', 'password']);
    }

    /**
     * Cannot register user with empty email passed
     *
     * @test
     * @return void
     */
    public function it_will_not_register_a_user_with_empty_email_passed()
    {
        $response = $this->postJson($this->register_url, ['name' => 'Jeffery Way', 'email' => '', 'password' => '12345678', 'password_confirmation' => '12345678', ]);
        $response
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
        $response->assertJsonMissingValidationErrors(['name', 'password']);
    }

    /**
     * Cannot register user with empty password passed
     *
     * @test
     * @return void
     */
    public function it_will_not_register_a_user_with_empty_password_passed()
    {
        $response = $this->postJson($this->register_url, ['name' => 'Jeffery Way', 'email' => 'user@app.com', 'password' => '',]);
        $response
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
        $response->assertJsonMissingValidationErrors(['name', 'email']);
    }

    /**
     * Cannot register user with incorrect password match
     *
     * @test
     * @return void
     */
    public function it_will_not_register_a_user_with_incorrect_password_match()
    {
        $response = $this->postJson($this->register_url, ['name' => 'Jeffery Way', 'email' => 'user@app.com', 'password' => '12345678', 'password_confirmation' => '87654321', ]);
        $response
            ->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
        $response->assertJsonMissingValidationErrors(['name', 'email']);
    }

    /**
     * Register user with correct details
     *
     * @test
     * @return void
     */
    public function it_will_register_a_user_with_correct_data()
    {
        Event::fake();
        $response = $this->postJson($this->register_url, ['name' => 'Jeffery Way', 'email' => 'user@app.com', 'password' => '12345678', 'password_confirmation' => '12345678', ]);
        $response->assertCreated();
        Event::assertDispatched(Registered::class);
    }
}
