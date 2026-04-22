<?php

namespace App\Http\Middleware;

use App\Services\Customer\CustomerAuthService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomerApi
{
    public function __construct(protected CustomerAuthService $customerAuthService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $result = $this->customerAuthService->resolveCustomerFromBearerToken($request->bearerToken());

        if (! $result) {
            return $this->unauthorizedResponse();
        }

        Auth::setUser($result['customer']->user);

        $request->setUserResolver(fn () => $result['customer']->user);
        $request->attributes->set('customer', $result['customer']);
        $request->attributes->set('customer_api_token', $result['token']);

        return $next($request);
    }

    protected function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }
}
