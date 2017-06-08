<?php

namespace MTRDesign\LaravelLogoFetcher\Providers;

use MTRDesign\LaravelLogoFetcher\Exceptions\InvalidProviderException;
use MTRDesign\LaravelLogoFetcher\Exceptions\ProviderNotFoundException;

interface FactoryContract
{
    /**
     * Get provider instance by key
     *
     * @param string $key
     * @return ProviderContract
     * @throws InvalidProviderException
     * @throws ProviderNotFoundException
     */
    public function provider($key);
}