<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_initial_status_history_when_an_order_is_created(): void
    {
        $customer = $this->createCustomer();

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
            'notes' => 'Handle carefully',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => null,
            'to_status' => OrderStatus::Pending->value,
            'notes' => 'Order created.',
        ]);
    }

    public function test_it_records_status_changes_in_order_history(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        $customer = $this->createCustomer();
        $agent = DeliveryAgent::create([
            'name' => 'Agent One',
            'phone' => '0888888888',
            'is_active' => true,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);

        $order->update([
            'delivery_agent_id' => $agent->id,
        ]);

        $order->update([
            'status' => OrderStatus::Assigned,
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => OrderStatus::Pending->value,
            'to_status' => OrderStatus::Assigned->value,
            'changed_by_user_id' => $admin->id,
        ]);
    }

    public function test_it_records_delivery_agent_assignment_in_order_history(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        $customer = $this->createCustomer();
        $agent = DeliveryAgent::create([
            'name' => 'Agent One',
            'phone' => '0888888888',
            'is_active' => true,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);

        $order->update([
            'delivery_agent_id' => $agent->id,
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => OrderStatus::Pending->value,
            'to_status' => OrderStatus::Pending->value,
            'changed_by_user_id' => $admin->id,
            'notes' => 'Delivery agent changed from Unassigned to Agent One.',
        ]);
    }

    public function test_it_rejects_creating_a_new_order_with_non_pending_status(): void
    {
        $customer = $this->createCustomer();

        $this->expectException(ValidationException::class);

        Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Assigned,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);
    }

    public function test_it_rejects_invalid_status_transitions(): void
    {
        $customer = $this->createCustomer();

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);

        $this->expectException(ValidationException::class);

        $order->update([
            'status' => OrderStatus::Delivered,
        ]);
    }

    public function test_it_requires_a_delivery_agent_for_assigned_statuses(): void
    {
        $customer = $this->createCustomer();

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);

        $this->expectException(ValidationException::class);

        $order->update([
            'status' => OrderStatus::Assigned,
        ]);
    }

    public function test_it_rejects_inactive_delivery_agent_assignment(): void
    {
        $customer = $this->createCustomer();
        $agent = DeliveryAgent::create([
            'name' => 'Inactive Agent',
            'phone' => '0666666666',
            'is_active' => false,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);

        $this->expectException(ValidationException::class);

        $order->update([
            'delivery_agent_id' => $agent->id,
        ]);
    }

    public function test_it_allows_the_default_operational_status_flow(): void
    {
        $customer = $this->createCustomer();
        $agent = DeliveryAgent::create([
            'name' => 'Agent One',
            'phone' => '0888888888',
            'is_active' => true,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);

        $order->update([
            'delivery_agent_id' => $agent->id,
        ]);

        $order->update([
            'status' => OrderStatus::Assigned,
        ]);

        $order->update([
            'status' => OrderStatus::InProgress,
        ]);

        $order->update([
            'status' => OrderStatus::Delivered,
        ]);

        $this->assertSame(OrderStatus::Delivered, $order->refresh()->status);
    }

    protected function createCustomer(): Customer
    {
        $user = User::factory()->create([
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);

        return Customer::create([
            'user_id' => $user->id,
            'full_name' => 'Customer Name',
            'phone' => '0777777777',
            'country' => 'Syria',
            'preferred_locale' => 'en',
            'is_active' => true,
        ]);
    }
}
