<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\UserPolicy;

class AcceptedPoliciesTest extends TestCase
{
    use DatabaseTransactions;
    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->app['router']->get('/test-route', function () {
            return response()->json(['message' => 'Authenticated']);
        })->middleware(['jwt', 'jwt.AcceptedPolicies:8,9']);

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }


    public function test_accepted_policies_successful(): void
    {
        $policie1 = UserPolicy::create(['user_id' => $this->user->id, 'policy_id' => 8]);
        $policie2 = UserPolicy::create(['user_id' => $this->user->id, 'policy_id' => 9]);
        // $policie2 = UserPolicy::create([$this->user->id, 9]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/test-route');

        $response->assertStatus(200);
        // $response->assertJson(['message' => 'Authenticated']);
    }

    public function test_accepted_policies_fail(): void
    {
        $policie1 = UserPolicy::create(['user_id' => $this->user->id, 'policy_id' => 1]);
        $policie2 = UserPolicy::create(['user_id' => $this->user->id, 'policy_id' => 10]);
        // $policie2 = UserPolicy::create([$this->user->id, 9]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/test-route');

        $response->assertStatus(403);
        $response->assertJsonStructure(['caution', 'message']);
    }
}
