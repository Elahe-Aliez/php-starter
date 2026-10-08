<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * Turns an error into an answer for the browser: an error page, or JSON for API requests.
 */
final class ErrorHandler
{
    public static function handle(Throwable $error, Request $request): Response
    {
        if ($error instanceof HttpError) {
            // An expected error, for example 404.
            $status = $error->status;
            $message = $error->getMessage();
            $details = null;
        } else {
            // An unexpected error. Write it to the log: "docker compose logs app" shows it.
            error_log((string) $error);

            $status = 500;
            $message = 'Something went wrong';
            $details = config('debug') ? (string) $error : null;
        }

        if ($request->wantsJson()) {
            $data = ['error' => $message];

            if ($details !== null) {
                $data['details'] = $details;
            }

            return Response::json($data, $status);
        }

        $html = View::render('error', [
            'title' => $status . ' ' . $message,
            'status' => $status,
            'message' => $message,
            'details' => $details,
        ]);

        return Response::html($html, $status);
    }
}
