# Admin Dashboard Plan

## 1. Purpose

This file defines the implementation plan for the admin dashboard using Laravel and Filament, based on:

- [admin-functional-requirements.md](./admin-functional-requirements.md)
- [erd.md](./erd.md)

The goal is to build the MVP admin dashboard for:

- basket management
- material management
- store management
- delivery agent management
- order management
- manual fulfillment workflows
- payment visibility

## 2. Current Planning Assumptions

This plan follows the latest ERD decisions:

- baskets are composed of `materials` through `basket_items`
- orders are order headers
- baskets selected inside an order are stored in `order_baskets`
- each `order_baskets` row stores its own `store_id`
- order tracking remains status-based at the `orders` level

If any of those decisions change, the implementation plan should be updated before development starts.

## 3. Admin Dashboard Modules

### 3.1 Access and Security

- Admin authentication through Filament login
- Admin-only authorization for all dashboard routes
- Secure handling of customer and recipient data
- Server-side enforcement of access rules

### 3.2 Materials

- Create material
- Edit material
- Delete or archive material
- View materials list

### 3.3 Baskets

- Create basket
- Edit basket
- Delete or archive basket
- Manage basket materials
- Manage approved stores for each basket

### 3.4 Stores

- Create store
- Edit store
- Delete or archive store
- Assign stores to baskets

### 3.5 Delivery Agents

- Create delivery agent
- Edit delivery agent
- Activate/deactivate delivery agent
- View delivery agent assignments

### 3.6 Orders

- View all orders
- View order details
- View selected baskets inside an order
- View selected store per basket inside an order
- Assign delivery agent
- Change order status
- View order status history

### 3.7 Payments

- View payment state for each order
- View Stripe reference information
- Keep payment records read-only in admin

## 4. Proposed Filament Resources

- `MaterialResource`
- `BasketResource`
- `StoreResource`
- `DeliveryAgentResource`
- `CustomerResource`
- `OrderResource`
- `PaymentResource`

## 5. Proposed Relation Managers / Nested Management

- Basket -> Basket Materials
- Basket -> Approved Stores
- Store -> Approved Baskets
- Order -> Order Baskets
- Order -> Status History
- Delivery Agent -> Assigned Orders

## 6. Phase Plan

### Phase 1: Foundation

Goal:
- establish the database, models, enums, and admin access baseline

Deliverables:
- migrations for `users`, `customers`, `materials`, `baskets`, `basket_items`, `stores`, `basket_store`, `delivery_agents`, `orders`, `order_baskets`, `order_status_histories`, `payments`
- Eloquent relationships
- status enum or equivalent constant structure
- Filament admin authentication
- admin seeder or initial admin creation flow

Definition of done:
- database migrates cleanly
- admin user can sign in to Filament
- core models and relationships are testable

### Phase 2: Catalog Management

Goal:
- enable admins to manage basket composition and approved stores

Deliverables:
- `MaterialResource`
- `BasketResource`
- basket-material management UI
- basket-store assignment UI
- `StoreResource`
- validation rules for unique basket-store and basket-material relationships

Definition of done:
- admin can create and edit materials
- admin can create a basket and attach materials with quantities
- admin can attach approved stores to baskets

### Phase 3: Delivery Operations

Goal:
- enable manual fulfillment workflows

Deliverables:
- `DeliveryAgentResource`
- `OrderResource`
- order details page with recipient information
- order baskets relation view
- manual delivery agent assignment action
- order status transition actions
- status history recording

Definition of done:
- admin can view orders
- admin can assign an active delivery agent
- admin can move orders through allowed statuses
- status changes are recorded historically

### Phase 4: Payment Visibility and Auditability

Goal:
- support operational review without exposing unsafe payment details

Deliverables:
- `PaymentResource` or payment section inside orders
- Stripe payment reference display
- read-only payment status visibility
- links between orders and payments
- audit-friendly order views

Definition of done:
- admin can confirm whether an order has a successful payment
- payment data is visible in a controlled read-only manner
- delivery-agent views do not expose payment-sensitive information

### Phase 5: Admin UX, Filters, and Polish

Goal:
- make the dashboard efficient for daily operations

Deliverables:
- table filters for orders by status, customer, store, delivery agent, and payment state
- dashboard widgets for operational overview
- localization support for English and Arabic
- RTL support for Arabic
- cleanup of labels, validation messages, and action naming

Definition of done:
- common admin tasks can be completed with minimal clicks
- dashboard supports English and Arabic
- operational views are clear enough for manual fulfillment

## 7. Suggested Resource Behavior

### `MaterialResource`

- standard CRUD
- active/inactive state
- search by name

### `BasketResource`

- standard CRUD
- fields: name, slug, description, fixed price, active state
- relation manager for basket materials
- relation manager for approved stores

### `StoreResource`

- standard CRUD
- fields: name, phone, address, active state
- relation manager for assigned baskets

### `DeliveryAgentResource`

- standard CRUD
- activate/deactivate action
- quick visibility of current assignments

### `OrderResource`

- read-heavy, operationally focused
- show customer and recipient information
- show selected baskets and selected stores
- assign delivery agent action
- status transition actions
- status history relation

### `PaymentResource`

- read-only
- visible mainly for admin verification and support

## 8. Business Rules to Enforce in Implementation

- Only admins may access the dashboard.
- Only active delivery agents may be assigned to orders.
- A basket may only be associated with approved stores.
- An `order_baskets.store_id` must be valid for that basket.
- Order statuses must follow the defined lifecycle.
- Payment data must remain read-only and limited in exposure.
- Delivery agents must not see payment-sensitive information.

## 9. Open Decisions Before Development

- Whether delete actions should be hard delete or soft delete
- Whether duplicate basket lines are allowed in the same order
- Whether `materials.unit` should be free text or controlled values
- Whether `PaymentResource` should be separate or embedded under orders only
- Whether `CustomerResource` should be editable or read-only in MVP

## 10. Recommended Build Order

1. Foundation and migrations
2. Materials and baskets
3. Stores and basket-store assignment
4. Delivery agents
5. Orders and order baskets
6. Status history and assignment actions
7. Payments visibility
8. Filters, widgets, localization, and polish
