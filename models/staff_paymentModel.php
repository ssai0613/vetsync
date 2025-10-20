<?php
include_once(__DIR__ . "/../db/database.php");
class PaymentModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // To save/update inventory and sales.
    public function saveSale($amountDue, $cash, $change, $cart) {
        try {
            $this->conn->beginTransaction();

            $saleId = $this->insertSale($amountDue, $cash, $change);

            // Let's loop through the cart.
            foreach ($cart as $prodId => $item) {
                $this->insertSaleItem($saleId, $prodId, $item);
                $this->deductStock($prodId, $item['prod_qty']);
            }

            $this->conn->commit();
            return $saleId;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error saving sale: " . $e->getMessage());
            return false;
        }
    }

    // Insert new record sa sales table.
    private function insertSale($amountDue, $cash, $change) {
        $query = "INSERT INTO sales (sale_total_amount, sale_amount_paid, sale_change_amount)
                  VALUES (:amountDue, :cash, :change) RETURNING sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':amountDue' => $amountDue, ':cash' => $cash, ':change' => $change]);
        return $stmt->fetchColumn();
    }

    // Insert items into the sales_items table.
    private function insertSaleItem($saleId, $prodId, $item) {
        $query = "INSERT INTO sales_items (sale_id, prod_id, sale_item_qty, sale_unit_price, sale_line_total)
                  VALUES (:sale_id, :prod_id, :prod_qty, :unit_price, :line_total)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':sale_id' => $saleId,
            ':prod_id' => $prodId,
            ':prod_qty' => $item['prod_qty'],
            ':unit_price' => $item['prod_price'],
            ':line_total' => $item['prod_qty'] * $item['prod_price']
        ]);
    }

    // Ibawas ang quantity sa product table.
    private function deductStock($prodId, $prod_qty) {
        $query = "UPDATE product SET prod_qty = prod_qty - :prod_qty WHERE prod_id = :prod_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':prod_qty' => $prod_qty, ':prod_id' => $prodId]);
    }

    // Kuhaon ang sales transactions based on a filter.
    public function getSalesTransactions($filter = 'all') {
        $sql = "SELECT s.sale_id, s.sale_date, s.sale_total_amount FROM sales s";
        $where = "";
        switch ($filter) {
            case 'today':
                $where = " WHERE s.sale_date >= CURRENT_DATE AND s.sale_date < CURRENT_DATE + INTERVAL '1 day'";
                break;
            case 'week':
                $where = " WHERE s.sale_date >= date_trunc('week', CURRENT_DATE) AND s.sale_date < date_trunc('week', CURRENT_DATE) + INTERVAL '1 week'";
                break;
            case 'month':
                $where = " WHERE s.sale_date >= date_trunc('month', CURRENT_DATE) AND s.sale_date < date_trunc('month', CURRENT_DATE) + INTERVAL '1 month'";
                break;
        }
        $sql .= $where . " ORDER BY s.sale_date DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching sales transactions: " . $e->getMessage());
            return [];
        }
    }

    // To get the deets for a single sale.
    public function getSalesTransactionDetailsById($saleId) {
        $sqlSale = "SELECT s.* FROM sales s WHERE s.sale_id = :sale_id";
        $sqlItems = "SELECT si.*, p.prod_name FROM sales_items si JOIN product p ON si.prod_id = p.prod_id WHERE si.sale_id = :sale_id";

        try {
            // Get the sale details first.
            $stmtSale = $this->conn->prepare($sqlSale);
            $stmtSale->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
            $stmtSale->execute();
            $saleDetails = $stmtSale->fetch(PDO::FETCH_ASSOC);

            // If found, get the items. Of course.
            if ($saleDetails) {
                $stmtItems = $this->conn->prepare($sqlItems);
                $stmtItems->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
                $stmtItems->execute();
                $saleDetails['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            }
            return $saleDetails;
        } catch (PDOException $e) {
            error_log("Error fetching sales transaction details: " . $e->getMessage());
            return null;
        }
    }
}
?>