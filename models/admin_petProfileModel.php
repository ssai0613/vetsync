<?php
include_once(__DIR__ . "/../db/database.php");
class PetProModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllPets() {
        $query = "SELECT p.*, o.owner_name FROM PET p JOIN OWNER o ON p.owner_id = o.owner_id ORDER BY p.pet_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllOwners() {
        $query = "SELECT * FROM OWNER ORDER BY owner_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPet($petName, $ownerId, $species, $breed, $color, $markings, $dob, $medicalNotes) {
        $query = "INSERT INTO PET (pet_name, owner_id, species, breed, color, markings, dob, medical_notes)
                  VALUES (:petName, :ownerId, :species, :breed, :color, :markings, :dob, :medicalNotes)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':petName' => $petName, ':ownerId' => $ownerId, ':species' => $species, ':breed' => $breed,
            ':color' => $color, ':markings' => $markings, ':dob' => $dob, ':medicalNotes' => $medicalNotes
        ]);
    }

    public function addOwner($ownerName, $contactNumber, $address, $email) {
        $query = "INSERT INTO OWNER (owner_name, contact_number, address, email)
                  VALUES (:ownerName, :contactNumber, :address, :email)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':ownerName' => $ownerName, ':contactNumber' => $contactNumber,
            ':address' => $address, ':email' => $email
        ]);
    }

    public function updatePet($petId, $petName, $ownerId, $species, $breed, $color, $markings, $dob, $medicalNotes) {
        $query = "UPDATE PET SET pet_name = :petName, owner_id = :ownerId, species = :species, breed = :breed,
                                color = :color, markings = :markings, dob = :dob, medical_notes = :medicalNotes
                  WHERE pet_id = :petId";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':petId' => $petId, ':petName' => $petName, ':ownerId' => $ownerId, ':species' => $species, ':breed' => $breed,
            ':color' => $color, ':markings' => $markings, ':dob' => $dob, ':medicalNotes' => $medicalNotes
        ]);
    }

    public function updateOwner($ownerId, $ownerName, $contactNumber, $address, $email) {
        $query = "UPDATE OWNER SET owner_name = :ownerName, contact_number = :contactNumber, address = :address, email = :email
                  WHERE owner_id = :ownerId";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':ownerId' => $ownerId, ':ownerName' => $ownerName,
            ':contactNumber' => $contactNumber, ':address' => $address, ':email' => $email
        ]);
    }

    public function deletePet($petId) {
        $query = "DELETE FROM PET WHERE pet_id = :petId";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':petId' => $petId]);
    }

    public function deleteOwner($ownerId) {
        $query = "DELETE FROM OWNER WHERE owner_id = :ownerId";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':ownerId' => $ownerId]);
    }

    public function searchPets($queryInput) {
        $query = "SELECT p.*, o.owner_name FROM PET p JOIN OWNER o ON p.owner_id = o.owner_id
                  WHERE p.pet_name ILIKE :queryInput OR o.owner_name ILIKE :queryInput
                  ORDER BY p.pet_id ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $queryInput . '%';
        $stmt->execute([':queryInput' => $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchOwners($queryInput) {
        $query = "SELECT * FROM OWNER
                  WHERE owner_name ILIKE :queryInput OR contact_number ILIKE :queryInput
                  ORDER BY owner_id ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $queryInput . '%';
        $stmt->execute([':queryInput' => $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnerPets($ownerId) {
        $query = "SELECT * FROM PET WHERE owner_id = :ownerId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':ownerId' => $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPetDetails($petId) {
        $query = "SELECT p.*, o.owner_name, o.contact_number FROM PET p JOIN OWNER o ON p.owner_id = o.owner_id WHERE p.pet_id = :petId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':petId' => $petId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOwnerDetails($ownerId) {
        $query = "SELECT * FROM OWNER WHERE owner_id = :ownerId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':ownerId' => $ownerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetches transaction history for a pet. Just the important stuff, you know.
    public function getPetTransactionHistory($petId) {
        $query = "SELECT at.transaction_date, a.appointment_date, a.purpose_of_visit, a.remarks
                  FROM appointment_transactions at
                  JOIN appointments a ON at.appointment_id = a.appointment_id
                  WHERE a.pet_id = :petId
                  ORDER BY at.transaction_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':petId' => $petId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>