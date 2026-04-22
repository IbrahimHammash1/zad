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

class CustomerCheckoutApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_review_requires_authentication(): void
    {
        $this->postJson('/api/customer/checkout/review', [])
            ->assertUnauthorized();
    }

    public function test_checkout_review_returns_validated_summary_and_subtotal(): void
    {
        $token = $this->authenticateCustomer();
        [$basket, $store] = $this->createOrderableBasket();

        $response = $this->postJson('/api/customer/checkout/review', [
            'recipient_name' => 'Receiver',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
            'notes' => 'Call before delivery',
            'basket_lines' => [
                ['basket_id' => $basket->id, 'store_id' => $store->id, 'quantity' => 1],
                ['basket_id' => $basket->id, 'store_id' => $store->id, 'quantity' => 2],
            ],
        ], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.currency', 'USD')
            ->assertJsonPath('data.basket_lines.0.quantity', 3)
            ->assertJsonPath('data.basket_lines.0.line_total', '76.50')
            ->assertJsonPath('data.subtotal', '76.50');
    }

    public function test_checkout_review_rejects_unapproved_store(): void
    {
        $token = $this->authenticateCustomer();
        [$basket] = $this->createOrderableBasket();
        $unapprovedStore = Store::query()->create([
            'name' => 'Unapproved Store',
            'is_active' => true,
        ]);

        $this->postJson('/api/customer/checkout/review', [
            'recipient_name' => 'Receiver',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
            'basket_lines' => [
                ['basket_id' => $basket->id, 'store_id' => $unapprovedStore->id, 'quantity' => 1],
            ],
        ], [
            'Authorization' => 'Bearer '.$token,
        ])
            ->assertStatus(422)
            ->assertJsonPath('errors.basket_lines.0', "Store {$unapprovedStore->id} is not approved for basket {$basket->id}.");
    }

    public function test_checkout_intent_is_created_without_creating_order(): void
    {
        $token = $this->authenticateCustomer();
        [$basket, $store] = $this->createOrderableBasket();

        $response = $this->postJson('/api/customer/checkout/intents', [
            'recipient_name' => 'Receiver',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
            'notes' => 'Leave at door',
            'basket_lines' => [
                ['basket_id' => $basket->id, 'store_id' => $store->id, 'quantity' => 2],
            ],
        ], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.currency', 'USD')
            ->assertJsonPath('data.subtotal', '51.00')
            ->assertJsonPath('data.basket_lines.0.quantity', 2);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('customer_checkout_intents', [
            'currency' => 'USD',
            'recipient_name' => 'Receiver',
            'recipient_phone' => '0999999999',
        ]);
    }

    protected function authenticateCustomer(): string
    {
        $user = User::factory()->create([
            'name' => 'Customer One',
            'email' => 'customer@example.com',
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
            'email' => 'customer@example.com',
            'password' => 'password',
            'device_name' => 'iPhone',
        ])->json('token');
    }

    protected function createOrderableBasket(): array
    {
        $basket = Basket::query()->create([
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

        $material = Material::query()->create([
            'name' => 'Rice',
            'slug' => 'rice',
            'unit' => 'kg',
            'is_active' => true,
        ]);

        $basket->stores()->attach($store);
        $basket->basketItems()->create([
            'material_id' => $material->id,
            'quantity' => 1,
            'sort_order' => 1,
        ]);

        return [$basket, $store];
    }
}
