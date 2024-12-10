<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\Session;



Functions::displayError(message: Session::get('onderhoud.error'));
Session::delete('onderhoud.error');

Functions::displaySuccess(message: Session::get('onderhoud.success'));
Session::delete('onderhoud.success');


Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoud.add']
]);


$database = Database::getInstance();

$query = $database->query("
SELECT ot.id, ot.beschrijving, ot.start_datum, ot.eind_datum,
       p.naam AS medewerker,
       s.naam AS status,
       r.naam AS rol,
       a.naam AS attractie
FROM onderhoud o
INNER JOIN onderhoudstaak ot ON ot.id = o.onderhoudstaak_id
INNER JOIN personeel p ON p.id = o.personeel_id
INNER JOIN status s ON s.id = o.status_id
INNER JOIN attractie a ON a.id = ot.attractie_id
INNER JOIN rol r ON r.id = p.rol_id;
");
$onderhoud = $query->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($onderhoud); $i++) {
    $onderhoud_ = $onderhoud[$i];
}

if (!empty($onderhoud)) {
    $headers = ['status', 'attractie', 'medewerker', 'rol', 'start_datum', 'eind_datum', 'acties'];

    $onderhoud = array_map(function($onderhoud_) {
        $onderhoud_['acties'] = "
            <a href='?page=onderhoud.view&id=" . $onderhoud_['id'] . "'>View</a>
            <a href='?page=onderhoud.edit&id=" . $onderhoud_['id'] . "'>Edit</a>
            <a href='?page=onderhoud.delete&id=" . $onderhoud_['id'] . "'>Delete</a>
        ";
        return $onderhoud_;
    }, $onderhoud);

    echo "<section>";
    Functions::drawTable($headers, $onderhoud);
    echo "</section>";
}

?>