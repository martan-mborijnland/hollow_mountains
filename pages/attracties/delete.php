<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;

// Check if 'id' is set in GET parameters
if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=attracties.overzicht');
}

$database = Database::getInstance();

// Sanitize the 'id' from GET parameters
$attractie_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to fetch previous image associated with the attractie
$previousImage = $database->query("
SELECT foto
FROM attractie
WHERE id = :id;
", [
    'id' => $attractie_id
])->fetch(PDO::FETCH_ASSOC);

// Delete attractie from the database
$query = $database->query(query: "
DELETE FROM attractie
WHERE attractie.id = :id;
", params: [
    'id' => $attractie_id
]);

// Delete previous image file if it exists
if (isset($previousImage['foto'])) {
    unlink($previousImage['foto']);
}

// Set success message and redirect
Session::set('attracties.success', "De attractie is verwijderd.");
Functions::jsRedirect(url: '?page=attracties.overzicht');

