<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Basket;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Order;
use App\Models\OrderBasket;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_endpoints_require_authentication(): void
    {
        $this->getJson('/api/customer/orders')->assertUnauthorized();
        $this->getJson('/api/customer/orders/1')->assertUnauthorized();
    }

    public function test_customer_only_sees_their_own_orders(): void
    {
        [$customerA, $tokenA] = $this->createCustomerWithToken('a@example.com');
        [$customerB] = $this->createCustomerWithToken('b@example.com');

        $orderA = $this->createOrderWithLine($customerA);
        $this->createOrderWithLine($customerB);

        $this->getJson('/api/customer/orders', [
            'Authorization' => 'Bearer '.$tokenA,
        ])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $orderA->id)
            ->assertJsonPath('data.0.status.value', OrderStatus::Pending->value)
            ->assertJsonPath('data.0.status.label', 'Pending');
    }

    public function test_customer_can_view_their_order_detail(): void
    {
        [$customer, $token] = $this->createCustomerWithToken('customer@example.com');
        $order = $this->createOrderWithLine($customer);

        $this->getJson('/api/customer/orders/'.$order->id, [
            'Authorization' => 'Bearer '.$token,
        ])
            ->assertOk()
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.recipient.name', 'Recipient Name')
            ->assertJsonPath('data.status.value', OrderStatus::Pending->value)
            ->assertJsonPath('data.basket_lines.0.basket_name', 'Family Basket')
            ->assertJsonPath('data.basket_lines.0.store_name', 'Main Store');
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        [$customerA, $tokenA] = $this->createCustomerWithToken('a@example.com');
        [$customerB] = $this->createCustomerWithToken('b@example.com');
        $orderB = $this->createOrderWithLine($customerB);

        $this->getJson('/api/customer/orders/'.$orderB->id, [
            'Authorization' => 'Bearer '.$tokenA,
        ])->assertNotFound();
    }

    protected function createCustomerWithToken(string $email): array
    {
        $user = User::factory()->create([
            'name' => 'Customer',
            'email' => $email,
            'password' => 'password',
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);

        $customer = Customer::query()->create([
            'user_id' => $user->id,
            'full_name' => 'Customer Name',
            'phone' => '0999999999',
            'country' => 'Syria',
            'preferred_locale' => 'en',
            'is_active' => true,
        ]);

        $token = $this->postJson('/api/customer/login', [
            'email' => $email,
            'password' => 'password',
            'device_name' => 'iPhone',
        ])->json('token');

        return [$customer, $token];
    }

    protected function createOrderWithLine(Customer $customer): Order
    {
        $basket = Basket::query()->create([
            'name' => 'Family Basket',
            'slug' => 'family-basket-'.uniqid(),
            'fixed_price' => 25.50,
            'is_active' => true,
        ]);

        $material = Material::query()->create([
            'name' => 'Rice '.uniqid(),
            'slug' => 'rice-'.uniqid(),
            'unit' => 'kg',
            'is_active' => true,
        ]);

        $store = Store::query()->create([
            'name' => 'Main Store',
            'is_active' => true,
        ]);

        $basket->basketItems()->create([
            'material_id' => $material->id,
            'quantity' => 2,
            'sort_order' => 1,
        ]);

        $basket->stores()->attach($store);

        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
            'notes' => 'Handle carefully',
        ]);

        OrderBasket::query()->create([
            'order_id' => $order->id,
            'basket_id' => $basket->id,
            'store_id' => $store->id,
            'quantity' => 2,
        ]);

        return $order->refresh();
    }
}
