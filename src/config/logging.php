<?php

use Monolog\Logger;
use MapleSnow\MonologConfig\formatter\DailyFormatter;
use MapleSnow\MonologConfig\Formatter\RedisFormatter;
use MapleSnow\MonologConfig\Formatter\RedisFilterFormatter;
use MapleSnow\MonologConfig\Formatter\MailFormatter;
use Monolog\Handler\ErrorLogHandler;

return [
    // default log channel
    'fallback' => 'single',

    // optional log channel
    'handlers' => [
        'single' => [
            'enabled' => false,
            'driver' => 'stream',
            'path' => storage_path('logs/laravel.log'),
            'level' => Logger::DEBUG
        ],
        'daily' => [
            'enabled' => false,
            'driver' => 'rotating_file',
            'path' => storage_path('logs/laravel.log'),
            'max_files' => 7,
            'level' => Logger::DEBUG,
            'formatter' => DailyFormatter::class,
        ],
        'syslog' => [
            'enabled' => false,
            'driver' => 'syslog',
            'ident' => 'laravel',
            'level' => Logger::DEBUG,
        ],
        'errorlog' => [
            'enabled' => false,
            'driver' => 'error_log',
            'message_type' => ErrorLogHandler::OPERATING_SYSTEM,
            'level' => Logger::DEBUG,
        ],
        'mail' => [
            'enabled' => false,
            'driver' => 'swift_mailer',
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'username' => env("MAIL_USERNAME"),
            'password' => env("MAIL_PASSWORD"),
            'encryption' => env("MAIL_ENCRYPTION"),
            'from' => env("MAIL_FROM_ADDRESS"),
            'to' => ['to@example.com'],
            'cc' => null,
            'subject' => 'URGENT BUG',
            'level' => Logger::ERROR,
            'formatter' => MailFormatter::class
        ],
        'redis' => [
            'enabled' => false,
            'driver' => 'redis',
            'scheme' => 'tcp',
            'host' => env('REDIS_HOST'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT'),
            'database' => 9,
            'key' => 'log',
            'expire' => 43200,          // 5 days
            'level' => Logger::DEBUG,
            'formatter' => RedisFormatter::class
        ],
        // output by level
        'redis_filter' => [
            'enabled' => false,
            'driver' => 'redis_filter',
            'scheme' => 'tcp',
            'host' => env('REDIS_HOST'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT'),
            'database' => 10,
            'expire' => 43200,          // 5 days
            'level' => Logger::DEBUG,
            'formatter' => RedisFilterFormatter::class
        ]
    ]
];