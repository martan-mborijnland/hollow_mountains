<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;

// Check if 'id' is set in GET parameters
if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=medewerkers');
}

// Get the database instance
$database = Database::getInstance();

// Sanitize the 'id' from GET parameters
$medewerker_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to delete the medewerker from the database
$query = $database->query(query: "
DELETE FROM personeel
WHERE personeel.id = :id;
", params: [
    'id' => $medewerker_id
]);

// Set success message and redirect
Session::set('medewerkers.success', "De medewerker is verwijderd.");
Functions::jsRedirect(url: '?page=medewerkers');
