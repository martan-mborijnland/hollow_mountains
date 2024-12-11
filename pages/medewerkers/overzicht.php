<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\Session;


// Display errors
Functions::displayError(message: Session::get('medewerkers.error'));
Session::delete('medewerkers.error');

// Display success messages
Functions::displaySuccess(message: Session::get('medewerkers.success'));
Session::delete('medewerkers.success');

// Draw the sidebar with navigation options
Functions::drawSidebar(options: [ 
    ['label' => 'Overzicht', 'page' => 'medewerkers.overzicht'],
    ['label' => 'Add', 'page' => 'medewerkers.add']
]);


// Get the database instance
$database = Database::getInstance();


// Query to fetch all medewerkers
$query = $database->query("
SELECT personeel.id, personeel.gebruikersnaam, personeel.naam, personeel.adres,
        rol.naam AS rol_naam
FROM personeel
INNER JOIN rol ON rol.id = personeel.rol_id;
");
$medewerkers = $query->fetchAll(PDO::FETCH_ASSOC);


// If medewerkers are found, display them in a table
if (!empty($medewerkers)) {
    $headers = ['gebruikersnaam', 'naam', 'adres', 'rol_naam', 'acties'];

    // Add links to view, edit and delete the medewerker in the acties column
    $medewerkers = array_map(function($medewerker) {
        $medewerker['acties'] = "
            <a href='?page=medewerkers.view&id=" . $medewerker['id'] . "'>View</a>
            <a href='?page=medewerkers.edit&id=" . $medewerker['id'] . "'>Edit</a>
            <a href='?page=medewerkers.delete&id=" . $medewerker['id'] . "'>Delete</a>
        ";
        return $medewerker;
    }, $medewerkers);

    echo "<section>";
    Functions::drawTable($headers, $medewerkers);
    echo "</section>";
}

?>
