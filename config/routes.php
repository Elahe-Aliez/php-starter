<?php

declare(strict_types=1);

use App\Controller\HomeController;

/*
 * The list of URLs of the project. One line per URL:
 *
 *   $router->get(URL, [controller class, method name]);
 *
 * A part in curly brackets is passed to the method:
 *   '/notes/{id}'  ->  public function show(Request $request, int $id)
 */

/** @var App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->get('/health', [HomeController::class, 'health']);

// Your routes go here, for example:
// $router->get('/notes', [NoteController::class, 'index']);
// $router->get('/notes/{id}', [NoteController::class, 'show']);
// $router->post('/notes', [NoteController::class, 'store']);
