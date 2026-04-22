<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerOrderDetailResource;
use App\Http\Resources\CustomerOrderListResource;
use App\Models\Customer;
use App\Services\Customer\CustomerOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerOrderController extends Controller
{
    public function __construct(protected CustomerOrderService $customerOrderService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer');

        return CustomerOrderListResource::collection($this->customerOrderService->listForCustomer($customer));
    }

    public function show(Request $request, int $orderId): CustomerOrderDetailResource
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('customer');

        return CustomerOrderDetailResource::make($this->customerOrderService->getByIdForCustomer($customer, $orderId));
    }
}
