<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:32', 'regex:/^\d+$/'],
            'country' => ['required', 'string', 'max:120'],
            'preferred_locale' => ['nullable', 'string', Rule::in(['en', 'ar'])],
            'device_name' => ['nullable', 'string', 'max:120'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function bodyParameters(): array
    {
        return [
            'full_name' => [
                'description' => 'Customer full name.',
                'example' => 'Customer One',
            ],
            'email' => [
                'description' => 'Customer email address.',
                'example' => 'customer@example.com',
            ],
            'password' => [
                'description' => 'Customer password.',
                'example' => 'password123',
            ],
            'phone' => [
                'description' => 'Customer phone number. Digits only.',
                'example' => '0999999999',
            ],
            'country' => [
                'description' => 'Customer country.',
                'example' => 'Syria',
            ],
            'preferred_locale' => [
                'description' => 'Preferred locale.',
                'example' => 'en',
            ],
            'device_name' => [
                'description' => 'Device name used for the issued token.',
                'example' => 'iPhone',
            ],
        ];
    }
}
