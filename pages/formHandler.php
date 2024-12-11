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

    case 'updateOnderhoudstaak':
        if (Functions::checkPermissions(['beheerder'])) {
            $formHandler->updateOnderhoudstaak();
        } else {
            header("Location: ?page=home");
        }

        break;

    case 'addOnderhoudstaak':
        if (Functions::checkPermissions(['beheerder'])) {
            $formHandler->addOnderhoudstaak();
        } else {
            header("Location: ?page=home");
        }

        break;

    case 'editOnderhoud':
        if (Functions::checkPermissions(['beheerder', 'manager', 'monteur'])) {
            $formHandler->editOnderhoud();
        } else {
            header("Location: ?page=home");
        }

        break;

    case 'addOnderhoud':
        if (Functions::checkPermissions(['beheerder', 'manager', 'monteur'])) {
            $formHandler->addOnderhoud();
        } else {
            header("Location: ?page=home");
        }

        break;

    case 'addComment':
        if (Functions::checkPermissions(['beheerder', 'manager', 'monteur'])) {
            $formHandler->addComment();
        } else {
            header("Location: ?page=home");
        }

        break;
    
    case 'deleteComment':
        if (Functions::checkPermissions(['beheerder', 'manager', 'monteur'])) {
            $formHandler->deleteComment();
        } else {
            header("Location: ?page=home");
        }

        break;

    default:
        header("Location: ?page=home");
        break;
}