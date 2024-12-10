<?php

use App\Utility\Functions;

if (!Functions::checkPermissions(['beheerder'])) {
    header("Location: ?page=home");
}