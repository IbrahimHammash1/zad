<?php

namespace App\Repositories\Customer\Eloquent;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Customer\Contracts\UserRepositoryInterface;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function createCustomerUser(array $attributes): User
    {
        return User::query()->create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);
    }

    public function findCustomerUserByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->where('role', UserRole::Customer)
            ->with('customer')
            ->first();
    }
}
