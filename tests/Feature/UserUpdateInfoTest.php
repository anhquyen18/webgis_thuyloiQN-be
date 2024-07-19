<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserUpdateInfoTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        // Tạo người dùng giả
        $this->user = User::factory()->create();

        $this->token = JWTAuth::fromUser($this->user);
    }


    public function test_update_user_info_successfull(): void
    {
        // Dữ liệu cập nhật
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone_number' => '1234567890',
            'birthday' => $this->user->birthday,
            'gender' => 'Nữ'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/update-user-info/{$this->user->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
    }

    public function test_miss_data(): void
    {
        // Thiếu hoặc sai dữ liệu từ client
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone_number' => '1234567890',
            'gender' => 'Nữ'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/update-user-info/{$this->user->id}", $updateData);

        $response->assertStatus(500);
        $response->assertJsonStructure(['message', 'caution']);
    }

    public function test_miss_user_id(): void
    {
        // Không tìm thấy user
        $userId = 0;
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone_number' => '1234567890',
            'birthday' => $this->user->birthday,
            'gender' => 'Nữ'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/update-user-info/{$userId}", $updateData);

        $response->assertStatus(404);
        $response->assertJsonStructure(['message', 'caution']);
    }
}
