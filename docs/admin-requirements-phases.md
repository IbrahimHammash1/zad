# Admin Requirements Phases

This file divides the admin implementation into phases using only:

- [admin-functional-requirements.md](./admin-functional-requirements.md)

This is a requirement-driven phase breakdown. If a phase description ever conflicts with the requirements file, the requirements file wins.

## Phase 1: Access and Admin Foundation

Goal:
- establish admin access, authorization, and the minimum platform foundation

Requirements:
- `FR-ADM-001`
- `FR-ADM-002`
- `FR-ADM-003`
- `NFR-ADM-006`
- `NFR-ADM-007`

Scope:
- admin authentication
- admin-only dashboard access
- server-side authorization baseline
- secure handling of admin-only data access

Exit criteria:
- admin can sign in
- non-admin users cannot access admin routes
- admin sees the dashboard shell and protected sections

## Phase 2: Materials Catalog

Goal:
- create the reusable material catalog used by baskets

Requirements:
- `FR-ADM-050`
- `FR-ADM-051`
- `FR-ADM-052`
- `FR-ADM-053`

Scope:
- create material
- edit material
- delete or archive material
- support reuse of materials across baskets

Exit criteria:
- admin can manage materials
- materials are reusable in basket composition

## Phase 3: Basket Catalog and Composition

Goal:
- let admins manage baskets, their composition, and their business definition

Requirements:
- `FR-ADM-101`
- `FR-ADM-102`
- `FR-ADM-103`
- `FR-ADM-104`
- `FR-ADM-105`
- `FR-ADM-106`
- `FR-ADM-109`
- `FR-ADM-110`
- `FR-ADM-111`
- `FR-ADM-112`
- `FR-ADM-113`
- `FR-ADM-114`

Scope:
- basket CRUD
- attach materials to baskets
- set basket material quantities
- enforce reusable basket-material relationships
- preserve historical basket values for already ordered basket lines

Exit criteria:
- admin can create and maintain baskets
- each basket can contain multiple materials
- duplicate material assignment within the same basket is blocked

## Phase 4: Store Management and Basket Availability

Goal:
- manage stores and control which baskets each store can fulfill

Requirements:
- `FR-ADM-107`
- `FR-ADM-108`
- `FR-ADM-201`
- `FR-ADM-202`
- `FR-ADM-203`
- `FR-ADM-204`
- `FR-ADM-205`
- `FR-ADM-206`
- `FR-ADM-207`

Scope:
- store CRUD
- assign baskets to stores
- enforce approved-store rules for baskets
- preserve historical store information for ordered basket lines

Exit criteria:
- admin can manage stores
- admin can control which baskets each store can fulfill
- invalid basket-store combinations are blocked

## Phase 5: Delivery Agent Management

Goal:
- manage delivery agents and assignment readiness

Requirements:
- `FR-ADM-301`
- `FR-ADM-302`
- `FR-ADM-303`
- `FR-ADM-305`
- `FR-ADM-505`

Scope:
- create delivery agent
- activate/deactivate delivery agent
- expose assignment-ready agent list
- keep assignment manual only

Exit criteria:
- admin can manage delivery agents
- only active agents are available for new assignments

## Phase 6: Orders Core

Goal:
- support order viewing and basket-level order structure

Requirements:
- `FR-ADM-401`
- `FR-ADM-402`
- `FR-ADM-403`
- `FR-ADM-416`
- `FR-ADM-417`
- `FR-ADM-418`
- `FR-ADM-419`
- `BR-002`
- `BR-003`
- `BR-010`

Scope:
- order listing
- order detail view
- one order containing one or more baskets
- quantity per ordered basket line
- visible selected store per ordered basket
- historical order basket values

Exit criteria:
- admin can inspect orders and their ordered basket lines
- each order basket line shows basket, store, and quantity
- historical order basket values remain stable after catalog changes

## Phase 7: Manual Delivery Workflow and Status Management

Goal:
- enable operational order handling from assignment to completion

Requirements:
- `FR-ADM-304`
- `FR-ADM-306`
- `FR-ADM-404`
- `FR-ADM-405`
- `FR-ADM-406`
- `FR-ADM-407`
- `FR-ADM-408`
- `FR-ADM-409`
- `FR-ADM-410`
- `FR-ADM-411`
- `FR-ADM-412`
- `FR-ADM-413`
- `FR-ADM-414`
- `FR-ADM-415`
- `FR-ADM-501`
- `FR-ADM-502`
- `FR-ADM-503`
- `FR-ADM-504`
- `NFR-ADM-002`
- `NFR-ADM-008`

Scope:
- assign delivery agent to order
- status transitions
- status history
- manual fulfillment workflow
- status-based tracking only
- no GPS/live tracking

Exit criteria:
- admin can assign an active delivery agent
- order status can move through the defined lifecycle
- status changes are recorded and visible

## Phase 8: Dashboard Widgets and Operational Summaries

Goal:
- give admins an immediate operational overview from the dashboard

Requirements:
- `FR-ADM-801`
- `FR-ADM-802`
- `FR-ADM-803`
- `FR-ADM-804`
- `FR-ADM-805`
- `NFR-ADM-001`

Scope:
- order status summary widgets
- unassigned or attention-needed workload summaries
- delivery-agent workload summaries
- recent operational activity widgets

Exit criteria:
- dashboard shows useful operational summaries without requiring resource navigation
- admin can identify order volume, assignment pressure, and recent activity from the dashboard

## Phase 9: Localization and Operational Polish

Goal:
- make the admin dashboard usable in production-like manual operations

Requirements:
- `FR-ADM-701`
- `FR-ADM-702`
- `NFR-ADM-001`
- `NFR-ADM-003`
- `NFR-ADM-004`
- `NFR-ADM-005`

Scope:
- English and Arabic support
- RTL support
- operationally efficient UI flows
- admin workflow polish

Exit criteria:
- dashboard supports English and Arabic
- Arabic layout works correctly with RTL
- common operational tasks are efficient

## Phase 10: Payment Visibility and Safety

Goal:
- support payment-aware order administration without exposing unsafe details

Requirements:
- `FR-ADM-601`
- `FR-ADM-602`
- `FR-ADM-603`
- `FR-ADM-604`
- `FR-ADM-605`
- `FR-ADM-606`
- `BR-001`
- `BR-009`

Scope:
- payment state visibility
- Stripe-based payment references
- no cash on delivery
- no access to payment-sensitive data for delivery agents

Exit criteria:
- admin can verify whether an order is paid
- failed payments do not produce normal valid orders
- delivery agents cannot access payment-sensitive information

## Phase Order Summary

1. Phase 1: Access and Admin Foundation
2. Phase 2: Materials Catalog
3. Phase 3: Basket Catalog and Composition
4. Phase 4: Store Management and Basket Availability
5. Phase 5: Delivery Agent Management
6. Phase 6: Orders Core
7. Phase 7: Manual Delivery Workflow and Status Management
8. Phase 8: Dashboard Widgets and Operational Summaries
9. Phase 9: Localization and Operational Polish
10. Phase 10: Payment Visibility and Safety

## Notes

- Open clarifications from the requirements file still apply.
- If a requirement is revised later, this phase file should be updated from the requirements file again.
- This phase file does not replace the requirements file.
