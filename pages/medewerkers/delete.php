<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;



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

Session::set('medewerkers.success', "De medewerker is verwijderd.");
Functions::jsRedirect(url: '?page=medewerkers');