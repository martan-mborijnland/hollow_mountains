<?php declare(strict_types=1);

namespace App\Core;



class Configuration
{
	private static $config;
	
	public static function load() {
		$configDir = dirname(dirname(__DIR__)) . "/config/";
		
		$config_app = require $configDir . "/app.php";
		$config_applocal = require $configDir . "/app.local.php";

		self::$config = array_merge($config_app, $config_applocal);
	}

	public static function read(?string $var = null, $default = null) 
	{
		$keys = explode('.', $var);

		return array_reduce($keys, function ($carry, $key) {
			return is_array($carry) && isset($carry[$key]) ? $carry[$key] : null;
		}, self::$config) ?? $default;
	}
}