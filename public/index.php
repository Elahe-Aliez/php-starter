<?php

declare(strict_types=1);

use App\Core\ErrorHandler;
use App\Core\Request;
use App\Core\Router;

/*
 * The front controller. Every request starts here:
 *
 *   browser -> public/index.php -> Router -> Controller -> Response -> browser
 */

require dirname(__DIR__) . '/bootstrap.php';

// 1. Collect the routes.
$router = new Router();
require dirname(__DIR__) . '/config/routes.php';

// 2. Describe the current request: method, URL, form data.
$request = Request::fromGlobals();

// 3. Find the controller for this URL and run it. Errors become an error page.
try {
    $response = $router->dispatch($request);
} catch (Throwable $error) {
    $response = ErrorHandler::handle($error, $request);
}

// 4. Send the answer to the browser.
$response->send();
