<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=attracties.overzicht');
}

// Draw the sidebar with navigation options for 'Overzicht' and 'Add'
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'attracties.overzicht'],
    ['label' => 'Add', 'page' => 'attracties.add']
]);

// Display errors
Functions::displayError(message: Session::get('attracties.error'));
Session::delete('attracties.error');

// Display success messages
Functions::displaySuccess(message: Session::get('attracties.success'));
Session::delete('attracties.success');

// Get the database instance
$database = Database::getInstance();

// Sanitize the 'id' from GET parameters
$attractie_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to fetch attractie details
$query_attractie = $database->query(query: "
SELECT attractie.id, attractie.naam, attractie.locatie, attractie.foto, attractie.specificaties,
        attractie_type.naam AS type_naam
    FROM attractie
    INNER JOIN attractie_type ON attractie_type.id = attractie.type_id
    WHERE attractie.id = :id;
", params: [
    'id' => $attractie_id
]);

// Fetch the attractie
$attractie = $query_attractie->fetch(PDO::FETCH_ASSOC);

// Redirect if no attractie found
if (empty($attractie)) {
    Functions::jsRedirect(url: '?page=attracties.overzicht');
}

// Format the specificaties as a pre tag
$attractie['specificaties'] = "<pre>" . $attractie['specificaties'] . "</pre>";
// Use the foto if it exists, otherwise use a placeholder
$attractie['foto'] = $attractie['foto'] ? '<img src="' . $attractie['foto'] . '" />' : '<img src="websrc/images/no_image.jpg">';
// Generate the edit and delete links
$attractie['acties'] = "
    <a href='?page=attracties.edit&id=" . $attractie['id'] . "'>Edit</a>
    <a href='?page=attracties.delete&id=" . $attractie['id'] . "'>Delete</a>
";

// Draw the table
echo "<section>";
Functions::drawTable(
    headers: ['foto', 'naam', 'locatie', 'type_naam', 'specificaties', 'acties'],
    rows: [$attractie],
    direction: 'vertical'
);
echo "</section>";

?>