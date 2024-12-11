<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;

// Redirect if 'id' is not set in GET parameters
if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=attracties.overzicht');
}

// Draw the sidebar with navigation options
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'attracties.overzicht'],
    ['label' => 'Add', 'page' => 'attracties.add']
]);

// Get the database instance
$database = Database::getInstance();

// Sanitize the 'id' from GET parameters
$attractie_id = DataProcessor::sanitizeData(data: $_GET['id']);

// Query to fetch attractie details
$query_attractie = $database->query(query: "
SELECT attractie.id, attractie.naam, attractie.locatie, attractie.foto, attractie.specificaties,
        attractie_type.naam AS type_naam, attractie_type.id AS type_id
    FROM attractie
    INNER JOIN attractie_type ON attractie_type.id = attractie.type_id
    WHERE attractie.id = :id;
", params: [
    'id' => $attractie_id
]);

$attractie = $query_attractie->fetch(PDO::FETCH_ASSOC);

// Query to fetch all attractie types
$query_typen = $database->query(query: "
SELECT * 
    FROM attractie_type;
");
$typen = $query_typen->fetchAll(PDO::FETCH_ASSOC);

// Redirect if no attractie found
if (empty($attractie)) {
    Functions::jsRedirect(url: '?page=attracties.overzicht');
}

?>

<form action='?page=formHandler' method='post' enctype="multipart/form-data">
    <input type='hidden' name='action' value='updateAttractie'>
    <input type='hidden' name='id' value='<?= $attractie['id'] ?>'>
    <label for="foto">Foto</label>
    <label for="foto" class="foto-display" style="background-image: url('<?= $attractie['foto'] ? $attractie['foto'] : 'websrc/images/no_image.jpg' ?>');"></label>
    <input type="file" id="foto" name="foto" accept="image/*">
    <label for="naam">Naam</label>
    <input type='text' name='naam' value='<?= $attractie['naam'] ?>' minlength="0" maxlength="64">
    <label for="locatie">Locatie</label>
    <input type='text' name='locatie' value='<?= $attractie['locatie'] ?>' minlength="0" maxlength="128">
    <label for="specificaties">Specificaties</label>
    <textarea name="specificaties" minlength="0" maxlength="256"><?= $attractie['specificaties'] ?></textarea>
    <label for="type_id">type</label>
    <select name='type_id'>
        <?php foreach ($typen as $type): ?>
            <option value="<?= $type['id'] ?>" <?= $type['id'] == $attractie['type_id'] ? 'selected' : '' ?>><?= $type['naam'] ?></option>
        <?php endforeach; ?>
    </select>
    <input type='submit' value='Update'>
</form>
<script>
    document.getElementById('foto').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.foto-display').style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);
        }
    });
</script>