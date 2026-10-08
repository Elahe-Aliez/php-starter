<?php

declare(strict_types=1);

namespace Tests;

use App\Core\HttpError;
use App\Core\Request;
use App\Core\Router;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\TestController;

final class RouterTest extends TestCase
{
    public function testItRunsTheControllerOfAMatchingRoute(): void
    {
        $router = new Router();
        $router->get('/hello', [TestController::class, 'hello']);

        $response = $router->dispatch(new Request('GET', '/hello'));

        $this->assertSame(200, $response->status());
        $this->assertSame('hello', $response->body());
    }

    public function testItPassesUrlPartsToTheController(): void
    {
        $router = new Router();
        $router->get('/notes/{id}', [TestController::class, 'show']);

        $response = $router->dispatch(new Request('GET', '/notes/42'));

        $this->assertSame('{"id":42}', $response->body());
    }

    public function testItAnswers404ForAnUnknownUrl(): void
    {
        $router = new Router();

        try {
            $router->dispatch(new Request('GET', '/nothing-here'));
            $this->fail('Expected an HttpError.');
        } catch (HttpError $error) {
            $this->assertSame(404, $error->status);
        }
    }

    public function testItAnswers404WhenANumberIsExpectedButTheUrlHasText(): void
    {
        $router = new Router();
        $router->get('/notes/{id}', [TestController::class, 'show']);

        try {
            $router->dispatch(new Request('GET', '/notes/abc'));
            $this->fail('Expected an HttpError.');
        } catch (HttpError $error) {
            $this->assertSame(404, $error->status);
        }
    }

    public function testItAnswers405ForTheWrongMethod(): void
    {
        $router = new Router();
        $router->get('/hello', [TestController::class, 'hello']);

        try {
            $router->dispatch(new Request('POST', '/hello'));
            $this->fail('Expected an HttpError.');
        } catch (HttpError $error) {
            $this->assertSame(405, $error->status);
        }
    }
}
