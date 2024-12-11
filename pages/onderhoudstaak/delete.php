<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}

$database = Database::getInstance();

$onderhoudstaak_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query = $database->query(query: "
DELETE FROM onderhoudstaak
WHERE onderhoudstaak.id = :id;
", params: [
    'id' => $onderhoudstaak_id
]);

Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');