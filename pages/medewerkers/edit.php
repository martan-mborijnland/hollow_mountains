<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;

// Redirect if 'id' is not set in GET parameters
if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=medewerkers');
}

// Draw the sidebar with navigation options
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'medewerkers.overzicht'],
    ['label' => 'Add', 'page' => 'medewerkers.add']
]);

// Get the database instance
$database = Database::getInstance();

// Sanitize the 'id' from GET parameters
$medewerker_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to fetch medewerker details
$query_medewerker = $database->query(query: "
SELECT personeel.id, personeel.gebruikersnaam, personeel.naam, personeel.adres, 
        rol.naam AS rol_naam, rol.id AS rol_id
    FROM personeel
    INNER JOIN rol ON rol.id = personeel.rol_id
    WHERE personeel.id = :id;
", params: [
    'id' => $medewerker_id
]);

// Fetch medewerker details as an associative array
$medewerker = $query_medewerker->fetch(PDO::FETCH_ASSOC);

// Query to fetch all roles
$query_rollen = $database->query(query: "
SELECT * 
    FROM rol;
");

// Fetch all roles as an associative array
$rollen = $query_rollen->fetchAll(PDO::FETCH_ASSOC);

// Redirect if no medewerker found
if (empty($medewerker)) {
    Functions::jsRedirect(url: '?page=medewerkers');
}

?>

<form action='?page=formHandler' method='post' enctype="multipart/form-data">
    <input type='hidden' name='action' value='updateMedewerker'>
    <input type='hidden' name='id' value='<?= $medewerker['id'] ?>'>
    <label for="naam">Naam</label>
    <input type='text' name='naam' value='<?= $medewerker['naam'] ?>'>
    <label for="gebruikersnaam">Gebruikersnaam</label>
    <input type='text' name='gebruikersnaam' value='<?= $medewerker['gebruikersnaam'] ?>'>
    <label for="adres">Adres</label>
    <input type='text' name='adres' value='<?= $medewerker['adres'] ?>'>
    <label for="rol_id">Rol</label>
    <select name='rol_id'>
        <?php foreach ($rollen as $rol): ?>
            <option value="<?= $rol['id'] ?>" <?= $rol['id'] == $medewerker['rol_id'] ? 'selected' : '' ?>><?= $rol['naam'] ?></option>
        <?php endforeach; ?>
    </select>
    <input type='submit' value='Update'>
</form>