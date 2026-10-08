<?php

declare(strict_types=1);

/*
 * Prepares everything the project needs. This file is loaded by:
 *   public/index.php   web requests
 *   bin/console.php    command line
 *   PHPUnit            tests (see phpunit.xml)
 */

// 1. Class loading.
//    After "composer install", the autoloader of Composer is used.
//    Without it, the small autoloader below does the same job for the "App" namespace,
//    so the project also runs before Composer has been installed.
$composerAutoloader = __DIR__ . '/vendor/autoload.php';

if (is_file($composerAutoloader)) {
    require_once $composerAutoloader;
} else {
    spl_autoload_register(static function (string $class): void {
        if (str_starts_with($class, 'App\\')) {
            // App\Controller\HomeController  ->  src/Controller/HomeController.php
            $file = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';

            if (is_file($file)) {
                require $file;
            }
        }
    });
}

// 2. Helper functions: config(), env(), e(), dd().
require_once __DIR__ . '/src/helpers.php';

// 3. Errors and time zone. All settings are in config/config.php.
error_reporting(E_ALL);
ini_set('display_errors', config('debug') ? '1' : '0');
date_default_timezone_set(config('timezone'));
