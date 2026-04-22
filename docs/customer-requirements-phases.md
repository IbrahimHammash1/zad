# Customer Requirements Phases

This file divides the customer implementation into phases using only:

- [customer-functional-requirements.md](./customer-functional-requirements.md)

This is a requirement-driven phase breakdown. If a phase description ever conflicts with the requirements file, the requirements file wins.

## Phase 1: Customer Access and API Foundation

Goal:
- establish customer authentication, access boundaries, and the mobile API baseline

Requirements:
- `FR-CUS-001`
- `FR-CUS-002`
- `FR-CUS-003`
- `FR-CUS-004`
- `NFR-CUS-006`
- `NFR-CUS-007`

Scope:
- customer registration
- customer sign-in and sign-out
- authenticated customer session or token handling for mobile APIs
- server-side ownership checks for customer-only data
- secure customer profile bootstrap for API use

Exit criteria:
- a customer can register and authenticate from the mobile app
- authenticated APIs are restricted to the owning customer
- unauthorized access to another customer's data is blocked server-side

## Phase 2: Basket Discovery and Selection

Goal:
- expose the active basket catalog and approved store options to the customer app

Requirements:
- `FR-CUS-101`
- `FR-CUS-102`
- `FR-CUS-103`
- `FR-CUS-104`
- `FR-CUS-105`
- `FR-CUS-106`
- `FR-CUS-107`

Scope:
- basket listing API
- basket detail API
- basket composition visibility
- fixed basket price visibility
- approved store visibility
- active-only basket discovery

Exit criteria:
- the customer can browse available baskets
- the customer can open basket details with materials, quantities, price, and approved stores
- the API only exposes ordering against approved stores

## Phase 3: Order Assembly and Checkout Input

Goal:
- support building an order with one or more basket lines and recipient information before payment

Requirements:
- `FR-CUS-108`
- `FR-CUS-109`
- `FR-CUS-110`
- `FR-CUS-201`
- `FR-CUS-202`
- `FR-CUS-203`
- `FR-CUS-204`
- `FR-CUS-205`
- `FR-CUS-206`
- `FR-CUS-207`
- `BR-CUS-002`
- `BR-CUS-003`
- `BR-CUS-004`
- `BR-CUS-007`

Scope:
- multi-basket order payload support
- quantity per basket line
- store selection per basket line
- recipient name, phone, address, and optional notes validation
- pre-payment order review or checkout summary API
- historical basket and store value capture rules

Exit criteria:
- the customer can prepare an order containing one or more basket lines
- each basket line stores quantity and one approved store
- the checkout flow returns a validated review summary before payment

## Phase 4: Order History and Status Tracking

Goal:
- let customers review their own current and past orders with MVP status tracking

Requirements:
- `FR-CUS-401`
- `FR-CUS-402`
- `FR-CUS-403`
- `FR-CUS-404`
- `FR-CUS-405`
- `FR-CUS-406`
- `FR-CUS-601`
- `BR-CUS-005`

Scope:
- customer order history API
- customer order detail API
- order line, store, recipient, and notes visibility
- current status exposure with MVP statuses only
- ownership enforcement for order list and detail

Exit criteria:
- a customer can see only their own orders
- order details show baskets, stores, recipient details, notes, and current status
- tracking remains status-based only with no GPS dependency

## Phase 5: Localization and Customer App Presentation Support

Goal:
- support bilingual customer usage for the mobile application

Requirements:
- `FR-CUS-501`
- `FR-CUS-502`
- `NFR-CUS-001`
- `NFR-CUS-002`
- `NFR-CUS-003`
- `NFR-CUS-004`
- `NFR-CUS-005`

Scope:
- locale-aware API responses where needed
- customer locale preference handling
- English and Arabic content support
- RTL-aware metadata or response conventions needed by the client
- flow simplification and response shape polish for mobile consumption

Exit criteria:
- the customer app can operate in English and Arabic
- Arabic flows can support RTL presentation requirements
- core customer flows remain simple and mobile-friendly

## Phase 6: Privacy and Access Hardening

Goal:
- harden customer data protection and role-based visibility boundaries

Requirements:
- `FR-CUS-602`
- `FR-CUS-603`
- `NFR-CUS-006`
- `NFR-CUS-007`

Scope:
- recipient data exposure controls
- payment-sensitive data minimization in customer-facing and operational responses
- delivery-agent visibility constraints
- final authorization and privacy review for customer APIs

Exit criteria:
- customer personal recipient data is exposed only where authorized
- delivery-agent-facing flows do not expose payment-sensitive data
- customer APIs enforce the intended privacy boundaries consistently

## Phase 7: Stripe Checkout and Payment Confirmation

Goal:
- complete payment-driven order creation through Stripe

Requirements:
- `FR-CUS-208`
- `FR-CUS-209`
- `FR-CUS-301`
- `FR-CUS-302`
- `FR-CUS-303`
- `FR-CUS-304`
- `FR-CUS-305`
- `FR-CUS-306`
- `FR-CUS-307`
- `FR-CUS-308`
- `FR-CUS-309`
- `BR-CUS-001`
- `BR-CUS-006`

Scope:
- Stripe checkout session or payment intent entry flow
- payment status handling
- success and failure result handling
- retry path after failed payment
- order creation only after successful payment confirmation

Exit criteria:
- checkout cannot complete without online payment
- successful payment creates a valid `Pending` order
- failed payment does not create a normal order

## Phase Order Summary

1. Phase 1: Customer Access and API Foundation
2. Phase 2: Basket Discovery and Selection
3. Phase 3: Order Assembly and Checkout Input
4. Phase 4: Order History and Status Tracking
5. Phase 5: Localization and Customer App Presentation Support
6. Phase 6: Privacy and Access Hardening
7. Phase 7: Stripe Checkout and Payment Confirmation

## Notes

- Open clarifications from the requirements file still apply.
- If a requirement is revised later, this phase file should be updated from the requirements file again.
- This phase file does not replace the requirements file.
