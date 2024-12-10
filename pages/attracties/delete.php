<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;


if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=attracties.overzicht');
}

$database = Database::getInstance();

$attractie_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query = $database->query(query: "
DELETE FROM attractie
WHERE attractie.id = :id;
", params: [
    'id' => $attractie_id
]);

Session::set('attracties.success', "De attractie is verwijderd.");
Functions::jsRedirect(url: '?page=attracties.overzicht');