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
        if (Functions::checkPermissions(['manager', 'beheerder'])) {
            $formHandler->updateMedewerker();
        } else {
            header("Location: ?page=home");
        }

        break;

    case 'addMedewerker':
        if (Functions::checkPermissions(['manager', 'beheerder'])) {
            $formHandler->addMedewerker();
        } else {
            header("Location: ?page=home");
        }

        break;

    case 'updateAttractie':
        if (Functions::checkPermissions(['beheerder'])) {
            $formHandler->updateAttractie();
        } else {
            header("Location: ?page=home");
        }

        break;

    case 'addAttractie':
        if (Functions::checkPermissions(['beheerder'])) {
            $formHandler->addAttractie();
        } else {
            header("Location: ?page=home");
        }

        break;

    default:
        header("Location: ?page=home");
        break;
}