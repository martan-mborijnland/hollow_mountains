<?php declare(strict_types=1);

namespace App\Utility;

use App\Utility\Database;

class FormHandler
{
    public login(): void
    {
        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $username = DataProcessor::sanitizeInput($username);
            $password = DataProcessor::sanitizeInput($password);

            $query = "SELECT * FROM personeel WHERE gebruikersnaam = :username LIMIT 1";
            $params = ['username' => $username, 'password' => DataProcessor::hashPassword($password)];
        }
    }
}