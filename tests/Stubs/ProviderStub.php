<?php

namespace Tests\Stubs;

use MTRDesign\LaravelLogoFetcher\Providers\ProviderContract;
use Psr\Http\Message\ResponseInterface;

class ProviderStub implements ProviderContract
{
    /**
     * Unique identifier for the provider
     *
     * @return string
     */
    public function key()
    {
        return 'stubbed';
    }

    /**
     * What's the request verb (GET, POST, etc)
     *
     * @return string
     */
    public function method()
    {
        return '';
    }

    /**
     * Where to send the request to
     *
     * @param string $domain
     * @param int $size In mb
     * @param string $format
     * @return string
     */
    public function url($domain, $size, $format)
    {
        return '';
    }

    /**
     * Transform the response to an image
     *
     * @param ResponseInterface $response
     * @return string
     */
    public function logoFromResponse(ResponseInterface $response)
    {
        return 'random-string';
    }
}