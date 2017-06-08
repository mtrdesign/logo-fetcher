<?php

namespace MTRDesign\LaravelLogoFetcher;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use MTRDesign\LaravelLogoFetcher\Providers\Factory;
use MTRDesign\LaravelLogoFetcher\Providers\FactoryContract;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FactoryContract::class, Factory::class);
        $this->app->bind(ClientInterface::class, Client::class);
    }
}
