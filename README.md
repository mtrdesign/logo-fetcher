## Laravel Logo Fetcher

## Installation

Require this package with composer:

```shell
composer require mtr-design/laravel-logo-fetcher
```

Add the ServiceProvider to the providers array in config/app.php

```php
MTRDesign\LaravelLogoFetcher\ServiceProvider::class,
```

Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="MTRDesign\LaravelLogoFetcher\ServiceProvider"
```

You now should have config/logo_fetcher.php file. You can open and tweak the configuration options

## Usage

1. Resolve the \MTRDesign\LaravelLogoFetcher\LogoFetcher class from the container
2. Set a provider using the provider() method
3. Call the fetch() method to get the logo
4. You can chain with store() to save it using your default storage disk

If you want to directly store the logo:
```php
$logoFetcher = app(\MTRDesign\LaravelLogoFetcher\LogoFetcher::class);
$logoFetcher->provider(Clearbit::class)
    ->fetch($domain)
    ->store();
```

If you want to just fetch the logo:
```php
$logoFetcher = app(\MTRDesign\LaravelLogoFetcher\LogoFetcher::class);
$logo = $logoFetcher
    ->provider(Clearbit::class)
    ->fetch($domain)
    ->logo;
```

One extra example to illustrate the domain() helper and the path property, assuming that you injected the logo fetcher class:
```php
$path = $this->logoFetcher
    ->provider(Clearbit::class)
    ->domain($domain)
    ->fetch()
    ->store()
    ->path;
```

## Providers

You can define your own providers - just create a class and implement the MTRDesign\LaravelLogoFetcher\Providers\ProviderContract

## Error handling

Different exceptions will be raised if the fetching fails but they all inherit from the `\MTRDesign\LaravelLogoFetcher\Exceptions\LogoFetcherException`. All are having human-readable messages and can be safely output to the client. You can find all the exceptions in the `\MTRDesign\LaravelLogoFetcher\Exceptions` namespace.
