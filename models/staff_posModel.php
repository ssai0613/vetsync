<?php
include_once(__DIR__ . "/../db/database.php");
class PosModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Kuhaon ang all products na naay stock.
    public function getAllProducts() {
        $query = "SELECT prod_id, prod_name, prod_qty, prod_price, cat_id 
                  FROM product 
                  WHERE prod_qty > 0 
                  ORDER BY prod_name ASC"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function getAllCategories() {
        $query = "SELECT cat_id, cat_name FROM product_category ORDER BY cat_name ASC"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search for products, but only if they have stock.
    public function searchProducts($queryInput) {
        $query = "SELECT prod_id, prod_name, prod_qty, prod_price, cat_id 
                  FROM product 
                  WHERE prod_name ILIKE :queryInput AND prod_qty > 0 
                  ORDER BY prod_name ASC"; 
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $queryInput . '%';
        $stmt->execute([':queryInput' => $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    public function getProductById($id) {
        // This is to check the current stock when you're like changing the cart.
        // It gets the product even if stock is 0, 'cause it might be in the cart na.
        $stmt = $this->conn->prepare("SELECT prod_id, prod_name, prod_qty, prod_price, cat_id FROM product WHERE prod_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
?>