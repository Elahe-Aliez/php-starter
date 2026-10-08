<?php
/*
 * The start page of the empty project. Replace it with your own page.
 *
 * @var string $phpVersion
 * @var string $database
 */
?>
<h1>It works</h1>

<table>
    <tr>
        <th>PHP</th>
        <td><?= e($phpVersion) ?></td>
    </tr>
    <tr>
        <th>Database</th>
        <td><?= e($database) ?></td>
    </tr>
</table>

<?php if ($database !== 'connected'): ?>
    <p>The database needs about 30 seconds after its first start. Wait and reload this page.</p>
<?php endif; ?>

<h2>Where to start</h2>

<ol>
    <li>Add a URL in <code>config/routes.php</code>.</li>
    <li>Add a controller in <code>src/Controller</code>.</li>
    <li>Add a page in <code>templates</code>.</li>
</ol>

<p>The file <code>docs/examples.md</code> has a ready example for each step.</p>
