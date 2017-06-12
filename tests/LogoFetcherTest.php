<?php

namespace Tests\Unit\LaravelLogoFetcher;

use Mockery;
use Tests\TestCase;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Filesystem\FilesystemManager;
use MTRDesign\LaravelLogoFetcher\LogoFetcher;
use MTRDesign\LaravelLogoFetcher\Providers\Factory;
use Tests\Unit\LaravelLogoFetcher\Stubs\ProviderStub;

class LogoFetcherTest extends TestCase
{
    public function testFetchValid()
    {
        $httpResponse = Mockery::mock(ResponseInterface::class);
        $httpClient = Mockery::mock(ClientInterface::class);
        $httpClient->shouldReceive('request')->andReturn($httpResponse);

        $factory = Mockery::mock(Factory::class);
        $factory->shouldReceive('provider')->andReturn(new ProviderStub);

        $filesystem = Mockery::mock(FilesystemManager::class);

        $logoFetcher = new LogoFetcher($httpClient, $factory, $filesystem);
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

        $config = config('logo_fetcher.general');
        $name = 'example.com-' . $providerStub->key() . '-' . $config['size'] . '.' . $config['format'];
        $path = $config['upload_path'] . $name;

        $filesystem = Mockery::mock(FilesystemManager::class);
        $filesystem->shouldReceive('put')->once()->andReturn(true);

        $logoFetcher = new LogoFetcher($httpClient, $factory, $filesystem);
        $logoFetcher->provider(ProviderStub::class)
            ->fetch('example.com')
            ->store();

        $this->assertEquals($path, $logoFetcher->path);
    }
}
