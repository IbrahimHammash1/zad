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
     * List Baskets
     *
     * @group Customer Baskets
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Family Basket",
     *       "slug": "family-basket",
     *       "description": "Balanced monthly essentials for a small family.",
     *       "fixed_price": "25.50",
     *       "image_url": "https://example.com/storage/baskets/family-basket.jpg",
     *       "materials_count": 4,
     *       "approved_stores_count": 2
     *     }
     *   ]
     * }
     */
    public function index(): AnonymousResourceCollection
    {
        return BasketListResource::collection($this->customerBasketService->listAvailable());
    }

    /**
     * Basket Details
     *
     * @group Customer Baskets
     *
     * @unauthenticated
     *
     * @urlParam basketSlug string required Basket slug. Example: family-basket
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Family Basket",
     *     "slug": "family-basket",
     *     "description": "Balanced monthly essentials for a small family.",
     *     "fixed_price": "25.50",
     *     "image_url": "https://example.com/storage/baskets/family-basket.jpg",
     *     "materials": [
     *       {
     *         "id": 1,
     *         "name": "Rice",
     *         "slug": "rice",
     *         "unit": "kg",
     *         "image_url": "https://example.com/storage/materials/rice.jpg",
     *         "quantity": 3,
     *         "sort_order": 1
     *       }
     *     ],
     *     "approved_stores": [
     *       {
     *         "id": 1,
     *         "name": "Damascus Main Store",
     *         "phone": "0111111111",
     *         "address": "Damascus - Mazzeh",
     *         "image_url": "https://example.com/storage/stores/damascus-main-store.jpg"
     *       }
     *     ]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Not Found"
     * }
     */
    public function show(string $basketSlug): BasketDetailResource
    {
        return BasketDetailResource::make($this->customerBasketService->getAvailableBySlug($basketSlug));
    }
}
