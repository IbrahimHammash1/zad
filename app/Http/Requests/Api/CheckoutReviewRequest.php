<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'regex:/^09\d{8}$/'],
            'delivery_address' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'basket_lines' => ['required', 'array', 'min:1'],
            'basket_lines.*.basket_id' => ['required', 'integer'],
            'basket_lines.*.store_id' => ['required', 'integer'],
            'basket_lines.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_phone.regex' => 'Recipient phone must be a valid Syria mobile number (09XXXXXXXX).',
        ];
    }
}
