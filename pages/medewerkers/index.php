<?php

use App\Utility\Functions;

if (!Functions::checkPermissions(['manager'])) {
    header("Location: ?page=home");
}