<?php
include_once(__DIR__ . '/../../db/database.php');

//time
date_default_timezone_set('Asia/Manila');


if (!isset($_GET['sale_id'])) {
    echo "No sale ID.";
    exit;
}

$saleId = $_GET['sale_id'];

$db = new Database();
$conn = $db->getConnection();

// kuha sa sale summary details
$saleStmt = $conn->prepare("SELECT * FROM sales WHERE sale_id = :id");
$saleStmt->execute([':id' => $saleId]);
$sale = $saleStmt->fetch(PDO::FETCH_ASSOC);


$itemStmt = $conn->prepare("
  SELECT si.sale_id, si.sale_item_qty, si.sale_unit_price, si.sale_line_total, p.prod_name
  FROM sales_items AS si
  LEFT JOIN product AS p ON si.prod_id = p.prod_id
  WHERE si.sale_id = :sale_id
");
$itemStmt->execute([':sale_id' => $saleId]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals based on new VAT logic
$grossTotal = 0;
foreach ($items as $item) {
    $grossTotal += $item['sale_line_total'];
}

$finalTotal = $sale['sale_total_amount'];
$discountAmount = $grossTotal - $finalTotal;

// Calculate VAT breakdown from the final discounted total
$vatDivisor = 1.12;
$vatableSales = $finalTotal / $vatDivisor;
$vatAmount = $finalTotal - $vatableSales;
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Receipt</title>
  <link rel="stylesheet" href="css/staff_receipt.css?v=<?= time(); ?>">
</head>
<body>
  <div class="receipt-container">
    <button class="print-button" onclick="window.print()">Print Receipt</button>
    
    <div class="receipt-header">
      <h2>Mactan Veterinary Clinic</h2>
      <p>ML Tan Building S. Osmeña St.</p>
      <p>Gun-ob, Lapu-Lapu City</p>
      <p>Contact: 09173248492 / 520-6249</p>
      <hr>
    </div>

    <div class="receipt-body">
      <p><strong>Sale ID:</strong> <?= htmlspecialchars($sale['sale_id']) ?></p>
      <p><strong>Date:</strong> <?= date("M d, Y h:i A", strtotime($sale['sale_date'])) ?></p>
      
      <table class="receipt-table">
        <thead>
          <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($items)): ?>
            <tr><td colspan="4">No items found.</td></tr>
          <?php else: ?>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['prod_name']) ?></td>
                <td><?= htmlspecialchars($item['sale_item_qty']) ?></td>
                <td><?= number_format($item['sale_unit_price'], 2) ?></td>
                <td><?= number_format($item['sale_line_total'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <hr>
      <div class="totals">
        <?php if ($discountAmount > 0.005): // Only show discount if it exists ?>
            <p><strong>Discount:</strong> - ₱<?= number_format($discountAmount, 2) ?></p>
        <?php endif; ?>
        <p><strong>Vatable Sales:</strong> ₱<?= number_format($vatableSales, 2) ?></p>
        <p><strong>VAT Amount (12%):</strong> ₱<?= number_format($vatAmount, 2) ?></p>
        <hr>
        <p><strong>Total Amount Due:</strong> ₱<?= number_format($finalTotal, 2) ?></p>
        <p><strong>Cash:</strong> ₱<?= number_format($sale['sale_amount_paid'], 2) ?></p>
        <p><strong>Change:</strong> ₱<?= number_format($sale['sale_change_amount'], 2) ?></p>
      </div>
    </div>

    <div class="receipt-footer">
      <p>Thank you for your purchase!</p>
      <p>Mactan Veterinary Clinic</p>
    </div>
  </div>

 
<div class="button">
  <button class="print-button" onclick="window.location.href='staff_POS.php'">Done</button>
</div>
</body>
</html>