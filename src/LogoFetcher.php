<?php

namespace MTRDesign\LaravelLogoFetcher;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Filesystem\FilesystemManager;
use MTRDesign\LaravelLogoFetcher\Providers\ProviderContract;
use MTRDesign\LaravelLogoFetcher\Providers\FactoryContract;
use MTRDesign\LaravelLogoFetcher\Exceptions\MissingConfigException;
use MTRDesign\LaravelLogoFetcher\Exceptions\MissingDomainException;
use MTRDesign\LaravelLogoFetcher\Exceptions\MissingLogoException;
use MTRDesign\LaravelLogoFetcher\Exceptions\InvalidDomainException;
use MTRDesign\LaravelLogoFetcher\Exceptions\LogoNotFoundException;
use MTRDesign\LaravelLogoFetcher\Exceptions\MissingProviderException;
use MTRDesign\LaravelLogoFetcher\Exceptions\SaveFailedException;
use MTRDesign\LaravelLogoFetcher\Exceptions\UnexpectedException;

class LogoFetcher
{
    /**
     * Where the logo was stored
     * The path is relative to the storage/app/ directory
     *
     * @var string
     */
    public $path;

    /**
     * What the provider returned for the given $domain
     *
     * @var string
     */
    public $logo;

    /** @var ClientInterface */
    protected $httpClient;

    /** @var FactoryContract */
    protected $factory;

    /** @var FilesystemManager */
    protected $filesystem;

    /** @var ProviderContract */
    protected $provider;

    /** @var array */
    protected $config;

    /**
     * The domain that is being processing
     *
     * @var string
     */
    protected $domain = '';

    /**
     * Fetcher constructor.
     *
     * @param ClientInterface $httpClient
     * @param FactoryContract $factory
     * @param FilesystemManager $filesystemManager
     * @throws MissingConfigException
     */
    public function __construct(ClientInterface $httpClient, FactoryContract $factory, FilesystemManager $filesystemManager)
    {
        $this->httpClient = $httpClient;
        $this->factory = $factory;
        $this->filesystem = $filesystemManager;

        $this->config = config('logo_fetcher.general');

        if (!$this->config) {
            throw new MissingConfigException('Missing config file. Solution: php artisan vendor:publish --provider="MTRDesign\LaravelLogoFetcher\ServiceProvider"');
        }
    }

    /**
     * Fetch and store company's logo using online tool
     *
     * @param string $domain
     * @return $this
     * @throws InvalidDomainException
     * @throws LogoNotFoundException
     * @throws MissingDomainException
     * @throws MissingProviderException
     * @throws UnexpectedException
     */
    public function fetch($domain = '')
    {
        if ($domain === '') {
            if ($this->domain === '') {
                throw new MissingDomainException('Firstly set a domain via the domain() method or pass it to the fetch() method as first argument');
            }

            $domain = $this->domain;
        }

        if (!$this->provider) {
            throw new MissingProviderException('Firstly set a provider via the provider() method');
        }

        try {
            $response = $this->httpClient->request(
                $this->provider->method(),
                $this->provider->url($domain, $this->config['size'], $this->config['format'])
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

        $this->domain = $domain;
        $this->logo = $this->provider->logoFromResponse($response);

        return $this;
    }

    /**
     * Store the logo on the filesystem
     *
     * @return $this
     * @throws MissingLogoException
     * @throws MissingProviderException
     * @throws SaveFailedException
     */
    public function store()
    {
        if (!$this->provider) {
            throw new MissingProviderException('Firstly set a provider via the provider() method');
        }

        if (!$this->logo) {
            throw new MissingLogoException('Firstly call the fetch() method');
        }

        $name = $this->domain . '-' .
            $this->provider->key() . '-' .
            $this->config['size'] . '.' .
            $this->config['format'];

        $path = $this->config['upload_path'] . $name;

        $save = $this->filesystem->put($path, $this->logo);

        if ($save === false) {
            throw new SaveFailedException('Could not save the logo');
        }

        $this->path = $path;

        return $this;
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

    /**
     * Set a domain
     *
     * @param string $domain
     * @return $this
     * @throws InvalidDomainException
     */
    public function domain($domain)
    {
        if (!is_string($domain)) {
            throw new InvalidDomainException('Domain must be string');
        }

        $this->domain = $domain;

        return $this;
    }
}