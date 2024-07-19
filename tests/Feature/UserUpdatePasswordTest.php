<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserUpdatePasswordTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        // Tạo người dùng giả
        $this->user = User::factory()->create(['password' => Hash::make('Password11@')]);
        $this->token = JWTAuth::fromUser($this->user);
    }


    public function test_update_user_password_successfull(): void
    {
        // Dữ liệu cập nhật
        $updateData = [
            'currentPass' => 'Password11@',
            'newPass' => 'Password12@',
            'checkPass' => 'Password12@',
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/update-user-password/{$this->user->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
    }

    public function test_wrong_password(): void
    {
        // Dữ liệu cập nhật
        $updateData = [
            'currentPass' => 'Password12@',
            'newPass' => 'Password12@',
            'checkPass' => 'Password12@',
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/update-user-password/{$this->user->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJsonStructure(['message']);
    }

    public function test_invalid_data(): void
    {
        // Dữ liệu cập nhật
        $updateData = [
            'currentPass' => 'Password12@',
            'newPass' => '123456789',
            'checkPass' => '123123123',
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/update-user-password/{$this->user->id}", $updateData);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message']);
    }

    public function test_user_not_found(): void
    {
        // Dữ liệu cập nhật
        $updateData = [
            'currentPass' => 'Password12@',
            'newPass' => 'Password12@',
            'checkPass' => 'Password12@',
        ];
        $userId = 0;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/update-user-password/{$userId}", $updateData);

        $response->assertStatus(404);
        $response->assertJsonStructure(['message']);
    }
}
