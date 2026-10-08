<?php

declare(strict_types=1);

/*
 * Small helper functions that can be used everywhere.
 */

/**
 * Reads an environment variable. Returns $default when it is not set.
 *
 *   env('DB_USER', 'app')
 */
function env(string $name, ?string $default = null): ?string
{
    $value = getenv($name);

    return $value === false ? $default : $value;
}

/**
 * Reads a setting from config/config.php. Use a dot to go one level deeper.
 *
 *   config('debug')
 *   config('db.dsn')
 */
function config(string $key, mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        $config = require dirname(__DIR__) . '/config/config.php';
    }

    $value = $config;

    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }

        $value = $value[$part];
    }

    return $value;
}

/**
 * Makes a value safe to print inside HTML. Use it for everything a user typed.
 *
 *   <?= e($note['title']) ?>
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Debugging: prints the values and stops the script.
 *
 *   dd($request->all());
 */
function dd(mixed ...$values): never
{
    echo '<pre>';

    foreach ($values as $value) {
        var_dump($value);
    }

    echo '</pre>';

    exit(1);
}
