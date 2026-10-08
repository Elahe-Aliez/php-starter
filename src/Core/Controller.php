<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base class for your controllers. It gives them three short helpers.
 *
 *   final class NoteController extends Controller
 *   {
 *       public function index(Request $request): Response
 *       {
 *           return $this->view('notes/index', ['notes' => $notes]);
 *       }
 *   }
 */
abstract class Controller
{
    /**
     * Answers with an HTML page from the folder "templates".
     *
     * @param array<string, mixed> $data
     */
    protected function view(string $template, array $data = [], int $status = 200): Response
    {
        return Response::html(View::render($template, $data), $status);
    }

    /** Answers with JSON. */
    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /** Sends the browser to another URL. Use it after a form was saved. */
    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }
}
