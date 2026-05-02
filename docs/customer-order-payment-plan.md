# Customer Order and Payment Plan

## Purpose

This document is the implementation reference for the customer order placement and payment flow.

It is based on:

- the base MVP requirements
- the current backend model
- the product decisions agreed during checkout/payment review

The goal is to replace the unhealthy checkout-intent direction with a real order-driven flow.

## Core Decisions

- The real business entity is `Order`.
- Checkout state should not be stored in temporary JSON tables.
- `customer_checkout_intents` should not be used as the foundation for order placement.
- Payment code should use real production names, even while the provider behavior is static.
- No production class/table/endpoint should use names like `mock`, `fake`, `simulated`, or `test`.
- The MVP payment provider is `Ziina`.
- The actual external Ziina API integration will be done last.
- Until the real integration is added, `ZiinaPaymentProvider` may return a static successful provider result internally.
- The static provider behavior must follow the real final workflow shape: payment succeeds, then a real order is created.

## Target Customer Flow

1. Customer browses baskets.
2. Customer opens basket details.
3. Customer selects one approved store for each selected basket.
4. Customer enters recipient details.
5. Customer reviews the order.
6. Customer places the order.
7. Backend processes payment through `PaymentService`.
8. `PaymentService` uses `PaymentProviderInterface`.
9. Current provider implementation is `ZiinaPaymentProvider`.
10. After successful payment, backend creates the real order.
11. Order starts with status `Pending`.
12. Customer can see the order in order history and order detail.

## API Shape

### Review Order

```http
POST /api/customer/orders/review
```

Purpose:

- validate selected basket lines
- validate selected stores
- validate quantities
- validate recipient details
- calculate line totals and order total
- return a clear review summary to the mobile app

Rules:

- Requires customer authentication.
- Must not create an order.
- Must not create a payment.
- Must not write checkout state to the database.
- Must use the same validation logic as order creation.

### Place Order

```http
POST /api/customer/orders
```

Purpose:

- validate the same order payload as review
- process payment through the payment service
- create the real order after payment success
- create order basket lines
- create payment record
- return created order data

Rules:

- Requires customer authentication.
- Must create a normal customer order only after successful payment.
- Must create the order with status `Pending`.
- Must store historical basket and store values on `order_baskets`.
- Must reject unapproved store selection.
- Failed payment must not create a normal customer order.

## Payment Architecture

Use an extensible provider design:

```php
interface PaymentProviderInterface
{
    public function charge(PaymentRequestData $paymentRequest): PaymentResultData;
}
```

Production-facing class names:

- `PaymentService`
- `PaymentProviderInterface`
- `ZiinaPaymentProvider`
- `PaymentRequestData`
- `PaymentResultData`

Important:

- `ZiinaPaymentProvider` is the real provider class name.
- For now, the internals can return a static successful result.
- Later, the internals will call Ziina without changing controller/service workflow names.

## Database Direction

Keep the workflow based on real domain tables:

- `orders`
- `order_baskets`
- `payments`
- `order_status_histories`

Remove or stop using:

- `customer_checkout_intents`
- checkout JSON line item storage

Payment fields should be provider-neutral where possible:

- prefer `provider_reference` or `provider_transaction_id`
- avoid Stripe-specific naming such as `provider_payment_intent_id`

Payment provider enum should support:

- `ziina`

## Documentation Requirements

Scribe documentation must be clear for both endpoints.

For `POST /api/customer/orders/review`, document:

- request body
- basket lines
- `basket_id`
- `store_id`
- `quantity`
- recipient name
- recipient phone
- delivery address
- optional notes
- successful review response
- validation error for unapproved store

For `POST /api/customer/orders`, document:

- request body
- successful created order response
- payment result fields safe for customer response
- validation errors
- unauthenticated response

The documentation should make the happy path obvious to the mobile developer.

## Test Requirements

Review endpoint tests:

- requires authentication
- validates basket lines
- rejects unapproved store
- returns basket names, store names, quantities, line totals, and total
- does not write orders or payments

Order creation endpoint tests:

- requires authentication
- creates order after successful Ziina payment result
- creates order basket lines
- creates payment record with provider `ziina`
- starts order as `Pending`
- preserves historical basket and store values
- returns created order response
- created order appears in customer order history

Regression tests:

- basket list remains public
- basket detail remains public
- authenticated customer cannot see another customer's orders
- failed payment does not create a normal customer order when failure handling is added

## Implementation Sequence

1. Update customer/admin requirements docs from `Stripe` to `Ziina`.
2. Update customer phase docs to reflect order-driven payment flow.
3. Remove `checkout/intents` route and related code.
4. Remove or replace `customer_checkout_intents` migration/model/repository/service usage.
5. Add `POST /api/customer/orders/review`.
6. Add `POST /api/customer/orders`.
7. Add payment provider interface and Ziina provider.
8. Add order placement service using service + repository pattern.
9. Update payment enum/table/resource naming from Stripe-specific names to provider-neutral names.
10. Update Scribe docs and examples.
11. Add/adjust tests for the full happy path.

## Non-Goals

- Do not implement real Ziina API calls yet.
- Do not add live tracking.
- Do not support cash on delivery.
- Do not support custom baskets.
- Do not support subscription orders.
- Do not support automated delivery assignment.
