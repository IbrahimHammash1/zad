<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryAgentOperationalOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_only_includes_non_completed_orders_in_operational_orders(): void
    {
        $agent = DeliveryAgent::create([
            'name' => 'Agent One',
            'phone' => '0888888888',
            'is_active' => true,
        ]);

        $pendingOrder = $this->createOrderForAgent($agent, OrderStatus::Pending);
        $assignedOrder = $this->createOrderForAgent($agent, OrderStatus::Assigned);
        $inProgressOrder = $this->createOrderForAgent($agent, OrderStatus::InProgress);
        $this->createOrderForAgent($agent, OrderStatus::Delivered);
        $this->createOrderForAgent($agent, OrderStatus::Cancelled);

        $operationalOrderIds = $agent->operationalOrders()->pluck('id')->all();

        $this->assertEqualsCanonicalizing(
            [$pendingOrder->id, $assignedOrder->id, $inProgressOrder->id],
            $operationalOrderIds,
        );
    }

    protected function createOrderForAgent(DeliveryAgent $agent, OrderStatus $status): Order
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

        $order = Order::create([
            'customer_id' => $customer->id,
            'delivery_agent_id' => $agent->id,
            'status' => OrderStatus::Pending,
            'currency' => 'USD',
            'recipient_name' => 'Recipient Name',
            'recipient_phone' => '0999999999',
            'delivery_address' => 'Damascus',
        ]);

        if ($status === OrderStatus::Assigned) {
            $order->update(['status' => OrderStatus::Assigned]);
        }

        if ($status === OrderStatus::InProgress) {
            $order->update(['status' => OrderStatus::Assigned]);
            $order->update(['status' => OrderStatus::InProgress]);
        }

        if ($status === OrderStatus::Delivered) {
            $order->update(['status' => OrderStatus::Assigned]);
            $order->update(['status' => OrderStatus::InProgress]);
            $order->update(['status' => OrderStatus::Delivered]);
        }

        if ($status === OrderStatus::Cancelled) {
            $order->update(['status' => OrderStatus::Cancelled]);
        }

        return $order->refresh();
    }
}
