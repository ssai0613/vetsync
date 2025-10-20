<?php

include_once(__DIR__ . '/../../controllers/admin_salesReportController.php');

$salesReportController = new SalesReportController();

// Get filter from query parameters, set default, and get custom dates.
$filter = $_GET['filter'] ?? 'all_time';
$startDate = $_GET['startDate'] ?? null;
$endDate = $_GET['endDate'] ?? null;


// The controller's handleRequest() function will exit if it's an AJAX detail request.
// So, we only fetch the full report data if it's a normal page load.
if (!isset($_GET['action']) || ($_GET['action'] !== 'getProductSaleDetails' && $_GET['action'] !== 'getServiceSaleDetails')) {
    $reportData = $salesReportController->getSalesReportData($filter, $startDate, $endDate);

    // Extract data for easier access in the view.
    $filter = $reportData['filter']; // Use the validated filter from the controller.
    $startDate = $reportData['startDate'];
    $endDate = $reportData['endDate'];
    $totalSales = $reportData['totalSales'];
    $averageSales = $reportData['averageSales'];
    $completedAppointments = $reportData['completedAppointments'];
    $totalProductsSold = $reportData['totalProductsSold'];
    $productSalesForChart = $reportData['productSalesForChart'];
    $serviceSalesForChart = $reportData['serviceSalesForChart'];
    $salesHistoryTransactions = $reportData['salesHistoryTransactions'];
} else {
    // If it is an AJAX request, initialize variables to avoid PHP notices in case of any output.
     $reportData = [];
     $salesHistoryTransactions = [];
     $filter = $_GET['filter'] ?? 'all_time';
     $startDate = $_GET['startDate'] ?? null;
     $endDate = $_GET['endDate'] ?? null;
     $totalSales = 0;
     $averageSales = 0;
     $completedAppointments = 0;
     $totalProductsSold = 0;
     $productSalesForChart = [];
     $serviceSalesForChart = [];
}


// Prepare data for Chart.js.
$productChartLabels = [];
$productChartData = [];
if (!empty($productSalesForChart)) {
    foreach ($productSalesForChart as $item) {
        $productChartLabels[] = htmlspecialchars($item['prod_name']);
        $productChartData[] = $item['total_sales_amount'];
    }
}

$serviceChartLabels = [];
$serviceChartData = [];
if (!empty($serviceSalesForChart)) {
    foreach ($serviceSalesForChart as $item) {
        $serviceChartLabels[] = htmlspecialchars($item['service_name']);
        $serviceChartData[] = $item['total_service_amount'];
    }
}

// Function to get display text for the filter dropdown.
function getFilterDisplayName($filterValue) {
    $names = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 Days',
        'last_15_days' => 'Last 15 Days',
        'last_30_days' => 'Last 30 Days',
        'this_week' => 'This Week',
        'this_month' => 'This Month',
        'this_year' => 'This Year',
        'all_time' => 'All Time',
        'custom' => 'Custom Range'
    ];
    return $names[$filterValue] ?? 'All Time';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="css/salesReport.css?v=<?= time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script src="/../../utils/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="sidebar">
  <div class="logo_details">
    <i class='bx bx-code-alt'></i>
    <div class="logo_name">VetSync</div>
  </div>
  
  <ul>
    <li><a href="dashboard.php"><i class='bx bxs-dashboard'></i><span class="links_name">Dashboard</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="appointment.php"><i class='bx bxs-calendar'></i><span class="links_name">Appointments</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="inventory.php"><i class='bx bx-cart'></i><span class="links_name">Inventory</span></a></li>
    <li><a href="services.php"><i class='bx bx-notepad'></i><span class="links_name">Services</span></a></li>
    <li><a href="petProfile.php"><i class='bx bxs-dog'></i><span class="links_name">Pet Profile</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="salesReport.php" class="active"><i class='bx bxs-report'></i><span class="links_name">Sales Report</span></a></li>
    <li><a href="transactionHistory.php"><i class='bx bxs-report'></i><span class="links_name">Transaction History</span></a></li>
    <br><br><br>
    <li class="login"><a href="../logout.php"><span class="links_name login_out">Logout</span><i class='bx bx-log-out' id="log_out"></i></a></li>
  </ul>
</div>
<section class="home_section">
    <div class="topbar">
        <div class="toggle"><i class="bx bx-menu" id="btn"></i></div>
        <div class="user_wrapper">
            <div class="user-menu" id="user-toggle"><i class='bx bx-user' id="profile-icon"></i><span class="user-name">Admin</span></div>
            <div class="dropdown-menu" id="user-dropdown" style="display: none;">
                </div>
        </div>
    </div>

    <div class="main_content">
        <div class="container">
             <div class="card">
                <h1>Sales Report</h1>

                 <div class="sales-report-controls">
                     <select id="filterByDate">
                         <option value="all_time" <?= ($filter == 'all_time') ? 'selected' : ''; ?>>All Time</option>
                         <option value="today" <?= ($filter == 'today') ? 'selected' : ''; ?>>Today</option>
                         <option value="yesterday" <?= ($filter == 'yesterday') ? 'selected' : ''; ?>>Yesterday</option>
                         <option value="last_7_days" <?= ($filter == 'last_7_days') ? 'selected' : ''; ?>>Last 7 Days</option>
                         <option value="last_15_days" <?= ($filter == 'last_15_days') ? 'selected' : ''; ?>>Last 15 Days</option>
                         <option value="last_30_days" <?= ($filter == 'last_30_days') ? 'selected' : ''; ?>>Last 30 Days</option>
                         <option value="this_week" <?= ($filter == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                         <option value="this_month" <?= ($filter == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                         <option value="this_year" <?= ($filter == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                         <option value="custom" <?= ($filter == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                     </select>

                     <div id="customDateRange" style="<?= ($filter == 'custom') ? 'display: flex;' : 'display: none;'; ?> gap: 10px; align-items: center;">
                         <label for="startDate">From:</label>
                         <input type="date" id="startDate" value="<?= htmlspecialchars($startDate ?? ''); ?>">
                         <label for="endDate">To:</label>
                         <input type="date" id="endDate" value="<?= htmlspecialchars($endDate ?? ''); ?>">
                         <button id="applyDateFilter" class="btn-generate">Apply</button>
                     </div>
                     <button class="btn-generate" id="generateReportBtn">Generate Report</button>
                 </div>

                <div class="card-boxes">
                     <div class="box">
                         <div class="right_side">
                             <div class="numbers">₱ <?= htmlspecialchars(number_format($totalSales, 2)); ?></div>
                             <div class="box_topic">Total Sales</div>
                         </div>
                         <i class='bx bxs-cart-add'></i>
                     </div>
                     <div class="box">
                         <div class="right_side">
                             <div class="numbers">₱ <?= htmlspecialchars(number_format($averageSales, 2)); ?></div>
                             <div class="box_topic">Average Sale</div>
                         </div>
                         <i class='bx bx-money'></i>
                     </div>
                     <div class="box">
                         <div class="right_side">
                             <div class="numbers"><?= htmlspecialchars($completedAppointments); ?></div>
                             <div class="box_topic">Completed Appointments</div>
                         </div>
                         <i class='bx bxs-check-circle'></i>
                     </div>
                     <div class="box">
                         <div class="right_side">
                             <div class="numbers"><?= htmlspecialchars($totalProductsSold); ?></div>
                             <div class="box_topic">Products Sold</div>
                         </div>
                         <i class='bx bxs-package'></i>
                     </div>
                </div>

                <div class="charts">
                     <div class="chart-container">
                         <canvas id="productSalesChart"></canvas>
                         <div class="total-sales-display">
                             <h3>Total Product Sales: ₱ <?= htmlspecialchars(number_format(array_sum($productChartData), 2)); ?></h3>
                         </div>
                     </div>
                     <div class="chart-container">
                         <canvas id="serviceSalesChart"></canvas>
                         <div class="total-sales-display">
                             <h3>Total Service Sales: ₱ <?= htmlspecialchars(number_format(array_sum($serviceChartData), 2)); ?></h3>
                         </div>
                     </div>
                </div>
                
                <div class="sales-history-section">
                    <h2>Sales Transaction History</h2>
                    <div class="table-wrapper">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Total Amount</th>
                                    <th>Amount Paid</th>
                                    <th>Change</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($salesHistoryTransactions)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">No sales transactions found for this period.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($salesHistoryTransactions as $transaction): ?>
                                        <tr data-transaction-id="<?= $transaction['transaction_id']; ?>" data-transaction-type="<?= htmlspecialchars($transaction['transaction_type']); ?>">
                                            <td><?= htmlspecialchars($transaction['transaction_id']); ?></td>
                                            <td><?= htmlspecialchars(date('M d, Y, h:i A', strtotime($transaction['transaction_date']))); ?></td>
                                            <td><?= htmlspecialchars($transaction['transaction_type']); ?></td>
                                            <td>₱ <?= htmlspecialchars(number_format($transaction['total_amount'], 2)); ?></td>
                                            <td>₱ <?= htmlspecialchars(number_format($transaction['amount_paid'], 2)); ?></td>
                                            <td>₱ <?= htmlspecialchars(number_format($transaction['change_amount'], 2)); ?></td>
                                            <td><button class="details-btn btn-generate">View</button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>© 2025 VetSync. All rights reserved. Software developed by <span class="fusion">COGTAS, LINGO & SEGOVIA</span></p>
        </div>
    </footer>
</section>

<div id="detailsModal" class="modal">
     <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Transaction Details</h2>
            <span class="close-button">&times;</span>
        </div>
        <div class="modal-body">
             <div id="modalDetails"><p>Loading...</p></div>
        </div>
     </div>
 </div>

<script>
$(document).ready(function() {
    // --- Helper function for HTML escaping ---
    function htmlspecialchars(str) {
        return String(str ?? '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // --- Sidebar Toggle ---
    const sidebar = document.querySelector(".sidebar");
    const closeBtn = document.querySelector("#btn");
    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            sidebar.classList.toggle("open");
            menuBtnChange();
        });
    }

    function menuBtnChange() {
        if (sidebar.classList.contains("open")) {
            closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else {
            closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        }
    }

    // --- Date Filter Logic ---
    $('#filterByDate').on('change', function() {
        const selectedFilter = $(this).val();
        if (selectedFilter === 'custom') {
            $('#customDateRange').css('display', 'flex');
        } else {
            $('#customDateRange').hide();
            window.location.href = `salesReport.php?filter=${selectedFilter}`;
        }
    });

    $('#applyDateFilter').on('click', function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        if (startDate && endDate) {
            window.location.href = `salesReport.php?filter=custom&startDate=${startDate}&endDate=${endDate}`;
        } else {
            alert('Please select both start and end dates for the custom range.');
        }
    });

    // --- Generate Report Button ---
    $('#generateReportBtn').on('click', function() {
        const filter = $('#filterByDate').val();
        let url = 'printable_sales_report.php?filter=' + filter;

        if (filter === 'custom') {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            if (startDate && endDate) {
                url += `&startDate=${startDate}&endDate=${endDate}`;
            } else {
                alert('Please select start and end dates for the custom report.');
                return;
            }
        }
        window.open(url, '_blank');
    });

    // --- Chart.js Initialization ---
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true },
            x: { ticks: { autoSkip: true, maxRotation: 0, minRotation: 0 } }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) { label += ': '; }
                        if (context.parsed.y !== null) {
                            label += '₱ ' + parseFloat(context.parsed.y).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                        return label;
                    }
                }
            }
        }
    };

    const productSalesChartCtx = document.getElementById("productSalesChart")?.getContext("2d");
    if (productSalesChartCtx) {
      new Chart(productSalesChartCtx, {
        type: "bar",
        data: {
          labels: <?= json_encode($productChartLabels); ?>,
          datasets: [{
            label: "Product Sales (₱)",
            data: <?= json_encode($productChartData); ?>,
            backgroundColor: "#003249",
            borderColor: "#092D48",
            borderWidth: 1
          }]
        },
        options: chartOptions
      });
    }

    const serviceSalesChartCtx = document.getElementById("serviceSalesChart")?.getContext("2d");
    if (serviceSalesChartCtx) {
      new Chart(serviceSalesChartCtx, {
        type: "bar",
        data: {
          labels: <?= json_encode($serviceChartLabels); ?>,
          datasets: [{
            label: "Service Sales (₱)",
            data: <?= json_encode($serviceChartData); ?>,
            backgroundColor: "#0c5272",
            borderColor: "#092D48",
            borderWidth: 1
          }]
        },
        options: chartOptions
      });
    }

    // --- Modal Logic for Transaction Details ---
    const modal = $('#detailsModal');
    $('.styled-table').on('click', '.details-btn', function() {
         const row = $(this).closest('tr');
         const transactionId = row.data('transaction-id');
         const transactionType = row.data('transaction-type');
         const modalDetailsDiv = $('#modalDetails');
         modalDetailsDiv.html('<p>Loading details...</p>');

         let action = (transactionType === 'Product Sale') ? 'getProductSaleDetails' : 'getServiceSaleDetails';
         let idParamName = (transactionType === 'Product Sale') ? 'saleId' : 'transactionId';
         let controllerUrl = '/controllers/admin_salesReportController.php';

         $('#modalTitle').text(`${transactionType} Details (ID: ${transactionId})`);

         $.ajax({
             url: controllerUrl,
             type: 'GET',
             data: { action: action, [idParamName]: transactionId },
             dataType: 'json',
             success: function(details) {
                 if (!details || details.error) {
                     modalDetailsDiv.html(`<p class="error">${htmlspecialchars(details.error || 'Could not load details.')}</p>`);
                     return;
                 }
                 
                 let html = '';
                 if (transactionType === 'Product Sale' && Array.isArray(details) && details.length > 0) {
                      html = '<table class="modal-details-table"><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Line Total</th></tr></thead><tbody>';
                      details.forEach(item => {
                          html += `<tr>
                                      <td>${htmlspecialchars(item.prod_name)}</td>
                                      <td>${htmlspecialchars(item.sale_item_qty)}</td>
                                      <td>₱ ${htmlspecialchars(parseFloat(item.sale_unit_price).toFixed(2))}</td>
                                      <td>₱ ${htmlspecialchars(parseFloat(item.sale_line_total).toFixed(2))}</td>
                                   </tr>`;
                      });
                      html += '</tbody></table>';
                 } else if (transactionType === 'Service Sale' && typeof details === 'object' && !Array.isArray(details) && Object.keys(details).length > 0) {
                      html = '<table class="modal-details-table">';
                      html += `<tr><th>Service</th><td>${htmlspecialchars(details.purpose_of_visit)}</td></tr>`;
                      html += `<tr><th>Client</th><td>${htmlspecialchars(details.owner_name)}</td></tr>`;
                      html += `<tr><th>Pet</th><td>${htmlspecialchars(details.pet_name)}</td></tr>`;
                      html += `<tr><th>Appointment</th><td>${htmlspecialchars(details.appointment_date)} at ${htmlspecialchars(details.appointment_time)}</td></tr>`;
                      html += `<tr><th>Total Fee</th><td>₱ ${htmlspecialchars(parseFloat(details.total_amount).toFixed(2))}</td></tr>`;
                      html += '</table>';
                 } else {
                      html = '<p>No items found for this transaction.</p>';
                 }
                 modalDetailsDiv.html(html);
             },
             error: function(xhr) {
                 modalDetailsDiv.html('<p>Error loading transaction details. Please check the console.</p>');
                 console.error('AJAX Error:', xhr.responseText);
             }
         });
        modal.show();
    });

    // Close modal events
    $('.close-button').on('click', () => modal.hide());
    $(window).on('click', (event) => {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });

});
</script>

</body>
</html>