<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!isset($_GET['type']) || !isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=onderhoud.overzicht');
}

$onderhoud_type = DataProcessor::sanitizeData(data: $_GET['type']);
switch ($onderhoud_type):
    case 'personeel':
        $redirect = !Functions::checkPermissions(permissions: ['manager', 'beheerder']);
        break;
    case 'status':
        $redirect = false;
        break;
    default:
        $redirect = true;
        break;
endswitch;


if ($redirect) {
    Functions::jsRedirect(url: '?page=onderhoud.overzicht');
    die();
}

Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoud.add']
]);


$database = Database::getInstance();

$onderhoud_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query_onderhoud = $database->query(query: "
SELECT o.id, o.status_id, o.personeel_id
FROM onderhoud o
INNER JOIN onderhoudstaak ot ON ot.id = o.onderhoudstaak_id
INNER JOIN attractie a ON a.id = ot.attractie_id
WHERE o.id = :id;
", params: [
    'id' => $onderhoud_id
]);

$onderhoud = $query_onderhoud->fetch(PDO::FETCH_ASSOC);

$extra_data = [];

$query_personeel = $database->query(query: "
SELECT * 
    FROM personeel;
");
$extra_data['personeel'] = $query_personeel->fetchAll(PDO::FETCH_ASSOC);

$query_status = $database->query(query: "
SELECT * 
    FROM status;
");
$extra_data['status'] = $query_status->fetchAll(PDO::FETCH_ASSOC);

if (empty($onderhoud)) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}

?>

<form action='?page=formHandler' method='post' enctype="multipart/form-data">
    <input type='hidden' name='action' value='editOnderhoud'>
    <input type='hidden' name='type' value='<?= $onderhoud_type ?>'>
    <input type='hidden' name='id' value='<?= $onderhoud['id'] ?>'>
<?php
    $type_id = $onderhoud_type ."_id";
    $selection = $extra_data[$onderhoud_type];

    echo "
        <label for='". $type_id ."'>". ucfirst($onderhoud_type) ."</label>
        <select name='". $type_id ."'>
    ";
    foreach ($selection as $selection_) {
        echo "<option value='". $selection_['id'] ."' ". ($selection_['id'] == $onderhoud[$type_id] ? 'selected' : '') .">". $selection_['naam'] ."</option>";
    }
    echo "</select>";
?>
    <input type='submit' value='Update'>
</form>