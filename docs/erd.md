# ERD

This ERD is derived from [admin-functional-requirements.md](./admin-functional-requirements.md) and the original MVP requirements.

## Design Decisions

- `users` stores authentication and authorization data.
- `customers` stores customer domain data and links one-to-one with `users`.
- `recipients` are not stored as a separate entity because the requirements say recipients do not interact with the system.
- `delivery_agents` are modeled separately from `users` because the current MVP only requires manual assignment and operational access, not full customer-style authentication.
- `materials` is the reusable catalog of basket components.
- `basket_items` defines the composition of each basket and the quantity of each material inside it.
- `orders` is the order header and operational workflow record.
- `order_baskets` stores the baskets selected within an order.
- Historical basket/store values are kept on `order_baskets` without using `_snapshot` naming.
- `order_status_histories` makes status changes auditable.
- `payments` is modeled separately to keep Ziina-related data isolated from operational order data.

## Mermaid ER Diagram

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role
        boolean is_active
        datetime created_at
        datetime updated_at
    }

    CUSTOMERS {
        bigint id PK
        bigint user_id FK
        string full_name
        string phone
        string country
        string preferred_locale
        boolean is_active
        datetime created_at
        datetime updated_at
    }

    BASKETS {
        bigint id PK
        string name
        string slug UK
        text description
        decimal fixed_price
        boolean is_active
        datetime created_at
        datetime updated_at
    }

    MATERIALS {
        bigint id PK
        string name
        string slug UK
        string unit
        boolean is_active
        datetime created_at
        datetime updated_at
    }

    BASKET_ITEMS {
        bigint id PK
        bigint basket_id FK
        bigint material_id FK
        integer quantity
        integer sort_order
        datetime created_at
        datetime updated_at
    }

    STORES {
        bigint id PK
        string name
        string phone
        text address
        boolean is_active
        datetime created_at
        datetime updated_at
    }

    BASKET_STORE {
        bigint basket_id FK
        bigint store_id FK
        datetime created_at
        datetime updated_at
    }

    DELIVERY_AGENTS {
        bigint id PK
        string name
        string phone
        boolean is_active
        text notes
        datetime created_at
        datetime updated_at
    }

    ORDERS {
        bigint id PK
        bigint customer_id FK
        bigint delivery_agent_id FK
        string status
        string currency
        string recipient_name
        string recipient_phone
        text delivery_address
        text notes
        datetime paid_at
        datetime created_at
        datetime updated_at
    }

    ORDER_BASKETS {
        bigint id PK
        bigint order_id FK
        bigint basket_id FK
        bigint store_id FK
        integer quantity
        string basket_name
        decimal basket_price
        string store_name
        datetime created_at
        datetime updated_at
    }

    ORDER_STATUS_HISTORIES {
        bigint id PK
        bigint order_id FK
        string from_status
        string to_status
        bigint changed_by_user_id FK
        text notes
        datetime changed_at
        datetime created_at
        datetime updated_at
    }

    PAYMENTS {
        bigint id PK
        bigint order_id FK
        string provider
        string provider_reference UK
        string currency
        decimal amount
        string status
        datetime paid_at
        datetime created_at
        datetime updated_at
    }

    USERS ||--|| CUSTOMERS : owns_profile
    CUSTOMERS ||--o{ ORDERS : places
    BASKETS ||--o{ BASKET_ITEMS : composed_of
    MATERIALS ||--o{ BASKET_ITEMS : used_in
    STORES ||--o{ BASKET_STORE : allowed_for
    BASKETS ||--o{ BASKET_STORE : available_at
    BASKETS ||--o{ ORDER_BASKETS : selected_in
    STORES ||--o{ ORDER_BASKETS : chosen_for
    DELIVERY_AGENTS ||--o{ ORDERS : assigned_to
    ORDERS ||--o{ ORDER_BASKETS : contains
    ORDERS ||--o{ ORDER_STATUS_HISTORIES : has_status_history
    USERS ||--o{ ORDER_STATUS_HISTORIES : changed_by
    ORDERS ||--|| PAYMENTS : paid_by
```

## Entity Notes

### `users`

- Stores authentication credentials and authorization data.
- Suggested role values: `admin`, `customer`.

### `customers`

- Stores customer-specific business data separately from authentication.
- Links one-to-one with `users`.
- Allows `orders.customer_id` to point to a dedicated customer entity instead of a generic user row.

### `baskets`

- Represents a predefined basket with a fixed total price.
- `is_active` controls whether it can be shown to customers for new orders.
- Basket composition is defined through `basket_items`.

### `materials`

- Reusable catalog of grocery materials or components.
- Intended to avoid retyping the same item names across baskets.

### `basket_items`

- Join table between `baskets` and `materials`.
- Stores the quantity of each material included in a basket.
- `sort_order` controls display order inside a basket.

### `stores`

- Represents approved physical stores in Syria.
- Stores are linked to baskets through `basket_store`.

### `basket_store`

- Many-to-many pivot between baskets and stores.
- Enforces the rule that customers may only order a basket from one of its approved stores.

### `delivery_agents`

- Stores the delivery workforce managed manually by admins.
- `is_active` controls whether an agent can receive new assignments.

### `orders`

- Central operational entity and order header.
- Contains recipient information because recipients do not use the system directly.
- Stores customer, recipient, assignment, payment timing, and status data.

### `order_baskets`

- Join table between `orders` and `baskets`.
- Supports one order containing multiple baskets.
- Stores historical basket/store values at order time without `_snapshot` suffixes:
  - `basket_name`
  - `basket_price`
  - `store_name`

### `order_status_histories`

- Audit table for order lifecycle changes.
- Supports reliable status tracking and admin traceability.

### `payments`

- Keeps payment provider information separate from order operations.
- Recommended provider value in MVP: `ziina`.

## Recommended Constraints

- Unique index on `customers.user_id`.
- Unique index on `materials.slug`.
- Unique composite index on `basket_items (basket_id, material_id)`.
- Unique composite index on `basket_store (basket_id, store_id)`.
- Foreign key from `order_baskets.store_id` must reference a store allowed for the selected basket at creation time.
- Unique composite index on `order_baskets (order_id, basket_id, store_id)` if duplicate lines should be prevented.
- Only active delivery agents should be assignable to new orders.
- `payments.order_id` should be unique for MVP one-payment-per-order behavior.
- `orders.status` should be limited to `pending`, `assigned`, `in_progress`, `delivered`, `cancelled`.

## Open Decisions Before Migrations

- Whether to use soft deletes on `baskets`, `stores`, and `delivery_agents`.
- Whether to use soft deletes on `materials`.
- Whether `customers` should store additional contact metadata beyond phone and locale.
- Whether `delivery_agents` should later become authenticatable users.
- Whether an order may contain the same basket more than once as separate lines or should use quantity aggregation.
- Whether `materials.unit` should be free text or a controlled list.
- Whether failed Ziina attempts should be stored in a separate `payment_attempts` table.
