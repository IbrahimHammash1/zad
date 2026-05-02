<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CustomerOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
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

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'recipient_phone.regex' => 'Recipient phone must be a valid Syria mobile number (09XXXXXXXX).',
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'recipient_name' => [
                'description' => 'Recipient full name.',
                'example' => 'Ahmad Ali',
            ],
            'recipient_phone' => [
                'description' => 'Recipient Syria mobile number.',
                'example' => '0999999999',
            ],
            'delivery_address' => [
                'description' => 'Delivery address inside Syria.',
                'example' => 'Damascus, Mazzeh',
            ],
            'notes' => [
                'description' => 'Optional delivery notes.',
                'example' => 'Call before delivery',
            ],
            'basket_lines' => [
                'description' => 'Selected basket lines.',
                'example' => [
                    [
                        'basket_id' => 1,
                        'store_id' => 1,
                        'quantity' => 2,
                    ],
                ],
            ],
            'basket_lines[].basket_id' => [
                'description' => 'Basket ID.',
                'example' => 1,
            ],
            'basket_lines[].store_id' => [
                'description' => 'Approved store ID for the selected basket.',
                'example' => 1,
            ],
            'basket_lines[].quantity' => [
                'description' => 'Basket quantity.',
                'example' => 2,
            ],
        ];
    }
}
