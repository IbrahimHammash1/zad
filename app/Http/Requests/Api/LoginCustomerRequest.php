<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'Customer email address.',
                'example' => 'customer@example.com',
            ],
            'password' => [
                'description' => 'Customer password.',
                'example' => 'password123',
            ],
            'device_name' => [
                'description' => 'Device name used for the issued token.',
                'example' => 'iPhone',
            ],
        ];
    }
}
