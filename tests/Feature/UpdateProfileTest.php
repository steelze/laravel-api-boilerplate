<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    public $update_profile = '/api/user';

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
     * Unauthenticated user cannot update profile
     *
     * @return void
     * @test
     */
    public function unauthenticated_user_cannot_update_profile()
    {
        $response = $this->putJson($this->update_profile);
        $response
            ->assertStatus(401)
            ->assertJson(['message' => true]);
    }

    /**
     * Authenticated user can update profile
     *
     * @return void
     * @test
     */
    public function authenticated_user_can_update_profile()
    {
        Notification::fake();
        $token = JWTAuth::fromUser($this->user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->putJson($this->update_profile, ['name' => 'Jeff Bezos', 'email' => 'jeff@bezos.com']);
        $response
            ->assertOk()
            ->assertJson(['message' => true]);
        $this->assertDatabaseHas('users', [
            'name' => 'Jeff Bezos',
            'email' => 'jeff@bezos.com',
        ]);
        $this->assertTrue('Jeff Bezos' === $this->user->fresh()->name);
        $this->assertTrue('jeff@bezos.com' === $this->user->fresh()->email);
    }
}
