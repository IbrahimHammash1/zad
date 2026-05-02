<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Repositories\Customer\Contracts\CustomerRepositoryInterface;
use App\Repositories\Customer\Contracts\UserRepositoryInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected CustomerRepositoryInterface $customerRepository,
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
                'token' => $this->issueToken($user, $attributes['device_name'] ?? 'mobile-app'),
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
            'token' => $this->issueToken($user, $deviceName),
        ];
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    protected function issueToken(User $user, string $deviceName): string
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    protected function isActiveCustomerUser(User $user): bool
    {
        return $user->is_active && $user->customer !== null && $user->customer->is_active;
    }
}
