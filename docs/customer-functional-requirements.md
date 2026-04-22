# Customer Functional Requirements

## 1. Purpose

This document defines the implementation-ready customer requirements for the customer application.

Primary source:
- `MVP Requirements Document.docx`

This file is the working source of truth for customer scope. It includes:

- requirements derived from the original MVP document
- clarified customer behavior
- accepted product decisions already reflected in the current backend and admin model

## 2. Current Product Decisions

These decisions are now part of the working customer requirements baseline:

- baskets are composed of `materials`
- basket composition is fixed and managed by admins only
- one order may contain one or more baskets
- each basket selected inside an order has its own store selection
- order operational status is tracked at the order level
- historical basket and store values must remain stable after order creation
- customer tracking remains status-based only in MVP

## 3. Scope

This document covers the customer-facing application and customer workflows for the MVP, including:

- customer authentication
- basket browsing
- basket detail viewing
- approved store selection
- recipient information entry
- order placement
- Stripe payment
- order status tracking
- order history
- customer localization
- customer privacy and access boundaries

This document does not define admin dashboard behavior except where it directly affects the customer experience.

## 4. Source Traceability

| Source Section | Source Topic | Covered In |
| --- | --- | --- |
| Section 1 | Platform overview | `FR-CUS-001` to `FR-CUS-004` |
| Section 2 | MVP goal | `FR-CUS-101` to `FR-CUS-110`, `FR-CUS-301` to `FR-CUS-309` |
| Section 3.1 | Customer actor | `FR-CUS-101` to `FR-CUS-110`, `FR-CUS-401` to `FR-CUS-406` |
| Section 4 | Basket core concept | `FR-CUS-101` to `FR-CUS-110` |
| Section 5 | End-to-end flow | `FR-CUS-101` to `FR-CUS-309`, `FR-CUS-401` to `FR-CUS-406` |
| Section 6 | Customer application requirements | `FR-CUS-201` to `FR-CUS-209`, `FR-CUS-501` to `FR-CUS-502` |
| Section 8 | Payment system | `FR-CUS-301` to `FR-CUS-309` |
| Section 9 | Delivery system | `FR-CUS-401` to `FR-CUS-406` |
| Section 10 | Order status flow | `FR-CUS-401` to `FR-CUS-406` |
| Section 11 | Non-functional requirements | `NFR-CUS-001` to `NFR-CUS-007` |
| Section 12 | Security requirements | `FR-CUS-602`, `NFR-CUS-006`, `NFR-CUS-007` |
| Section 13 | MVP scope summary | `MVP-CUS-001` to `MVP-CUS-012` |

Note:
- some requirements below reflect accepted implementation decisions that clarify the original MVP document

## 5. MVP Scope Classification

### 5.1 In Scope

- `MVP-CUS-001` Predefined baskets only
- `MVP-CUS-002` Approved stores per basket
- `MVP-CUS-003` Stripe payments
- `MVP-CUS-004` Status-based tracking only
- `MVP-CUS-005` Basket browsing and detail viewing
- `MVP-CUS-006` Customer registration and login
- `MVP-CUS-007` Order placement with recipient details
- `MVP-CUS-008` Order history
- `MVP-CUS-009` Arabic and English support
- `MVP-CUS-010` One order may contain multiple baskets
- `MVP-CUS-011` Store selection is captured per basket inside an order

### 5.2 Out of Scope

- `MVP-CUS-012` Live tracking or GPS
- `MVP-CUS-013` User-customized baskets
- `MVP-CUS-014` Subscription models
- `MVP-CUS-015` Cash on delivery

## 6. Functional Requirements

### 6.1 Customer Identity and Access

#### `FR-CUS-001`

The system shall allow a customer to register for an account.

Priority: Must have

Acceptance criteria:

- A new customer can create an account using the customer application.

#### `FR-CUS-002`

The system shall allow a customer to sign in to the customer application.

Priority: Must have

Acceptance criteria:

- A registered customer can authenticate and access their account.

#### `FR-CUS-003`

The system shall allow an authenticated customer to sign out.

Priority: Must have

Acceptance criteria:

- An authenticated customer can end their session.

#### `FR-CUS-004`

The system shall restrict customer account access to the authenticated customer only.

Priority: Must have

Acceptance criteria:

- A customer cannot access another customer's account data or orders.
- Authorization checks are enforced server-side.

### 6.2 Basket Discovery and Selection

#### `FR-CUS-101`

The system shall allow a customer to browse available predefined baskets.

Priority: Must have

Acceptance criteria:

- The customer can view a basket listing screen.
- The listing only contains baskets available for ordering.

#### `FR-CUS-102`

The system shall allow a customer to open basket details.

Priority: Must have

Acceptance criteria:

- The customer can open a basket from the listing and view its details.

#### `FR-CUS-103`

The basket details view shall display the basket composition.

Priority: Must have

Acceptance criteria:

- The customer can view the materials included in the basket.
- The customer can view the quantity for each material.

#### `FR-CUS-104`

The basket details view shall display the fixed total price for the basket.

Priority: Must have

Acceptance criteria:

- The customer can view the basket price before ordering.

#### `FR-CUS-105`

The basket details view shall display the approved stores for the basket.

Priority: Must have

Acceptance criteria:

- The customer can view one or more approved stores linked to the basket.

#### `FR-CUS-106`

The system shall only allow a customer to select from approved stores linked to the basket.

Priority: Must have

Acceptance criteria:

- The customer cannot choose a store that is not linked to the basket.

#### `FR-CUS-107`

The system shall support a basket as a fixed predefined package.

Priority: Must have

Acceptance criteria:

- The customer application does not expose basket customization in MVP.

#### `FR-CUS-108`

The system shall support one order containing one or more baskets.

Priority: Must have

Acceptance criteria:

- A customer can prepare an order containing multiple selected baskets.

#### `FR-CUS-109`

The system shall support store selection per basket inside an order.

Priority: Must have

Acceptance criteria:

- Each selected basket in the order captures its own approved store.

#### `FR-CUS-110`

The system should preserve historical basket and selected store values after order creation.

Priority: Must have

Acceptance criteria:

- Changes to baskets or store assignments after checkout do not alter the order data shown for historical customer orders.

### 6.3 Checkout and Order Placement

#### `FR-CUS-201`

The system shall allow a customer to create an order from selected baskets.

Priority: Must have

Acceptance criteria:

- The customer can proceed from basket selection into an order placement flow.

#### `FR-CUS-202`

The system shall require recipient name for order placement.

Priority: Must have

Acceptance criteria:

- The customer must enter a recipient name before checkout can continue.

#### `FR-CUS-203`

The system shall require recipient phone number inside Syria for order placement.

Priority: Must have

Acceptance criteria:

- The customer must enter a recipient phone number before checkout can continue.

#### `FR-CUS-204`

The system shall require delivery address for order placement.

Priority: Must have

Acceptance criteria:

- The customer must enter a delivery address before checkout can continue.

#### `FR-CUS-205`

The system shall allow an optional order note.

Priority: Should have

Acceptance criteria:

- The customer can provide optional notes with the order.

#### `FR-CUS-206`

The system shall store quantity per basket line inside the order.

Priority: Must have

Acceptance criteria:

- Each ordered basket line stores a quantity.

#### `FR-CUS-207`

The system shall present an order review before payment.

Priority: Must have

Acceptance criteria:

- The customer can review selected baskets, selected stores, recipient details, and order totals before payment.

#### `FR-CUS-208`

The system shall create the order with status `Pending` after successful payment.

Priority: Must have

Acceptance criteria:

- A successfully paid new order starts with `Pending` status.

#### `FR-CUS-209`

The system shall not create a valid customer order before payment succeeds.

Priority: Must have

Acceptance criteria:

- The customer does not receive a normal placed order if payment has not been confirmed.

### 6.4 Payment

#### `FR-CUS-301`

The system shall use Stripe for customer payment processing.

Priority: Must have

Acceptance criteria:

- Customer payment in MVP is processed through Stripe.

#### `FR-CUS-302`

The system shall require online payment to complete order placement.

Priority: Must have

Acceptance criteria:

- Checkout cannot be completed without online payment.

#### `FR-CUS-303`

The system shall confirm payment before confirming order creation to the customer.

Priority: Must have

Acceptance criteria:

- The customer sees order confirmation only after payment succeeds.

#### `FR-CUS-304`

The system shall not support cash on delivery in MVP.

Priority: Must have

Acceptance criteria:

- The customer application does not expose cash-on-delivery as a payment option.

#### `FR-CUS-305`

The system shall inform the customer when payment fails.

Priority: Must have

Acceptance criteria:

- The customer receives a visible payment failure result when the payment is unsuccessful.

#### `FR-CUS-306`

The system shall not create a valid order when payment fails.

Priority: Must have

Acceptance criteria:

- Failed payment does not produce a normal customer order in order history.

#### `FR-CUS-307`

The system should allow the customer to retry checkout after a payment failure.

Priority: Should have

Acceptance criteria:

- The customer can attempt payment again after an unsuccessful payment result.

#### `FR-CUS-308`

The system should present payment state clearly during checkout.

Priority: Should have

Acceptance criteria:

- The customer can distinguish between pending, failed, and successful payment outcomes.

#### `FR-CUS-309`

The system shall securely handle Stripe payment processing.

Priority: Must have

Acceptance criteria:

- The customer application does not expose payment-sensitive internals beyond the payment flow itself.

### 6.5 Order Tracking and History

#### `FR-CUS-401`

The system shall allow a customer to view their order history.

Priority: Must have

Acceptance criteria:

- The customer can access a list of their own past and current orders.

#### `FR-CUS-402`

The system shall allow a customer to view the details of one of their orders.

Priority: Must have

Acceptance criteria:

- The customer can open an order detail view from order history.

#### `FR-CUS-403`

The customer order details view shall display ordered baskets, selected stores, recipient details, notes, and order status.

Priority: Must have

Acceptance criteria:

- All relevant order details are visible to the customer for their own order.

#### `FR-CUS-404`

The system shall support the following customer-visible order statuses in MVP: `Pending`, `Assigned`, `In Progress`, `Delivered`, and `Cancelled`.

Priority: Must have

Acceptance criteria:

- Only those statuses are exposed in MVP tracking unless explicitly extended later.

#### `FR-CUS-405`

The system shall support status-based tracking only.

Priority: Must have

Acceptance criteria:

- Customer tracking is limited to order status and related status changes.

#### `FR-CUS-406`

The system shall not require live location tracking or GPS in MVP.

Priority: Must have

Acceptance criteria:

- The customer application does not depend on live delivery tracking.

### 6.6 Localization

#### `FR-CUS-501`

The customer application shall support English and Arabic.

Priority: Must have

Acceptance criteria:

- Customer-facing application text can be presented in English and Arabic.

#### `FR-CUS-502`

The customer application shall support right-to-left layout behavior for Arabic.

Priority: Must have

Acceptance criteria:

- Arabic customer views render with RTL-compatible layout behavior.

### 6.7 Privacy and Security

#### `FR-CUS-601`

The system shall ensure that a customer can only view their own orders.

Priority: Must have

Acceptance criteria:

- A customer cannot view another customer's order list or order detail.

#### `FR-CUS-602`

The system shall securely handle recipient addresses and phone numbers.

Priority: Must have

Acceptance criteria:

- Personal recipient data is only accessible to authorized internal roles and the owning customer where required for the order.

#### `FR-CUS-603`

The system shall keep payment-sensitive data inaccessible to delivery agents.

Priority: Must have

Acceptance criteria:

- Delivery-agent-facing operational views do not expose customer payment data.

## 7. Customer Non-Functional Requirements

#### `NFR-CUS-001`

The customer application shall support simple and fast user flows.

Acceptance criteria:

- Common tasks such as browsing baskets, selecting stores, and completing checkout can be completed without unnecessary steps.

#### `NFR-CUS-002`

The system shall support global users.

Acceptance criteria:

- Customer-facing flows do not assume local-only usage.

#### `NFR-CUS-003`

The system shall support cross-border payment usage.

Acceptance criteria:

- Customer payment flow works for the intended overseas user base.

#### `NFR-CUS-004`

The system shall provide reliable order status visibility.

Acceptance criteria:

- Customers can consistently view the current status of their orders.

#### `NFR-CUS-005`

The system shall support bilingual customer usage in English and Arabic.

Acceptance criteria:

- Customer-facing content and layout framework support both languages.

#### `NFR-CUS-006`

The system shall protect personal address and phone data.

Acceptance criteria:

- Personal delivery data is not exposed outside authorized access boundaries.

#### `NFR-CUS-007`

The system shall securely handle Stripe-based payment processing.

Acceptance criteria:

- The payment flow preserves secure handling expectations for customer payment data.

## 8. Business Rules

- `BR-CUS-001` A customer order is valid only after successful payment.
- `BR-CUS-002` A customer can order one or more predefined baskets.
- `BR-CUS-003` Each ordered basket line must be linked to one approved store.
- `BR-CUS-004` Basket composition is fixed and not customer-editable in MVP.
- `BR-CUS-005` Customer tracking is status-based only in MVP.
- `BR-CUS-006` Cash on delivery is not supported in MVP.
- `BR-CUS-007` Historical basket and store values should remain stable after order creation.

## 9. Open Clarifications

These are not blockers for starting customer implementation, but they should be confirmed before detailed UI and API enforcement:

- `CL-CUS-001` Whether guest checkout is allowed or authentication is always required
- `CL-CUS-002` Whether duplicate basket lines in the same order should be merged into quantity
- `CL-CUS-003` Whether the customer can edit or cancel an order before admin assignment
- `CL-CUS-004` Whether the customer should see status history timestamps or current status only
- `CL-CUS-005` Whether currency display is fixed to one currency in MVP
- `CL-CUS-006` Whether stored customer profile fields extend beyond name, email, phone, country, and preferred locale
- `CL-CUS-007` Whether the customer application should expose store details beyond store name for trust and selection
- `CL-CUS-008` Where the customer Stitch design artifact is stored and whether it is now the visual source of truth for implementation

## 10. Recommended First Customer Implementation Slice

- customer authentication
- basket listing and basket details
- approved store selection
- order placement with recipient details
- Stripe checkout entry flow
- customer order history
- customer order detail with status display
