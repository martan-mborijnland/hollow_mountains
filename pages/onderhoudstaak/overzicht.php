<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\Session;



// Display errors
Functions::displayError(message: Session::get('onderhoudstaak.error'));
Session::delete('onderhoudstaak.error');

// Display success messages
Functions::displaySuccess(message: Session::get('onderhoudstaak.success'));
Session::delete('onderhoudstaak.success');


// Draw the sidebar with navigation options
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoudstaak.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoudstaak.add']
]);


// Get the database instance
$database = Database::getInstance();

// Query to fetch all rows from the onderhoudstaak table
$query = $database->query("
SELECT ot.id, ot.naam, ot.beschrijving, ot.start_datum, ot.duur_dagen, ot.herhaling_dagen,
       a.naam AS attractie, a.id AS attractie_id
FROM onderhoudstaak ot
INNER JOIN attractie a ON a.id = ot.attractie_id;
");

// Fetch all results as an associative array
$onderhoud = $query->fetchAll(PDO::FETCH_ASSOC);

// Check if onderhoud array is not empty
if (!empty($onderhoud)) {
    // Define table headers
    $headers = ['id', 'naam', 'attractie', 'start_datum', 'duur_dagen', 'herhaling_dagen', 'acties'];

    // Map over onderhoud items to add links to 'attractie' and 'acties'
    $onderhoud = array_map(function($onderhoud_) {
        // Add hyperlink to the 'attractie'
        $onderhoud_['attractie'] = "<a href='?page=attracties.view&id=" . $onderhoud_['attractie_id'] . "'>". $onderhoud_['attractie'] ."</a>";
        // Add 'View', 'Edit', and 'Delete' actions
        $onderhoud_['acties'] = "
            <a href='?page=onderhoudstaak.view&id=" . $onderhoud_['id'] . "'>View</a>
            <a href='?page=onderhoudstaak.edit&id=" . $onderhoud_['id'] . "'>Edit</a>
            <a href='?page=onderhoudstaak.delete&id=" . $onderhoud_['id'] . "'>Delete</a>
        ";
        return $onderhoud_;
    }, $onderhoud);

    // Render the table with headers and onderhoud data
    echo "<section>";
    Functions::drawTable($headers, $onderhoud);
    echo "</section>";
}

?>