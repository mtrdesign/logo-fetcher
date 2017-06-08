<?php

namespace MTRDesign\LaravelLogoFetcher\Providers;

use Psr\Http\Message\ResponseInterface;

class Clearbit implements ProviderContract
{
    /**
     * Unique identifier for the provider
     *
     * @return string
     */
    public function key()
    {
        return 'clearbit';
    }

    /**
     * What's the request verb (GET, POST, etc)
     *
     * @return string
     */
    public function method()
    {
        return 'GET';
    }

    /**
     * Where to send the request to
     *
     * @param string $domain
     * @param int $size In pixels
     * @param string $format
     * @return string
     */
    public function url($domain, $size, $format)
    {
        return config('logo_fetcher.clearbit.endpoint') . $domain . '?size=' . $size . '&format=' . $format;
    }

    /**
     * Transform the response to an image
     *
     * @param ResponseInterface $response
     * @return string
     */
    public function logoFromResponse(ResponseInterface $response)
    {
        return $response->getBody();
    }
}