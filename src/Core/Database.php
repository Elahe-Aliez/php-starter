<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Opens the database connection once and returns the same connection every time.
 * The settings are in config/config.php under "db".
 *
 *   $pdo = Database::connection();
 *
 *   $statement = $pdo->prepare('SELECT * FROM notes WHERE id = ?');
 *   $statement->execute([$id]);
 *   $note = $statement->fetch();
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = new PDO(
                config('db.dsn'),
                config('db.user'),
                config('db.password'),
                [
                    // A failed query throws an exception instead of failing silently.
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // Rows come back as arrays: $row['title'].
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Real prepared statements. They protect against SQL injection.
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$pdo;
    }

    /** Replaces the connection, for example with a test database. */
    public static function setConnection(?PDO $pdo): void
    {
        self::$pdo = $pdo;
    }
}
