<?php

use App\Utility\Functions;


if ($_GET['page'] === 'attracties.index') {
    header("Location: ?page=home");
}

if (!Functions::checkPermissions(['beheerder'])) {
    header("Location: ?page=home");
}