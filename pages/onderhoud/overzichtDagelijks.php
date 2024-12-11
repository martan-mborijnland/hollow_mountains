<?php

use App\Utility\Database;
use App\Utility\DataProcessor;
use App\Utility\Functions;
use App\Utility\Session;

// Display any errors or success messages
Functions::displayError(message: Session::get('onderhoud.error'));
Session::delete('onderhoud.error');

Functions::displaySuccess(message: Session::get('onderhoud.success'));
Session::delete('onderhoud.success');

// Ensure the user has the 'monteur' role
if (!Functions::checkPermissions(permissions: ['monteur'])) {
    die('Access denied');
}

// Draw the sidebar with the 'Overzicht' option
Functions::drawSidebar(options: [
    ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht']
]);

// Get the database instance
$database = Database::getInstance();

// Get today's date
$today = date('Y-m-d');

// Set up the query parameters
$params = [];

// Massive query to select all rows from the onderhoud table
$query = "
SELECT o.id, 
        ot.beschrijving, ot.start_datum, ot.duur_dagen, ot.herhaling_dagen,
        p.naam AS medewerker, p.id AS medewerker_id,
        s.naam AS status,
        r.naam AS rol,
        a.naam AS attractie, a.id AS attractie_id
FROM onderhoud o
INNER JOIN onderhoudstaak ot ON ot.id = o.onderhoudstaak_id
INNER JOIN personeel p ON p.id = o.personeel_id
INNER JOIN status s ON s.id = o.status_id
INNER JOIN attractie a ON a.id = ot.attractie_id
INNER JOIN rol r ON r.id = p.rol_id
WHERE p.id = :medewerker_id
AND (ot.start_datum = :today 
    OR (ot.herhaling_dagen IS NOT NULL AND (DATEDIFF(:today, ot.start_datum) % ot.herhaling_dagen = 0)))
ORDER BY FIELD(s.naam, 'Niet Gestart', 'In Behandeling', 'Voltooid')
";

// Set the query parameters
$params['medewerker_id'] = Session::get('user')['id'];
$params['today'] = $today;

// Execute the query
$query_onderhoud = $database->query($query, $params);
// Fetch all results as an associative array
$onderhoud = $query_onderhoud->fetchAll(PDO::FETCH_ASSOC);

// Set the table headers
$headers = ['status', 'attractie', 'beschrijving', 'start_datum', 'eind_datum', 'acties'];
if (!empty($onderhoud)) {
    // Map over the results and add the 'eind_datum' and 'acties' columns
    $onderhoud = array_map(function($onderhoud_) {
        $onderhoud_['eind_datum'] = date('Y-m-d', strtotime($onderhoud_['start_datum'] . ' + ' . $onderhoud_['duur_dagen'] . ' days'));
        $onderhoud_['acties'] = "
            <a href='?page=onderhoud.view&id=" . $onderhoud_['id'] . "'>View</a>
            <a href='?page=onderhoud.edit&type=status&id=" . $onderhoud_['id'] . "'>Wijzig status</a>
        ";
        return $onderhoud_;
    }, $onderhoud);
}
?>

<section>
    <div class="form-wrapper">
        <form method="get" action="">
            <input type="hidden" name="page" value="onderhoud.overzicht">
            <div>
                <div>
                    <label for="sd">Start Datum:</label>
                    <input type="date" id="sd" name="sd" value="<?= DataProcessor::sanitizeData($_GET['sd'] ?? '') ?>">
                </div>
                <div>
                    <label for="ed">Eind Datum:</label>
                    <input type="date" id="ed" name="ed" value="<?= DataProcessor::sanitizeData($_GET['ed'] ?? '') ?>">
                </div>
                <div>
                    <label for="s">Status:</label>
                    <select id="s" name="s">
                        <option value="">--Selecteer Status--</option>
                        <option value="Niet Gestart" <?= isset($_GET['s']) && $_GET['s'] == 'Niet Gestart' ? 'selected' : '' ?>>Niet Gestart</option>
                        <option value="In Behandeling" <?= isset($_GET['s']) && $_GET['s'] == 'In Behandeling' ? 'selected' : '' ?>>In Behandeling</option>
                        <option value="Voltooid" <?= isset($_GET['s']) && $_GET['s'] == 'Voltooid' ? 'selected' : '' ?>>Voltooid</option>
                    </select>
                </div>
            </div>            
            <div class="button-wrapper">
                <button type="submit">Filter</button>
                <a href="?page=onderhoud.overzicht" class="button">Clear Filter</a>
            </div>
        </form>
    </div>
    <?php 
        Functions::drawTable($headers, $onderhoud);
    ?>
</section>
