<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;



// Check if the 'id' parameter is set in the GET request
if (!isset($_GET['id'])) {
    // If not, redirect to the 'onderhoudstaak.overzicht' page
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}

// Render the sidebar with navigation options
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoudstaak.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoudstaak.add']
]);

// Display any errors or success messages
Functions::displayError(message: Session::get('onderhoudstaak.error'));
Session::delete('onderhoudstaak.error');

Functions::displaySuccess(message: Session::get('onderhoudstaak.success'));
Session::delete('onderhoudstaak.success');

// Get the database instance
$database = Database::getInstance();

// Sanitize the 'id' parameter from the GET request
$onderhoudstaak_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Fetch the onderhoudstaak details from the database
$query_onderhoudstaak = $database->query(query: "
SELECT ot.id, ot.naam, ot.beschrijving, ot.start_datum, ot.duur_dagen, ot.herhaling_dagen,
       a.naam AS attractie, a.id AS attractie_id
FROM onderhoudstaak ot
INNER JOIN attractie a ON a.id = ot.attractie_id
WHERE ot.id = :id;
", params: [
    'id' => $onderhoudstaak_id
]);

// Fetch the result as an associative array
$onderhoudstaak = $query_onderhoudstaak->fetch(PDO::FETCH_ASSOC);

// Redirect to the 'onderhoudstaak.overzicht' page if the onderhoudstaak does not exist
if (empty($onderhoudstaak)) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}

// Format the 'beschrijving' column by wrapping it in a <pre> tag
$onderhoudstaak['beschrijving'] = "<pre>" . $onderhoudstaak['beschrijving'] . "</pre>";
// Format the 'attractie' column by adding a hyperlink to the attractie page
$onderhoudstaak['attractie'] = "<a href='?page=attracties.view&id=" . $onderhoudstaak['attractie_id'] . "'>". $onderhoudstaak['attractie'] ."</a>";
// Format the 'acties' column by adding links to the edit and delete pages
$onderhoudstaak['acties'] = "
    <a href='?page=onderhoudstaak.edit&id=" . $onderhoudstaak['id'] . "'>Edit</a>
    <a href='?page=onderhoudstaak.delete&id=" . $onderhoudstaak['id'] . "'>Delete</a>
";

// Render the table with the formatted onderhoudstaak data
echo "<section>";
Functions::drawTable(
    headers: ['attractie', 'naam', 'beschrijving', 'start_datum', 'duur_dagen', 'herhaling_dagen', 'acties'],
    rows: [$onderhoudstaak],
    direction: 'vertical'
);
echo "</section>";

?>