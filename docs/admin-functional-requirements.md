# Admin Functional Requirements

## 1. Purpose

This document defines the implementation-ready admin requirements for the backend and admin dashboard.

Primary source:
- `MVP Requirements Document.docx`

This file is the current source of truth for admin scope. It includes:

- requirements derived from the original MVP document
- clarified admin behavior
- accepted product decisions made during planning and ERD design

## 2. Current Product Decisions

These decisions are now part of the working requirements baseline:

- baskets are composed of `materials`
- basket composition is managed through a basket-to-material relation
- one order may contain one or more baskets
- each basket selected inside an order has its own store selection
- order operational status is tracked at the order level
- historical basket and store values must remain stable after order creation

## 3. Scope

This document covers the admin dashboard and backend administration workflows for the MVP, including:

- admin authentication and access control
- admin dashboard overview and operational summaries
- material management
- basket management
- store management
- delivery agent management
- order management
- manual delivery coordination
- payment-related admin behavior
- admin localization
- admin security
- auditability and operational constraints

This document does not define customer mobile app requirements except where they directly affect admin workflows.

## 4. Source Traceability

| Source Section | Source Topic | Covered In |
| --- | --- | --- |
| Section 3.4 | Admin actor | `FR-ADM-001` to `FR-ADM-003` |
| Section 4 | Basket core concept | `FR-ADM-101` to `FR-ADM-114` |
| Section 5 Step 7 | Manual agent assignment | `FR-ADM-304`, `FR-ADM-404`, `FR-ADM-501` |
| Section 5 Step 10 | Delivered completion | `FR-ADM-410` |
| Section 7.1 | Basket management | `FR-ADM-101` to `FR-ADM-114` |
| Section 7.2 | Store management | `FR-ADM-201` to `FR-ADM-207` |
| Section 7.3 | Delivery agent management | `FR-ADM-301` to `FR-ADM-306` |
| Section 7.4 | Order management | `FR-ADM-401` to `FR-ADM-419` |
| Section 8 | Payment system | `FR-ADM-601` to `FR-ADM-606` |
| Section 9 | Delivery system | `FR-ADM-501` to `FR-ADM-505` |
| Section 10 | Order status flow | `FR-ADM-406` to `FR-ADM-416` |
| Derived admin operations visibility | Dashboard widgets and operational summaries | `FR-ADM-801` to `FR-ADM-805` |
| Section 11 | Non-functional requirements | `NFR-ADM-001` to `NFR-ADM-008` |
| Section 12 | Security requirements | `FR-ADM-002`, `FR-ADM-606`, `NFR-ADM-006` to `NFR-ADM-008` |
| Section 13 | MVP scope summary | `MVP-001` to `MVP-011` |

Note:
- some requirements below reflect accepted implementation decisions that clarify the original MVP document

## 5. MVP Scope Classification

### 5.1 In Scope

- `MVP-001` Predefined baskets only
- `MVP-002` Approved stores per basket
- `MVP-003` Stripe payments
- `MVP-004` Manual delivery assignment
- `MVP-005` Status-based tracking only
- `MVP-006` Full admin management for materials, baskets, stores, delivery agents, and orders
- `MVP-007` Basket composition through materials
- `MVP-008` One order may contain multiple baskets
- `MVP-009` Store selection is captured per basket inside an order

### 5.2 Out of Scope

- `MVP-010` Live tracking or GPS
- `MVP-011` User-customized baskets
- `MVP-012` Automated delivery assignment
- `MVP-013` Subscription models

## 6. Functional Requirements

### 6.1 Access Control

#### `FR-ADM-001`

The system shall provide an authenticated admin login for access to the admin dashboard.

Priority: Must have

Acceptance criteria:

- An unauthenticated user attempting to access `/admin` is redirected to the admin login page.
- An admin user with valid credentials can sign in and access the dashboard.

#### `FR-ADM-002`

The system shall restrict admin dashboard access to authorized admin users only.

Priority: Must have

Acceptance criteria:

- Non-admin users cannot access admin routes.
- Authorization checks are enforced server-side.

#### `FR-ADM-003`

Authorized admin users shall have access to manage materials, baskets, stores, delivery agents, and orders.

Priority: Must have

Acceptance criteria:

- After login, an admin can access the material, basket, store, delivery agent, and order management sections.

### 6.2 Material Management

#### `FR-ADM-050`

The admin shall be able to create a material.

Priority: Must have

Acceptance criteria:

- Admin can create a material record from the dashboard.

#### `FR-ADM-051`

The admin shall be able to edit a material.

Priority: Must have

Acceptance criteria:

- Admin can update material details from the dashboard.

#### `FR-ADM-052`

The admin shall be able to delete or archive a material.

Priority: Must have

Acceptance criteria:

- Admin can remove a material from active use in new basket composition.

#### `FR-ADM-053`

The system shall support storing a reusable material catalog.

Priority: Must have

Acceptance criteria:

- A material can be reused across multiple baskets.

### 6.3 Basket Management

#### `FR-ADM-101`

The admin shall be able to create a basket.

Priority: Must have

Acceptance criteria:

- Admin can create a basket record from the dashboard.
- The basket is persisted and appears in the basket listing.

#### `FR-ADM-102`

The admin shall be able to edit an existing basket.

Priority: Must have

Acceptance criteria:

- Admin can update basket details from the dashboard.
- Updated values are visible in basket listings and detail views.

#### `FR-ADM-103`

The admin shall be able to delete or archive a basket.

Priority: Must have

Acceptance criteria:

- Admin can remove a basket from the active catalog.
- Removed baskets are no longer available for new customer orders.

#### `FR-ADM-104`

The admin shall be able to define the materials included in a basket.

Priority: Must have

Acceptance criteria:

- Admin can add one or more materials to a basket.
- Each material is associated with the correct basket.

#### `FR-ADM-105`

The admin shall be able to define the quantity for each material in a basket.

Priority: Must have

Acceptance criteria:

- Each basket material line has an admin-editable quantity field.
- Quantity changes are saved and shown in basket details.

#### `FR-ADM-106`

The admin shall be able to define a fixed total price for a basket.

Priority: Must have

Acceptance criteria:

- Basket price is stored as a single fixed amount.
- Admin can edit the fixed price.

#### `FR-ADM-107`

The admin shall be able to assign one or more approved stores to a basket.

Priority: Must have

Acceptance criteria:

- Admin can link multiple stores to a basket.
- Linked stores are visible in the basket detail view.

#### `FR-ADM-108`

The system shall ensure that a basket can only be ordered from its approved stores.

Priority: Must have

Acceptance criteria:

- Order basket lines cannot be created against a store that is not linked to the selected basket.
- Admin-visible relationships reflect only valid basket-store assignments.

#### `FR-ADM-109`

The system shall support a basket as a predefined fixed package.

Priority: Must have

Acceptance criteria:

- Basket structure is managed by admins only.
- The admin dashboard does not expose customer-driven basket customization in MVP.

#### `FR-ADM-110`

The system shall support each basket having a list of materials, quantities, fixed total price, and approved stores.

Priority: Must have

Acceptance criteria:

- Basket management supports all four elements in a single admin workflow.

#### `FR-ADM-111`

The system should preserve historical basket information for ordered baskets already placed.

Priority: Must have

Acceptance criteria:

- Changes to a basket after an order is created do not alter the stored basket information associated with historical order basket lines.

#### `FR-ADM-112`

The system shall support a reusable basket-to-material relationship.

Priority: Must have

Acceptance criteria:

- The same material can be assigned to multiple baskets.

#### `FR-ADM-113`

The system should support display ordering for materials inside a basket.

Priority: Should have

Acceptance criteria:

- Basket materials can be ordered consistently in admin views.

#### `FR-ADM-114`

The system should prevent duplicate material assignment within the same basket.

Priority: Must have

Acceptance criteria:

- The same material cannot be linked twice to the same basket.

### 6.4 Store Management

#### `FR-ADM-201`

The admin shall be able to create a store.

Priority: Must have

Acceptance criteria:

- Admin can create a store from the dashboard.
- The store appears in store listings and can be assigned to baskets.

#### `FR-ADM-202`

The admin shall be able to edit a store.

Priority: Must have

Acceptance criteria:

- Admin can update store details.
- Updated details are visible wherever the store is referenced in admin views.

#### `FR-ADM-203`

The admin shall be able to delete or archive a store.

Priority: Must have

Acceptance criteria:

- Admin can remove a store from the active catalog.
- Removed stores are no longer assignable to new baskets or new order basket lines.

#### `FR-ADM-204`

The admin shall be able to assign baskets to a store.

Priority: Must have

Acceptance criteria:

- Admin can assign one or more baskets to a store.

#### `FR-ADM-205`

The system shall support a basket being linked to multiple approved stores.

Priority: Must have

Acceptance criteria:

- A single basket can have multiple linked stores without duplication errors.

#### `FR-ADM-206`

The system shall support a store being linked to multiple baskets.

Priority: Must have

Acceptance criteria:

- A single store can be assigned to multiple baskets.

#### `FR-ADM-207`

The system should preserve historical selected-store information for ordered basket lines already placed.

Priority: Must have

Acceptance criteria:

- Changes to store assignments or store details do not alter historical order basket records.

### 6.5 Delivery Agent Management

#### `FR-ADM-301`

The admin shall be able to add a delivery agent.

Priority: Must have

Acceptance criteria:

- Admin can create a delivery agent record.
- The agent becomes available for manual order assignment when active.

#### `FR-ADM-302`

The admin shall be able to activate a delivery agent.

Priority: Must have

Acceptance criteria:

- Admin can mark an inactive agent as active.
- Active agents are available in assignment controls.

#### `FR-ADM-303`

The admin shall be able to deactivate a delivery agent.

Priority: Must have

Acceptance criteria:

- Admin can mark an agent as inactive.
- Inactive agents are excluded from new assignments.

#### `FR-ADM-304`

The admin shall be able to manually assign an order to a delivery agent.

Priority: Must have

Acceptance criteria:

- Admin can choose an active delivery agent for an order.
- The selected agent is saved on the order.

#### `FR-ADM-305`

The system shall support manual delivery assignment only in the MVP.

Priority: Must have

Acceptance criteria:

- The system does not auto-assign delivery agents.

#### `FR-ADM-306`

The system shall provide delivery agents only the operational order details required for fulfillment.

Priority: Must have

Acceptance criteria:

- Delivery agent operational views or exports include baskets, selected stores, recipient name, recipient phone, address, and notes where present.
- Payment-sensitive information is excluded.

### 6.6 Order Management

#### `FR-ADM-401`

The admin shall be able to view all orders.

Priority: Must have

Acceptance criteria:

- Admin can access an order listing page.
- The listing shows all orders in the system.

#### `FR-ADM-402`

The admin shall be able to view the details of a single order.

Priority: Must have

Acceptance criteria:

- Admin can open an order detail view from the listing.

#### `FR-ADM-403`

The order details view shall include the selected baskets, selected stores, recipient name, recipient phone number, delivery address, and optional notes.

Priority: Must have

Acceptance criteria:

- All listed fields are visible on the order detail view when present.

#### `FR-ADM-404`

The admin shall be able to manually assign a delivery agent to an order.

Priority: Must have

Acceptance criteria:

- Admin can assign or update the assigned delivery agent from the order management workflow.

#### `FR-ADM-405`

The admin shall be able to change the status of an order.

Priority: Must have

Acceptance criteria:

- Admin can update order status from the order detail view or equivalent management action.

#### `FR-ADM-406`

The system shall support the following order statuses: `Pending`, `Assigned`, `In Progress`, `Delivered`, and `Cancelled`.

Priority: Must have

Acceptance criteria:

- Only those five statuses are available in MVP status controls unless explicitly extended later.

#### `FR-ADM-407`

The system shall create an order with the status `Pending` after successful payment.

Priority: Must have

Acceptance criteria:

- A newly created paid order starts in `Pending`.

#### `FR-ADM-408`

The admin shall be able to move an order from `Pending` to `Assigned` when a delivery agent is assigned.

Priority: Must have

Acceptance criteria:

- After assigning an agent, the order can be moved to `Assigned`.

#### `FR-ADM-409`

The admin shall be able to move an order to `In Progress` when fulfillment or delivery has started.

Priority: Must have

Acceptance criteria:

- Admin can set status to `In Progress` for an assigned order.

#### `FR-ADM-410`

The admin shall be able to move an order to `Delivered` when delivery is completed.

Priority: Must have

Acceptance criteria:

- Admin can mark an in-progress order as `Delivered`.

#### `FR-ADM-411`

The admin shall be able to move an order to `Cancelled` when the order is cancelled.

Priority: Must have

Acceptance criteria:

- Admin can mark an eligible order as `Cancelled`.

#### `FR-ADM-412`

The system should enforce valid status transitions for the MVP workflow.

Priority: Should have

Acceptance criteria:

- The default valid flow is `Pending -> Assigned -> In Progress -> Delivered`.
- `Cancelled` can be applied before delivery completion.

#### `FR-ADM-413`

The system shall support status-based tracking only.

Priority: Must have

Acceptance criteria:

- Order tracking data available in MVP is limited to the current order status and related status changes.

#### `FR-ADM-414`

The system shall not require live location tracking or GPS for order management.

Priority: Must have

Acceptance criteria:

- No GPS coordinates or live map tracking are required to process or manage orders.

#### `FR-ADM-415`

The system should record order status change history.

Priority: Must have

Acceptance criteria:

- Status changes are stored with timestamp data.
- Historical status changes are reviewable by admins.

#### `FR-ADM-416`

The system should preserve order basket values as historical order data after order creation.

Priority: Must have

Acceptance criteria:

- Changes to baskets or stores after order creation do not overwrite the stored order basket values.

#### `FR-ADM-417`

The admin should be able to identify the selected store for each basket inside an order.

Priority: Must have

Acceptance criteria:

- The selected store is visible for each ordered basket in order listings, detail views, or both.

#### `FR-ADM-418`

The system shall support one order containing one or more baskets.

Priority: Must have

Acceptance criteria:

- An order can contain multiple order basket lines.

#### `FR-ADM-419`

The system shall store quantity per basket inside an order.

Priority: Must have

Acceptance criteria:

- Each order basket line stores a quantity value.

### 6.7 Manual Delivery Coordination

#### `FR-ADM-501`

The system shall support fully manual delivery assignment by the admin.

Priority: Must have

Acceptance criteria:

- Admin assignment is the only supported assignment mechanism in MVP.

#### `FR-ADM-502`

The system shall provide the assigned delivery agent with the order details needed to purchase the baskets from the selected stores and deliver them to the recipient address.

Priority: Must have

Acceptance criteria:

- Operational data required for purchase and delivery is available to the assigned agent through an approved internal mechanism.

#### `FR-ADM-503`

The MVP shall support status-based delivery tracking only.

Priority: Must have

Acceptance criteria:

- Delivery progress is represented through order statuses only.

#### `FR-ADM-504`

The MVP shall not require GPS or live location tracking.

Priority: Must have

Acceptance criteria:

- Delivery coordination can be completed entirely without GPS data.

#### `FR-ADM-505`

The system should allow admins to view the assigned delivery agent for each order.

Priority: Must have

Acceptance criteria:

- Assigned agent information is visible in order management views.

### 6.8 Payment-Related Administration

#### `FR-ADM-601`

The system shall use Stripe as the payment provider.

Priority: Must have

Acceptance criteria:

- Admin-facing payment records reference Stripe-based payments only in MVP.

#### `FR-ADM-602`

The system shall confirm payment before creating an order.

Priority: Must have

Acceptance criteria:

- No order record is finalized as a valid customer order without successful payment confirmation.

#### `FR-ADM-603`

The system shall not create an order when payment fails.

Priority: Must have

Acceptance criteria:

- Failed payments do not create valid customer orders visible in normal admin order management.

#### `FR-ADM-604`

The system shall not support cash on delivery in the MVP.

Priority: Must have

Acceptance criteria:

- There is no admin workflow for recording or managing cash-on-delivery orders.

#### `FR-ADM-605`

The admin shall be able to identify the payment state of an order as part of operational review.

Priority: Should have

Acceptance criteria:

- Admin can determine whether payment succeeded for an order.

#### `FR-ADM-606`

Delivery agents shall not have access to payment data.

Priority: Must have

Acceptance criteria:

- Delivery agent operational views do not expose payment method details, payment tokens, or other payment-sensitive information.

### 6.9 Localization

#### `FR-ADM-701`

The admin dashboard shall support English and Arabic.

Priority: Must have

Acceptance criteria:

- Admin interface text can be presented in English and Arabic.

#### `FR-ADM-702`

The admin dashboard shall support right-to-left layout behavior for Arabic.

Priority: Must have

Acceptance criteria:

- Arabic admin views render with RTL-compatible layout behavior.

### 6.10 Dashboard Widgets and Operational Summaries

#### `FR-ADM-801`

The admin dashboard should provide summary widgets for operational monitoring.

Priority: Should have

Acceptance criteria:

- The dashboard can display high-level operational summary information without requiring navigation into each resource.

#### `FR-ADM-802`

The admin dashboard should provide order status summary widgets.

Priority: Should have

Acceptance criteria:

- Admin can see counts for the main order statuses used in the MVP workflow.

#### `FR-ADM-803`

The admin dashboard should provide visibility into unassigned or assignment-ready operational workload.

Priority: Should have

Acceptance criteria:

- Admin can identify how many orders still require assignment or operational attention from the dashboard.

#### `FR-ADM-804`

The admin dashboard should provide summary visibility for delivery-agent operational capacity.

Priority: Should have

Acceptance criteria:

- Admin can identify active delivery agents and view a high-level summary of current delivery workload.

#### `FR-ADM-805`

The admin dashboard should provide recent operational activity summaries.

Priority: Should have

Acceptance criteria:

- Admin can review recent order-related operational changes such as assignment or status activity from the dashboard.

## 7. Admin Non-Functional Requirements

#### `NFR-ADM-001`

The admin dashboard shall support simple and fast operational workflows.

Acceptance criteria:

- Common tasks such as creating records, assigning agents, and updating order statuses can be completed without requiring multi-step custom workflows beyond standard CRUD interactions.

#### `NFR-ADM-002`

The system shall provide reliable status visibility for order tracking.

Acceptance criteria:

- Admins can consistently view the current status of each order.
- Status history reflects the order lifecycle accurately.

#### `NFR-ADM-003`

The system shall support bilingual admin usage in English and Arabic.

Acceptance criteria:

- Admin-facing content and layout framework support both languages.

#### `NFR-ADM-004`

The system shall support RTL presentation for Arabic admin interfaces.

Acceptance criteria:

- Arabic presentation does not require a separate dashboard implementation.

#### `NFR-ADM-005`

The system shall support global customer operations and cross-border payment administration as part of the admin workflow.

Acceptance criteria:

- Admin workflows do not assume local-only payment behavior.

#### `NFR-ADM-006`

The system shall securely handle recipient addresses and phone numbers.

Acceptance criteria:

- Personal delivery data is only available to authorized internal roles.

#### `NFR-ADM-007`

The system shall enforce server-side authorization for admin-only operations.

Acceptance criteria:

- Restricted actions are not protected only by the UI.

#### `NFR-ADM-008`

The system shall maintain auditability for operationally significant admin actions.

Acceptance criteria:

- The implementation supports traceable changes for at least order status changes and delivery agent assignment.

## 8. Business Rules

- `BR-001` A customer order is valid only after successful payment.
- `BR-002` One order may contain one or more baskets.
- `BR-003` Each ordered basket line is linked to one approved store.
- `BR-004` Each basket can be linked only to approved stores selected by the admin.
- `BR-005` Basket composition is managed through reusable materials.
- `BR-006` Delivery assignment is manual in MVP.
- `BR-007` Delivery tracking is status-based only in MVP.
- `BR-008` Admin has full system management access within MVP scope.
- `BR-009` Delivery agents must not access payment-sensitive data.
- `BR-010` Historical order basket information should remain stable after catalog changes.

## 9. Open Clarifications

These are not blockers for starting implementation, but they should be confirmed before detailed workflow enforcement:

- `CL-001` Whether basket deletion should be hard delete or soft delete
- `CL-002` Whether store deletion should be hard delete or soft delete
- `CL-003` Whether material deletion should be hard delete or soft delete
- `CL-004` Whether delivery agent deactivation should be blocked when the agent has active assigned orders
- `CL-005` Whether admins can move an order directly from `Pending` to `Cancelled`
- `CL-006` Whether admins can move an order directly from `Assigned` to `Delivered`
- `CL-007` Whether duplicate basket lines in the same order should be prevented and merged into quantity
- `CL-008` Whether `materials.unit` should be free text or controlled values
- `CL-009` Whether failed payment attempts should be stored in a separate payments log

## 10. Recommended First Implementation Slice

- admin authentication and admin-only access
- material CRUD
- basket CRUD with basket-material management
- store CRUD with basket-store assignment
- delivery agent CRUD with active/inactive state
- order entity with order basket values
- order status history
- manual agent assignment
- admin order management screens
