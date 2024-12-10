<?php

use App\Utility\FormHandler;
use App\Utility\Functions;



$formHandler = new FormHandler();

if (!isset($_POST['action'])) {
    header("Location: ?page=home");
}
switch ($_POST['action']) {
    case 'login':
        $formHandler->login();
        break;
        
    case 'logout':
        $formHandler->logout();
        break;

    case 'updateMedewerker':
        if (Functions::checkPermissions(['manager'])) {
            $formHandler->medewerkerUpdate();
        } else {
            header("Location: ?page=home");
        }

        break;

    default:
        header("Location: ?page=home");
        break;
}