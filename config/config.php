<?php

declare(strict_types=1);

/*
 * All settings in one place. Read them with config('db.dsn').
 *
 * With Docker, the values come from the "environment" part of docker-compose.yml.
 * Without Docker, the second value in each env(...) call is used. Change it here.
 */

return [
    // true: show the full error in the browser. Use false for a finished project.
    'debug' => env('APP_DEBUG', '1') === '1',

    'timezone' => env('APP_TIMEZONE', 'Europe/Berlin'),

    'db' => [
        // With Docker the host is "db", the name of the database service in docker-compose.yml.
        // Without Docker it is the MySQL on your own computer: 127.0.0.1.
        'dsn' => env('DB_DSN', 'mysql:host=127.0.0.1;dbname=app;charset=utf8mb4'),
        'user' => env('DB_USER', 'app'),
        'password' => env('DB_PASSWORD', 'secret'),
    ],
];
