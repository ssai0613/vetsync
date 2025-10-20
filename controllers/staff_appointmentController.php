<?php

include_once(__DIR__ . '/../models/staff_appointmentModel.php');

if (isset($_SERVER['REQUEST_METHOD'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $ajaxController = new AppointmentController();
        $ajaxController->handlePostRequest();
        exit;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        $ajaxController = new AppointmentController();
        $ajaxController->handleGetRequest();
        exit;
    }
}

class AppointmentController
{
    private $appointmentModel;

    public function __construct()
    {
        $this->appointmentModel = new Appointment();
    }

    public function getAllAppointments()
    {
        return $this->appointmentModel->getAllAppointments();
    }

    public function handlePostRequest()
    {
        if (isset($_POST['action'])) {
            $responseMessage = "Error: Invalid action or missing data.";
            switch ($_POST['action']) {
                case 'add':
                    $ownerId = $_POST['owner_id'] ?? null;
                    $petId = $_POST['pet_id'] ?? null;
                    $purpose = $_POST['visitPurpose'] ?? null;
                    $date = $_POST['appointmentDate'] ?? null;
                    $time = $_POST['appointmentTime'] ?? null;
                    $remarks = $_POST['remarks'] ?? '';
                    $status = 'Pending';

                    if (empty($ownerId) || empty($petId) || empty($purpose) || empty($date) || empty($time)) {
                        $responseMessage = "Error: Please fill in all required fields.";
                    } else {
                        $result = $this->appointmentModel->addAppointment($ownerId, $petId, $purpose, $date, $time, $remarks, $status);

                        if ($result === true) {
                            $responseMessage = "Appointment added successfully.";
                        } elseif ($result === "timeslot_occupied") {
                            $responseMessage = "Error: The selected time slot is already occupied. Please choose another time.";
                        } else {
                            $responseMessage = "Failed to add appointment. Please try again or check server logs.";
                        }
                    }
                    break;

                case 'cancel':
                    $appointmentId = $_POST['appointment_id'] ?? null;
                    if ($appointmentId) {
                        if ($this->appointmentModel->cancelAppointment($appointmentId)) {
                            $responseMessage = "Appointment cancelled successfully.";
                        } else {
                            $responseMessage = "Failed to cancel appointment.";
                        }
                    } else {
                        $responseMessage = "Error: Appointment ID is required to cancel.";
                    }
                    break;

                 case 'complete':
                    $appointmentId = $_POST['appointment_id'] ?? null;
                    $totalAmount = $_POST['total_amount'] ?? null;
                    $amountPaid = $_POST['amount_paid'] ?? null;
                    $changeAmount = $_POST['change_amount'] ?? null;

                    if ($appointmentId && isset($totalAmount) && isset($amountPaid) && isset($changeAmount)) {
                        if (!is_numeric($totalAmount) || !is_numeric($amountPaid) || !is_numeric($changeAmount) || floatval($amountPaid) < floatval($totalAmount)) {
                            $responseMessage = "Error: Invalid amount details or insufficient payment.";
                        } else {
                            if ($this->appointmentModel->completeAppointment($appointmentId, $totalAmount, $amountPaid, $changeAmount)) {
                                $responseMessage = "Appointment completed and transaction saved successfully.";
                            } else {
                                $responseMessage = "Failed to complete appointment. Please check server logs.";
                            }
                        }
                    } else {
                         $responseMessage = "Error: Missing data for completing the appointment.";
                    }
                    break;
                default:
                    $responseMessage = "Error: Invalid POST action specified.";
                    break;
            }
            echo $responseMessage;
        } else {
            echo "Error: No action specified for POST request.";
        }
    }

    public function handleGetRequest()
    {
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            switch ($action) {
                case 'getAllAppointments':
                    $appointments = $this->getAllAppointments();
                    header('Content-Type: application/json');
                    echo json_encode($appointments);
                    break;
                case 'search':
                    $queryInput = $_GET['query'] ?? '';
                    $results = $this->appointmentModel->searchAppointments($queryInput);
                    $this->renderAppointmentTableRows($results);
                    break;
                case 'sortByStatus':
                    $status = $_GET['status'] ?? 'all';
                    if ($status === 'all') {
                         $results = $this->getAllAppointments();
                    } else {
                        $results = $this->appointmentModel->getAppointmentsByStatus($status);
                    }
                    $this->renderAppointmentTableRows($results);
                    break;
                case 'getOwners':
                    $owners = $this->appointmentModel->getAllOwners();
                    header('Content-Type: application/json');
                    echo json_encode($owners);
                    break;
                 case 'getOwnerPets':
                    $ownerId = $_GET['owner_id'] ?? null;
                    $pets = [];
                    if ($ownerId) {
                        $pets = $this->appointmentModel->getOwnerPets($ownerId);
                    }
                    header('Content-Type: application/json');
                    echo json_encode($pets);
                    break;
                case 'getServices':
                    $services = $this->appointmentModel->getAllServices();
                    header('Content-Type: application/json');
                    echo json_encode($services);
                    break;
                 case 'getAppointmentDetails':
                    $appointmentId = $_GET['appointment_id'] ?? null;
                    $appointmentDetails = null;
                    if ($appointmentId) {
                        $appointmentDetails = $this->appointmentModel->getAppointmentById($appointmentId);
                    }
                    header('Content-Type: application/json');
                    echo json_encode($appointmentDetails);
                    break;
                default:
                    echo "Error: Invalid GET action requested.";
                    break;
            }
        }
    }

    private function renderAppointmentTableRows($appointments) {
         if (empty($appointments)) {
             echo "<tr><td colspan='9'>No appointments found.</td></tr>";
             return;
         }
         $rowNumber = 1;
         foreach ($appointments as $appointment) {
            echo "<tr data-appointment-id='" . htmlspecialchars($appointment['appointment_id']) . "'>";
            echo "<td>" . $rowNumber++ . "</td>";
            echo "<td>" . htmlspecialchars($appointment['appointment_id']) . "</td>";
            echo "<td>" . htmlspecialchars($appointment['owner_name']) . "</td>";
            echo "<td>" . htmlspecialchars($appointment['pet_breed']) . "</td>";
            echo "<td>" . htmlspecialchars($appointment['contact_number']) . "</td>";
            echo "<td>" . htmlspecialchars($appointment['purpose_of_visit']) . "</td>";
            echo "<td>" . htmlspecialchars($appointment['appointment_date']) . "</td>";
            echo "<td><span class='status-" . strtolower(htmlspecialchars($appointment['status'])) . "'>" . htmlspecialchars($appointment['status']) . "</span></td>";
            echo "<td class='action-icons'>";
            echo "<button class='edit_btn'><i class='bx bx-show'></i> View</button>";
            echo "</td>";
            echo "</tr>";
        }
     }
}