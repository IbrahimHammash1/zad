<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustomerApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $customer = $user?->customer;

        if (! $user) {
            return $this->unauthorizedResponse();
        }

        if (
            $user->role !== UserRole::Customer
            || ! $user->is_active
            || ! $customer
            || ! $customer->is_active
        ) {
            return response()->json([
                'message' => 'This customer account is inactive.',
            ], 403);
        }

        $request->attributes->set('customer', $customer);

        return $next($request);
    }

    protected function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }
}
