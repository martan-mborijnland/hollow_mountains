<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}


Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoudstaak.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoudstaak.add']
]);


Functions::displayError(message: Session::get('onderhoudstaak.error'));
Session::delete('onderhoudstaak.error');

Functions::displaySuccess(message: Session::get('onderhoudstaak.success'));
Session::delete('onderhoudstaak.success');

$database = Database::getInstance();

$onderhoudstaak_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query_onderhoudstaak = $database->query(query: "
SELECT ot.id, ot.naam, ot.beschrijving, ot.start_datum, ot.duur_dagen, ot.herhaling_dagen,
       a.naam AS attractie, a.id AS attractie_id
FROM onderhoudstaak ot
INNER JOIN attractie a ON a.id = ot.attractie_id
WHERE ot.id = :id;
", params: [
    'id' => $onderhoudstaak_id
]);

$onderhoudstaak = $query_onderhoudstaak->fetch(PDO::FETCH_ASSOC);


if (empty($onderhoudstaak)) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}

$onderhoudstaak['beschrijving'] = "<pre>" . $onderhoudstaak['beschrijving'] . "</pre>";
$onderhoudstaak['attractie'] = "<a href='?page=attracties.view&id=" . $onderhoudstaak['attractie_id'] . "'>". $onderhoudstaak['attractie'] ."</a>";
$onderhoudstaak['acties'] = "
    <a href='?page=onderhoudstaak.edit&id=" . $onderhoudstaak['id'] . "'>Edit</a>
    <a href='?page=onderhoudstaak.delete&id=" . $onderhoudstaak['id'] . "'>Delete</a>
";

echo "<section>";
Functions::drawTable(
    headers: ['attractie', 'naam', 'beschrijving', 'start_datum', 'duur_dagen', 'herhaling_dagen', 'acties'],
    rows: [$onderhoudstaak],
    direction: 'vertical'
);
echo "</section>";

?>