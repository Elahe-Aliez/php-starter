<?php

declare(strict_types=1);

namespace App\Core;

/**
 * The current request: method, URL and the data that came with it.
 */
final class Request
{
    /**
     * @param array<string, mixed> $query   values from the URL, for example ?page=2
     * @param array<string, mixed> $body    values from a form or from a JSON body
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        private array $query = [],
        private array $body = [],
        private array $headers = [],
    ) {
    }

    /** Builds the request from what PHP received. */
    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // '/notes/5?page=2' -> '/notes/5'. A slash at the end is ignored.
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = '/' . trim(rawurldecode($path), '/');

        $headers = [
            'Accept' => $_SERVER['HTTP_ACCEPT'] ?? '',
            'Content-Type' => $_SERVER['CONTENT_TYPE'] ?? '',
        ];

        // A form sends its fields in $_POST. An API client sends JSON in the body.
        $body = $_POST;

        if (str_contains($headers['Content-Type'], 'application/json')) {
            $decoded = json_decode((string) file_get_contents('php://input'), true);
            $body = is_array($decoded) ? $decoded : [];
        }

        return new self($method, $path, $_GET, $body, $headers);
    }

    /** A value from the URL: /notes?page=2 -> query('page') */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /** A value from the form or the JSON body: input('title') */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * All values from the form or the JSON body.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->body;
    }

    /** True when the client expects JSON: the URL starts with /api/ or it asks for JSON. */
    public function wantsJson(): bool
    {
        return str_starts_with($this->path, '/api/')
            || str_contains($this->headers['Accept'] ?? '', 'application/json');
    }
}
