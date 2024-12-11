<?php declare(strict_types=1);

namespace App\Utility;

use App\Core\Configuration;

/**
 * Class Session
 *
 * @author Martan van Verseveld
 */
class Session
{
    /**
     * @return void
     */
    public static function start(): void
    {
        session_start();
    }

    /**
     * @return void
     */
    public static function destroy(): void
    {
        session_destroy();
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public static function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }
}

