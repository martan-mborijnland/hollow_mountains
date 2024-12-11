<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



// Draw the sidebar with navigation options for 'Overzicht' and 'Add'
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoud.add']
]);

// Get the database instance
$database = Database::getInstance();

// Query to select all rows from the onderhoudstaak table
// and join with the attractie table on the attractie_id column
$query_onderhoudstaak = $database->query(query: "
SELECT ot.id, 
        CONCAT(ot.naam, ' | ', a.naam) AS naam
    FROM onderhoudstaak ot
    INNER JOIN attractie a ON a.id = ot.attractie_id;
");
// Fetch all results as an associative array
$onderhoudstaak = $query_onderhoudstaak->fetchAll(PDO::FETCH_ASSOC);

// Query to select all rows from the personeel table
$query_personeel = $database->query(query: "
SELECT * 
    FROM personeel;
");
// Fetch all results as an associative array
$personeel = $query_personeel->fetchAll(PDO::FETCH_ASSOC);

?>

<form action='?page=formHandler' method='post' enctype="multipart/form-data">
    <input type='hidden' name='action' value='addOnderhoud'>
    <label for="onderhoudstaak_id">Onderhoudstaak</label>
    <select name="onderhoudstaak_id">
    <?php foreach($onderhoudstaak as $taak): ?>
        <option value="<?= $taak['id'] ?>"><?= $taak['naam'] ?></option>
    <?php endforeach; ?>
    </select>
    <label for="personeel_id">Medewerker</label>
    <select name="personeel_id">
    <?php foreach($personeel as $medewerker): ?>
        <option value="<?= $medewerker['id'] ?>"><?= $medewerker['naam'] ?></option>
    <?php endforeach; ?>
    </select>
    <input type='submit' value='Update'>
</form>