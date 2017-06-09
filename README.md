## Laravel Logo Fetcher

 - The package is not covered by tests yet
 - Readme is in progress. Not complete and may have incorrect information

## Installation

Require this package with composer:

```shell
composer require mtr-design/laravel-logo-fetcher
```

After updating composer, add the ServiceProvider to the providers array in config/app.php

```php
MTRDesign\LaravelLogoFetcher\ServiceProvider::class,
```

Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="MTRDesign\LaravelLogoFetcher\ServiceProvider"
```

You now should have config/logo_fetcher.php file. You can open and tweak the configuration options, which are well documented in the configuration file itself

## Usage

1. Inject the LogoFetcher class or resolve it from the container
2. Set a provider using the provider() method
3. Call the fetch() method to get the logo
4. You can chain with save() to save it on your filesystem

If you want to directly store the logo on your filesystem:
```php
$this->logoFetcher
    ->provider(Clearbit::class)
    ->fetch($domain)
    ->store();
```

If you want to just fetch the logo:
```php
$logo = $this->logoFetcher
    ->provider(Clearbit::class)
    ->fetch($domain)
    ->logo;
```

One extra example to illustrate the domain() helper and the path property:
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

Different exceptions will be raised if the fetching fails. All are having human-readable names and can safely be using to output to the client. All the exceptions inherit from the LogoFetcherException. You can find all the exceptions in /MTRDesign/LogoFetcher/Exceptions