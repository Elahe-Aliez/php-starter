<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Core\Request;
use App\Core\Response;

/**
 * A tiny controller that exists only for the router tests.
 */
final class TestController
{
    public function hello(Request $request): string
    {
        return 'hello';
    }

    public function show(Request $request, int $id): Response
    {
        return Response::json(['id' => $id]);
    }
}
