<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutReviewRequest;
use App\Models\Customer;
use App\Services\Customer\CustomerCheckoutService;
use Illuminate\Http\JsonResponse;

class CustomerCheckoutController extends Controller
{
    public function __construct(protected CustomerCheckoutService $customerCheckoutService) {}

    public function review(CheckoutReviewRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->customerCheckoutService->review($request->validated()),
        ]);
    }

    public function createIntent(CheckoutReviewRequest $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer');

        $intent = $this->customerCheckoutService->createIntent($customer, $request->validated());

        return response()->json([
            'data' => [
                'id' => $intent->id,
                'currency' => $intent->currency,
                'subtotal' => $intent->subtotal,
                'expires_at' => $intent->expires_at?->toISOString(),
                'basket_lines' => $intent->line_items,
            ],
        ], 201);
    }
}
