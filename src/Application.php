<?php declare(strict_types=1);

namespace App;

use App\Utility\Database;
use App\Core\Configuration;
use App\Utility\Session;


class Application
{
    private string $page = "login";
    private Database $database;

    public function __construct()
    {
        Configuration::load();
        Session::start();

        $this->database = new Database(Configuration::read('db.host'), Configuration::read('db.dbname'), Configuration::read('db.username'), Configuration::read('db.password'), Configuration::read('db.driver'), Configuration::read('db.port'));


        if (isset($_GET['page'])) {
            $this->page = $_GET['page'];
        }

        $this->route();
    }

    private function route(): void
    {
        require_once dirname(__DIR__) . '/pages/' . $this->page . '.php';
    }
}