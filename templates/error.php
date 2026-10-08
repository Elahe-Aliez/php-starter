<?php
/*
 * The error page, used for 404, 405 and 500.
 *
 * @var int $status
 * @var string $message
 * @var string|null $details the full error, only when "debug" is on
 */
?>
<h1><?= e($status) ?></h1>

<p><?= e($message) ?></p>

<?php if ($details !== null): ?>
    <pre><?= e($details) ?></pre>
<?php endif; ?>

<p><a href="/">Back to the start page</a></p>
