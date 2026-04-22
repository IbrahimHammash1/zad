<?php

namespace App\Repositories\Customer\Eloquent;

use App\Models\Customer;
use App\Models\CustomerApiToken;
use App\Repositories\Customer\Contracts\CustomerApiTokenRepositoryInterface;

class EloquentCustomerApiTokenRepository implements CustomerApiTokenRepositoryInterface
{
    public function create(Customer $customer, string $name, string $hashedToken): CustomerApiToken
    {
        return $customer->apiTokens()->create([
            'name' => $name,
            'token' => $hashedToken,
        ]);
    }

    public function findWithCustomerAndUserById(int|string $tokenId): ?CustomerApiToken
    {
        return CustomerApiToken::query()
            ->with(['customer.user'])
            ->find($tokenId);
    }

    public function touchLastUsed(CustomerApiToken $token): void
    {
        $token->forceFill([
            'last_used_at' => now(),
        ])->save();
    }

    public function delete(CustomerApiToken $token): void
    {
        $token->delete();
    }
}
