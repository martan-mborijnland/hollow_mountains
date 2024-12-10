<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=medewerkers');
}

$database = Database::getInstance();

$medewerker_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query_medewerker = $database->query(query: "
SELECT personeel.id, personeel.gebruikersnaam, personeel.naam, personeel.adres, 
        rol.naam AS rol_naam, rol.id AS rol_id
    FROM personeel
    INNER JOIN rol ON rol.id = personeel.rol_id
    WHERE personeel.id = :id;
", params: [
    'id' => $medewerker_id
]);

$medewerker = $query_medewerker->fetch(PDO::FETCH_ASSOC);

$query_rollen = $database->query(query: "
SELECT * 
    FROM rol;
");
$rollen = $query_rollen->fetchAll(PDO::FETCH_ASSOC);

if (empty($medewerker)) {
    Functions::jsRedirect(url: '?page=medewerkers');
}

?>

<form action='?page=formHandler' method='post'>
    <input type='hidden' name='action' value='updateMedewerker'>
    <input type='hidden' name='id' value='<?= $medewerker['id'] ?>'>
    <label for="naam">Naam</label>
    <input type='text' name='naam' value='<?= $medewerker['naam'] ?>'>
    <label for="naam">Gebruikersnaam</label>
    <input type='text' name='gebruikersnaam' value='<?= $medewerker['gebruikersnaam'] ?>'>
    <label for="naam">Adres</label>
    <input type='text' name='adres' value='<?= $medewerker['adres'] ?>'>
    <label for="naam">Rol</label>
    <select name='rol_id'>
        <?php foreach ($rollen as $rol): ?>
            <option value="<?= $rol['id'] ?>" <?= $rol['id'] == $medewerker['rol_id'] ? 'selected' : '' ?>><?= $rol['naam'] ?></option>
        <?php endforeach; ?>
    </select>
    <input type='submit' value='Update'>
</form>