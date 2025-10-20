<?php
include_once(__DIR__ . "/../db/database.php");
class Appointment
{
    private $conn; 

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    private function generateUniqueAppointmentId($appointmentDate) {
        $datePart = date('Ymd', strtotime($appointmentDate));
        $randomPart = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $customId = $datePart . $randomPart;

        $query = "SELECT appointment_id FROM appointments WHERE appointment_id = :customId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':customId' => $customId]);

        if ($stmt->rowCount() > 0) {
            return $this->generateUniqueAppointmentId($appointmentDate); // Ulitin if naa na, uy!
        }
        return $customId;
    }

    private function autoCancelPastDueAppointments() {
        $query = "UPDATE appointments SET status = 'Cancelled' WHERE appointment_date < CURRENT_DATE AND status = 'Pending'";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Naay error sa autoCancelPastDueAppointments: " . $e->getMessage());
        }
    }

    public function getAllAppointments()
    {
        $this->autoCancelPastDueAppointments(); // Let's call the auto-cancel first. So responsible.

        $query = "SELECT a.appointment_id, po.owner_name, p.breed AS pet_breed, po.contact_number, a.purpose_of_visit,
                         a.appointment_date, a.appointment_time, a.status
                  FROM appointments a
                  JOIN owner po ON a.owner_id = po.owner_id 
                  JOIN pet p ON a.pet_id = p.pet_id 
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAppointment($ownerId, $petId, $purpose, $date, $time, $remarks, $status)
    {
        // 1. Check sa if naay existing active appointment. Bawal double book.
        $checkQuery = "SELECT appointment_id FROM appointments WHERE appointment_date = :date AND appointment_time = :time AND status NOT IN ('Completed', 'Cancelled')";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->execute([':date' => $date, ':time' => $time]);

        if ($checkStmt->rowCount() > 0) {
            return "timeslot_occupied"; // Return a sign na occupied na.
        }

        // 2. If free ang slot, i-add ang appointment.
        $appointmentId = $this->generateUniqueAppointmentId($date);

        $query = "INSERT INTO appointments (appointment_id, owner_id, pet_id, purpose_of_visit, appointment_date, appointment_time, remarks, status)
                  VALUES (:appointmentId, :ownerId, :petId, :purpose, :date, :time, :remarks, :status)";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute([':appointmentId' => $appointmentId, ':ownerId' => $ownerId, ':petId' => $petId, ':purpose' => $purpose, ':date' => $date, ':time' => $time, ':remarks' => $remarks, ':status' => $status])) {
            return true;
        }
        error_log("Failed to execute addAppointment INSERT query. Anyare?");
        return false;
    }

    public function cancelAppointment($appointmentId)
    {
        $query = "UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = :appointmentId";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':appointmentId' => $appointmentId]);
    }

    public function completeAppointment($appointmentId, $totalAmount, $amountPaid, $changeAmount)
    {
        try {
            $this->conn->beginTransaction(); 
            
            $queryUpdate = "UPDATE appointments SET status = 'Completed' WHERE appointment_id = :appointmentId";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            if(!$stmtUpdate->execute([':appointmentId' => $appointmentId])){
                $this->conn->rollBack();
                error_log("Failed to update appointment status during completion. So sad.");
                return false;
            }

            $queryInsertTransaction = "INSERT INTO appointment_transactions (appointment_id, total_amount, amount_paid, change_amount)
                                       VALUES (:appointmentId, :totalAmount, :amountPaid, :changeAmount)";
            $stmtInsertTransaction = $this->conn->prepare($queryInsertTransaction);
            if(!$stmtInsertTransaction->execute([':appointmentId' => $appointmentId, ':totalAmount' => $totalAmount, ':amountPaid' => $amountPaid, ':changeAmount' => $changeAmount])){
                $this->conn->rollBack();
                error_log("Failed to insert transaction during completion. Why man?");
                return false;
            }
            
            $this->conn->commit(); 
            return true; 
        } catch (PDOException $e) {
            $this->conn->rollBack(); 
            error_log("PDOException in completeAppointment: " . $e->getMessage()); 
            return false; 
        }
    }

    public function searchAppointments($searchTerm)
    {
        $this->autoCancelPastDueAppointments();

        $query = "SELECT a.appointment_id, po.owner_name, p.breed AS pet_breed, po.contact_number, a.purpose_of_visit,
                         a.appointment_date, a.appointment_time, a.status
                  FROM appointments a
                  JOIN owner po ON a.owner_id = po.owner_id
                  JOIN pet p ON a.pet_id = p.pet_id
                  WHERE (po.owner_name ILIKE :searchTerm OR p.pet_name ILIKE :searchTerm OR a.appointment_id ILIKE :searchTerm)
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $this->conn->prepare($query);
        $likeTerm = '%' . $searchTerm . '%'; 
        $stmt->execute([':searchTerm' => $likeTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentsByStatus($status)
    {
        $this->autoCancelPastDueAppointments();
        
        $query = "SELECT a.appointment_id, po.owner_name, p.breed AS pet_breed, po.contact_number, a.purpose_of_visit,
                         a.appointment_date, a.appointment_time, a.status
                  FROM appointments a
                  JOIN owner po ON a.owner_id = po.owner_id
                  JOIN pet p ON a.pet_id = p.pet_id
                  WHERE a.status = :status
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllOwners() {
        $query = "SELECT owner_id, owner_name, contact_number FROM owner ORDER BY owner_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnerPets($ownerId) {
        $query = "SELECT pet_id, pet_name, breed FROM pet WHERE owner_id = :ownerId ORDER BY pet_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':ownerId' => $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllServices() {
        $query = "SELECT svc_id, svc_name, svc_price FROM service ORDER BY svc_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAppointmentById($appointmentId)
    {
        $query = "SELECT a.appointment_id, po.owner_name, po.contact_number, p.pet_name, p.breed AS pet_breed, 
                         a.purpose_of_visit, a.appointment_date, a.appointment_time, a.remarks, a.status, 
                         po.owner_id, p.pet_id
                  FROM appointments a
                  JOIN owner po ON a.owner_id = po.owner_id
                  JOIN pet p ON a.pet_id = p.pet_id
                  WHERE a.appointment_id = :appointmentId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':appointmentId' => $appointmentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAppointmentTransactions($filter = 'all') {
        $query = "SELECT at.transaction_id, at.appointment_id, at.transaction_date, po.owner_name, p.pet_name, 
                         a.purpose_of_visit, at.total_amount, at.amount_paid, at.change_amount 
                  FROM appointment_transactions at 
                  JOIN appointments a ON at.appointment_id = a.appointment_id 
                  JOIN owner po ON a.owner_id = po.owner_id 
                  JOIN pet p ON a.pet_id = p.pet_id";
        $whereClause = ""; 
        $params = []; 
        
        switch ($filter) {
            case 'today': 
                $whereClause = " WHERE DATE(at.transaction_date) = :today"; 
                $params[':today'] = date('Y-m-d'); 
                break;
            case 'week': 
                $whereClause = " WHERE at.transaction_date >= DATE_TRUNC('week', CURRENT_DATE)"; 
                break;
            case 'month': 
                $whereClause = " WHERE at.transaction_date >= DATE_TRUNC('month', CURRENT_DATE)"; 
                break;
        }
        
        $finalQuery = $query . $whereClause . " ORDER BY at.transaction_date DESC";
        $stmt = $this->conn->prepare($finalQuery);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransactionDetailsById($transactionId) {
        $query = "SELECT at.transaction_id, at.appointment_id, at.transaction_date, po.owner_name, p.pet_name, 
                         a.purpose_of_visit, at.total_amount, at.amount_paid, at.change_amount 
                  FROM appointment_transactions at 
                  JOIN appointments a ON at.appointment_id = a.appointment_id 
                  JOIN owner po ON a.owner_id = po.owner_id 
                  JOIN pet p ON a.pet_id = p.pet_id 
                  WHERE at.transaction_id = :transaction_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':transaction_id', $transactionId, PDO::PARAM_INT); 
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>