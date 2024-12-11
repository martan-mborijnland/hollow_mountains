<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!Functions::checkPermissions(['beheerder', 'manager'])) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}

if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}


Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoudstaak.overzicht'],
    ['label' => 'Add', 'page' => 'onderhoudstaak.add']
]);


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

$query_attracties = $database->query(query: "
SELECT * 
    FROM attractie;
");
$attracties = $query_attracties->fetchAll(PDO::FETCH_ASSOC);

if (empty($onderhoudstaak)) {
    Functions::jsRedirect(url: '?page=onderhoudstaak.overzicht');
}

?>

<form action='?page=formHandler' method='post' enctype="multipart/form-data">
    <input type='hidden' name='action' value='updateOnderhoudstaak'>
    <input type='hidden' name='id' value='<?= $onderhoudstaak['id'] ?>'>
    <label for="attractie_id">Attractie</label>
    <select name='attractie_id'>
        <?php foreach ($attracties as $attractie): ?>
            <option value="<?= $attractie['id'] ?>" <?= $attractie['id'] == $onderhoudstaak['attractie_id'] ? 'selected' : '' ?>><?= $attractie['naam'] ?></option>
        <?php endforeach; ?>
    </select>
    <label for="naam">Naam</label>
    <input type="text" name="naam" value="<?= $onderhoudstaak['naam'] ?>" minlength="0" maxlength="32">
    <label for="beschrijving">Beschrijving</label>
    <textarea name="beschrijving" minlength="0" maxlength="256"><?= $onderhoudstaak['beschrijving'] ?></textarea>
    <label for="start_datum">Start datum</label>
    <input type='date' name='start_datum' value='<?= $onderhoudstaak['start_datum'] ?>'>
    <label for="duur_dagen">Duur (dagen)</label>
    <input type="number" name="duur_dagen" value="<?= $onderhoudstaak['duur_dagen'] ?>">
    <label for="herhaling_dagen">Herhaling (dagen)</label>
    <input type="number" name="herhaling_dagen" value="<?= $onderhoudstaak['herhaling_dagen'] ?>">
    <input type='submit' value='Update'>
</form>