<?php
declare(strict_types=1);

namespace App\Utility;

use App\Core\Configuration;

/**
 * Class DataProcessor
 *
 * DataProcessor is used to sanitize and validate data, and for hashing passwords.
 *
 * @author Martan van Verseveld
 */
class DataProcessor
{
    /**
     * Sanitize data recursively.
     *
     * @param array|string $data The data to sanitize.
     * @return array|string The sanitized data.
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
     * Sanitize a single input string.
     *
     * @param string $input The input string to sanitize.
     * @return string The sanitized input.
     */
    private static function sanitizeInput(string $input): string
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_COMPAT | ENT_HTML5, 'UTF-8');

        return $input;
    }

    /**
     * Validate if all fields are present in data.
     *
     * @param array $data The data to check.
     * @param array $fields The required fields.
     * @return bool True if all fields are present, false otherwise.
     */
    public static function validateFields(array $data, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate data types with a regular expression.
     *
     * @param array $data The data to validate.
     * @param array $options Options for the validator.
     * @return bool True if all data is valid, false otherwise.
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
     * Hash a password with a pepper and salt.
     *
     * @param string $password The password to hash.
     * @return string The hashed password.
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
     * Check if a password matches a hash.
     *
     * @param string $password The password to check.
     * @param string $hash The hash to compare against.
     * @return bool True if the password matches the hash, false otherwise.
     */
    public static function checkPassword(string $password, string $hash): bool
    {
        $pepper = Configuration::read('Security.password.pepper');
        $salt = Configuration::read('Security.password.salt');

        return password_verify($pepper . $password . $salt, $hash);
    }
}
