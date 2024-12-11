<?php declare(strict_types=1);

namespace App\Utility;

use PDO;
use App\Utility\Database;
use App\Utility\DataProcessor;
use App\Utility\Session;



/**
 * FormHandler
 * 
 * @author Martan van Verseveld
 */
class FormHandler
{
    
    /**
     * @var Database The database instance used for querying.
     */
    private Database $database;

    /**
     * Constructor initializes the database instance.
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * Handles user login by validating credentials and setting session data.
     * 
     * @return void
     */
    public function login(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            $query = "
                SELECT personeel.id, personeel.naam, personeel.gebruikersnaam, personeel.wachtwoord, rol.naam AS rol
                    FROM personeel
                    INNER JOIN rol ON rol.id = personeel.rol_id
                    WHERE gebruikersnaam = :username 
                    LIMIT 1;
            ";
            $params = ['username' => $sanatizedPOST['username']];

            $result = $this->database->query($query, $params);

            if ($result->rowCount() <= 0) {
                Session::set('error', 'Username and password do not match!');
                header("Location: ?page=login");
                exit();
            }

            $row = $result->fetch(PDO::FETCH_ASSOC);
            if (!DataProcessor::checkPassword($sanatizedPOST['password'], $row['wachtwoord'])) {
                Session::set('error', 'Username and password do not match!');
                header("Location: ?page=login");
                exit();
            }

            unset($row['wachtwoord']);
            Session::set('user', $row);
            Session::set('loggedIn', true);

            header("Location: ?page=login");
            exit();
        } catch (\Exception $e) {
            Session::set('login.error', $e->getMessage());
            header("Location: ?page=login");
        }
    }

    /**
     * Handles user logout by destroying the session.
     * 
     * @return void
     */
    public function logout(): void
    {
        Session::destroy();
        header("Location: ?page=login");
    }

    /**
     * Handles medewerker update by validating and updating the database.
     * 
     * @return void
     */
    public function updateMedewerker(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);
            
            if (!DataProcessor::validateFields($sanatizedPOST, ['id', 'naam', 'gebruikersnaam', 'rol_id', 'adres'])) {
                Session::set('medewerkers.error', 'Required fields not found.');
                header("Location: ?page=medewerkers.view&id=" . $sanatizedPOST['id']);
                exit();
            }

            $query = "
                UPDATE personeel
                    SET naam = :naam, gebruikersnaam = :gebruikersnaam, rol_id = :rol_id, adres = :adres
                    WHERE id = :id;
            ";
            $params = [
                'naam' => $sanatizedPOST['naam'],
                'gebruikersnaam' => $sanatizedPOST['gebruikersnaam'],
                'rol_id' => $sanatizedPOST['rol_id'],
                'adres' => $sanatizedPOST['adres'],
                'id' => $sanatizedPOST['id']
            ];

            $result = $this->database->query($query, $params);

            Session::set('medewerkers.success', "De medewerker is gewijzigd.");
            header("Location: ?page=medewerkers.view&id=" . $sanatizedPOST['id']);
        } catch (\Exception $e) {
            Session::set('medewerkers.error', $e->getMessage());
            header("Location: ?page=medewerkers.view&id=" . $sanatizedPOST['id']);
        }
    }
    /**
     * Handles medewerker add by validating and adding the medewerker to the database.
     * 
     * @return void
     */
    public function addMedewerker(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            if (!DataProcessor::validateFields($sanatizedPOST, ['naam', 'gebruikersnaam', 'wachtwoord', 'rol_id', 'adres'])) {
                Session::set('medewerkers.error', 'Required fields not found.');
                header("Location: ?page=medewerkers.overzicht");
                exit();
            }

            $query = "    
                INSERT INTO personeel (naam, gebruikersnaam, wachtwoord, rol_id, adres)
                    VALUES (:naam, :gebruikersnaam, :wachtwoord, :rol_id, :adres);
            ";
            $params = [
                'naam' => $sanatizedPOST['naam'],
                'gebruikersnaam' => $sanatizedPOST['gebruikersnaam'],
                'wachtwoord' => DataProcessor::hashPassword($sanatizedPOST['wachtwoord']),
                'rol_id' => $sanatizedPOST['rol_id'],
                'adres' => $sanatizedPOST['adres']
            ];

            $result = $this->database->query($query, $params);

            Session::set('medewerkers.success', "De medewerker is toegevoegd.");
            header("Location: ?page=medewerkers.overzicht");
        } catch (\Exception $e) {
            Session::set('medewerkers.error', $e->getMessage());
            header("Location: ?page=medewerkers.overzicht");
        }
    }

    /**
     * Handles attractie update by validating and updating the attractie in the database.
     * 
     * @return void
     */
    public function updateAttractie(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            if (!DataProcessor::validateFields($sanatizedPOST, ['id', 'naam', 'locatie', 'type_id', 'specificaties'])) {
                Session::set('attracties.error', 'Required fields not found.');
                header("Location: ?page=attracties.overzicht");
                exit();
            }
            
    
            $previousImage = $this->database->query("
                SELECT foto
                FROM attractie
                WHERE id = :id;
            ", [
                'id' => $sanatizedPOST['id']
            ])->fetch(PDO::FETCH_ASSOC);

            if (isset($_FILES['foto']) && $_FILES['foto']['size'] > 0) {
                if ($_FILES['foto']['error'] != 0) {
                    Session::set('attracties.error', 'Invalid image file.');
                    header("Location: ?page=attracties.overzicht");
                    exit();
                }
    
                if (!in_array($_FILES['foto']['type'], ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
                    Session::set('attracties.error', 'Invalid image file.');
                    header("Location: ?page=attracties.overzicht");
                    exit();
                }
    
                if (!empty($previousImage['foto']) && file_exists($previousImage['foto'])) {
                    unlink($previousImage['foto']);
                }
    
                $filename = uniqid() . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $filepath = 'websrc/images/attracties/' . $filename;
                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $filepath)) {
                    Session::set('attracties.error', 'Failed to upload image.');
                    header("Location: ?page=attracties.overzicht");
                    exit();
                }
            } else {
                $filepath = $previousImage['foto'];
            }

            $query = "
                UPDATE attractie
                    SET naam = :naam, locatie = :locatie, type_id = :type_id, specificaties = :specificaties, foto = :foto
                    WHERE id = :id;
            ";
            $params = [
                'naam' => $sanatizedPOST['naam'],
                'locatie' => $sanatizedPOST['locatie'],
                'type_id' => $sanatizedPOST['type_id'],
                'specificaties' => $sanatizedPOST['specificaties'],
                'foto' => $filepath,
                'id' => $sanatizedPOST['id']
            ];

            $result = $this->database->query($query, $params);

            Session::set('attracties.success', "De attractie is gewijzigd.");
            header("Location: ?page=attracties.view&id=" . $sanatizedPOST['id']);
        } catch (\Exception $e) {
            Session::set('attracties.error', $e->getMessage());
            header("Location: ?page=attracties.view&id=" . $sanatizedPOST['id']);
        }
    }

    /**
     * Adds a new attractie to the database after validating input data and handling image upload.
     * 
     * This method sanitizes and validates form input data for required fields: 'naam', 'locatie', 
     * 'type_id', and 'specificaties'. It then checks the uploaded image for errors and valid file 
     * types. If successful, the image is uploaded and a new record is inserted into the 'attractie' 
     * table. On success, a success message is set in the session and the user is redirected to the 
     * attracties overview page. On failure, an error message is set and the user is redirected to 
     * the same page.
     * 
     * @return void
     */
    public function addAttractie(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            if (!DataProcessor::validateFields($sanatizedPOST, ['naam', 'locatie', 'type_id', 'specificaties'])) {
                Session::set('attracties.error', 'Required fields not found.');
                header("Location: ?page=attracties.overzicht");
                exit();
            }

            if (!isset($_FILES['foto']) || $_FILES['foto']['error'] != 0) {
                Session::set('attracties.error', 'Invalid image file.');
                header("Location: ?page=attracties.overzicht");
                exit();
            }

            if (!in_array($_FILES['foto']['type'], ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'])) {
                Session::set('attracties.error', 'Invalid image file.');
                header("Location: ?page=attracties.overzicht");
                exit();
            }

            $filename = uniqid() . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], 'websrc/images/attracties/' . $filename)) {
                Session::set('attracties.error', 'Failed to upload image.');
                header("Location: ?page=attracties.overzicht");
                exit();
            }

            $query = "
                INSERT INTO attractie (naam, locatie, type_id, specificaties, foto)
                    VALUES (:naam, :locatie, :type_id, :specificaties, :foto);
            ";
            $params = [
                'naam' => $sanatizedPOST['naam'],
                'locatie' => $sanatizedPOST['locatie'],
                'type_id' => $sanatizedPOST['type_id'],
                'specificaties' => $sanatizedPOST['specificaties'],
                'foto' => 'websrc/images/attracties/' . $filename
            ];

            $result = $this->database->query($query, $params);

            Session::set('attracties.success', "De attractie is toegevoegd.");
            header("Location: ?page=attracties.overzicht");
        } catch (\Exception $e) {
            Session::set('attracties.error', $e->getMessage());
            header("Location: ?page=attracties.overzicht");
        }
    }


    /**
     * Updates an existing onderhoudstaak in the database after validating input data.
     * 
     * This method sanitizes and validates form input data for required fields: 'id', 'naam', 'beschrijving', 
     * 'attractie_id', 'start_datum', 'duur_dagen', and 'herhaling_dagen'. It then updates a record in the 'onderhoudstaak' 
     * table. On success, a success message is set in the session and the user is redirected to the 
     * onderhoudstaak view page. On failure, an error message is set and the user is redirected to the same page.
     * 
     * @return void
     */
    public function updateOnderhoudstaak(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            if (!DataProcessor::validateFields($sanatizedPOST, ['id', 'naam', 'beschrijving', 'attractie_id', 'start_datum', 'duur_dagen', 'herhaling_dagen'])) {
                Session::set('onderhoud.error', 'Required fields not found.');
                header("Location: ?page=onderhoud.overzicht");
                exit();
            }

            $query = "
                UPDATE onderhoudstaak
                    SET naam = :naam, beschrijving = :beschrijving, attractie_id = :attractie_id, start_datum = :start_datum, duur_dagen = :duur_dagen, herhaling_dagen = :herhaling_dagen
                    WHERE id = :id;
            ";
            $params = [
                'naam' => $sanatizedPOST['naam'],
                'beschrijving' => $sanatizedPOST['beschrijving'],
                'attractie_id' => $sanatizedPOST['attractie_id'],
                'start_datum' => $sanatizedPOST['start_datum'],
                'duur_dagen' => $sanatizedPOST['duur_dagen'],
                'herhaling_dagen' => $sanatizedPOST['herhaling_dagen'],
                'id' => $sanatizedPOST['id']
            ];

            $result = $this->database->query($query, $params);

            Session::set('onderhoudstaak.success', "De onderhoudstaak is gewijzigd.");
            header("Location: ?page=onderhoudstaak.view&id=" . $sanatizedPOST['id']);
        } catch (\Exception $e) {
            Session::set('onderhoudstaak.error', $e->getMessage());
            header("Location: ?page=onderhoudstaak.view&id=" . $sanatizedPOST['id']);
        }
    }

    /**
     * Adds a new onderhoudstaak to the database after validating input data.
     * 
     * This method sanitizes and validates form input data for required fields: 'naam', 'beschrijving', 'attractie_id', 'start_datum', 'duur_dagen', and 'herhaling_dagen'. It then inserts a new record into the 'onderhoudstaak' table. On success, a success message is set in the session and the user is redirected to the onderhoudstaak view page. On failure, an error message is set and the user is redirected to the same page.
     * 
     * @return void
     */
    public function addOnderhoudstaak(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            if (!DataProcessor::validateFields($sanatizedPOST, ['naam', 'beschrijving', 'attractie_id', 'start_datum', 'duur_dagen', 'herhaling_dagen'])) {
                Session::set('onderhoudstaak.error', 'Required fields not found.');
                header("Location: ?page=onderhoudstaak.overzicht");
                exit();
            }

            $query = "
                INSERT INTO onderhoudstaak (naam, beschrijving, attractie_id, start_datum, duur_dagen, herhaling_dagen)
                    VALUES (:naam, :beschrijving, :attractie_id, :start_datum, :duur_dagen, :herhaling_dagen);
            ";
            $params = [
                'naam' => $sanatizedPOST['naam'],
                'beschrijving' => $sanatizedPOST['beschrijving'],
                'attractie_id' => $sanatizedPOST['attractie_id'],
                'start_datum' => $sanatizedPOST['start_datum'],
                'duur_dagen' => $sanatizedPOST['duur_dagen'],
                'herhaling_dagen' => $sanatizedPOST['herhaling_dagen']
            ];

            $result = $this->database->query($query, $params);

            Session::set('onderhoudstaak.success', "De onderhoudstaak is toegevoegd.");
            header("Location: ?page=onderhoudstaak.overzicht");
        } catch (\Exception $e) {
            Session::set('onderhoudstaak.error', $e->getMessage());
            header("Location: ?page=onderhoudstaak.overzicht");
        }
    }

    /**
     * Edits an existing onderhoud in the database after validating input data.
     * 
     * This method sanitizes and validates form input data for required fields: 'type', 'type_id', and 'id'. It then updates the 'onderhoud' table with the new data. On success, a success message is set in the session and the user is redirected to the onderhoud overview page. On failure, an error message is set and the user is redirected to the same page.
     * 
     * @return void
     */
    public function editOnderhoud(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            if (!isset($sanatizedPOST['type']) || !in_array($sanatizedPOST['type'], ['status', 'personeel'])) {
                Session::set('onderhoud.error', "Ongeldig edit type.");
                header("Location: ?page=onderhoud.overzicht");
            }

            $query = "
                UPDATE onderhoud
                    SET ". $sanatizedPOST['type'] ."_id = :type_id
                    WHERE id = :id;
            ";
            $params = [
                'type_id' => $sanatizedPOST[$sanatizedPOST['type'] ."_id"],
                'id' => $sanatizedPOST['id']
            ];

            $result = $this->database->query($query, $params);

            Session::set('onderhoud.success', "De onderhoud is gewijzigd.");
            header("Location: ?page=onderhoud.overzicht");
        } catch (\Exception $e) {
            Session::set('onderhoud.error', $e->getMessage());
            header("Location: ?page=onderhoud.overzicht");
        }
    }

    /**
     * Adds a new onderhoud in the database after validating input data.
     * 
     * This method sanitizes and validates form input data for required fields: 'onderhoudstaak_id' and 'personeel_id'. It then inserts a new record into the 'onderhoud' table. On success, a success message is set in the session and the user is redirected to the onderhoud overview page. On failure, an error message is set and the user is redirected to the same page.
     * 
     * @return void
     */
    public function addOnderhoud(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            if (!DataProcessor::validateFields($sanatizedPOST, ['onderhoudstaak_id', 'personeel_id'])) {
                Session::set('onderhoud.error', 'Required fields not found.');
                header("Location: ?page=onderhoud.overzicht");
                exit();
            }

            $query = "
                INSERT INTO onderhoud (onderhoudstaak_id, personeel_id)
                    VALUES (:onderhoudstaak_id, :personeel_id);
            ";
            $params = [
                'onderhoudstaak_id' => $sanatizedPOST['onderhoudstaak_id'],
                'personeel_id' => $sanatizedPOST['personeel_id']
            ];

            $result = $this->database->query($query, $params);

            Session::set('onderhoud.success', "De onderhoud is toegevoegd.");
            header("Location: ?page=onderhoud.overzicht");
        } catch (\Exception $e) {
            Session::set('onderhoud.error', $e->getMessage());
            header("Location: ?page=onderhoud.overzicht");
        }
    }


    /**
     * Adds a new comment to the onderhoudstaak in the database after validating input data.
     * 
     * This method sanitizes and validates form input data for required fields: 'onderhoudstaak_id' and 'opmerking'. 
     * It then inserts a new record into the 'onderhoudstaak_opmerking' table with the current user's personeel_id. 
     * On success, a success message is set in the session and the user is redirected to the onderhoud view page. 
     * On failure, an error message is set and the user is redirected to the same page.
     * 
     * @return void
     */
    public function addComment(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            if (!DataProcessor::validateFields($sanatizedPOST, ['onderhoudstaak_id', 'opmerking'])) {
                Session::set('onderhoud.error', 'Required fields not found.');
                header("Location: ?page=onderhoud.view&id=" . $sanatizedPOST['onderhoudstaak_id']);
                exit();
            }

            $personeel_id = Session::get('user')['id'];

            $query = "
                INSERT INTO onderhoudstaak_opmerking (onderhoudstaak_id, personeel_id, opmerking)
                    VALUES (:onderhoudstaak_id, :personeel_id, :opmerking);
            ";
            $params = [
                'onderhoudstaak_id' => $sanatizedPOST['onderhoudstaak_id'],
                'personeel_id' => $personeel_id,
                'opmerking' => $sanatizedPOST['opmerking']
            ];

            $result = $this->database->query($query, $params);

            Session::set('onderhoud.success', "De opmerking is toegevoegd.");
            header("Location: ?page=onderhoud.view&id=" . $sanatizedPOST['onderhoudstaak_id']);
        } catch (\Exception $e) {
            Session::set('onderhoud.error', $e->getMessage());
            header("Location: ?page=onderhoud.view&id=" . $sanatizedPOST['onderhoudstaak_id']);
        }
    }

    /**
     * Deletes a comment from the onderhoudstaak_opmerking table after validating input data.
     * 
     * This method sanitizes and validates form input data for required fields: 'onderhoudstaak_opmerking_id' and 'onderhoudstaak_id'. 
     * It then checks if the current user has the required permissions to delete the comment. If not, it redirects to the onderhoud view page with an error message.
     * If successful, a success message is set in the session and the user is redirected to the onderhoud view page. On failure, an error message is set and the user is redirected to the same page.
     * 
     * @return void
     */
    public function deleteComment(): void
    {
        try {
            $sanatizedPOST = DataProcessor::sanitizeData($_POST);

            $row = $this->database->query("
                SELECT personeel_id
                    FROM onderhoudstaak_opmerking
                    WHERE id = :onderhoudstaak_opmerking_id;
            ", [
                'onderhoudstaak_opmerking_id' => $sanatizedPOST['onderhoudstaak_opmerking_id']
            ])->fetch(PDO::FETCH_ASSOC);
            if (!Functions::checkPermissions(['beheerder', 'manager'])) {
                if ($row['personeel_id'] != Session::get('user')['id']) {
                    Session::set('onderhoud.error', 'You are not allowed to delete this comment.');
                    header("Location: ?page=onderhoud.view&id=" . $sanatizedPOST['onderhoudstaak_id']);
                    exit();
                }
            }

            if (!DataProcessor::validateFields($sanatizedPOST, ['onderhoudstaak_opmerking_id'])) {
                Session::set('onderhoud.error', 'Required fields not found.');
                header("Location: ?page=onderhoud.view&id=" . $sanatizedPOST['onderhoudstaak_id']);
                exit();
            }

            $query = "
                DELETE FROM onderhoudstaak_opmerking
                    WHERE id = :onderhoudstaak_opmerking_id;
            ";
            $params = [
                'onderhoudstaak_opmerking_id' => $sanatizedPOST['onderhoudstaak_opmerking_id']
            ];

            $result = $this->database->query($query, $params);
            
            if ($result->rowCount() > 0) {
                Session::set('onderhoud.success', "De opmerking is verwijderd.");
                header("Location: ?page=onderhoud.view&id=" . $sanatizedPOST['onderhoudstaak_id']);
                exit();
            } else {
                Session::set('onderhoud.error', "Geen opmerking verwijderd.");
                header("Location: ?page=onderhoud.view&id=" . $sanatizedPOST['onderhoudstaak_id']);
                exit();
            }
        } catch (\Exception $e) {
            Session::set('onderhoud.error', $e->getMessage());
            header("Location: ?page=onderhoud.view&id=" . $sanatizedPOST['onderhoudstaak_id']);
        }
    }

}
