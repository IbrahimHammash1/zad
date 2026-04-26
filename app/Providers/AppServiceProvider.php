<?php

namespace App\Providers;

use App\Repositories\Customer\Contracts\BasketRepositoryInterface;
use App\Repositories\Customer\Contracts\CheckoutIntentRepositoryInterface;
use App\Repositories\Customer\Contracts\CustomerRepositoryInterface;
use App\Repositories\Customer\Contracts\OrderRepositoryInterface;
use App\Repositories\Customer\Contracts\UserRepositoryInterface;
use App\Repositories\Customer\Eloquent\EloquentBasketRepository;
use App\Repositories\Customer\Eloquent\EloquentCheckoutIntentRepository;
use App\Repositories\Customer\Eloquent\EloquentCustomerRepository;
use App\Repositories\Customer\Eloquent\EloquentOrderRepository;
use App\Repositories\Customer\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
        $this->app->bind(BasketRepositoryInterface::class, EloquentBasketRepository::class);
        $this->app->bind(CheckoutIntentRepositoryInterface::class, EloquentCheckoutIntentRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
