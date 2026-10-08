<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Throw this to stop a request with an HTTP status code.
 *
 *   throw new HttpError(404, 'Note not found');
 *   throw new HttpError(403, 'Please log in first');
 */
final class HttpError extends RuntimeException
{
    public function __construct(
        public readonly int $status,
        string $message = '',
    ) {
        parent::__construct($message);
    }
}
