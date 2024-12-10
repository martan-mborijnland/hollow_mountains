<?php declare(strict_types=1);

namespace App;

use App\Utility\Database;
use App\Core\Configuration;
use App\Utility\Session;
use App\Utility\Functions;


class Application
{
    private string $page = "home";
    private Database $database;

    private array $ignoreElements = ['logout', 'login'];
    private array $ignoreHead = ['logout'];

    private array $ignoreLoggedIn = ['home', 'login'];



    public function __construct()
    {
        Configuration::load();

        Session::start();
        if (Session::get('loggedIn') !== true) {
            Session::delete('user');
            Session::set('loggedIn', false);
        }

        $this->database = Database::getInstance();

        if (!isset($_GET['page'])) {
            header("Location: ?page=". $this->page);
        }

        $this->route();

        if (!file_exists(dirname(__DIR__) . '/pages/' . $this->page . '.php')) {
            require_once dirname(__DIR__) . '/pages/404.php';
            die();
        }

        if ($this->page !== 'formHandler') {
            if (!in_array($this->page, $this->ignoreHead)) {
                require_once dirname(__DIR__) . '/elements/head.php';
            }

            if (!in_array($this->page, $this->ignoreElements)) {
                require_once dirname(__DIR__) . '/elements/header.php';
                echo "<script>document.body.classList.add('has-header')</script>";
            }

            echo "<main id='" . (str_replace('/', '-', $this->page) ?? "") . "'>";
            require_once dirname(__DIR__) . '/pages/' . $this->page . '.php';
            echo "</main>";

            if (!in_array($this->page, $this->ignoreElements)) {
                require_once dirname(__DIR__) . '/elements/footer.php';
            }
        } else {
            require_once dirname(__DIR__) . '/pages/' . $this->page . '.php';
        }
    }


    private function route(): void
    {
        if (isset($_GET['page']) && $_GET['page'] !== '') {
            $this->page = $_GET['page'];
        }

        if (!Session::get('loggedIn') && !in_array($this->page, $this->ignoreLoggedIn)) {
            header("Location: ?page=login");
        }

        if (Session::get('loggedIn') === true && $this->page === 'login') {
            header("Location: ?page=home");
        }

        if (count(explode('.', $this->page)) > 1) {
            if (file_exists(dirname(__DIR__) . '/pages/' . explode('.', $this->page)[0] . '/index.php')) {
                require_once dirname(__DIR__) . '/pages/' . explode('.', $this->page)[0] . '/index.php';
            }

            $this->page = str_replace('.', '/', $this->page);
        }
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }
}