<?php

declare(strict_types=1);

/*
 * Command line entry point, for tasks that are not web pages
 * (read a file, calculate something, print a result).
 *
 * Run it:
 *   docker compose exec app php bin/console.php
 *   php bin/console.php                          (without Docker)
 */

require dirname(__DIR__) . '/bootstrap.php';

echo 'PHP ' . PHP_VERSION . ' is ready.' . PHP_EOL;

// Write your code here, for example:
//
// $calculator = new App\Service\PriceCalculator();
// echo $calculator->total([10, 20, 30]) . PHP_EOL;
