<?php
include_once(__DIR__ . '/../../controllers/admin_inventoryController.php');

$controller = new InventoryController();
$historyRecords = $controller->getInventoryHistory();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory History</title>
    <link rel="stylesheet" href="css/inventory.css?v=<?= time(); ?>">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <script src="/../../utils/jquery-3.7.1.js"></script>
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
        <li><a href="inventory.php" class="active"><i class='bx bx-cart'></i><span class="links_name">Inventory</span></a></li>
        <li><a href="services.php"><i class='bx bx-notepad'></i><span class="links_name">Services</span></a></li>
        <li><a href="petProfile.php"><i class='bx bxs-dog'></i><span class="links_name">Pet Profile</span></a></li>
        <div class="sidebar-line"></div>
        <li><a href="salesReport.php"><i class='bx bxs-report'></i><span class="links_name">Sales Report</span></a></li>
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
                    </div>
        </div>
        <div class="main_content">
            <div class="container">
                <h2>Inventory History</h2>

                <div class="table-section">
                    <div class="table-controls">
                         <h3>History Records</h3>
                         </div>
                    <div class="products_table_wrapper">
                        <table class="products_table">
                            <thead>
                                <tr>
                                    <th>History ID</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Change Type</th>
                                    <th>Old Quantity</th>
                                    <th>New Quantity</th>
                                    <th>Old Price</th>
                                    <th>New Price</th>
                                     <th>Qty Change</th>
                                    <th>Date and Time</th>
                                    </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <?php if (empty($historyRecords)): ?>
                                    <tr>
                                        <td colspan="10">No history records found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($historyRecords as $record): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($record['history_id']) ?></td>
                                            <td><?= htmlspecialchars($record['prod_id']) ?></td>
                                            <td><?= htmlspecialchars($record['prod_name']) ?></td>
                                            <td><?= htmlspecialchars($record['change_type']) ?></td>
                                            <td><?= htmlspecialchars($record['old_qty'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($record['new_qty'] ?? 'N/A') ?></td>
                                            <td>₱ <?= htmlspecialchars(number_format($record['old_price'] ?? 0, 2)) ?></td>
                                            <td>₱ <?= htmlspecialchars(number_format($record['new_price'] ?? 0, 2)) ?></td>
                                             <td><?= htmlspecialchars($record['qty_change'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($record['change_timestamp']) ?></td>
                                            </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
            <div class="footer-content">
                <p>© 2025 VetSync. All rights reserved.Software developed by <span class="fusion">COGTAS, LINGO & SEGOVIA</span></p>
            </div>
        </footer>
        </section>

    <script>
        // Sidebar Toggle
        const sidebar = document.querySelector(".sidebar");
        const closeBtn = document.querySelector("#btn");
        if(closeBtn) {
           closeBtn.addEventListener("click", () => {
               sidebar.classList.toggle("open");
               changeBtn();
           });
        }

        function changeBtn() {
            if (sidebar.classList.contains("open")) {
                closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
            } else {
                closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
            }
        }

        // User toggle
        const userToggle = document.getElementById('user-toggle');
        const userDropdown = document.getElementById('user-dropdown');

        if(userToggle && userDropdown) {
             userToggle.addEventListener('click', () => {
                userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
            });

            document.addEventListener('click', (event) => {
                 if (userToggle && userDropdown && !userToggle.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.style.display = 'none';
                }
            });
        } else {
            console.log("User toggle or dropdown element not found on productHistory.php.");
        }

        // You can add JavaScript here later for filtering/searching
    </script>

</body>
</html>