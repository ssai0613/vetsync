<?php

include_once(__DIR__ . '/../models/admin_appointmentModel.php');
include_once(__DIR__ . '/../models/staff_paymentModel.php');

date_default_timezone_set('Asia/Manila'); 

$controller = new TransactionHistoryController();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $controller->handleGetRequest();
}

class TransactionHistoryController
{
    private $appointmentModel; 
    private $paymentModel;       

    public function __construct()
    {
        $this->appointmentModel = new Appointment();
        $this->paymentModel = new PaymentModel();
    }

    public function handleGetRequest()
    {
        $action = $_GET['action'];

        switch ($action) {
            case 'getAppointmentTransactions':
                header('Content-Type: text/html');
                $filter = $_GET['filter'] ?? 'all';
                try {
                    $transactions = $this->appointmentModel->getAppointmentTransactions($filter);
                    $this->renderAppointmentTransactionTableRows($transactions);
                } catch (Exception $e) {
                    error_log("Error fetching appointment transactions: " . $e->getMessage());
                    echo "<tr><td colspan='8'>Error: Could not load appointment transactions.</td></tr>";
                }
                break;

            case 'getTransactionDetails':
                header('Content-Type: application/json');
                if (isset($_GET['transaction_id'])) {
                    $transactionId = $_GET['transaction_id'];
                    try {
                        $transactionDetails = $this->appointmentModel->getTransactionDetailsById($transactionId);
                        echo json_encode($transactionDetails);
                    } catch (Exception $e) {
                        error_log("Error fetching appointment details: " . $e->getMessage());
                        echo json_encode(['error' => 'Error: Could not load appointment details.']);
                    }
                } else {
                    echo json_encode(['error' => 'Error: Appointment Transaction ID not provided.']);
                }
                break;

            case 'getSalesTransactions':
                header('Content-Type: text/html');
                $filter = $_GET['filter'] ?? 'all';
                try {
                    $salesTransactions = $this->paymentModel->getSalesTransactions($filter);
                    $this->renderSalesTransactionTableRows($salesTransactions);
                } catch (Exception $e) {
                    error_log("Error fetching sales transactions: " . $e->getMessage());
                    echo "<tr><td colspan='4'>Error: Could not load sales transactions.</td></tr>";
                }
                break;

            case 'getSalesTransactionDetails':
                header('Content-Type: application/json');
                if (isset($_GET['sale_id'])) {
                    $saleId = $_GET['sale_id'];
                    try {
                        $salesDetails = $this->paymentModel->getSalesTransactionDetailsById($saleId);
                        echo json_encode($salesDetails ?? ['error' => 'Error: Sales transaction not found.']);
                    } catch (Exception $e) {
                        error_log("Error fetching sales transaction details: " . $e->getMessage());
                        echo json_encode(['error' => 'Error: Could not load sales details.']);
                    }
                } else {
                    echo json_encode(['error' => 'Error: Sale ID not provided.']);
                }
                break;

            default:
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Error: Unknown action specified.']);
                break;
        }
    }

     private function renderAppointmentTransactionTableRows($transactions) {
         if (!is_array($transactions) || empty($transactions)) {
             echo "<tr><td colspan='8'>No appointment transactions found.</td></tr>";
             return;
         }

         $rowNumber = 1;
         foreach ($transactions as $transaction) {
            echo "<tr>";
            echo "<td>" . $rowNumber++ . "</td>";
            echo "<td>" . htmlspecialchars($transaction['appointment_id'] ?? 'N/A') . "</td>";
            
            $transactionDate = $transaction['transaction_date'] ?? null;
            echo "<td>" . htmlspecialchars($transactionDate ? date('Y-m-d H:i', strtotime($transactionDate)) : 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($transaction['pet_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($transaction['owner_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($transaction['purpose_of_visit'] ?? 'N/A') . "</td>";

            $totalAmount = $transaction['total_amount'] ?? 0;
            echo "<td>₱ " . htmlspecialchars(number_format($totalAmount, 2)) . "</td>";

            $transactionId = $transaction['transaction_id'] ?? null;
            echo "<td>";
            if ($transactionId !== null) {
                echo "<button class='action-btn view-transaction-btn' data-transaction-type='appointment' data-transaction-id='" . htmlspecialchars($transactionId) . "'>View Transaction</button>";
            } else {
                echo "N/A";
            }
            echo "</td>";
            echo "</tr>";
        }
     }

     private function renderSalesTransactionTableRows($salesTransactions) {
        if (!is_array($salesTransactions) || empty($salesTransactions)) {
            echo "<tr><td colspan='4'>No sales transactions found.</td></tr>";
            return;
        }

        $rowNumber = 1;
        foreach ($salesTransactions as $sale) {
           echo "<tr>";
           echo "<td>" . $rowNumber++ . "</td>";
           echo "<td>" . htmlspecialchars($sale['sale_date'] ? date('Y-m-d H:i', strtotime($sale['sale_date'])) : 'N/A') . "</td>";

           $saleTotalAmount = $sale['sale_total_amount'] ?? 0;
           echo "<td>₱ " . htmlspecialchars(number_format($saleTotalAmount, 2)) . "</td>";

           $saleId = $sale['sale_id'] ?? null;
           echo "<td>";
           if ($saleId !== null) {
               echo "<button class='action-btn view-transaction-btn' data-transaction-type='sale' data-transaction-id='" . htmlspecialchars($saleId) . "'>View Transaction</button>";
           } else {
               echo "N/A";
           }
           echo "</td>";
           echo "</tr>";
       }
     }
}
?>