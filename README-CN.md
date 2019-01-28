## 简介

此工具提供了一种在Laravel中配置monolog的简单方法。提供了多种日志输出类型的选择，并可以同时启用。  
项目参考[Astromic/laravel-monlog-config](https://github.com/Astrotomic/laravel-monolog-config)做了改进和开发。

## 安装

1. 利用composer安装工具

    ```bash
    composer require maplesnow/laravel-monolog-config
    ```

2. 更新composer后，将服务注册到 `config/app.php` 中的`providers`数组中

    ```php
    \MapleSnow\MonologConfig\MonologConfigServiceProvider::class
    ```

    **Laravel 5.5** 有了依赖自动发现功能, 所有不需要再注册`ServiceProvider`.

3. 发布工具的配置文件到项目中:

    ```
    php artisan vendor:publish --provider="MapleSnow\MonologConfig\MonologConfigServiceProvider"
    ```

4. 在`bootstrap/app.php` 调用 `app` 的 `configureMonologUsing` 方法

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

    参考官方文档 [laravel custom-monolog-configuration](https://laravel.com/docs/5.5/errors#custom-monolog-configuration)

## 配置
  
日志输出类型

| 名称      | 描述 | Handler |
| :------:  | :-----:  | :-----: |
| `single`  | 像laravel提供的single方式输出日志 | `StreamHandler` |
| `daily`   | 像laravel提供的daily方式输出日志 | `RotatingFileHandler` |
| `syslog`  | 像laravel提供的syslog方式输出日志 | `SyslogHandler` |
| `errorlog`| 像laravel提供的errorlog方式输出日志 | `ErrorLogHandler` |
| `mail`    | 通过邮件发送紧急的异常日志 | `SwfitMailHandler` |
| `redis`   |  将日志输出到redis中，用一个key保存数据  | `RedisHandler` |
| `redis-filter` |  根据日期和日志等级输出日志到redis中 |  `FilterHandler` |

`enable` 是控制开关。
