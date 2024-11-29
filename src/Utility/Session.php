<?php declare(strict_types=1);

namespace App\Utility;

use App\Core\Configuration;

class Session
{
    public static function start(): void
    {
        session_start();
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key): ?string
    {
        return $_SESSION[$key] ?? null;
    }

    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }
}