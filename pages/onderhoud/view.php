<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=attracties.overzicht');
}

Functions::displayError(message: Session::get('attracties.error'));
Session::delete('attracties.error');

$database = Database::getInstance();

$attractie_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query_attractie = $database->query(query: "
SELECT attractie.id, attractie.naam, attractie.locatie, attractie.foto, attractie.specificaties,
        attractie_type.naam AS type_naam
    FROM attractie
    INNER JOIN attractie_type ON attractie_type.id = attractie.type_id
    WHERE attractie.id = :id;
", params: [
    'id' => $attractie_id
]);

$attractie = $query_attractie->fetch(PDO::FETCH_ASSOC);

if (empty($attractie)) {
    Functions::jsRedirect(url: '?page=attracties.overzicht');
}

$attractie['acties'] = "
    <a href='?page=attracties.edit&id=" . $attractie['id'] . "'>Edit</a>
    <a href='?page=attracties.delete&id=" . $attractie['id'] . "'>Delete</a>
";
$attractie['foto'] = $attractie['foto'] ? '<img src="' . $attractie['foto'] . '" />' : '<img src="websrc/images/no_image.jpg">';


echo "<section>";
echo "<a href='?page=attracties.overzicht'>ga terug...</a>";
Functions::drawTable(
    headers: ['foto', 'naam', 'locatie', 'type_naam', 'specificaties', 'acties'],
    rows: [$attractie],
    direction: 'vertical'
);
echo "</section>";

?>