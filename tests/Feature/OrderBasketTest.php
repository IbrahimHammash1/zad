<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Basket;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderBasket;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderBasketTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_copies_historical_basket_and_store_values_on_create(): void
    {
        [$basket, $store] = $this->createApprovedBasketAndStore();
        $order = $this->createOrder();

        $orderBasket = OrderBasket::create([
            'order_id' => $order->id,
            'basket_id' => $basket->id,
            'store_id' => $store->id,
            'quantity' => 2,
        ]);

        $this->assertSame('Family Basket', $orderBasket->basket_name);
        $this->assertSame('Main Store', $orderBasket->store_name);
        $this->assertSame('25.50', $orderBasket->basket_price);
    }

    public function test_it_rejects_inactive_baskets(): void
    {
        [$basket, $store] = $this->createApprovedBasketAndStore([
            'is_active' => false,
        ]);

        $order = $this->createOrder();

        $this->expectException(ValidationException::class);

        OrderBasket::create([
            'order_id' => $order->id,
            'basket_id' => $basket->id,
            'store_id' => $store->id,
            'quantity' => 1,
        ]);
    }

    public function test_it_rejects_inactive_stores(): void
    {
        [$basket, $store] = $this->createApprovedBasketAndStore([], [
            'is_active' => false,
        ]);

        $order = $this->createOrder();

        $this->expectException(ValidationException::class);

        OrderBasket::create([
            'order_id' => $order->id,
            'basket_id' => $basket->id,
            'store_id' => $store->id,
            'quantity' => 1,
        ]);
    }

    public function test_it_rejects_unapproved_store_assignments(): void
    {
        $basket = Basket::create([
            'name' => 'Family Basket',
            'slug' => 'family-basket',
            'fixed_price' => 25.50,
            'is_active' => true,
        ]);

        $store = Store::create([
            'name' => 'Other Store',
            'is_active' => true,
        ]);

        $order = $this->createOrder();

        $this->expectException(ValidationException::class);

        OrderBasket::create([
            'order_id' => $order->id,
            'basket_id' => $basket->id,
            'store_id' => $store->id,
            'quantity' => 1,
        ]);
    }

    protected function createApprovedBasketAndStore(array $basketOverrides = [], array $storeOverrides = []): array
    {
        $basket = Basket::create(array_merge([
            'name' => 'Family Basket',
            'slug' => 'family-basket',
            'fixed_price' => 25.50,
            'is_active' => true,
        ], $basketOverrides));

        $store = Store::create(array_merge([
            'name' => 'Main Store',
            'is_active' => true,
        ], $storeOverrides));

        $basket->stores()->attach($store);

        return [$basket, $store];
    }

    protected function createOrder(): Order
    {
        $user = User::factory()->create([
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'user_id' => $user->id,
            'full_name' => 'Customer Name',
            'phone' => '0777777777',
            'country' => 'Syria',
            'preferred_locale' => 'en',
            'is_active' => true,
        ]);

        return Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);
    }
}
