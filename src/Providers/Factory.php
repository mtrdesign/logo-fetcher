<?php

namespace MTRDesign\LaravelLogoFetcher\Providers;

use MTRDesign\LaravelLogoFetcher\Exceptions\InvalidProviderException;
use MTRDesign\LaravelLogoFetcher\Exceptions\ProviderNotFoundException;

class Factory implements FactoryContract
{
    /**
     * Get provider instance by key
     * This is the default factory implementation utilizing the Laravel Container
     *
     * @param string $key
     * @return ProviderContract
     * @throws InvalidProviderException
     * @throws ProviderNotFoundException
     */
    public function provider($key)
    {
        $provider = app($key);

        if (!$provider) {
            throw new ProviderNotFoundException('No provider bound for key: ' . $key);
        }

        if (!$provider instanceof ProviderContract) {
            throw new InvalidProviderException('Provider must be instance of ' . ProviderContract::class);
        }

        return $provider;
    }
}