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
    MapleSnow\MonologConfig\MonologConfigServiceProvider::class
    ```

    **Laravel 5.5** uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

3. Publish the configuration for monolog using the following command:

    ```
    php artisan vendor:publish --provider="MapleSnow\MonologConfig\MonologConfigServiceProvider"
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

All of the configuration for your application's logging system is housed in the `config/logging.php` configuration file. 
`enable` is the trigger to control your application's log channels active or disabled.

| Name      | Description | Handler |
| :------:  | :-----:  | :-----: |
| `single`  | writing log as like as laravel single channel | `StreamHandler` |
| `daily`   | writing log as like as laravel daily channel | `RotatingFileHandler` |
| `syslog`  | writing log as like as laravel syslog channel | `SyslogHandler` |
| `errorlog`| writing log as like as laravel errorlog channel | `ErrorLogHandler` |
| `mail`    | sending a mail report some urgent exception  | `SwfitMailHandler` |
| `redis`   |  writing log to redis  | `RedisHandler` |
| `redisFilter` |  writing log to redis depends on level and date |  `RedisFilterHandler` |

# Usage

You may write information to the logs using the laravel's `Log` facade.The logger provides the eight logging levels: *emergency*, *alert*, *critical*, *error*, *warning*, *notice*, *info* and *debug*。

```php
Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);
```
If you need to output custom information, you need to define the content to be output under an array of `extra` for the key.

```php
Log::debug($message,['extra' => "extra message"]);
```
