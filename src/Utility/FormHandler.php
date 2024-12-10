<?php declare(strict_types=1);

namespace App\Utility;

use PDO;
use App\Utility\Database;
use App\Utility\DataProcessor;
use App\Utility\Session;



class FormHandler
{
    
    private Database $database;
    
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    public function login(): void
    {
        $sanatizedPOST = DataProcessor::sanitizeData($_POST);

        $query = "
            SELECT personeel.naam, personeel.gebruikersnaam, personeel.wachtwoord, rol.naam AS rol
                FROM personeel
                INNER JOIN rol ON rol.id = personeel.rol_id
                WHERE gebruikersnaam = :username 
                LIMIT 1;
        ";
        $params = ['username' => $sanatizedPOST['username']];

        $result = $this->database->query($query, $params);

        if ($result->rowCount() <= 0) {
            Session::set('error', 'Username and password do not match!');
            header("Location: ?page=login");
            exit();
        }

        $row = $result->fetch(PDO::FETCH_ASSOC);
        if (!DataProcessor::checkPassword($sanatizedPOST['password'], $row['wachtwoord'])) {
            Session::set('error', 'Username and password do not match!');
            header("Location: ?page=login");
            exit();
        }

        unset($row['wachtwoord']);
        Session::set('user', $row);
        Session::set('loggedIn', true);

        header("Location: ?page=home");
        exit();
    }

    public function logout(): void
    {
        Session::destroy();
        header("Location: ?page=login");
    }

    public function medewerkerUpdate(): void
    {
        $sanatizedPOST = DataProcessor::sanitizeData($_POST);
        $query = "
            UPDATE personeel
                SET naam = :naam, gebruikersnaam = :gebruikersnaam, rol_id = :rol_id, adres = :adres
                WHERE id = :id;
        ";
        $params = [
            'naam' => $sanatizedPOST['naam'],
            'gebruikersnaam' => $sanatizedPOST['gebruikersnaam'],
            'rol_id' => $sanatizedPOST['rol_id'],
            'adres' => $sanatizedPOST['adres'],
            'id' => $sanatizedPOST['id']
        ];

        $result = $this->database->query($query, $params);

        header("Location: ?page=medewerkers.overzicht");
    }
}