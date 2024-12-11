<?php

use App\Utility\Database;
use App\Utility\Functions;
use App\Utility\DataProcessor;
use App\Utility\Session;



if (!isset($_GET['id'])) {
    Functions::jsRedirect(url: '?page=onderhoud.overzicht');
}


if (!Functions::checkPermissions(permissions: ['manager', 'beheerder'])) {
    Functions::drawSidebar(options: [
        ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht']
    ]);
} else {
    Functions::drawSidebar(options: [
        ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht'],
        ['label' => 'Add', 'page' => 'onderhoud.add']
    ]);
}


Functions::displayError(message: Session::get('onderhoud.error'));
Session::delete('onderhoud.error');

Functions::displaySuccess(message: Session::get('onderhoud.success'));
Session::delete('onderhoud.success');

$database = Database::getInstance();

$onderhoud_id = DataProcessor::sanitizeData(data: $_GET['id']);

$query_onderhoud = $database->query(query: "
SELECT ot.id, ot.beschrijving, ot.start_datum, ot.duur_dagen, ot.herhaling_dagen, ot.naam,
       p.naam AS medewerker, p.id AS medewerker_id,
       s.naam AS status,
       r.naam AS rol,
       a.naam AS attractie, a.id AS attractie_id, a.specificaties, a.foto, a.locatie,
       o.onderhoudstaak_id,
       CONCAT(p.naam, ' : ',  r.naam) AS naam_rol
FROM onderhoud o
INNER JOIN onderhoudstaak ot ON ot.id = o.onderhoudstaak_id
INNER JOIN personeel p ON p.id = o.personeel_id
INNER JOIN status s ON s.id = o.status_id
INNER JOIN attractie a ON a.id = ot.attractie_id
INNER JOIN rol r ON r.id = p.rol_id
WHERE o.id = :id;
", params: [
    'id' => $onderhoud_id
]);

$onderhoud = $query_onderhoud->fetch(PDO::FETCH_ASSOC);

if (empty($onderhoud)) {
    Functions::jsRedirect(url: '?page=onderhoud.overzicht');
}

$onderhoud['specificaties'] = "<pre>" . $onderhoud['specificaties'] . "</pre>";
$onderhoud['beschrijving'] = "<pre>" . $onderhoud['beschrijving'] . "</pre>";
$onderhoud['foto'] = $onderhoud['foto'] ? '<img src="' . $onderhoud['foto'] . '" />' : '<img src="websrc/images/no_image.jpg">';
if (Functions::checkPermissions(permissions: ['manager', 'beheerder'])) {
    $onderhoud['naam_rol'] = "<a href='?page=medewerkers.view&id=" . $onderhoud['medewerker_id'] . "'>" . $onderhoud['naam_rol'] . "</a>";
    $onderhoud['attractie'] = "<a href='?page=attracties.view&id=" . $onderhoud['attractie_id'] . "'>". $onderhoud['attractie'] ."</a>";
    $onderhoud['acties'] = "                
        <a href='?page=onderhoud.edit&type=personeel&id=" . $onderhoud['id'] . "'>Wijzig medewerker</a>
        <a href='?page=onderhoud.edit&type=status&id=" . $onderhoud['id'] . "'>Wijzig status</a>
        <a href='?page=onderhoud.delete&id=" . $onderhoud['id'] . "'>Delete</a>
    ";
} else {
    $onderhoud['acties'] = "
        <a href='?page=onderhoud.edit&type=status&id=" . $onderhoud['id'] . "'>Wijzig status</a>
    ";
}   

echo "<section>";
Functions::drawTable(
    headers: ['foto', 'attractie', 'status', 'naam', 'beschrijving', 'naam_rol', 'locatie', 'specificaties', 'start_datum', 'duur_dagen', 'herhaling_dagen', 'acties'],
    rows: [$onderhoud],
    direction: 'vertical'
);
// echo "</section>";


// Query to fetch comments related to the specified onderhoudstaak_id
$query_comments = $database->query(query: "
SELECT oto.id, oto.opmerking, oto.created_at,
        p.naam AS personeel_naam, p.id AS personeel_id,
        r.naam AS rol_naam,
        p.id AS medewerker_id,
        CONCAT(p.naam, ' : ',  r.naam) AS medewerker
    FROM onderhoudstaak_opmerking oto
    INNER JOIN personeel p ON p.id = oto.personeel_id
    INNER JOIN rol r ON r.id = p.rol_id
    WHERE onderhoudstaak_id = :onderhoudstaak_id
    ORDER BY created_at DESC;
", params: [
    'onderhoudstaak_id' => $onderhoud['onderhoudstaak_id']
]);

// Fetch all comments as an associative array
$comments = $query_comments->fetchAll(PDO::FETCH_ASSOC);

// Ensure $comments is an array even if no results are returned
if (empty($comments)) {
    $comments = [];
}

// Add links for manager and beheerder roles to the 'medewerker' field
if (!empty($comments)) {
    $comments = array_map(function($comment) {
        if (Functions::checkPermissions(permissions: ['manager', 'beheerder'])) {
            $comment['medewerker'] = "<a href='?page=medewerkers.view&id=" . $comment['medewerker_id'] . "'>" . $comment['medewerker'] . "</a>";
        }
        return $comment;
    }, $comments);
}

?>

<div class="comments">
    <div class="comment-form">
        <form action='?page=formHandler' method='post' enctype="multipart/form-data">
            <input type='hidden' name='action' value='addComment'>
            <input type='hidden' name='onderhoudstaak_id' value='<?= $onderhoud['onderhoudstaak_id'] ?>'>
            <textarea name="opmerking" minlength="0" maxlength="256"></textarea>
            <input type='submit' value='Add'>
        </form>
    </div>
    <div class="comment-section">
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <div class="comment-header">
                <p class="comment-personeel"><?= $comment['medewerker'] ?></p>
            <?php
                if (Functions::checkPermissions(['beheerder', 'manager']) || $comment['personeel_id'] == Session::get('user')['id']) {
                    echo "
                    <form action='?page=formHandler' method='post' enctype='multipart/form-data'>
                        <input type='hidden' name='action' value='deleteComment'>
                        <input type='hidden' name='onderhoudstaak_opmerking_id' value='" . $comment['id'] . "'>
                        <input type='hidden' name='onderhoudstaak_id' value='" . $onderhoud['onderhoudstaak_id'] . "'>
                        <label for='x-". $comment['id'] ."' class='delete-button'><i class='bi bi-trash'></i></label>
                        <input type='submit' id='x-". $comment['id'] ."' value=''>
                    </form>
                    ";
                }
            ?>
            </div>
            <pre class="comment-text"><?= $comment['opmerking'] ?></pre>
            <p class="comment-date"><?= $comment['created_at'] ?></p>
        </div>
    <?php endforeach; ?>
    </div>
</div>
</section>