## Introduction

This package provides a simple way to configure monolog in Laravel.  
This makes it a cinch to configure these handlers, allowing you to mix and match them to customize your application's log handling.  
Project development with reference to [Astromic/laravel-monlog-config](https://github.com/Astrotomic/laravel-monolog-config) .

-----

## Installation

1. Require this package with composer using the following command:

    ```bash
    composer require maplesnow/laravel-monolog-config
    ```

2. After updating composer, add the service provider to the `providers` array in `config/app.php`

    ```php
    \MapleSnow\MonologConfig\MonologConfigServiceProvider::class
    ```

    **Laravel 5.5** uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

3. Publish the configuration for monolog using the following command:

    ```
    php artisan vendor:publish --provider="MapleSnow\MonologConfig\MonologConfigServiceProvider"
    php artisan config:cache
    ```

4. Use the application's `configureMonologUsing` method in your `bootstrap/app.php` like this
    
    ```php
    /*
    |--------------------------------------------------------------------------
    | Configure Monolog
    |--------------------------------------------------------------------------
    */
    $app->configureMonologUsing(function (\Monolog\Logger $monolog) {
        (new MapleSnow\MonologConfig\MonologConfigurator($monolog))->run();
    });

    ```

    Refer [laravel custom-monolog-configuration](https://laravel.com/docs/5.5/errors#custom-monolog-configuration)

## Configuration

Available output channel  

| Name      | Description | Handler |
| :------:  | :-----:  | :-----: |
| `single`  | output log as like as laravel single channel | `StreamHandler` |
| `daily`   | output log as like as laravel daily channel | `RotatingFileHandler` |
| `syslog`  | output log as like as laravel syslog channel | `SyslogHandler` |
| `errorlog`| output log as like as laravel errorlog channel | `ErrorLogHandler` |
| `mail`    | send a mail report some urgent exception  | `SwfitMailHandler` |
| `redis`   |  output log to redis  | `RedisHandler` |
| `redis-filter` |  output log to redis depends level and date |  `FilterHandler` |

`enable` is the trigger to control channel active or disabled
