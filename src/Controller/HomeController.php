<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use Throwable;

/**
 * The start page of the empty project. It shows that PHP and the database work.
 * Replace it with your own pages.
 */
final class HomeController extends Controller
{
    /** GET / */
    public function index(Request $request): Response
    {
        return $this->view('home', [
            'title' => 'PHP starter',
            'phpVersion' => PHP_VERSION,
            'database' => $this->databaseStatus(),
        ]);
    }

    /** GET /health : the same check as JSON. */
    public function health(Request $request): Response
    {
        return $this->json([
            'status' => 'ok',
            'php' => PHP_VERSION,
            'database' => $this->databaseStatus(),
        ]);
    }

    private function databaseStatus(): string
    {
        try {
            Database::connection()->query('SELECT 1');

            return 'connected';
        } catch (Throwable $error) {
            return 'not connected: ' . $error->getMessage();
        }
    }
}
