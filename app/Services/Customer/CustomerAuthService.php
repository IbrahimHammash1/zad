<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\CustomerApiToken;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\Customer\Contracts\CustomerApiTokenRepositoryInterface;
use App\Repositories\Customer\Contracts\CustomerRepositoryInterface;
use App\Repositories\Customer\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomerAuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected CustomerRepositoryInterface $customerRepository,
        protected CustomerApiTokenRepositoryInterface $tokenRepository,
    ) {}

    public function register(array $attributes): array
    {
        return DB::transaction(function () use ($attributes): array {
            $user = $this->userRepository->createCustomerUser([
                'name' => $attributes['full_name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
            ]);

            $customer = $this->customerRepository->createForUser($user->id, [
                'full_name' => $attributes['full_name'],
                'phone' => $attributes['phone'],
                'country' => $attributes['country'],
                'preferred_locale' => $attributes['preferred_locale'] ?? config('app.locale'),
            ]);

            return [
                'customer' => $this->customerRepository->loadProfile($customer),
                'token' => $this->issueToken($customer, $attributes['device_name'] ?? 'mobile-app'),
            ];
        });
    }

    public function login(string $email, string $password, string $deviceName = 'mobile-app'): array
    {
        $user = $this->userRepository->findCustomerUserByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        if (! $this->isActiveCustomerUser($user)) {
            throw new HttpResponseException(response()->json([
                'message' => 'This customer account is inactive.',
            ], 403));
        }

        return [
            'customer' => $this->customerRepository->loadProfile($user->customer),
            'token' => $this->issueToken($user->customer, $deviceName),
        ];
    }

    public function resolveCustomerFromBearerToken(?string $bearerToken): ?array
    {
        if (! is_string($bearerToken) || ! str_contains($bearerToken, '|')) {
            return null;
        }

        [$tokenId, $plainTextToken] = explode('|', $bearerToken, 2);

        $token = $this->tokenRepository->findWithCustomerAndUserById($tokenId);

        if (! $token || ! hash_equals($token->token, hash('sha256', $plainTextToken))) {
            return null;
        }

        $customer = $token->customer;

        if (! $customer || ! $customer->user || ! $customer->is_active || ! $customer->user->is_active) {
            return null;
        }

        $this->tokenRepository->touchLastUsed($token);

        return [
            'customer' => $customer,
            'token' => $token,
        ];
    }

    public function revokeToken(?CustomerApiToken $token): void
    {
        if ($token) {
            $this->tokenRepository->delete($token);
        }
    }

    protected function issueToken(Customer $customer, string $deviceName): string
    {
        $plainTextToken = Str::random(64);
        $token = $this->tokenRepository->create($customer, $deviceName, hash('sha256', $plainTextToken));

        return sprintf('%s|%s', $token->id, $plainTextToken);
    }

    protected function isActiveCustomerUser(User $user): bool
    {
        return $user->is_active && $user->customer !== null && $user->customer->is_active;
    }
}
