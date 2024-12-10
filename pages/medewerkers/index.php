<?php

use App\Utility\Functions;



if ($_GET['page'] === 'medewerkers.index') {
    header("Location: ?page=home");
}

if (!Functions::checkPermissions(['manager', 'beheerder'])) {
    header("Location: ?page=home");
}