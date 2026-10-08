<?php

// No "declare(strict_types=1)" in this file, on purpose:
// URL parts are always strings, and this lets PHP turn "5" into the int 5
// when your controller method asks for "int $id".

namespace App\Core;

use Closure;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * Knows which controller method belongs to which URL.
 *
 *   $router->get('/notes/{id}', [NoteController::class, 'show']);
 */
final class Router
{
    /** @var list<array{method: string, pattern: string, handler: array|Closure}> */
    private array $routes = [];

    public function get(string $path, array|Closure $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array|Closure $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, array|Closure $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, array|Closure $handler): void
    {
        $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, array|Closure $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function add(string $method, string $path, array|Closure $handler): void
    {
        // '/notes/{id}' becomes the pattern '#^/notes/(?P<id>[^/]+)$#'
        $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';

        $this->routes[] = ['method' => $method, 'pattern' => $pattern, 'handler' => $handler];
    }

    /**
     * Runs the controller method for the request and returns its answer.
     *
     * @throws HttpError 404 when no route has this URL, 405 when the URL exists with another method
     */
    public function dispatch(Request $request): Response
    {
        $urlExists = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $request->path, $matches)) {
                continue;
            }

            $urlExists = true;

            if ($route['method'] !== $request->method) {
                continue;
            }

            // Keep only the named URL parts, for example ['id' => '5'].
            $urlParts = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));

            $handler = $route['handler'];
            $this->checkNumbers($handler, $urlParts);

            if (is_array($handler)) {
                [$class, $action] = $handler;
                $result = (new $class())->$action($request, ...$urlParts);
            } else {
                $result = $handler($request, ...$urlParts);
            }

            return $this->toResponse($result);
        }

        throw $urlExists
            ? new HttpError(405, 'Method not allowed')
            : new HttpError(404, 'Page not found');
    }

    /**
     * When the controller method asks for "int $id" and the URL has "/notes/abc",
     * the answer is 404 instead of a PHP type error.
     *
     * @param list<string> $urlParts
     */
    private function checkNumbers(array|Closure $handler, array $urlParts): void
    {
        $method = is_array($handler)
            ? new ReflectionMethod($handler[0], $handler[1])
            : new ReflectionFunction($handler);

        // The first parameter is the Request. The others receive the URL parts, in order.
        $parameters = array_slice($method->getParameters(), 1);

        foreach ($parameters as $index => $parameter) {
            $type = $parameter->getType();
            $wantsInt = $type instanceof ReflectionNamedType && $type->getName() === 'int';

            if ($wantsInt && isset($urlParts[$index]) && !ctype_digit($urlParts[$index])) {
                throw new HttpError(404, 'Page not found');
            }
        }
    }

    /**
     * A controller method may return a Response, an array (sent as JSON) or a string (sent as HTML).
     */
    private function toResponse(mixed $result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result)) {
            return Response::json($result);
        }

        return Response::html((string) $result);
    }
}
