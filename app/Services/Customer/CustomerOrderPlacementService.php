<?php

namespace App\Services\Customer;

use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Payments\Data\PaymentRequestData;
use App\Repositories\Customer\Contracts\OrderRepositoryInterface;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerOrderPlacementService
{
    public function __construct(
        protected CustomerOrderReviewService $orderReviewService,
        protected PaymentService $paymentService,
        protected OrderRepositoryInterface $orderRepository,
    ) {}

    public function place(Customer $customer, array $payload): Order
    {
        $review = $this->orderReviewService->review($payload);
        $paymentResult = $this->paymentService->charge(new PaymentRequestData(
            amount: $review['subtotal'],
            currency: $review['currency'],
            customerId: $customer->id,
            recipientName: $review['recipient']['name'],
        ));

        if ($paymentResult->status !== PaymentStatus::Succeeded) {
            throw ValidationException::withMessages([
                'payment' => 'Payment was not successful.',
            ]);
        }

        return DB::transaction(
            fn (): Order => $this->orderRepository->createPaidOrder($customer, $review, $paymentResult),
        );
    }
}
