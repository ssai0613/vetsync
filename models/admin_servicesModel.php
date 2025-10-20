<?php
include_once(__DIR__ . "/../db/database.php");
class Service {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllServices() {
        $query = "SELECT * FROM service ORDER BY svc_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addService($serviceName, $serviceCost, $description) {
        $query = "INSERT INTO service (svc_name, svc_price, svc_descript) VALUES (:serviceName, :serviceCost, :description)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':serviceName' => $serviceName, ':serviceCost' => $serviceCost, ':description' => $description]);
    }

    public function updateService($serviceID, $serviceName, $serviceCost, $description) {
        $query = "UPDATE service SET svc_name = :serviceName, svc_price = :serviceCost, svc_descript = :description WHERE svc_id = :serviceID";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':serviceID' => $serviceID, ':serviceName' => $serviceName, ':serviceCost' => $serviceCost, ':description' => $description]);
    }

    public function removeService($serviceID) {
        $query = "DELETE FROM service WHERE svc_id = :serviceID";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':serviceID' => $serviceID]);
    }

    public function searchServices($queryInput) {
        $query = "SELECT * FROM service WHERE svc_name ILIKE :queryInput ORDER BY svc_id ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $queryInput . '%';
        $stmt->execute([':queryInput' => $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>