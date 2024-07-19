<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\WithFaker;

class JwtMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->app['router']->get('/test-route', function () {
            return response()->json(['message' => 'Authenticated']);
        })->middleware('jwt');
    }


    public function test_valid_jwt_token(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Gửi request và kiểm tra phản hồi
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/test-route');

        $response->assertStatus(200);
        // $response->assertJson(['message' => 'Authenticated']);
    }

    public function test_invalid_jwt_token(): void
    {
        $invalidToken = 'invalid_token';

        // Gửi request và kiểm tra phản hồi
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .    $invalidToken,
        ])->getJson('/api/test-route');

        $response->assertStatus(401);
    }
}
