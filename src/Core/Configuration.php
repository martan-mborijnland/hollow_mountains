<?php declare(strict_types=1);

namespace App\Core;

/**
 * Class Configuration
 * 
 * This class handles the loading and reading of configuration settings.
 * 
 * @author Martan van Verseveld
 */
class Configuration
{
    /**
     * @var array The loaded configuration settings.
     */
    private static $config;

    /**
     * Loads the configuration settings from files.
     * 
     * This method merges configurations from 'app.php' and 'app.local.php'.
     *
     * @return void
     */
    public static function load(): void
    {
        $configDir = dirname(dirname(__DIR__)) . "/config/";

        $config_app = require $configDir . "/app.php";
        $config_applocal = require $configDir . "/app.local.php";

        self::$config = array_merge($config_app, $config_applocal);
    }

    /**
     * Reads a configuration value.
     * 
     * This method retrieves a configuration value based on a dot-separated key.
     *
     * @param string|null $var The dot-separated configuration key.
     * @param mixed $default The default value to return if the key is not found.
     * @return mixed The configuration value or the default value.
     */
    public static function read(?string $var = null, $default = null)
    {
        $keys = explode('.', $var);

        return array_reduce($keys, function ($carry, $key) {
            return is_array($carry) && isset($carry[$key]) ? $carry[$key] : null;
        }, self::$config) ?? $default;
    }
}
