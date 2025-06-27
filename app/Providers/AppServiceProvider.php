<?php

namespace App\Providers;

use App\Contract\CartServiceInterface;
use App\Services\SessionCartService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use WpOrg\Requests\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CartServiceInterface::class,
            SessionCartService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Number::useCurrency("IDR");
    }
}
