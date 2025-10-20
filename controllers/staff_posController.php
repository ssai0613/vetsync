<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

include_once(__DIR__ . '/../models/staff_posModel.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $ajaxController = new PosController();
    $ajaxController->handleGetRequest();
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $ajaxController = new PosController();
    $ajaxController->handlePostRequest();
    header("Location: ../views/staff/staff_POS.php"); 
    exit;
}

class PosController {

    private $posModel;

    public function __construct() {
        $this->posModel = new PosModel();  
    }

    public function getAllProducts() {
        return $this->posModel->getAllProducts();
    }

    public function getAllCategories() {
        return $this->posModel->getAllCategories();
    }

    public function handleGetRequest() {
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'search':
                    $queryInput = $_GET['query'] ?? '';
                    $results = $this->posModel->searchProducts($queryInput);
                    $output = '';
                    if (empty($results)) {
                        $output = "<tr><td colspan='5' style='text-align:center; padding:10px;'>No products loaded.</td></tr>";
                    } else {
                        foreach ($results as $product) {
                            $output .= "<tr data-cat-id='" . htmlspecialchars($product['cat_id']) . "'>";
                            $output .= "<td>" . htmlspecialchars($product['prod_id']) . "</td>";
                            $output .= "<td>" . htmlspecialchars($product['prod_name']) . "</td>";
                            $output .= "<td>" . htmlspecialchars($product['prod_qty']) . "</td>";
                            $output .= "<td>₱ " . number_format($product['prod_price'], 2) . "</td>";
                            $output .= "<td class='action-icons'>
                                        <form method='POST' action='/vetsync/controllers/staff_posController.php'>
                                            <input type='hidden' name='action' value='addToCart'>
                                            <input type='hidden' name='prod_id' value='" . htmlspecialchars($product['prod_id']) . "'>
                                            <input type='hidden' name='prod_name' value='" . htmlspecialchars($product['prod_name']) . "'>
                                            <input type='hidden' name='prod_price' value='" . htmlspecialchars($product['prod_price']) . "'>
                                            <button type='submit'>Add to Cart</button>
                                        </form>
                                      </td>";
                            $output .= "</tr>";
                        }
                    }
                    echo $output;
                    break;
                default:
                    echo "<tr><td colspan='5' style='text-align:center; padding:10px;'>Error: Invalid GET action.</td></tr>";
                    break;    
            }
        }
    }

    public function handlePostRequest() {
        $action = $_POST['action'] ?? '';

        if ($action === 'addToCart' && isset($_POST['prod_id'])) {
            $id = $_POST['prod_id'];
            $name = $_POST['prod_name'];
            $price = $_POST['prod_price'];

            $product = $this->posModel->getProductById($id); 
            $available_stock = $product ? (int)$product['prod_qty'] : 0;

            if ($available_stock <= 0) {
                $_SESSION['pos_message'] = ['text' => "Sorry, " . htmlspecialchars($name) . " is out of stock.", 'type' => 'error'];
            } else {
                if (isset($_SESSION['cart'][$id])) {
                    if (($_SESSION['cart'][$id]['prod_qty'] + 1) <= $available_stock) {
                        $_SESSION['cart'][$id]['prod_qty'] += 1;
                    } else {
                         $_SESSION['pos_message'] = ['text' => "Cannot add more " . htmlspecialchars($name) . ". Only " . $available_stock . " left.", 'type' => 'warning'];
                    }
                } else {
                    $_SESSION['cart'][$id] = ['prod_name' => $name, 'prod_price' => $price, 'prod_qty' => 1, 'stock' => $available_stock];
                }
            }
        } elseif ($action === 'changeQty' && isset($_POST['change_qty_id'], $_POST['change_type'])) {
            $id = $_POST['change_qty_id'];
            $type = $_POST['change_type'];

            if (isset($_SESSION['cart'][$id])) {
                $product = $this->posModel->getProductById($id); 
                $available_stock = $product ? (int)$product['prod_qty'] : 0; 

                if ($type === "increase") {
                    if (($_SESSION['cart'][$id]['prod_qty'] + 1) <= $available_stock) {
                        $_SESSION['cart'][$id]['prod_qty'] += 1;
                    } else {
                         $_SESSION['pos_message'] = ['text' => "Cannot add more " . htmlspecialchars($_SESSION['cart'][$id]['prod_name']) . ". Only " . $available_stock . " available.", 'type' => 'warning'];
                    }
                } elseif ($type === "decrease") {
                    $_SESSION['cart'][$id]['prod_qty'] -= 1;
                    if ($_SESSION['cart'][$id]['prod_qty'] <= 0) {
                        unset($_SESSION['cart'][$id]);
                    }
                }
            }
        } elseif ($action === 'removeItem' && isset($_POST['remove_id'])) {
            unset($_SESSION['cart'][$_POST['remove_id']]);
        }
    }
}

$posPageController = new PosController(); 
?>