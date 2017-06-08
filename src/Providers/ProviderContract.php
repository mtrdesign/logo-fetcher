<?php

namespace MTRDesign\LaravelLogoFetcher\Providers;

use Psr\Http\Message\ResponseInterface;

interface ProviderContract
{
    /**
     * Unique identifier for the provider
     *
     * @return string
     */
    public function key();

    /**
     * What's the request verb (GET, POST, etc)
     *
     * @return string
     */
    public function method();

    /**
     * Where to send the request to
     *
     * @param string $domain
     * @param int $size In mb
     * @param string $format
     * @return string
     */
    public function url($domain, $size, $format);

    /**
     * Transform the response to an image
     *
     * @param ResponseInterface $response
     * @return string
     */
    public function logoFromResponse(ResponseInterface $response);
}