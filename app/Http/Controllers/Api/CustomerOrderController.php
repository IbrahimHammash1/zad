<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CustomerOrderRequest;
use App\Http\Resources\CustomerOrderDetailResource;
use App\Http\Resources\CustomerOrderListResource;
use App\Models\Customer;
use App\Services\Customer\CustomerOrderPlacementService;
use App\Services\Customer\CustomerOrderReviewService;
use App\Services\Customer\CustomerOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerOrderController extends Controller
{
    public function __construct(
        protected CustomerOrderService $customerOrderService,
        protected CustomerOrderReviewService $customerOrderReviewService,
        protected CustomerOrderPlacementService $customerOrderPlacementService,
    ) {}

    /**
     * Review Order
     *
     * @group Customer Orders
     *
     * Validates basket lines, approved store selection, recipient details, and returns totals without creating records.
     *
     * @response 200 {
     *   "data": {
     *     "currency": "USD",
     *     "recipient": {
     *       "name": "Ahmad Ali",
     *       "phone": "0999999999",
     *       "delivery_address": "Damascus, Mazzeh",
     *       "notes": "Call before delivery"
     *     },
     *     "basket_lines": [
     *       {
     *         "basket_id": 1,
     *         "basket_name": "Family Basket",
     *         "basket_slug": "family-basket",
     *         "store_id": 1,
     *         "store_name": "Main Store",
     *         "quantity": 2,
     *         "unit_price": "25.50",
     *         "line_total": "51.00"
     *       }
     *     ],
     *     "subtotal": "51.00"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Store 2 is not approved for basket 1.",
     *   "errors": {
     *     "basket_lines": [
     *       "Store 2 is not approved for basket 1."
     *     ]
     *   }
     * }
     */
    public function review(CustomerOrderRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->customerOrderReviewService->review($request->validated()),
        ]);
    }

    /**
     * List Orders
     *
     * @group Customer Orders
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "status": {
     *         "value": "pending",
     *         "label": "Pending"
     *       },
     *       "currency": "USD",
     *       "basket_lines_count": 1,
     *       "subtotal": "51.00",
     *       "created_at": "2026-05-03T00:00:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer');

        return CustomerOrderListResource::collection($this->customerOrderService->listForCustomer($customer));
    }

    /**
     * Create Order
     *
     * @group Customer Orders
     *
     * Uses the current Ziina payment provider flow and creates the real order only after payment succeeds.
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "status": {
     *       "value": "pending",
     *       "label": "Pending"
     *     },
     *     "currency": "USD",
     *     "recipient": {
     *       "name": "Ahmad Ali",
     *       "phone": "0999999999",
     *       "delivery_address": "Damascus, Mazzeh",
     *       "notes": "Call before delivery"
     *     },
     *     "basket_lines": [
     *       {
     *         "id": 1,
     *         "basket_id": 1,
     *         "basket_name": "Family Basket",
     *         "basket_price": "25.50",
     *         "store_id": 1,
     *         "store_name": "Main Store",
     *         "quantity": 2,
     *         "line_total": "51.00"
     *       }
     *     ],
     *     "subtotal": "51.00",
     *     "payment": {
     *       "provider": "ziina",
     *       "status": "succeeded",
     *       "amount": "51.00",
     *       "currency": "USD",
     *       "provider_reference": "ziina_00000000-0000-0000-0000-000000000000",
     *       "paid_at": "2026-05-03T00:00:00.000000Z"
     *     },
     *     "created_at": "2026-05-03T00:00:00.000000Z",
     *     "paid_at": "2026-05-03T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Store 2 is not approved for basket 1.",
     *   "errors": {
     *     "basket_lines": [
     *       "Store 2 is not approved for basket 1."
     *     ]
     *   }
     * }
     */
    public function store(CustomerOrderRequest $request): CustomerOrderDetailResource
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer');

        return CustomerOrderDetailResource::make(
            $this->customerOrderPlacementService->place($customer, $request->validated()),
        );
    }

    /**
     * Order Details
     *
     * @group Customer Orders
     *
     * @urlParam orderId integer required Order ID. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "status": {
     *       "value": "pending",
     *       "label": "Pending"
     *     },
     *     "currency": "USD",
     *     "recipient": {
     *       "name": "Ahmad Ali",
     *       "phone": "0999999999",
     *       "delivery_address": "Damascus, Mazzeh",
     *       "notes": "Call before delivery"
     *     },
     *     "basket_lines": [
     *       {
     *         "id": 1,
     *         "basket_id": 1,
     *         "basket_name": "Family Basket",
     *         "basket_price": "25.50",
     *         "store_id": 1,
     *         "store_name": "Main Store",
     *         "quantity": 2,
     *         "line_total": "51.00"
     *       }
     *     ],
     *     "subtotal": "51.00",
     *     "payment": {
     *       "provider": "ziina",
     *       "status": "succeeded",
     *       "amount": "51.00",
     *       "currency": "USD",
     *       "provider_reference": "ziina_00000000-0000-0000-0000-000000000000",
     *       "paid_at": "2026-05-03T00:00:00.000000Z"
     *     },
     *     "created_at": "2026-05-03T00:00:00.000000Z",
     *     "paid_at": "2026-05-03T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Not Found"
     * }
     */
    public function show(Request $request, int $orderId): CustomerOrderDetailResource
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer');

        return CustomerOrderDetailResource::make($this->customerOrderService->getByIdForCustomer($customer, $orderId));
    }
}
