<?php

use App\Utility\Database;
use App\Utility\Functions;



$database = Database::getInstance();

$query = $database->query("
SELECT attractie.id, attractie.naam, attractie.locatie, attractie.foto, attractie.specificaties,
        attractie_typen.naam AS type_naam
FROM attractie
INNER JOIN attractie_typen ON attractie_typen.id = attractie.type_id;
");
$attracties = $query->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($attracties); $i++) {
    $attractie = $attracties[$i];
}

if (!empty($attracties)) {
    $headers = ['foto', 'naam', 'locatie', 'type_naam', 'acties'];

    $attracties = array_map(function($attractie) {
        $attractie['acties'] = "
            <a href='?page=attracties.view&id=" . $attractie['id'] . "'>View</a>
            <a href='?page=attracties.editen&id=" . $attractie['id'] . "'>Edit</a>
            <a href='?page=attracties.delete&id=" . $attractie['id'] . "'>Delete</a>
        ";
        $attractie['foto'] = $attractie['foto'] ? '<img src="websrc/images/attracties/' . $attractie['foto'] . '" />' : '<img src="websrc/images/no_image.jpg">';
        return $attractie;
    }, $attracties);

    Functions::drawTable($headers, $attracties);
}

?>