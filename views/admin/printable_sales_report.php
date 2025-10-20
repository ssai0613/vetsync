<?php
// Set the default timezone to Philippine time to ensure correct timestamps
date_default_timezone_set('Asia/Manila');

// This file generates a printer-friendly version of the sales report.
include_once(__DIR__ . '/../../controllers/admin_salesReportController.php');

$salesReportController = new SalesReportController();

// Get filter and date parameters from the URL
$filter = $_GET['filter'] ?? 'all_time';
$startDate = $_GET['startDate'] ?? null;
$endDate = $_GET['endDate'] ?? null;

// Fetch the report data using the existing controller method
$reportData = $salesReportController->getSalesReportData($filter, $startDate, $endDate);

// Extract data for easy access
$totalSales = $reportData['totalSales'];
$averageSales = $reportData['averageSales'];
$completedAppointments = $reportData['completedAppointments'];
$totalProductsSold = $reportData['totalProductsSold'];
$salesHistoryTransactions = $reportData['salesHistoryTransactions'];

// Helper to create a readable title for the report
function getReportTitle($filter, $startDate, $endDate) {
    switch ($filter) {
        case 'today': return 'Sales Report for Today';
        case 'yesterday': return 'Sales Report for Yesterday';
        case 'last_7_days': return 'Sales Report for the Last 7 Days';
        case 'last_15_days': return 'Sales Report for the Last 15 Days';
        case 'last_30_days': return 'Sales Report for the Last 30 Days';
        case 'this_week': return 'Sales Report for This Week';
        case 'this_month': return 'Sales Report for This Month';
        case 'this_year': return 'Sales Report for This Year';
        case 'custom':
            $start = date('M d, Y', strtotime($startDate));
            $end = date('M d, Y', strtotime($endDate));
            return "Sales Report from {$start} to {$end}";
        case 'all_time':
        default:
            return 'Comprehensive Sales Report (All Time)';
    }
}

$reportTitle = getReportTitle($filter, $startDate, $endDate);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printable Sales Report</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .print-container {
            max-width: 800px;
            margin: auto;
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #003249;
            padding-bottom: 15px;
        }
        .report-header h1 {
            margin: 0;
            color: #003249;
            font-size: 24px;
        }
        .report-header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555;
        }
        .summary-cards {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .summary-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            flex-grow: 1;
        }
        .summary-card .value {
            font-size: 22px;
            font-weight: 600;
            color: #003249;
        }
        .summary-card .topic {
            font-size: 13px;
            color: #6c757d;
        }
        .section-title {
            font-size: 20px;
            color: #003249;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #003249;
            color: white;
            font-weight: 600;
        }
        .report-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .print-button {
            padding: 10px 20px;
            background-color: #003249;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 20px auto;
            display: block;
        }

        @media print {
            body {
                padding: 0;
            }
            .print-button {
                display: none;
            }
            .summary-card {
                border: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="report-header">
            <h1>VetSync Sales Report</h1>
            <p><?= htmlspecialchars($reportTitle) ?></p>
            <p>Generated on: <?= date('M d, Y, h:i A') ?></p>
        </div>

        <button onclick="window.print()" class="print-button">Print Report</button>

        <h2 class="section-title">Summary</h2>
        <div class="summary-cards">
            <div class="summary-card">
                <div class="value">₱<?= htmlspecialchars(number_format($totalSales, 2)) ?></div>
                <div class="topic">Total Sales</div>
            </div>
            <div class="summary-card">
                <div class="value">₱<?= htmlspecialchars(number_format($averageSales, 2)) ?></div>
                <div class="topic">Average Sale</div>
            </div>
            <div class="summary-card">
                <div class="value"><?= htmlspecialchars($completedAppointments) ?></div>
                <div class="topic">Services Rendered</div>
            </div>
            <div class="summary-card">
                <div class="value"><?= htmlspecialchars($totalProductsSold) ?></div>
                <div class="topic">Products Sold</div>
            </div>
        </div>

        <h2 class="section-title">Transaction Details</h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Total (₱)</th>
                    <th>Paid (₱)</th>
                    <th>Change (₱)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($salesHistoryTransactions)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No transactions found for this period.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($salesHistoryTransactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['transaction_id']) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d h:i A', strtotime($transaction['transaction_date']))) ?></td>
                            <td><?= htmlspecialchars($transaction['transaction_type']) ?></td>
                            <td style="text-align: right;"><?= htmlspecialchars(number_format($transaction['total_amount'], 2)) ?></td>
                            <td style="text-align: right;"><?= htmlspecialchars(number_format($transaction['amount_paid'], 2)) ?></td>
                            <td style="text-align: right;"><?= htmlspecialchars(number_format($transaction['change_amount'], 2)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>
</html>