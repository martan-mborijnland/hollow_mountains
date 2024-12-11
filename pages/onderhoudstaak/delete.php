<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



// Check if the 'id' parameter is set in the GET parameters
if (!isset($_GET['id'])) {
    // If not, redirect to the 'onderhoudstaak.overzicht' page
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}

// Get the instance of the database
$database = Database::getInstance();

// Sanitize the 'id' parameter from the GET parameters
$onderhoudstaak_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to delete the onderhoudstaak from the database
$query = $database->query(query: "
DELETE FROM onderhoudstaak
WHERE onderhoudstaak.id = :id;
", params: [
    'id' => $onderhoudstaak_id
]);

// Redirect to the 'onderhoudstaak.overzicht' page after the query is executed
Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
