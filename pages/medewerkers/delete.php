<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=medewerkers');
}

$database = Database::getInstance();

$medewerker_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query = $database->query(query: "
DELETE FROM personeel
WHERE personeel.id = :id;
", params: [
    'id' => $medewerker_id
]);

Functions::jsRedirect(url: '?page=medewerkers');