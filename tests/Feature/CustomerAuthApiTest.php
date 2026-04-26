<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_receive_an_api_token(): void
    {
        $response = $this->postJson('/api/customer/register', [
            'full_name' => 'Customer One',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'phone' => '0999999999',
            'country' => 'Syria',
            'preferred_locale' => 'en',
            'device_name' => 'iPhone',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('customer.full_name', 'Customer One')
            ->assertJsonPath('customer.email', 'customer@example.com')
            ->assertJsonPath('token_type', 'Bearer');

        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();

        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'iPhone',
        ]);
    }

    public function test_customer_can_log_in_and_view_their_profile(): void
    {
        $customer = $this->createCustomer();

        $loginResponse = $this->postJson('/api/customer/login', [
            'email' => $customer->user->email,
            'password' => 'password',
            'device_name' => 'Android',
        ]);

        $token = $loginResponse->json('token');

        $loginResponse
            ->assertOk()
            ->assertJsonPath('customer.full_name', $customer->full_name);

        $this->getJson('/api/customer/me', [
            'Authorization' => 'Bearer '.$token,
        ])
            ->assertOk()
            ->assertJsonPath('data.email', $customer->user->email)
            ->assertJsonPath('data.full_name', $customer->full_name);
    }

    public function test_registration_rejects_non_numeric_phone(): void
    {
        $this->postJson('/api/customer/register', [
            'full_name' => 'Customer One',
            'email' => 'customer@example.com',
            'password' => 'password123',
            'phone' => '0999-abc',
            'country' => 'Syria',
            'preferred_locale' => 'en',
            'device_name' => 'iPhone',
        ])->assertStatus(422)->assertJsonValidationErrors(['phone']);
    }

    public function test_logout_revokes_the_current_api_token(): void
    {
        $customer = $this->createCustomer();

        $loginResponse = $this->postJson('/api/customer/login', [
            'email' => $customer->user->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('token');
        [$tokenId] = explode('|', $token, 2);

        $this->postJson('/api/customer/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ])->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    public function test_profile_requires_authentication(): void
    {
        $this->getJson('/api/customer/me')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    protected function createCustomer(): Customer
    {
        $user = User::factory()->create([
            'name' => 'Customer One',
            'email' => 'customer@example.com',
            'password' => 'password',
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);

        return Customer::query()->create([
            'user_id' => $user->id,
            'full_name' => 'Customer One',
            'phone' => '0999999999',
            'country' => 'Syria',
            'preferred_locale' => 'en',
            'is_active' => true,
        ])->load('user');
    }
}
