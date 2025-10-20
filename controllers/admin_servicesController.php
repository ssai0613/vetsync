<?php

include_once(__DIR__ . '/../models/admin_servicesModel.php');

$controller = new ServicesController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->handlePostRequest();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->handleGetRequest();
}
class ServicesController {
    private $service;

    public function __construct() {
        $this->service = new Service();
    }

    public function getAllServices() {
        return $this->service->getAllServices();
    }

    public function handlePostRequest() {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $serviceName = $_POST['serviceName'];
                    $serviceCost = $_POST['serviceCost'];
                    $description = $_POST['description'];

                    if ($this->service->addService($serviceName, $serviceCost, $description)) {
                        echo "Service added successfully.";
                    } else {
                        echo "Failed to add service.";
                    }
                    break;

                case 'update':
                    $serviceID = $_POST['serviceID'];
                    $serviceName = $_POST['serviceName'];
                    $serviceCost = $_POST['serviceCost'];
                    $description = $_POST['description'];

                    if ($this->service->updateService($serviceID, $serviceName, $serviceCost, $description)) {
                        echo "Service updated successfully.";
                    } else {
                        echo "Failed to update service.";
                    }
                    break;

                case 'remove':
                    $serviceID = $_POST['serviceID'];

                    if ($this->service->removeService($serviceID)) {
                        echo "Service removed successfully.";
                    } else {
                        echo "Failed to remove service.";
                    }
                    break;
            }
        }
    }

    public function handleGetRequest() {
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'search':
                    $queryInput = $_GET['query'];
                    $results = $this->service->searchServices($queryInput);
                    foreach ($results as $service) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($service['svc_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($service['svc_name']) . "</td>";
                        echo "<td>₱ " . htmlspecialchars(number_format($service['svc_price'], 2)) . "</td>";
                        echo "<td>" . htmlspecialchars($service['svc_descript']) . "</td>";
                        echo "</tr>";
                    }
                    break;
            }
        }
    }
}
?>