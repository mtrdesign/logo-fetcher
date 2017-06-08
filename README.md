## Laravel Logo Fetcher

## Installation

Require this package with composer:

```shell
composer require MTRDesign/laravel-logo-fetcher
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

Inject the Fetcher class

```php
LogoFetcher::info($object);
LogoFetcher::error('Error!');
LogoFetcher::warning('Watch out…');
LogoFetcher::addMessage('Another message', 'mylabel');
```
