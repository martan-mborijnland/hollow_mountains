<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!Functions::checkPermissions(['beheerder', 'manager'])) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}


Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoudstaak.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoudstaak.add']
]);


$database = Database::getInstance();

$query_attracties = $database->query(query: "
SELECT * 
    FROM attractie;
");
$attracties = $query_attracties->fetchAll(PDO::FETCH_ASSOC);

?>

<form action='?page=formHandler' method='post' enctype="multipart/form-data">
    <input type='hidden' name='action' value='addOnderhoudstaak'>
    <label for="attractie_id">Attractie</label>
    <select name='attractie_id'>
        <?php foreach ($attracties as $attractie): ?>
            <option value="<?= $attractie['id'] ?>"><?= $attractie['naam'] ?></option>
        <?php endforeach; ?>
    </select>
    <label for="naam">Naam</label>
    <input type="text" name="naam" value='' minlength="0" maxlength="32">
    <label for="beschrijving">Beschrijving</label>
    <textarea name="beschrijving" minlength="0" maxlength="256"></textarea>
    <label for="start_datum">Start datum</label>
    <input type='date' name='start_datum' value=''>
    <label for="duur_dagen">Duur (dagen)</label>
    <input type="number" name="duur_dagen" value="<?= $onderhoudstaak['duur_dagen'] ?>">
    <label for="herhaling_dagen">Herhaling (dagen)</label>
    <input type="number" name="herhaling_dagen" value="<?= $onderhoudstaak['herhaling_dagen'] ?>">
    <input type='submit' value='Add'>
</form>