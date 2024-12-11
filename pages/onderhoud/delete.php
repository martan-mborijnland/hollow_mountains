<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



// Check if 'id' is set in GET parameters
if (!isset($_GET['id'])) {
    // If not, redirect to the onderhoud overview page
    Functions::jsRedirect(url: '?page=onderhoud.overzicht');
}

// Get the database instance
$database = Database::getInstance();

// Sanitize the 'id' from GET parameters
$onderhoud_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to delete the onderhoud from the database
$query = $database->query(query: "
DELETE FROM onderhoud
WHERE onderhoud.id = :id;
", params: [
    'id' => $onderhoud_id
]);

// Redirect to the onderhoud overview page
Functions::jsRedirect(url: '?page=onderhoud.overzicht');
