<?php

namespace MTRDesign\LaravelLogoFetcher;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use MTRDesign\LaravelLogoFetcher\Exceptions\MissingConfigException;
use MTRDesign\LaravelLogoFetcher\Providers\ProviderContract;
use MTRDesign\LaravelLogoFetcher\Providers\FactoryContract;
use MTRDesign\LaravelLogoFetcher\Exceptions\InvalidDomainException;
use MTRDesign\LaravelLogoFetcher\Exceptions\LogoNotFoundException;
use MTRDesign\LaravelLogoFetcher\Exceptions\MissingProviderException;
use MTRDesign\LaravelLogoFetcher\Exceptions\SaveFailedException;
use MTRDesign\LaravelLogoFetcher\Exceptions\UnexpectedException;

class LogoFetcher
{
    /** @var ClientInterface */
    protected $httpClient;

    /** @var FactoryContract */
    protected $factory;

    /** @var  ProviderContract */
    protected $provider;

    /**
     * Fetcher constructor.
     *
     * @param ClientInterface $httpClient
     * @param FactoryContract $factory
     */
    public function __construct(ClientInterface $httpClient, FactoryContract $factory)
    {
        $this->httpClient = $httpClient;
        $this->factory = $factory;
    }

    /**
     * Fetch and store company's logo using online tool
     *
     * @param string $domain
     * @return array With keys: string path
     * @throws InvalidDomainException
     * @throws LogoNotFoundException
     * @throws MissingConfigException
     * @throws MissingProviderException
     * @throws SaveFailedException
     * @throws UnexpectedException
     */
    public function fetch($domain)
    {
        if (!$this->provider) {
            throw new MissingProviderException('Firstly set a provider via the provider() method');
        }

        $config = config('logo_fetcher.general');

        if (!$config) {
            throw new MissingConfigException('Missing config file. Solution: php artisan vendor:publish --provider="MTRDesign\LaravelLogoFetcher\ServiceProvider"');
        }

        try {
            $response = $this->httpClient->request(
                $this->provider->method(),
                $this->provider->url($domain, $config['size'], $config['format'])
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $code = $response->getStatusCode();

            if ($code === 404) {
                throw new LogoNotFoundException('Logo not found');
            } elseif (strpos(strval($code), '4') === 0) {
                throw new InvalidDomainException('Invalid domain');
            }

            $exception = new UnexpectedException('Unexpected error');
            $exception->setException($e);
            throw $exception;
        } catch (\Exception $e) {
            $exception = new UnexpectedException('Unexpected error');
            $exception->setException($e);
            throw $exception;
        }

        $name = $domain . '-' . $this->provider->key() . '-' . $config['size'] . '.' . $config['format'];

        $path = storage_path('app' . DIRECTORY_SEPARATOR . $config['upload_path'] . $name);

        $save = file_put_contents(
            $path,
            $this->provider->logoFromResponse($response)
        );

        if ($save === false) {
            throw new SaveFailedException('Could not save the logo');
        }

        return ['path' => $config['upload_path'] . $name];
    }

    /**
     * Set provider by its key
     *
     * @param string $key
     * @return $this
     */
    public function provider($key)
    {
        $this->provider = $this->factory->provider($key);

        return $this;
    }
}