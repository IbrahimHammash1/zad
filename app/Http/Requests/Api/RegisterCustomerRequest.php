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
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'phone' => ['required', 'string', 'max:32'],
            'country' => ['required', 'string', 'max:120'],
            'preferred_locale' => ['nullable', 'string', Rule::in(['en', 'ar'])],
            'device_name' => ['nullable', 'string', 'max:120'],
        ];
    }
}
