## Laravel Logo Fetcher

## Installation

Require this package with composer:

```shell
composer require mtr-design/laravel-logo-fetcher
```

After updating composer, add the ServiceProvider to the providers array in config/app.php

```php
MTRDesign\LogoFetcher\ServiceProvider::class,
```

Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="MTRDesign\LogoFetcher\ServiceProvider"
```

## Usage

1. Inject the LogoFetcher class or resolve it from the container
2. Set a provider using the provider() method
3. Call the fetch() method to save the logo on your filesystem

```php
$result = $this->logoFetcher
    ->provider(Clearbit::class)
    ->fetch($domain);
    
// $result['path'] will hold the path to the logo relative to the resources/storage/app directory
```
