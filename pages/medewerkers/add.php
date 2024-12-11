<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



// Draw the sidebar with navigation options for 'Overzicht' and 'Add'
Functions::drawSidebar(options: [ 
    ['label' => 'Overzicht', 'page' => 'medewerkers.overzicht'],
    ['label' => 'Add', 'page' => 'medewerkers.add']
]);

// Get the database instance
$database = Database::getInstance();

// Execute a query to select all rows from the rol table
$query_rollen = $database->query(query: "
SELECT * 
    FROM rol;
");

// Fetch all results as an associative array
$rollen = $query_rollen->fetchAll(PDO::FETCH_ASSOC);

?>

<form action='?page=formHandler' method='post' enctype="multipart/form-data">
    <input type='hidden' name='action' value='addMedewerker'>
    <input type='hidden' name='id' value=''>
    <label for="naam">Naam</label>
    <input type='text' name='naam' value=''>
    <label for="gebruikersnaam">Gebruikersnaam</label>
    <input type='text' name='gebruikersnaam' value=''>
    <label for="password">Wachtwoord</label>
    <input type='password' name='wachtwoord' value=''>
    <label for="adres">Adres</label>
    <input type='text' name='adres' value=''>
    <label for="rol_id">Rol</label>
    <select name='rol_id'>
        <?php foreach ($rollen as $rol): ?>
            <option value="<?= $rol['id'] ?>"><?= $rol['naam'] ?></option>
        <?php endforeach; ?>
    </select>
    <input type='submit' value='Voeg medewerker toe'>
</form>