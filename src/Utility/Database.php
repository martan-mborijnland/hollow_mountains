<?php declare(strict_types=1);

namespace App\Utility;

use PDO;
use PDOException;
use PDOStatement;
use App\Core\Configuration;



class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    private array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];

    private function __construct()
    {
        $host = Configuration::read('db.host');
        $dbname = Configuration::read('db.dbname');
        $username = Configuration::read('db.username');
        $password = Configuration::read('db.password');
        $driver = Configuration::read('db.driver', 'mysql');
        $port = Configuration::read('db.port', 3306);

        $dsn = "$driver:host=$host;port=$port;dbname=$dbname";

        try {
            $this->connection = new PDO($dsn, $username, $password, $this->options);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    // Prevent cloning the Singleton instance
    private function __clone() {}

    // Prevent unserializing the Singleton instance
    public function __wakeup() {}

    /**
     * Get the Singleton instance of the Database class.
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            // No arguments needed, configuration is handled inside the constructor
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Perform a database query with optional parameters.
     *
     * @param string $query
     * @param array $params
     * @return PDOStatement
     */
    public function query(string $query, array $params = []): PDOStatement
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement;
    }

    /**
     * Get the PDO connection instance.
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}