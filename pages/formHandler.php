<?php

use App\Utility\FormHandler;
use App\Utility\Functions;

// Instantiate the FormHandler class
$formHandler = new FormHandler();

// Redirect to home if no action is specified
if (!isset($_POST['action'])) {
    header("Location: ?page=home");
}

// Handle different form actions based on the 'action' parameter in POST
switch ($_POST['action']) {
    case 'login':
        // Handle login action
        $formHandler->login();
        break;
        
    case 'logout':
        // Handle logout action
        $formHandler->logout();
        break;

    case 'updateMedewerker':
        // Update medewerker if user has appropriate permissions
        if (Functions::checkPermissions(['manager', 'beheerder'])) {
            $formHandler->updateMedewerker();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    case 'addMedewerker':
        // Add medewerker if user has appropriate permissions
        if (Functions::checkPermissions(['manager', 'beheerder'])) {
            $formHandler->addMedewerker();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    case 'updateAttractie':
        // Update attractie if user has appropriate permissions
        if (Functions::checkPermissions(['beheerder'])) {
            $formHandler->updateAttractie();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    case 'addAttractie':
        // Add attractie if user has appropriate permissions
        if (Functions::checkPermissions(['beheerder'])) {
            $formHandler->addAttractie();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    case 'updateOnderhoudstaak':
        // Update onderhoudstaak if user has appropriate permissions
        if (Functions::checkPermissions(['beheerder'])) {
            $formHandler->updateOnderhoudstaak();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    case 'addOnderhoudstaak':
        // Add onderhoudstaak if user has appropriate permissions
        if (Functions::checkPermissions(['beheerder'])) {
            $formHandler->addOnderhoudstaak();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    case 'editOnderhoud':
        // Edit onderhoud if user has appropriate permissions
        if (Functions::checkPermissions(['beheerder', 'manager', 'monteur'])) {
            $formHandler->editOnderhoud();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    case 'addOnderhoud':
        // Add onderhoud if user has appropriate permissions
        if (Functions::checkPermissions(['beheerder', 'manager', 'monteur'])) {
            $formHandler->addOnderhoud();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    case 'addComment':
        // Add comment if user has appropriate permissions
        if (Functions::checkPermissions(['beheerder', 'manager', 'monteur'])) {
            $formHandler->addComment();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;
    
    case 'deleteComment':
        // Delete comment if user has appropriate permissions
        if (Functions::checkPermissions(['beheerder', 'manager', 'monteur'])) {
            $formHandler->deleteComment();
        } else {
            // Redirect to home if permissions are insufficient
            header("Location: ?page=home");
        }
        break;

    default:
        // Redirect to home for any unspecified actions
        header("Location: ?page=home");
        break;
}
