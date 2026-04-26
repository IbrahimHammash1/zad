<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Basket;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBasketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_basket_endpoints_require_authentication(): void
    {
        $this->getJson('/api/customer/baskets')->assertUnauthorized();
        $this->getJson('/api/customer/baskets/family-basket')->assertUnauthorized();
    }

    public function test_it_lists_only_baskets_available_for_customer_ordering(): void
    {
        $token = $this->authenticateCustomer();

        $availableBasket = Basket::query()->create([
            'name' => 'Family Basket',
            'slug' => 'family-basket',
            'description' => 'A complete basket.',
            'fixed_price' => 25.50,
            'is_active' => true,
        ]);

        $store = Store::query()->create([
            'name' => 'Main Store',
            'phone' => '0111111111',
            'address' => 'Damascus',
            'is_active' => true,
        ]);

        $availableBasket->stores()->attach($store);

        Basket::query()->create([
            'name' => 'Hidden Basket',
            'slug' => 'hidden-basket',
            'fixed_price' => 10,
            'is_active' => false,
        ]);

        Basket::query()->create([
            'name' => 'No Store Basket',
            'slug' => 'no-store-basket',
            'fixed_price' => 15,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/customer/baskets', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'family-basket');
    }

    public function test_it_returns_basket_detail_with_materials_and_approved_stores(): void
    {
        $token = $this->authenticateCustomer();

        $basket = Basket::query()->create([
            'name' => 'Family Basket',
            'slug' => 'family-basket',
            'description' => 'A complete basket.',
            'fixed_price' => 25.50,
            'is_active' => true,
        ]);

        $material = Material::query()->create([
            'name' => 'Rice',
            'slug' => 'rice',
            'unit' => 'kg',
            'is_active' => true,
        ]);

        $store = Store::query()->create([
            'name' => 'Main Store',
            'phone' => '0111111111',
            'address' => 'Damascus',
            'is_active' => true,
        ]);

        $basket->basketItems()->create([
            'material_id' => $material->id,
            'quantity' => 2,
            'sort_order' => 1,
        ]);

        $basket->stores()->attach($store);

        $this->getJson('/api/customer/baskets/family-basket', [
            'Authorization' => 'Bearer '.$token,
        ])
            ->assertOk()
            ->assertJsonPath('data.slug', 'family-basket')
            ->assertJsonPath('data.materials.0.name', 'Rice')
            ->assertJsonPath('data.materials.0.quantity', 2)
            ->assertJsonPath('data.approved_stores.0.name', 'Main Store');
    }

    public function test_it_returns_not_found_for_non_orderable_baskets(): void
    {
        $token = $this->authenticateCustomer();

        $basket = Basket::query()->create([
            'name' => 'Family Basket',
            'slug' => 'family-basket',
            'fixed_price' => 25.50,
            'is_active' => true,
        ]);

        $inactiveStore = Store::query()->create([
            'name' => 'Inactive Store',
            'is_active' => false,
        ]);

        $basket->stores()->attach($inactiveStore);

        $this->getJson('/api/customer/baskets/family-basket', [
            'Authorization' => 'Bearer '.$token,
        ])
            ->assertNotFound();
    }

    protected function authenticateCustomer(): string
    {
        $user = User::factory()->create([
            'name' => 'Customer One',
            'email' => 'basket-customer@example.com',
            'password' => 'password',
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);

        Customer::query()->create([
            'user_id' => $user->id,
            'full_name' => 'Customer One',
            'phone' => '0999999999',
            'country' => 'Syria',
            'preferred_locale' => 'en',
            'is_active' => true,
        ]);

        return $this->postJson('/api/customer/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'iPhone',
        ])->json('token');
    }
}
