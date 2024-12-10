<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;



Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'attracties.overzicht'],
    ['label' => 'Add', 'page' => 'attracties.add']
]);


$database = Database::getInstance();

$query_typen = $database->query(query: "
SELECT * 
    FROM attractie_type;
");
$typen = $query_typen->fetchAll(PDO::FETCH_ASSOC);

?>

<form action='?page=formHandler' method='post' enctype="multipart/form-data">
    <input type='hidden' name='action' value='addAttractie'>
    <label for="foto">Foto</label>
    <label for="foto" class="foto-display" style="background-image: url('websrc/images/no_image.jpg');"></label>
    <input type="file" id="foto" name="foto" accept="image/*">
    <label for="naam">Naam</label>
    <input type='text' name='naam' value='' minlength="0" maxlength="64">
    <label for="locatie">Locatie</label>
    <input type='text' name='locatie' value='' minlength="0" maxlength="128">
    <label for="specificaties">Specificaties</label>
    <textarea name="specificaties" minlength="0" maxlength="256"></textarea>
    <label for="type_id">type</label>
    <select name='type_id'>
        <?php foreach ($typen as $type): ?>
            <option value="<?= $type['id'] ?>"><?= $type['naam'] ?></option>
        <?php endforeach; ?>
    </select>
    <input type='submit' value='Voeg attractie toe'>
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