<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



// Check if the required GET parameters are set
if (!isset($_GET['type']) || !isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=onderhoud.overzicht');
}

// Get the type of edit
$onderhoud_type = DataProcessor::sanitizeData(data: $_GET['type']);

// Check if the user has permission to edit this type
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

// If the user doesn't have permission, redirect to the overview page
if ($redirect) {
    Functions::jsRedirect(url: '?page=onderhoud.overzicht');
    die();
}

// Draw the sidebar with navigation options
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoud.add']
]);

// Get the database instance
$database = Database::getInstance();

// Get the ID of the onderhoud to edit
$onderhoud_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to select the onderhoud to edit
$query_onderhoud = $database->query(query: "
SELECT o.id, o.status_id, o.personeel_id
FROM onderhoud o
INNER JOIN onderhoudstaak ot ON ot.id = o.onderhoudstaak_id
INNER JOIN attractie a ON a.id = ot.attractie_id
WHERE o.id = :id;
", params: [
    'id' => $onderhoud_id
]);

// Fetch the result as an associative array
$onderhoud = $query_onderhoud->fetch(PDO::FETCH_ASSOC);

// Get the extra data needed for the form
$extra_data = [];

// Query to select all rows from the personeel table
$query_personeel = $database->query(query: "
SELECT * 
    FROM personeel;
");
// Fetch all results as an associative array
$extra_data['personeel'] = $query_personeel->fetchAll(PDO::FETCH_ASSOC);

// Query to select all rows from the status table
$query_status = $database->query(query: "
SELECT * 
    FROM status;
");
// Fetch all results as an associative array
$extra_data['status'] = $query_status->fetchAll(PDO::FETCH_ASSOC);

// If the onderhoud to edit is not found, redirect to the overview page
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