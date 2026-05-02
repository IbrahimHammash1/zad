<?php

namespace App\Providers;

use App\Payments\Contracts\PaymentProviderInterface;
use App\Payments\Providers\ZiinaPaymentProvider;
use App\Repositories\Customer\Contracts\BasketRepositoryInterface;
use App\Repositories\Customer\Contracts\CustomerRepositoryInterface;
use App\Repositories\Customer\Contracts\OrderRepositoryInterface;
use App\Repositories\Customer\Contracts\UserRepositoryInterface;
use App\Repositories\Customer\Eloquent\EloquentBasketRepository;
use App\Repositories\Customer\Eloquent\EloquentCustomerRepository;
use App\Repositories\Customer\Eloquent\EloquentOrderRepository;
use App\Repositories\Customer\Eloquent\EloquentUserRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(PaymentProviderInterface::class, ZiinaPaymentProvider::class);
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
