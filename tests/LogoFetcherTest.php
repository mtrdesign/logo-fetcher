<?php

namespace Tests;

use Mockery;
use GuzzleHttp\ClientInterface;
use MTRDesign\LaravelLogoFetcher\Exceptions\InvalidConfigException;
use MTRDesign\LaravelLogoFetcher\Exceptions\InvalidDomainException;
use Tests\Stubs\ProviderStub;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Filesystem\FilesystemManager;
use MTRDesign\LaravelLogoFetcher\LogoFetcher;
use MTRDesign\LaravelLogoFetcher\Providers\Factory;

class LogoFetcherTest extends \PHPUnit\Framework\TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testConstructValid()
    {
        $httpClient = Mockery::mock(ClientInterface::class);
        $factory = Mockery::mock(Factory::class);
        $filesystem = Mockery::mock(FilesystemManager::class);
        $config = require __DIR__ . '/../config/logo_fetcher.php';

        $logoFetcher = new LogoFetcher($httpClient, $factory, $filesystem, $config['general']);

        $this->assertInstanceOf(LogoFetcher::class, $logoFetcher);
    }

    public function testConstructInvalid()
    {
        $httpClient = Mockery::mock(ClientInterface::class);
        $factory = Mockery::mock(Factory::class);
        $filesystem = Mockery::mock(FilesystemManager::class);

        $this->expectException(InvalidConfigException::class);

        new LogoFetcher($httpClient, $factory, $filesystem, []);
    }

    public function testFetchValid()
    {
        $httpResponse = Mockery::mock(ResponseInterface::class);
        $httpClient = Mockery::mock(ClientInterface::class);
        $httpClient->shouldReceive('request')->andReturn($httpResponse);

        $factory = Mockery::mock(Factory::class);
        $factory->shouldReceive('provider')->andReturn(new ProviderStub);

        $filesystem = Mockery::mock(FilesystemManager::class);

        $config = require __DIR__ . '/../config/logo_fetcher.php';

        $logoFetcher = new LogoFetcher($httpClient, $factory, $filesystem, $config['general']);
        $logoFetcher->provider(ProviderStub::class)
            ->fetch('example.com');

        $this->assertEquals('random-string', $logoFetcher->logo);
    }

    public function testStoreValid()
    {
        $httpResponse = Mockery::mock(ResponseInterface::class);
        $httpClient = Mockery::mock(ClientInterface::class);
        $httpClient->shouldReceive('request')->andReturn($httpResponse);

        $providerStub = new ProviderStub;

        $factory = Mockery::mock(Factory::class);
        $factory->shouldReceive('provider')->andReturn($providerStub);

        $config = require __DIR__ . '/../config/logo_fetcher.php';
        $config = $config['general'];
        $name = 'example.com-' . $providerStub->key() . '-' . $config['size'] . '.' . $config['format'];
        $path = $config['upload_path'] . $name;

        $filesystem = Mockery::mock(FilesystemManager::class);
        $filesystem->shouldReceive('put')->once()->andReturn(true);

        $logoFetcher = new LogoFetcher($httpClient, $factory, $filesystem, $config);
        $logoFetcher->provider(ProviderStub::class)
            ->fetch('example.com')
            ->store();

        $this->assertEquals($path, $logoFetcher->path);
    }

    public function testDomainValid()
    {
        $httpClient = Mockery::mock(ClientInterface::class);
        $factory = Mockery::mock(Factory::class);
        $filesystem = Mockery::mock(FilesystemManager::class);
        $config = require __DIR__ . '/../config/logo_fetcher.php';

        $logoFetcher = new LogoFetcher($httpClient, $factory, $filesystem, $config['general']);
        $this->assertEquals($logoFetcher, $logoFetcher->domain('example.com'));
    }

    public function testDomainInvalid()
    {
        $httpClient = Mockery::mock(ClientInterface::class);
        $factory = Mockery::mock(Factory::class);
        $filesystem = Mockery::mock(FilesystemManager::class);
        $config = require __DIR__ . '/../config/logo_fetcher.php';

        $this->expectException(InvalidDomainException::class);

        $logoFetcher = new LogoFetcher($httpClient, $factory, $filesystem, $config['general']);
        $logoFetcher->domain(123);
    }
}
