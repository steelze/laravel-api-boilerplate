<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public $login_url = '/api/login';

    protected $user;

    protected $data = [
        'email' => 'admin@blog.com',
        'password' => '123456',
    ];

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
     * A user cannot login without email
     *
     * @return void
     * @test
     */
    public function user_cannot_login_without_email()
    {
        $response = $this->postJson($this->login_url, [
            'password' => '123456',
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonMissing(['access_token']);
    }

    /**
     * A user cannot login without password wrong credentials
     *
     * @return void
     * @test
     */
    public function user_cannot_login_with_wrong_credentials()
    {
        $response = $this->postJson($this->login_url, [
            'email' => 'admin@blog.com',
            'password' => '1234',
        ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => true,
            ])
            ->assertJsonMissing(['access_token']);
    }

    /**
     * A user can login
     *
     * @return void
     * @test
     */
    public function user_can_login()
    {
        $response = $this->postJson($this->login_url, $this->data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'access_token' => true,
            ]);
    }
}
