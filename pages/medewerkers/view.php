<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
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


// Redirect if 'id' is not set in GET parameters
if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=medewerkers.overzicht');
}

// Get the database instance
$database = Database::getInstance();

// Sanitize the 'id' from GET parameters
$medewerker_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to fetch medewerker details
$query_medewerker = $database->query(query: "
SELECT personeel.id, personeel.gebruikersnaam, personeel.naam, personeel.adres, 
        rol.naam AS rol_naam
    FROM personeel
    INNER JOIN rol ON rol.id = personeel.rol_id
    WHERE personeel.id = :id;
", params: [
    'id' => $medewerker_id
]);

// Fetch medewerker details as an associative array
$medewerker = $query_medewerker->fetch(PDO::FETCH_ASSOC);

// Redirect if no medewerker found
if (empty($medewerker)) {
    Functions::jsRedirect(url: '?page=medewerkers.overzicht');
}

// Add links to view, edit and delete the medewerker in the acties column
$medewerker['acties'] = "
    <a href='?page=medewerkers.edit&id=" . $medewerker['id'] . "'>Edit</a>
    <a href='?page=medewerkers.delete&id=" . $medewerker['id'] . "'>Delete</a>
";

// Display the medewerker details in a table
echo "<section>";
Functions::drawTable(
    headers: ['gebruikersnaam', 'naam', 'adres', 'rol_naam', 'acties'],
    rows: [$medewerker],
    direction: 'vertical'
);
echo "</section>";

?>