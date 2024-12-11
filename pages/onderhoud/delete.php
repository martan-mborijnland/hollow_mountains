<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=onderhoud.overzicht');
}

$database = Database::getInstance();

$onderhoud_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query = $database->query(query: "
DELETE FROM onderhoud
WHERE onderhoud.id = :id;
", params: [
    'id' => $onderhoud_id
]);

Functions::jsRedirect(url: '?page=onderhoud.overzicht');