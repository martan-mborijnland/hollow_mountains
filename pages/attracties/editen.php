<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=attracties');
}

$database = Database::getInstance();

$attractie_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query_attractie = $database->query(query: "
SELECT attractie.id, attractie.gebruikersnaam, attractie.naam, attractie.adres, 
        rol.naam AS rol_naam, rol.id AS rol_id
    FROM attractie
    INNER JOIN rol ON rol.id = attractie.rol_id
    WHERE attractie.id = :id;
", params: [
    'id' => $attractie_id
]);

$attractie = $query_attractie->fetch(PDO::FETCH_ASSOC);

$query_rollen = $database->query(query: "
SELECT * 
    FROM rol;
");
$rollen = $query_rollen->fetchAll(PDO::FETCH_ASSOC);

if (empty($attractie)) {
    Functions::jsRedirect(url: '?page=attracties');
}

?>

<form action='?page=formHandler' method='post'>
    <input type='hidden' name='action' value='updateAttractie'>
    <input type='hidden' name='id' value='<?= $attractie['id'] ?>'>
    <label for="naam">Naam</label>
    <input type='text' name='naam' value='<?= $attractie['naam'] ?>'>
    <label for="naam">Gebruikersnaam</label>
    <input type='text' name='gebruikersnaam' value='<?= $attractie['gebruikersnaam'] ?>'>
    <label for="naam">Adres</label>
    <input type='text' name='adres' value='<?= $attractie['adres'] ?>'>
    <label for="naam">Rol</label>
    <select name='rol_id'>
        <?php foreach ($rollen as $rol): ?>
            <option value="<?= $rol['id'] ?>" <?= $rol['id'] == $attractie['rol_id'] ? 'selected' : '' ?>><?= $rol['naam'] ?></option>
        <?php endforeach; ?>
    </select>
    <input type='submit' value='Update'>
</form>