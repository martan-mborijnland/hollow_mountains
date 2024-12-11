<?php declare(strict_types=1);

namespace App;

use App\Utility\Database;
use App\Core\Configuration;
use App\Utility\Session;
use App\Utility\Functions;


/**
 * Class Application
 *
 * This class is the main application class for the Hollow Mountains theme park management system.
 *
 * @author Martan van Verseveld
 */
class Application
{
    /**
     * The current page requested by the user.
     * Defaults to "home" if not specified.
     */
    private string $page = "home";

    /**
     * Instance of the Database class for database interactions.
     */
    private Database $database;

    /**
     * Pages that do not require specific elements to be included.
     */
    private array $ignoreElements = ['logout', 'login'];

    /**
     * Pages that do not require specific head elements to be included.
     */
    private array $ignoreHead = ['logout'];

    /**
     * Pages that can be accessed without logging in.
     */
    private array $ignoreLoggedIn = ['home', 'login'];



    /**
     * Constructor for the Application class.
     *
     * This constructor is responsible for:
     * - Starting the session
     * - Checking if the user is logged in
     * - Loading the configuration
     * - Initializing the database
     * - Routing the application
     * - Including the necessary pages
     */
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


    /**
     * Handles the routing of the application.
     * 
     * - If `$_GET['page']` is set, it will be used to determine the page to load.
     * - If the user is not logged in and the page is not in the `$ignoreLoggedIn` array, the user will be redirected to the login page.
     * - If the user is logged in and the page is the login page, the user will be redirected to the home page.
     * - If the page contains a dot (`.`), it will be replaced with a slash (`/`) and the page will be loaded from the subdirectory.
     * - If the page does not exist, a 404 page will be displayed.
     */
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

    /**
     * Returns the database instance.
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }
}