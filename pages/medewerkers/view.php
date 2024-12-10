<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=medewerkers.overzicht');
}

$database = Database::getInstance();

$medewerker_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query_medewerker = $database->query(query: "
SELECT personeel.id, personeel.gebruikersnaam, personeel.naam, personeel.adres, 
        rol.naam AS rol_naam
    FROM personeel
    INNER JOIN rol ON rol.id = personeel.rol_id
    WHERE personeel.id = :id;
", params: [
    'id' => $medewerker_id
]);

$medewerker = $query_medewerker->fetch(PDO::FETCH_ASSOC);

if (empty($medewerker)) {
    Functions::jsRedirect(url: '?page=medewerkers.overzicht');
}

$medewerker['acties'] = "
    <a href='?page=medewerkers.editen&id=" . $medewerker['id'] . "'>Edit</a>
    <a href='?page=medewerkers.delete&id=" . $medewerker['id'] . "'>Delete</a>
";

echo "<a href='?page=medewerkers.overzicht'>ga terug...</a>";
Functions::drawTable(
    headers: ['gebruikersnaam', 'naam', 'adres', 'rol_naam', 'acties'],
    rows: [$medewerker],
    direction: 'vertical'
);

?>