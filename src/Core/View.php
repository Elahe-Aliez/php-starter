<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Turns a template file from the folder "templates" into HTML.
 *
 *   View::render('notes/index', ['notes' => $notes]);
 *
 * This renders templates/notes/index.php and puts the result into templates/layout.php.
 * Each key of the data array becomes a variable in the template: 'notes' -> $notes.
 */
final class View
{
    /**
     * @param array<string, mixed> $data
     * @param string|null $layout the surrounding page, or null for no layout
     */
    public static function render(string $template, array $data = [], ?string $layout = 'layout'): string
    {
        $content = self::renderFile($template, $data);

        if ($layout === null) {
            return $content;
        }

        return self::renderFile($layout, $data + ['content' => $content]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function renderFile(string $template, array $data): string
    {
        $file = dirname(__DIR__, 2) . '/templates/' . $template . '.php';

        if (!is_file($file)) {
            throw new RuntimeException('Template not found: templates/' . $template . '.php');
        }

        extract($data, EXTR_SKIP);

        ob_start();

        try {
            require $file;

            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
