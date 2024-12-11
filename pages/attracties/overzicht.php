<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\Session;


// Display errors
Functions::displayError(message: Session::get('attracties.error'));
Session::delete('attracties.error');

// Display success messages
Functions::displaySuccess(message: Session::get('attracties.success'));
Session::delete('attracties.success');

// Draw the sidebar with navigation options
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'attracties.overzicht'],
    ['label' => 'Add', 'page' => 'attracties.add']
]);

// Get the database instance
$database = Database::getInstance();

// Fetch all attracties from the database
$query = $database->query("
SELECT attractie.id, attractie.naam, attractie.locatie, attractie.foto, attractie.specificaties,
        attractie_type.naam AS type_naam
FROM attractie
INNER JOIN attractie_type ON attractie_type.id = attractie.type_id;
");
$attracties = $query->fetchAll(PDO::FETCH_ASSOC);

// If there are attracties, display them in a table
if (!empty($attracties)) {
    $headers = ['foto', 'naam', 'locatie', 'type_naam', 'acties'];

    $attracties = array_map(function($attractie) {
        $attractie['acties'] = "
            <a href='?page=attracties.view&id=" . $attractie['id'] . "'>View</a>
            <a href='?page=attracties.edit&id=" . $attractie['id'] . "'>Edit</a>
            <a href='?page=attracties.delete&id=" . $attractie['id'] . "'>Delete</a>
        ";
        $attractie['foto'] = $attractie['foto'] ? '<img src="' . $attractie['foto'] . '" />' : '<img src="websrc/images/no_image.jpg">';
        return $attractie;
    }, $attracties);

    echo "<section>";
    Functions::drawTable($headers, $attracties);
    echo "</section>";
}

?>