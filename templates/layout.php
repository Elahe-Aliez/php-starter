<?php

use App\Core\Session;

/*
 * The page around every template: head, messages, and the template itself in $content.
 *
 * @var string $content
 * @var string|null $title
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'App') ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main class="container">
        <?php foreach (['success', 'error'] as $type): ?>
            <?php if ($flash = Session::flash($type)): ?>
                <p class="flash flash-<?= $type ?>"><?= e($flash) ?></p>
            <?php endif; ?>
        <?php endforeach; ?>

        <?= $content ?>
    </main>
</body>
</html>
