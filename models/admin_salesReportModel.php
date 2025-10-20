<?php
include_once(__DIR__ . "/../db/database.php");
class SalesReportModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Helper to generate SQL date conditions for columns of type DATE.
    private function getDateFilterCondition($column, $filter = 'all_time', $startDate = null, $endDate = null) {
        $condition = "";
        switch ($filter) {
            case 'today': $condition = "AND {$column} = CURRENT_DATE"; break;
            case 'yesterday': $condition = "AND {$column} = CURRENT_DATE - INTERVAL '1 day'"; break;
            case 'last_7_days': $condition = "AND {$column} BETWEEN CURRENT_DATE - INTERVAL '7 days' AND CURRENT_DATE"; break;
            case 'last_15_days': $condition = "AND {$column} BETWEEN CURRENT_DATE - INTERVAL '15 days' AND CURRENT_DATE"; break;
            case 'last_30_days': $condition = "AND {$column} BETWEEN CURRENT_DATE - INTERVAL '30 days' AND CURRENT_DATE"; break;
            case 'this_week': $condition = "AND {$column} >= date_trunc('week', CURRENT_DATE)::date AND {$column} <= CURRENT_DATE"; break;
            case 'this_month': $condition = "AND {$column} >= date_trunc('month', CURRENT_DATE)::date AND {$column} <= CURRENT_DATE"; break;
            case 'this_year': $condition = "AND {$column} >= date_trunc('year', CURRENT_DATE)::date AND {$column} <= CURRENT_DATE"; break;
            case 'custom': if ($startDate && $endDate) { $condition = "AND {$column} BETWEEN :start_date AND :end_date"; } break;
        }
        return $condition;
    }

    // Helper to generate SQL date conditions for columns of type TIMESTAMP.
    private function getTimestampFilterCondition($column, $filter = 'all_time', $startDate = null, $endDate = null) {
        $condition = "";
        switch ($filter) {
            case 'today': $condition = "AND DATE({$column}) = CURRENT_DATE"; break;
            case 'yesterday': $condition = "AND DATE({$column}) = CURRENT_DATE - INTERVAL '1 day'"; break;
            case 'last_7_days': $condition = "AND DATE({$column}) BETWEEN CURRENT_DATE - INTERVAL '7 days' AND CURRENT_DATE"; break;
            case 'last_15_days': $condition = "AND DATE({$column}) BETWEEN CURRENT_DATE - INTERVAL '15 days' AND CURRENT_DATE"; break;
            case 'last_30_days': $condition = "AND DATE({$column}) BETWEEN CURRENT_DATE - INTERVAL '30 days' AND CURRENT_DATE"; break;
            case 'this_week': $condition = "AND DATE({$column}) >= date_trunc('week', CURRENT_DATE)::date AND DATE({$column}) <= CURRENT_DATE"; break;
            case 'this_month': $condition = "AND DATE({$column}) >= date_trunc('month', CURRENT_DATE)::date AND DATE({$column}) <= CURRENT_DATE"; break;
            case 'this_year': $condition = "AND DATE({$column}) >= date_trunc('year', CURRENT_DATE)::date AND DATE({$column}) <= CURRENT_DATE"; break;
            case 'custom': if ($startDate && $endDate) { $condition = "AND DATE({$column}) BETWEEN :start_date AND :end_date"; } break;
        }
        return $condition;
    }

    public function getCompletedAppointmentsCount($filter = 'all_time', $startDate = null, $endDate = null) {
        $condition = $this->getDateFilterCondition('appointment_date', $filter, $startDate, $endDate);
        $query = "SELECT COUNT(*) FROM appointments WHERE status = 'Completed' {$condition}";
        $stmt = $this->conn->prepare($query);
         if ($filter === 'custom' && $startDate && $endDate) {
             $stmt->bindParam(':start_date', $startDate);
             $stmt->bindParam(':end_date', $endDate);
         }
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

    public function getTotalSalesAmount($filter = 'all_time', $startDate = null, $endDate = null) {
        // Get date conditions for both tables, using their respective aliases and date columns.
        $dateConditionSales = $this->getTimestampFilterCondition('s.sale_date', $filter, $startDate, $endDate);
        $dateConditionAppt = $this->getTimestampFilterCondition('at.transaction_date', $filter, $startDate, $endDate);
    
        // Query for product sales totals
        $productSalesQuery = "SELECT sale_total_amount AS total FROM sales s WHERE 1=1 {$dateConditionSales}";
    
        // Query for service sales totals
        $serviceSalesQuery = "SELECT total_amount AS total FROM appointment_transactions at WHERE 1=1 {$dateConditionAppt}";
    
        // Combine the two queries using UNION ALL inside a subquery, and sum the results.
        $query = "SELECT COALESCE(SUM(total), 0) FROM (
                      ({$productSalesQuery}) UNION ALL ({$serviceSalesQuery})
                  ) AS combined_sales";
    
        $stmt = $this->conn->prepare($query);
    
        // Handle parameter binding for custom date ranges, which is more complex for UNION queries.
        if ($filter === 'custom' && $startDate && $endDate) {
            // Placeholders must be unique, so we replace them and create a new final query.
            $productSalesQueryWithBinding = str_replace([':start_date', ':end_date'], [':ps_start_date', ':ps_end_date'], $productSalesQuery);
            $serviceSalesQueryWithBinding = str_replace([':start_date', ':end_date'], [':ss_start_date', ':ss_end_date'], $serviceSalesQuery);
            
            $finalQuery = "SELECT COALESCE(SUM(total), 0) FROM (
                              ({$productSalesQueryWithBinding}) UNION ALL ({$serviceSalesQueryWithBinding})
                           ) AS combined_sales";
                           
            $stmt = $this->conn->prepare($finalQuery);
    
            // Bind parameters for both parts of the UNION query.
            $stmt->bindParam(':ps_start_date', $startDate);
            $stmt->bindParam(':ps_end_date', $endDate);
            $stmt->bindParam(':ss_start_date', $startDate);
            $stmt->bindParam(':ss_end_date', $endDate);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAverageSalesAmount($filter = 'all_time', $startDate = null, $endDate = null) {
         $condition = $this->getTimestampFilterCondition('sale_date', $filter, $startDate, $endDate);
         $query = "SELECT COALESCE(AVG(sale_total_amount), 0) FROM sales WHERE 1=1 {$condition}";
         $stmt = $this->conn->prepare($query);
          if ($filter === 'custom' && $startDate && $endDate) {
              $stmt->bindParam(':start_date', $startDate);
              $stmt->bindParam(':end_date', $endDate);
          }
         $stmt->execute();
         return $stmt->fetchColumn();
    }

    public function getTotalProductsSoldCount($filter = 'all_time', $startDate = null, $endDate = null) {
        $condition = $this->getTimestampFilterCondition('s.sale_date', $filter, $startDate, $endDate);
        $query = "SELECT COALESCE(SUM(si.sale_item_qty), 0) FROM sales_items si JOIN sales s ON si.sale_id = s.sale_id WHERE 1=1 {$condition}";
        $stmt = $this->conn->prepare($query);
         if ($filter === 'custom' && $startDate && $endDate) {
             $stmt->bindParam(':start_date', $startDate);
             $stmt->bindParam(':end_date', $endDate);
         }
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

     public function getProductSalesForChart($filter = 'this_month', $startDate = null, $endDate = null, $limit = 7) {
         $condition = $this->getTimestampFilterCondition('s.sale_date', $filter, $startDate, $endDate);
         $query = "SELECT p.prod_name, COALESCE(SUM(si.sale_line_total), 0) AS total_sales_amount
                   FROM sales_items si JOIN product p ON si.prod_id = p.prod_id JOIN sales s ON si.sale_id = s.sale_id
                   WHERE 1=1 {$condition} GROUP BY p.prod_name ORDER BY total_sales_amount DESC LIMIT :limit";
         $stmt = $this->conn->prepare($query);
         $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
          if ($filter === 'custom' && $startDate && $endDate) {
              $stmt->bindParam(':start_date', $startDate);
              $stmt->bindParam(':end_date', $endDate);
          }
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

     public function getServiceSalesForChart($filter = 'this_month', $startDate = null, $endDate = null, $limit = 7) {
         $condition = $this->getTimestampFilterCondition('at.transaction_date', $filter, $startDate, $endDate);
         $query = "SELECT a.purpose_of_visit AS service_name, COALESCE(SUM(at.total_amount), 0) AS total_service_amount
                   FROM appointment_transactions at JOIN appointments a ON at.appointment_id = a.appointment_id
                   WHERE 1=1 {$condition} GROUP BY a.purpose_of_visit ORDER BY total_service_amount DESC LIMIT :limit";
         $stmt = $this->conn->prepare($query);
         $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
          if ($filter === 'custom' && $startDate && $endDate) {
              $stmt->bindParam(':start_date', $startDate);
              $stmt->bindParam(':end_date', $endDate);
          }
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

    // Function to get a list of sales transactions (products and services)
    public function getSalesHistoryTransactions($filter = 'all_time', $startDate = null, $endDate = null) {
        $dateConditionSales = $this->getTimestampFilterCondition('s.sale_date', $filter, $startDate, $endDate);
        $dateConditionAppt = $this->getTimestampFilterCondition('at.transaction_date', $filter, $startDate, $endDate);

        $productSalesQuery = "SELECT s.sale_id AS transaction_id, s.sale_date AS transaction_date, 'Product Sale' AS transaction_type,
                                     s.sale_total_amount AS total_amount, s.sale_amount_paid AS amount_paid, s.sale_change_amount AS change_amount,
                                     NULL AS appointment_id, NULL AS purpose_of_visit, NULL AS pet_name, NULL AS owner_name
                              FROM sales s WHERE 1=1 {$dateConditionSales}";

        $serviceSalesQuery = "SELECT at.transaction_id, at.transaction_date, 'Service Sale' AS transaction_type, at.total_amount,
                                     at.amount_paid, at.change_amount, a.appointment_id, a.purpose_of_visit, p.pet_name, o.owner_name
                              FROM appointment_transactions at
                              JOIN appointments a ON at.appointment_id = a.appointment_id
                              JOIN pet p ON a.pet_id = p.pet_id JOIN owner o ON a.owner_id = o.owner_id
                              WHERE 1=1 {$dateConditionAppt}";
        
        $query = "({$productSalesQuery}) UNION ALL ({$serviceSalesQuery}) ORDER BY transaction_date DESC";
        
        $stmt = $this->conn->prepare($query);

        if ($filter === 'custom' && $startDate && $endDate) {
            // Since placeholders must be unique in a UNION query, we rewrite and bind them separately.
            $productSalesQueryWithBinding = str_replace([':start_date', ':end_date'], [':ps_start_date', ':ps_end_date'], $productSalesQuery);
            $serviceSalesQueryWithBinding = str_replace([':start_date', ':end_date'], [':ss_start_date', ':ss_end_date'], $serviceSalesQuery);
            
            $finalQuery = "({$productSalesQueryWithBinding}) UNION ALL ({$serviceSalesQueryWithBinding}) ORDER BY transaction_date DESC";
            
            $stmt = $this->conn->prepare($finalQuery);

            $stmt->bindParam(':ps_start_date', $startDate); 
            $stmt->bindParam(':ps_end_date', $endDate);
            $stmt->bindParam(':ss_start_date', $startDate); 
            $stmt->bindParam(':ss_end_date', $endDate);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function getProductSaleDetails($saleId) {
         $query = "SELECT si.sale_item_id, si.sale_id, si.prod_id, p.prod_name, si.sale_item_qty, si.sale_unit_price, si.sale_line_total
                   FROM sales_items si JOIN product p ON si.prod_id = p.prod_id
                   WHERE si.sale_id = :sale_id";
         $stmt = $this->conn->prepare($query);
         $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }

     public function getServiceSaleDetails($transactionId) {
         $query = "SELECT at.transaction_id, at.appointment_id, at.total_amount, at.amount_paid, at.change_amount,
                          at.transaction_date, a.purpose_of_visit, a.appointment_date, a.appointment_time, p.pet_name, o.owner_name
                   FROM appointment_transactions at
                   JOIN appointments a ON at.appointment_id = a.appointment_id
                   JOIN pet p ON a.pet_id = p.pet_id JOIN owner o ON a.owner_id = o.owner_id
                   WHERE at.transaction_id = :transaction_id";
         $stmt = $this->conn->prepare($query);
         $stmt->bindParam(':transaction_id', $transactionId, PDO::PARAM_INT);
         $stmt->execute();
         $details = $stmt->fetch(PDO::FETCH_ASSOC);
         return $details ?: [];
     }
}
?>