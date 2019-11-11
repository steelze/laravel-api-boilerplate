<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public $logout_url = '/api/logout';

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
     * Unauthenticated user cannot logout
     *
     * @return void
     * @test
     */
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson($this->logout_url);
        $response
            ->assertUnauthorized();
    }

    /**
     * Authenticated user can logout
     *
     * @return void
     * @test
     */
    public function authenticated_user_can_logout()
    {
        $token = JWTAuth::fromUser($this->user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson($this->logout_url);
        $response
            ->assertNoContent();
    }
}
