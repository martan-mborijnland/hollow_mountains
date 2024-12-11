<?php

use App\Utility\Database;
use App\Utility\DataProcessor;
use App\Utility\Functions;
use App\Utility\Session;


$filters = [
    'datum' => ['sd', 'ed'], 
    'status' => ['s'],
    'today' => ['today']
];

// Display errors and success messages
Functions::displayError(message: Session::get('onderhoud.error'));
Session::delete('onderhoud.error');

Functions::displaySuccess(message: Session::get('onderhoud.success'));
Session::delete('onderhoud.success');

// Ensure the user has the 'monteur' role
if (!Functions::checkPermissions(permissions: ['manager', 'beheerder'])) {
    // Draw the sidebar with the 'Overzicht' option
    Functions::drawSidebar(options: [
        ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht']
    ]);
} else {
    // Draw the sidebar with the 'Overzicht' and 'Add' options
    Functions::drawSidebar(options: [
        ['label' => 'Overzicht', 'page' => 'onderhoud.overzicht'],
        ['label' => 'Add', 'page' => 'onderhoud.add']
    ]);
}

// Create the database connection
$database = Database::getInstance();

// Build the WHERE clause based on filters
$params = [];
$query = "
SELECT o.id, 
        ot.beschrijving, ot.start_datum, ot.duur_dagen, ot.duur_dagen, ot.herhaling_dagen,
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
";

$whereClauses = [];
if (!empty($_GET['sd']) && !empty($_GET['ed'])) {
    // Filter for start and end date
    $whereClauses[] = "ot.start_datum >= :start_datum AND ot.start_datum + INTERVAL ot.duur_dagen DAY <= :end_datum";
    $params['start_datum'] = $_GET['sd'];
    $params['end_datum'] = $_GET['ed'];
}
if (!empty($_GET['s'])) {
    // Filter for status
    $whereClauses[] = "s.naam = :status";
    $params['status'] = $_GET['s'];
}
if (Functions::checkPermissions(permissions: ['monteur'])) {
    // Filter for current user
    $whereClauses[] = "p.id = :medewerker_id";
    $params['medewerker_id'] = Session::get('user')['id'];
}
if (!empty($whereClauses)) {
    // Add the WHERE clause to the query
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$query .= " ORDER BY FIELD(s.naam, 'Niet Gestart', 'In Behandeling', 'Voltooid')";


// Execute the query
$query_onderhoud = $database->query($query, $params);
// Fetch all results as an associative array
$onderhoud = $query_onderhoud->fetchAll(PDO::FETCH_ASSOC);

// Set the table headers
$headers = ['status', 'attractie', 'medewerker', 'rol', 'start_datum', 'eind_datum', 'acties'];

// If there are results, add extra columns for the manager and beheerder roles
if (!empty($onderhoud)) {
    $onderhoud = array_map(function($onderhoud_) {
        if (Functions::checkPermissions(permissions: ['manager', 'beheerder'])) {
            // Add extra columns for the manager and beheerder roles
            $onderhoud_['medewerker'] = "<a href='?page=medewerkers.view&id=" . $onderhoud_['medewerker_id'] . "'>" . $onderhoud_['medewerker'] . "</a>";
            $onderhoud_['attractie'] = "<a href='?page=attracties.view&id=" . $onderhoud_['attractie_id'] . "'>". $onderhoud_['attractie'] ."</a>";
            $onderhoud_['acties'] = "                
                <a href='?page=onderhoud.view&id=" . $onderhoud_['id'] . "'>View</a>
                <a href='?page=onderhoud.edit&type=personeel&id=" . $onderhoud_['id'] . "'>Wijzig medewerker</a>
                <a href='?page=onderhoud.edit&type=status&id=" . $onderhoud_['id'] . "'>Wijzig status</a>
                <a href='?page=onderhoud.delete&id=" . $onderhoud_['id'] . "'>Delete</a>
            ";
        } else {
            // Add extra columns for the monteur role
            $onderhoud_['acties'] = "
                <a href='?page=onderhoud.view&id=" . $onderhoud_['id'] . "'>View</a>
                <a href='?page=onderhoud.edit&type=status&id=" . $onderhoud_['id'] . "'>Wijzig status</a>
            ";
        }  
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