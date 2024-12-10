<?php

use App\Utility\Database;
use App\Utility\Functions;



$database = Database::getInstance();

$query = $database->query("
SELECT personeel.id, personeel.gebruikersnaam, personeel.naam, personeel.adres,
        rol.naam AS rol_naam
FROM personeel
INNER JOIN rol ON rol.id = personeel.rol_id;
");
$medewerkers = $query->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($medewerkers); $i++) {
    $medewerker = $medewerkers[$i];
}

if (!empty($medewerkers)) {
    $headers = ['gebruikersnaam', 'naam', 'adres', 'rol_naam', 'acties'];

    $medewerkers = array_map(function($medewerker) {
        $medewerker['acties'] = "
            <a href='?page=medewerkers.view&id=" . $medewerker['id'] . "'>View</a>
            <a href='?page=medewerkers.editen&id=" . $medewerker['id'] . "'>Edit</a>
            <a href='?page=medewerkers.delete&id=" . $medewerker['id'] . "'>Delete</a>
        ";
        return $medewerker;
    }, $medewerkers);

    Functions::drawTable($headers, $medewerkers);
}

?>