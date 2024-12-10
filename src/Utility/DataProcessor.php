<?php
declare(strict_types=1);


namespace App\Utility;

use App\Core\Configuration;



/**
 *	DataProcessor is used to sanitize and validate data,
 *	and for hashing passwords.
 */
class DataProcessor
{
	/**
	 *	@param array $data
	 *
	 *	@return array|string 
	 */
    public static function sanitizeData($data): array|string
    {
        $returnData = [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $returnData[$key] = self::sanitizeData($value);
                } else {
                    $returnData[$key] = self::sanitizeInput($value);
                }
            }
        } else {
            $returnData = self::sanitizeInput($data);
        }

        return $returnData;
    }

	/**
	 *	@param string $input
	 *
	 *	@return string sanatized version of $input
	 */
    private static function sanitizeInput(string $input): string
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_COMPAT | ENT_HTML5, 'UTF-8');

        return $input;
    }

	/**
	 *	the keys from $data are compared with the values 
	 *	of $fields, if any are missing it returns false.
	 *
	 *	@param array $data
	 *	@param array $fields
	 *
	 *	@return bool
	 */
    public static function validateFields(array $data, array $fields): bool
    {
        // Check if $data has all required $fields
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                return false;
            }
        }
    
        return true;
    }

	/**
	 *	$data is validated using the validator in $options
	 *
	 *	@param array $data
	 *	@param array $options
	 *
	 *	@return bool
	 */
    public static function validateType(array $data, array $options = ["regexp" => "/^(?![\x80-\xFF]).*$/"]): bool
    {
        foreach ($data as $key => $value) {
            if (!filter_var($key, $value, ["options" => $options])) {
                return false;
            }
        }

        return true;
    }

	/**
	 *	@param string $password
	 *
	 *	@return string hashed password
	 */
    public static function hashPassword(string $password): string
    {
		$pepper = Configuration::read('Security.password.pepper');
		$salt = Configuration::read('Security.password.salt');
		$algorithm = Configuration::read('Security.password.algorithm');

        $hash = password_hash($pepper . $password . $salt, $algorithm);
        return $hash;
    }

    /**
     *	@param string $password
     *	@param string $hash
     *
     *	@return bool
     *
     *	Checks if the given $password matches the given $hash.
     *	It uses the same pepper, salt and encryption method as hashPassword()
     *	to generate a hash from $password and then compares it with $hash.
     */
    public static function checkPassword(string $password, string $hash): bool
    {
		$pepper = Configuration::read('Security.password.pepper');
		$salt = Configuration::read('Security.password.salt');

        return password_verify($pepper . $password . $salt, $hash);
    }
}