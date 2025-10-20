<?php

include_once(__DIR__ . '/../models/staff_petProfileModel.php');

class PetProController {
    private $model;

    public function __construct() {
        $this->model = new PetProModel();
    }

    public function getAllPets() {
        return $this->model->getAllPets();
    }

    public function getAllOwners() {
        return $this->model->getAllOwners();
    }

    public function handlePetRequest() {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'addPet':
                    $this->model->addPet(
                        $_POST['petName'], $_POST['ownerId'], $_POST['species'], $_POST['breed'],
                        $_POST['color'], $_POST['markings'], $_POST['dob'], $_POST['medicalNotes']
                    ) ? print "Pet added successfully." : print "Failed to add pet.";
                    break;

                case 'updatePet':
                    $this->model->updatePet(
                        $_POST['petId'], $_POST['petName'], $_POST['ownerId'], $_POST['species'],
                        $_POST['breed'], $_POST['color'], $_POST['markings'], $_POST['dob'], $_POST['medicalNotes']
                    ) ? print "Pet updated successfully." : print "Failed to update pet.";
                    break;

                case 'deletePet':
                    $this->model->deletePet($_POST['petId']) ? print "Pet deleted successfully." : print "Failed to delete pet.";
                    break;
            }
        }
    }

    public function handleOwnerRequest() {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'addOwner':
                    $this->model->addOwner(
                        $_POST['ownerName'], $_POST['contactNumber'], $_POST['address'], $_POST['email']
                    ) ? print "Owner added successfully." : print "Failed to add owner.";
                    break;

                case 'updateOwner':
                    $this->model->updateOwner(
                        $_POST['ownerId'], $_POST['ownerName'], $_POST['contactNumber'], $_POST['address'], $_POST['email']
                    ) ? print "Owner updated successfully." : print "Failed to update owner.";
                    break;

                case 'deleteOwner':
                    $this->model->deleteOwner($_POST['ownerId']) ? print "Owner deleted successfully." : print "Failed to delete owner.";
                    break;
            }
        }
    }
    
    public function handleGetRequest() {
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'searchPets':
                    echo json_encode($this->model->searchPets($_GET['query']));
                    break;

                case 'searchOwners':
                    echo json_encode($this->model->searchOwners($_GET['query']));
                    break;

                case 'getOwnerPets':
                    echo json_encode($this->model->getOwnerPets($_GET['clientId']));
                    break;

                case 'getPetDetails':
                    if (isset($_GET['petId'])) {
                        header('Content-Type: application/json');
                        echo json_encode($this->model->getPetDetails($_GET['petId']));
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Error: Pet ID not provided."]);
                    }
                    break;

                case 'getOwnerDetails':
                    if (isset($_GET['ownerId'])) {
                        header('Content-Type: application/json');
                        echo json_encode($this->model->getOwnerDetails($_GET['ownerId']));
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Error: Owner ID not provided."]);
                    }
                    break;

                case 'getPetHistory':
                    if (isset($_GET['petId'])) {
                        header('Content-Type: application/json');
                        echo json_encode($this->model->getPetTransactionHistory($_GET['petId']));
                    } else {
                        http_response_code(400);
                        echo json_encode(["error" => "Error: Pet ID is missing."]);
                    }
                    break;
            }
        }
    }
}

$controller = new PetProController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['type']) && $_POST['type'] === 'pet') {
        $controller->handlePetRequest();
    } elseif (isset($_POST['type']) && $_POST['type'] === 'owner') {
        $controller->handleOwnerRequest();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->handleGetRequest();
}
?>