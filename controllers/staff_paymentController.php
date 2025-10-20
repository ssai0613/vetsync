<?php
session_start();
include_once(__DIR__ . '/../models/staff_paymentModel.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amountDue = $_POST['amount_due'];
    $cash = $_POST['cash'];
    $change = $_POST['change'];

    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $paymentModel = new PaymentModel();
    $saleId = $paymentModel->saveSale($amountDue, $cash, $change, $_SESSION['cart']);

    if ($saleId) {
        unset($_SESSION['cart']); 
        echo json_encode([
            "status" => "success",
            "sale_id" => $saleId
        ]);
    } else {
        echo json_encode([
            "status" => "error"
        ]);
    }
}
?>