<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserLoginTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     */
    public function test_login_successful(): void
    {
        $user = User::factory()->create(['password' => 'testpassword1122']);

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'testpassword1122',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_login_fail(): void
    {
        $user = User::factory()->create(['password' => 'testpassword1122']);

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'testpassword11223',
        ]);

        $response->assertStatus(400);
    }
}
