<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\Session;



Functions::displayError(message: Session::get('onderhoudstaak.error'));
Session::delete('onderhoudstaak.error');

Functions::displaySuccess(message: Session::get('onderhoudstaak.success'));
Session::delete('onderhoudstaak.success');


Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoudstaak.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoudstaak.add']
]);


$database = Database::getInstance();

$query = $database->query("
SELECT ot.id, ot.naam, ot.beschrijving, ot.start_datum, ot.duur_dagen, ot.herhaling_dagen,
       a.naam AS attractie, a.id AS attractie_id
FROM onderhoudstaak ot
INNER JOIN attractie a ON a.id = ot.attractie_id;
");
$onderhoud = $query->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($onderhoud); $i++) {
    $onderhoud_ = $onderhoud[$i];
}

if (!empty($onderhoud)) {
    $headers = ['id', 'naam', 'attractie', 'start_datum', 'duur_dagen', 'herhaling_dagen', 'acties'];

    $onderhoud = array_map(function($onderhoud_) {
        $onderhoud_['attractie'] = "<a href='?page=attracties.view&id=" . $onderhoud_['attractie_id'] . "'>". $onderhoud_['attractie'] ."</a>";
        $onderhoud_['acties'] = "
            <a href='?page=onderhoudstaak.view&id=" . $onderhoud_['id'] . "'>View</a>
            <a href='?page=onderhoudstaak.edit&id=" . $onderhoud_['id'] . "'>Edit</a>
            <a href='?page=onderhoudstaak.delete&id=" . $onderhoud_['id'] . "'>Delete</a>
        ";
        return $onderhoud_;
    }, $onderhoud);

    echo "<section>";
    Functions::drawTable($headers, $onderhoud);
    echo "</section>";
}

?>