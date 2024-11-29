<?php declare(strict_types=1);

namespace App\Utility;

use PDO;
use PDOException;
use PDOStatement;



class Database
{
    private PDO $connection;

    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];

    
    public function __construct($host, $dbname, $username, $password, $driver="mysql", $port=3306)
    {
        $dsn = "$driver:host=$host;port=$port;dbname=$dbname";

        try {
            $this->connection = new PDO($dsn, $username, $password, $this->options);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function query($query, $params = []) : PDOStatement
    {
        $statement = $this->connection->prepare($query);

        $statement->execute($params);

        return $statement;
    }
}