<?php
include_once(__DIR__ . '/../../controllers/admin_servicesController.php');

$controller = new ServicesController();
$services = $controller->getAllServices();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="css/services.css?v=<?= time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script src="/../../utils/jquery-3.7.1.js"></script></head> 
<body>
<!---SideBar--->
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
    <li><a href="services.php" class="active"><i class='bx bx-notepad'></i><span class="links_name">Services</span></a></li>
    <li><a href="petProfile.php"><i class='bx bxs-dog'></i><span class="links_name">Pet Profile</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="salesReport.php"><i class='bx bxs-report'></i><span class="links_name">Sales Report</span></a></li>
    <li><a href="transactionHistory.php"><i class='bx bxs-report'></i><span class="links_name">Transaction History</span></a></li>
    <br><br><br>
    <li class="login"><a href="../logout.php"><span class="links_name login_out">Logout</span><i class='bx bx-log-out' id="log_out"></i></a></li>
  </ul>
</div>
<!-- End Sidebar -->

    <section class="home_section">
        <div class="topbar">
            <div class="toggle"><i class="bx bx-menu" id="btn"></i></div>
                <div class="user_wrapper">
                    <div class="user-menu" id="user-toggle"><i class='bx bx-user' id="profile-icon"></i><span class="user-name">   Admin</span>
            </div>
            </div>
        </div>
        <div class="main_content">
            <div class="container">
                <h2>Service Management</h2>
                <div class="content">
                    <form method="POST" class="form" action="vetsync/controllers/admin_servicesController.php">
                        <div class="form-group">
                            <label for="serviceName">Service Name</label>
                            <input type="text" name="serviceName" id="serviceName" placeholder="e.g. General Check-up">
                        </div>

                        <div class="form-group">
                            <label for="serviceCost">Service Cost</label>
                            <input type="number" name="serviceCost" id="serviceCost" placeholder="e.g. 1000.00" min="0" step="0.01">
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" name="description" id="description" placeholder="Description here...">
                        </div>

                        <input type="hidden" id="serviceID">
                        <input type="hidden" name="action" value="add">
                        <div class="buttons">
                            <button class="add-btn" id="addServiceBtn">Add Service</button>
                            <button class="update-btn" id="updateServiceBtn">Update Service</button>
                            <button type="button" class="remove-btn" id="removeServiceBtn">Remove Service</button>
                            <button type="button" class="clear-btn" id="clearServiceBtn">Clear</button>
                        </div>
                    </form>

                    <div class="table-section">
                        <div class="table-controls">
                            <input type="text" id="searchService" placeholder="Search Service Name">
                        </div><br>

                        <div class="service_table_wrapper">
                            <table class="service_table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Cost</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody id="serviceTableBody">
                                    <?php foreach ($services as $service): ?>
                                        <tr>
                                            <td><?= $service['svc_id'] ?></td>
                                            <td><?= $service['svc_name']; ?></td>
                                            <td>₱ <?= number_format($service['svc_price'], 2); ?></td>
                                            <td><?= $service['svc_descript']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="confirmDeleteModal" class="modal confirmation-modal" style="display: none;">
    <div class="modal-content confirmation-modal-content">
        <h2 class="modal-section-title">Confirm Deletion</h2>
        <p id="confirmDeleteMessage">Are you sure you want to delete this service?</p>
        <div class="confirmation-actions">
        <button type="button" class="confirm-btn yes-btn" id="confirmDeleteYesBtn">Yes</button>
        <button type="button" class="confirm-btn no-btn" id="confirmDeleteNoBtn">No</button>
        </div>
    </div>
    </div>

        </section>
        <footer class="footer">
            <div class="footer-content">
                <p>© 2025 VetSync. All rights reserved.Software developed by <span class="fusion">COGTAS, LINGO & SEGOVIA</span></p>
            </div>
        </footer>

        <script>

            const servicesControllerUrl = '/controllers/admin_servicesController.php';

        $(document).ready(function() {
            $('#updateServiceBtn').hide();
            $('#removeServiceBtn').hide();
            $('#confirmDeleteModal').hide();

            let serviceIdToRemove = null;

            $('.form').on('submit', function(e) {
                e.preventDefault();

                const action = $('input[name="action"]').val();
                const serviceID = $('#serviceID').val();
                const serviceName = $('#serviceName').val();
                const serviceCost = $('#serviceCost').val();
                const description = $('#description').val();

                const formData = {
                    action: action,
                    serviceID: serviceID,
                    serviceName: serviceName,
                    serviceCost: serviceCost,
                    description: description
                };

                $.ajax({
                    url: servicesControllerUrl,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });

            $('#addServiceBtn').click(function() {
                $('input[name="action"]').val('add');
            });

            $('#updateServiceBtn').click(function() {
                $('input[name="action"]').val('update');
            });

            $('#removeServiceBtn').click(function() {
                const serviceId = $('#serviceID').val();
                serviceIdToRemove = serviceId;
                $('#confirmDeleteModal').show();
            });

            $('#confirmDeleteYesBtn').click(function() {
                if (serviceIdToRemove) {
                    $.ajax({
                        url: servicesControllerUrl,
                        type: 'POST',
                        data: {
                            action: 'remove',
                            serviceID: serviceIdToRemove
                        },
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                    $('#confirmDeleteModal').hide();
                    serviceIdToRemove = null;
                }
            });

            $('#confirmDeleteNoBtn').click(function() {
                $('#confirmDeleteModal').hide();
                serviceIdToRemove = null;
            });

            $(document).on('click', '#serviceTableBody tr', function() {
                $('#serviceID').val($(this).find('td:eq(0)').text());
                $('#serviceName').val($(this).find('td:eq(1)').text());
                const costText = $(this).find('td:eq(2)').text().replace('₱', '').replace(',', '').trim();
                $('#serviceCost').val(costText);
                $('#description').val($(this).find('td:eq(3)').text());

                $('#addServiceBtn').hide();
                $('#updateServiceBtn').show();
                $('#removeServiceBtn').show();
                // $('input[name="action"]').val('update');
            });

            $('#searchService').on('input', function() {
                const searchQuery = $(this).val();
                $.ajax({
                    url: servicesControllerUrl,
                    type: 'GET',
                    data: {
                        action: 'search',
                        query: searchQuery
                    },
                    success: function(response) {
                        $('#serviceTableBody').html(response);
                    }
                });
            });

            $('#clearServiceBtn').click(function() {
                $('#serviceID').val('');
                $('#serviceName').val('');
                $('#serviceCost').val('');
                $('#description').val('');
                $('input[name="action"]').val('add');
                $('#addServiceBtn').show();
                $('#updateServiceBtn').hide();
                $('#removeServiceBtn').hide();
            });
        });

        // Sidebar Toggle (Keep this as it is)
        let sidebar = document.querySelector(".sidebar");
        let closeBtn = document.querySelector("#btn");
        closeBtn.addEventListener("click", () => {
            sidebar.classList.toggle("open");
            changeBtn();
        });
        function changeBtn() {
            if(sidebar.classList.contains("open")) {
                closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
            } else {
                closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
            }
        }

        // User toggle (Keep this as it is)
        const userToggle = document.getElementById('user-toggle');
        const userDropdown = document.getElementById('user-dropdown');
        userToggle.addEventListener('click', () => {
            userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', (event) => {
            if (!userToggle.contains(event.target)) {
            userDropdown.style.display = 'none';
            }
        });
        </script>
</body>
</html>