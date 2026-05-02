<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginCustomerRequest;
use App\Http\Requests\Api\RegisterCustomerRequest;
use App\Http\Resources\CustomerProfileResource;
use App\Models\Customer;
use App\Services\Customer\CustomerAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{
    public function __construct(protected CustomerAuthService $customerAuthService) {}

    /**
     * Register
     *
     * @group Customer Auth
     *
     * @unauthenticated
     *
     * @response 201 {
     *   "token": "1|plain-text-token",
     *   "token_type": "Bearer",
     *   "customer": {
     *     "id": 1,
     *     "full_name": "Customer One",
     *     "email": "customer@example.com",
     *     "phone": "0999999999",
     *     "country": "Syria",
     *     "preferred_locale": "en"
     *   }
     * }
     */
    public function register(RegisterCustomerRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->register($request->validated());

        return response()->json([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'customer' => CustomerProfileResource::make($result['customer']),
        ], 201);
    }

    /**
     * Login
     *
     * @group Customer Auth
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "token": "1|plain-text-token",
     *   "token_type": "Bearer",
     *   "customer": {
     *     "id": 1,
     *     "full_name": "Customer One",
     *     "email": "customer@example.com",
     *     "phone": "0999999999",
     *     "country": "Syria",
     *     "preferred_locale": "en"
     *   }
     * }
     */
    public function login(LoginCustomerRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->login(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
            $request->input('device_name', 'mobile-app'),
        );

        return response()->json([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'customer' => CustomerProfileResource::make($result['customer']),
        ]);
    }

    /**
     * Profile
     *
     * @group Customer Auth
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "full_name": "Customer One",
     *     "email": "customer@example.com",
     *     "phone": "0999999999",
     *     "country": "Syria",
     *     "preferred_locale": "en"
     *   }
     * }
     */
    public function me(Request $request): CustomerProfileResource
    {
        /** @var Customer $customer */
        $customer = $request->user()->customer;

        return CustomerProfileResource::make($customer->loadMissing('user'));
    }

    /**
     * Logout
     *
     * @group Customer Auth
     *
     * @response 204 {}
     */
    public function logout(Request $request): JsonResponse
    {
        $this->customerAuthService->revokeCurrentToken($request->user());

        return response()->json([], 204);
    }
}
