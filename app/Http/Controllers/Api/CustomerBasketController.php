<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasketDetailResource;
use App\Http\Resources\BasketListResource;
use App\Services\Customer\CustomerBasketService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerBasketController extends Controller
{
    public function __construct(protected CustomerBasketService $customerBasketService) {}

    /**
     * @unauthenticated
     */
    public function index(): AnonymousResourceCollection
    {
        return BasketListResource::collection($this->customerBasketService->listAvailable());
    }

    /**
     * @unauthenticated
     */
    public function show(string $basketSlug): BasketDetailResource
    {
        return BasketDetailResource::make($this->customerBasketService->getAvailableBySlug($basketSlug));
    }
}
