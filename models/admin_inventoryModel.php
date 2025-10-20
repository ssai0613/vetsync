<?php
include_once(__DIR__ . "/../db/database.php");
class Inventory {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllCategories() {
        $query = "SELECT * FROM product_category ORDER BY cat_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllProducts() {
        $query = "SELECT p.*, pc.cat_name FROM product p LEFT JOIN product_category pc ON p.cat_id = pc.cat_id ORDER BY p.prod_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($prodName, $prodQty, $prodPrice, $category) {
        try {
            $this->conn->beginTransaction(); 
            $query = "INSERT INTO product (prod_name, prod_qty, prod_price, cat_id) VALUES (:prodName, :prodQty, :prodPrice, :category)";
            $stmt = $this->conn->prepare($query);
            $success = $stmt->execute([':prodName' => $prodName, ':prodQty' => $prodQty, ':prodPrice' => $prodPrice, ':category' => $category]);
            if ($success) {
                $newProdID = $this->conn->lastInsertId();
                $logSuccess = $this->logInventoryChange($newProdID, 'added', null, $prodQty, null, $prodPrice, $prodQty);
                if ($logSuccess) { $this->conn->commit(); return true; } 
                else { $this->conn->rollBack(); error_log("History logging failed during addProduct."); return false; }
            } else { $this->conn->rollBack(); error_log("Product insert failed. Ambot ngano."); return false; }
        } catch (Exception $e) { $this->conn->rollBack(); error_log("Exception caught during addProduct: " . $e->getMessage()); return false; }
    }

    public function updateProduct($prodID, $prodName, $prodQty, $prodPrice, $category) {
        try {
            $this->conn->beginTransaction(); 
            $currentProductQuery = "SELECT prod_qty, prod_price FROM product WHERE prod_id = :prodID";
            $currentProductStmt = $this->conn->prepare($currentProductQuery);
            $currentProductStmt->execute([':prodID' => $prodID]);
            $currentProduct = $currentProductStmt->fetch(PDO::FETCH_ASSOC);
            if (!$currentProduct) { $this->conn->rollBack(); return false; }
            $oldQty = $currentProduct['prod_qty']; $oldPrice = $currentProduct['prod_price'];
            
            $query = "UPDATE product SET prod_name = :prodName, prod_qty = :prodQty, prod_price = :prodPrice, cat_id = :category WHERE prod_id = :prodID";
            $stmt = $this->conn->prepare($query);
            $success = $stmt->execute([':prodID' => $prodID, ':prodName' => $prodName, ':prodQty' => $prodQty, ':prodPrice' => $prodPrice, ':category' => $category]);
            
            if ($success) {
                $changeType = 'updated_details'; $qtyChange = $prodQty - ($oldQty ?? 0);
                $qtyChanged = ($prodQty != $oldQty); $priceChanged = ($prodPrice != $oldPrice);
                if ($qtyChanged && $priceChanged) { $changeType = 'updated_qty_price'; } 
                elseif ($qtyChanged) { $changeType = 'updated_qty'; } 
                elseif ($priceChanged) { $changeType = 'updated_price'; }
                
                if ($qtyChanged || $priceChanged) {
                     $logSuccess = $this->logInventoryChange($prodID, $changeType, $oldQty, $prodQty, $oldPrice, $prodPrice, $qtyChange);
                     if ($logSuccess) { $this->conn->commit(); return true; } 
                     else { $this->conn->rollBack(); error_log("History logging failed for updateProduct. Sad."); return false; }
                } else { $this->conn->commit(); return true; }
            } else { $this->conn->rollBack(); error_log("Product update failed for updateProduct."); return false; }
        } catch (Exception $e) { $this->conn->rollBack(); error_log("Exception caught during updateProduct: " . $e->getMessage()); return false; }
    }

    public function removeProduct($prodID) {
         try {
            $this->conn->beginTransaction(); 

            // 1. Kuhaon ang product details before i-delete. For logging purposes.
            $productQuery = "SELECT prod_name, prod_qty, prod_price FROM product WHERE prod_id = :prodID";
            $productStmt = $this->conn->prepare($productQuery);
            $productStmt->execute([':prodID' => $prodID]);
            $productDetails = $productStmt->fetch(PDO::FETCH_ASSOC);
            if (!$productDetails) {
                $this->conn->rollBack(); return false; 
            }

            // 2. I-log muna ang removal. Para naay record.
            $logSuccess = $this->logInventoryChange($prodID, 'removed', $productDetails['prod_qty'], 0, $productDetails['prod_price'], null, -($productDetails['prod_qty']));
            if (!$logSuccess) {
                $this->conn->rollBack(); return false;
            }

            // 3. And then, i-delete na jud ang product.
            $deleteQuery = "DELETE FROM product WHERE prod_id = :prodID";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteSuccess = $deleteStmt->execute([':prodID' => $prodID]);

            if ($deleteSuccess && $deleteStmt->rowCount() > 0) {
                $this->conn->commit(); return true;
            } else {
                $this->conn->rollBack(); return false;
            }
        } catch (Exception $e) {
             $this->conn->rollBack();
             error_log("Exception caught during removeProduct: " . $e->getMessage());
             return false;
        }
    }

    public function getLowStockProducts($threshold) {
        $query = "SELECT prod_id, prod_name, prod_qty FROM product WHERE prod_qty <= :threshold ORDER BY prod_qty ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function logInventoryChange($prodID, $changeType, $oldQty, $newQty, $oldPrice, $newPrice, $qtyChange) {
        $query = "INSERT INTO inventory_history (prod_id, change_type, old_qty, new_qty, old_price, new_price, qty_change)
                  VALUES (:prod_id, :change_type, :old_qty, :new_qty, :old_price, :new_price, :qty_change)";
        $stmt = $this->conn->prepare($query);
        
        try {
            return $stmt->execute([
                ':prod_id' => $prodID, ':change_type' => $changeType, 
                ':old_qty' => is_numeric($oldQty) ? (int)$oldQty : null, 
                ':new_qty' => is_numeric($newQty) ? (int)$newQty : null,
                ':old_price' => is_numeric($oldPrice) ? (float)$oldPrice : null, 
                ':new_price' => is_numeric($newPrice) ? (float)$newPrice : null, 
                ':qty_change' => is_numeric($qtyChange) ? (int)$qtyChange : null
            ]);
        } catch (PDOException $e) {
            error_log("PDOException in logInventoryChange: " . $e->getMessage());
            return false;
        }
    }

    public function searchProducts($queryInput) {
        $query = "SELECT p.*, pc.cat_name 
                  FROM product p LEFT JOIN product_category pc ON p.cat_id = pc.cat_id
                  WHERE p.prod_name ILIKE :queryInput ORDER BY p.prod_id ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $queryInput . '%';
        $stmt->execute([':queryInput' => $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInventoryHistory($filters = []) {
        $query = "SELECT ih.history_id, ih.prod_id, ih.change_type, ih.old_qty, ih.new_qty, ih.old_price, 
                         ih.new_price, ih.qty_change, 
                         TO_CHAR(ih.change_timestamp, 'Mon DD, YYYY HH12:MI AM') AS change_timestamp,
                         p.prod_name
                  FROM inventory_history ih JOIN product p ON ih.prod_id = p.prod_id";

        $whereClauses = []; 
        $bindParams = [];

        if (!empty($filters['prod_id'])) { $whereClauses[] = "ih.prod_id = :prod_id"; $bindParams[':prod_id'] = $filters['prod_id']; }
        if (!empty($filters['change_type'])) { $whereClauses[] = "ih.change_type = :change_type"; $bindParams[':change_type'] = $filters['change_type']; }
        if (!empty($filters['start_date'])) { $whereClauses[] = "DATE(ih.change_timestamp) >= :start_date"; $bindParams[':start_date'] = $filters['start_date']; }
        if (!empty($filters['end_date'])) { $whereClauses[] = "DATE(ih.change_timestamp) <= :end_date"; $bindParams[':end_date'] = $filters['end_date']; }
        if (!empty($filters['product_name'])) { $whereClauses[] = "p.prod_name ILIKE :product_name"; $bindParams[':product_name'] = '%' . $filters['product_name'] . '%'; }

        if (!empty($whereClauses)) { $query .= " WHERE " . implode(" AND ", $whereClauses); }
        
        $query .= " ORDER BY ih.change_timestamp DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($bindParams);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>