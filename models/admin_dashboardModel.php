<?php
include_once(__DIR__ . "/../db/database.php");
class DashboardModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Helper function to create a simple date filter SQL snippet.
    private function getDateCondition($filter, $columnName) {
        switch ($filter) {
            case 'today':
                return "AND DATE($columnName) = CURRENT_DATE";
            case 'yesterday':
                return "AND DATE($columnName) = CURRENT_DATE - INTERVAL '1 day'";
            case 'this_week':
                return "AND $columnName >= date_trunc('week', CURRENT_DATE)::date AND $columnName < (date_trunc('week', CURRENT_DATE) + INTERVAL '1 week')::date";
            case 'this_month':
                return "AND $columnName >= date_trunc('month', CURRENT_DATE)::date AND $columnName < (date_trunc('month', CURRENT_DATE) + INTERVAL '1 month')::date";
            case 'this_year':
                return "AND $columnName >= date_trunc('year', CURRENT_DATE)::date AND $columnName < (date_trunc('year', CURRENT_DATE) + INTERVAL '1 year')::date";
            default:
                return ""; // For 'all_time' or other cases, no date condition is added.
        }
    }

    // Gets the count of completed appointments for a given period.
    public function getCompletedAppointmentsCount($filter = 'today') {
        $condition = $this->getDateCondition($filter, 'appointment_date');
        $query = "SELECT COUNT(*) FROM appointments WHERE status = 'Completed' {$condition}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

    // Gets the count of pending appointments for a given period.
    public function getPendingAppointmentsCount($filter = 'today') {
        $condition = $this->getDateCondition($filter, 'appointment_date');
        $query = "SELECT COUNT(*) FROM appointments WHERE status = 'Pending' {$condition}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

    // Gets the details of pending appointments for the specified period.
    public function getPendingAppointmentsDetails($filter = 'today') {
        $condition = $this->getDateCondition($filter, 'a.appointment_date');
        $query = "SELECT a.appointment_id, p.pet_name, o.owner_name AS pet_owner_name, o.contact_number, 
                         a.purpose_of_visit AS type_of_service, COALESCE(a.remarks, 'N/A') AS remarks, 
                         a.appointment_date, a.appointment_time
                  FROM appointments a
                  JOIN pet p ON a.pet_id = p.pet_id
                  JOIN owner o ON p.owner_id = o.owner_id 
                  WHERE a.status = 'Pending' {$condition}
                  ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Gets total sales from products.
    public function getTotalProductSalesAmount($filter = 'today') {
        $condition = $this->getDateCondition($filter, 'sale_date');
        $query = "SELECT COALESCE(SUM(sale_total_amount), 0) FROM sales WHERE 1=1 {$condition}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

    // Gets total revenue from services.
    public function getTotalServiceRevenue($filter = 'today') {
        $condition = $this->getDateCondition($filter, 'transaction_date');
        $query = "SELECT COALESCE(SUM(total_amount), 0) FROM appointment_transactions WHERE 1=1 {$condition}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

    // Gets the total count of all registered pets, ignoring any date filters.
    public function getTotalRegisteredPetsCount() {
        $query = "SELECT COUNT(pet_id) FROM pet";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

    // Gets product sales data formatted for the dashboard chart.
    public function getProductSalesForChart($filter = 'this_month', $limit = 7) {
        $condition = $this->getDateCondition($filter, 's.sale_date');
        $query = "SELECT pr.prod_name, COALESCE(SUM(si.sale_line_total), 0) AS total_sales_amount
                  FROM sales_items si
                  JOIN product pr ON si.prod_id = pr.prod_id
                  JOIN sales s ON si.sale_id = s.sale_id
                  WHERE 1=1 {$condition}
                  GROUP BY pr.prod_name ORDER BY total_sales_amount DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Gets service sales data formatted for the dashboard chart.
    public function getServiceSalesForChart($filter = 'this_month', $limit = 7) {
        $condition = $this->getDateCondition($filter, 'at.transaction_date');
        $query = "SELECT a.purpose_of_visit AS service_name, COALESCE(SUM(at.total_amount), 0) AS total_service_amount
                  FROM appointment_transactions at
                  JOIN appointments a ON at.appointment_id = a.appointment_id
                  WHERE 1=1 {$condition}
                  GROUP BY a.purpose_of_visit ORDER BY total_service_amount DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Gets the top-selling products for the period.
    public function getTopProducts($filter = 'all_time', $limit = 5) {
        $condition = $this->getDateCondition($filter, 's.sale_date');
        $query = "SELECT pr.prod_name, COALESCE(SUM(si.sale_line_total), 0) AS total_sales_amount
                  FROM sales_items si
                  JOIN product pr ON si.prod_id = pr.prod_id
                  JOIN sales s ON si.sale_id = s.sale_id
                  WHERE 1=1 {$condition}
                  GROUP BY pr.prod_name ORDER BY total_sales_amount DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Gets the top-selling services for the period.
    public function getTopServices($filter = 'all_time', $limit = 5) {
        $condition = $this->getDateCondition($filter, 'at.transaction_date');
        $query = "SELECT a.purpose_of_visit AS service_name, COALESCE(SUM(at.total_amount), 0) AS total_service_amount
                  FROM appointment_transactions at
                  JOIN appointments a ON at.appointment_id = a.appointment_id
                  WHERE 1=1 {$condition}
                  GROUP BY a.purpose_of_visit ORDER BY total_service_amount DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}