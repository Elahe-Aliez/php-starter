<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Remembers values between requests of the same visitor, for example who is logged in.
 *
 *   Session::set('user_id', 5);
 *   $userId = Session::get('user_id');
 *
 *   Session::flash('success', 'Note saved.');   // set a message
 *   Session::flash('success');                  // read it once, then it is gone
 */
final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // On the command line (tests) there are no cookies. A plain array is enough there.
        if (PHP_SAPI === 'cli') {
            $_SESSION ??= [];

            return;
        }

        session_start([
            'cookie_httponly' => true,   // JavaScript cannot read the session cookie
            'cookie_samesite' => 'Lax',  // other websites cannot send forms with this cookie
        ]);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();

        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();

        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        self::start();

        unset($_SESSION[$key]);
    }

    /**
     * A message that is shown one time, on the next page.
     * With a message: stores it. Without a message: returns it and deletes it.
     */
    public static function flash(string $key, ?string $message = null): ?string
    {
        self::start();

        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;

            return null;
        }

        $stored = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);

        return $stored;
    }

    /** Gives the session a new id. Call it right after a successful login. */
    public static function regenerate(): void
    {
        self::start();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /** Forgets everything. Call it on logout. */
    public static function destroy(): void
    {
        self::start();

        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
