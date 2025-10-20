<?php

include_once(__DIR__ . '/../models/admin_inventoryModel.php');

if (isset($_SERVER['REQUEST_METHOD'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ajaxController = new InventoryController();
        $ajaxController->handlePostRequest();
        exit;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        $ajaxController = new InventoryController();
        $ajaxController->handleGetRequest();
        exit;
    }
}

class InventoryController {
    private $inventoryModel;

    public function __construct() {
        $this->inventoryModel = new Inventory();
    }

    public function getAllCategories() {
        return $this->inventoryModel->getAllCategories();
    }

    public function getAllProducts() {
        return $this->inventoryModel->getAllProducts();
    }

    public function getLowStockItemsForAlert($threshold) {
        return $this->inventoryModel->getLowStockProducts($threshold);
    }

    public function getInventoryHistory($filters = []) {
        return $this->inventoryModel->getInventoryHistory($filters);
    }

    public function handlePostRequest() {
        if (isset($_POST['action'])) {
            $responseMessage = '';
            switch ($_POST['action']) {
                case 'add':
                    $prodName = $_POST['prodName'];
                    $prodQty = $_POST['prodQty'];
                    $prodPrice = $_POST['prodPrice'];
                    $category = $_POST['category'];
                    if (empty($prodName) || $prodQty === '' || $prodPrice === '' || empty($category)) {
                        $responseMessage = "Error: All fields are required.";
                    } elseif (!is_numeric($prodQty) || $prodQty < 0) {
                        $responseMessage = "Error: The stock quantity must be a non-negative number.";
                    } elseif (!is_numeric($prodPrice) || $prodPrice < 0) {
                        $responseMessage = "Error: The price must be a non-negative number.";
                    } else {
                        if ($this->inventoryModel->addProduct($prodName, $prodQty, $prodPrice, $category)) {
                            $responseMessage = "Product added successfully.";
                        } else {
                            $responseMessage = "Failed to add product.";
                        }
                    }
                    break;

                case 'update':
                    $prodID = $_POST['prodID'];
                    $prodName = $_POST['prodName'];
                    $prodQty = $_POST['prodQty'];
                    $prodPrice = $_POST['prodPrice'];
                    $category = $_POST['category'];
                     if (empty($prodID) || empty($prodName) || $prodQty === '' || $prodPrice === '' || empty($category)) {
                        $responseMessage = "Error: All fields are required for an update.";
                    } elseif (!is_numeric($prodQty) || $prodQty < 0) {
                        $responseMessage = "Error: Stock quantity must be non-negative number.";
                    } elseif (!is_numeric($prodPrice) || $prodPrice < 0) {
                        $responseMessage = "Error: Product price needs to be non-negative.";
                    } else {
                        if ($this->inventoryModel->updateProduct($prodID, $prodName, $prodQty, $prodPrice, $category)) {
                            $responseMessage = "Product updated successfully.";
                        } else {
                            $responseMessage = "Failed to update product.";
                        }
                    }
                    break;

                case 'remove':
                    $prodID = $_POST['prodID'];
                    if (empty($prodID)) {
                        $responseMessage = "Error: Product ID is required for removal.";
                    } else {
                        if ($this->inventoryModel->removeProduct($prodID)) {
                            $responseMessage = "Product removed successfully.";
                        } else {
                            $responseMessage = "Failed to remove product.";
                        }
                    }
                    break;
                default:
                    $responseMessage = "Error: Invalid POST action.";
                    break;
            }
            echo $responseMessage;
        }
    }

    public function handleGetRequest() {
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'search':
                    $queryInput = $_GET['query'] ?? '';
                    $results = $this->inventoryModel->searchProducts($queryInput);
                    $output = '';
                    if (empty($results)) {
                         $output = "<tr><td colspan='5'>No products found.</td></tr>";
                    } else {
                        foreach ($results as $product) {
                            $categoryName = isset($product['cat_name']) ? htmlspecialchars($product['cat_name']) : 'N/A';
                            $output .= "<tr data-cat-id='" . htmlspecialchars($product['cat_id']) . "'>";
                            $output .= "<td>" . htmlspecialchars($product['prod_id']) . "</td>";
                            $output .= "<td>" . htmlspecialchars($product['prod_name']) . "</td>";
                            $output .= "<td>" . htmlspecialchars($product['prod_qty']) . "</td>";
                            $output .= "<td>₱ " . number_format($product['prod_price'], 2) . "</td>";
                            $output .= "<td>" . $categoryName . "</td>";
                            $output .= "</tr>";
                        }
                    }
                    echo $output;
                    break;

                case 'getHistory':
                    $filters = [
                        'prod_id' => $_GET['prod_id'] ?? '',
                        'change_type' => $_GET['change_type'] ?? '',
                        'start_date' => $_GET['start_date'] ?? '',
                        'end_date' => $_GET['end_date'] ?? '',
                        'product_name' => $_GET['product_name'] ?? ''
                    ];
                    $history = $this->getInventoryHistory($filters);
                    $output = '';
                    if (empty($history)) {
                         $output = "<tr><td colspan='9'>No history records found matching your criteria.</td></tr>";
                    } else {
                        foreach ($history as $record) {
                            $output .= "<tr>";
                            $output .= "<td>" . htmlspecialchars($record['history_id']) . "</td>";
                            $output .= "<td>" . htmlspecialchars($record['prod_id']) . "</td>";
                            $output .= "<td>" . htmlspecialchars($record['prod_name']) . "</td>";
                            $output .= "<td>" . htmlspecialchars($record['change_type']) . "</td>";
                            $output .= "<td>" . htmlspecialchars($record['old_qty'] ?? 'N/A') . "</td>";
                            $output .= "<td>" . htmlspecialchars($record['new_qty'] ?? 'N/A') . "</td>";
                            $output .= "<td>₱ " . htmlspecialchars(number_format($record['old_price'] ?? 0, 2)) . "</td>";
                            $output .= "<td>₱ " . htmlspecialchars(number_format($record['new_price'] ?? 0, 2)) . "</td>";
                            $output .= "<td>" . htmlspecialchars($record['qty_change'] ?? 'N/A') . "</td>";
                            $output .= "<td>" . htmlspecialchars($record['change_timestamp']) . "</td>";
                            $output .= "</tr>";
                        }
                    }
                    echo $output;
                    break;
                default:
                    echo "Error: Invalid GET action.";
                    break;
            }
        }
    }
}
?>