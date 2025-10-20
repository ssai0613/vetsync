<?php

include_once(__DIR__ . '/../../controllers/staff_dashboardController.php');
include_once(__DIR__ . '/../../controllers/staff_appointmentController.php'); 

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
$dashboardController = new DashboardController();
$dashboardData = $dashboardController->getDashboardData($filter);
$appointmentController = new AppointmentController(); 

$completedAppointments = $dashboardData['completedAppointments'];
$pendingAppointments = $dashboardData['pendingAppointments'];
$totalSales = $dashboardData['totalSales']; 
$totalRegisteredPets = $dashboardData['totalRegisteredPets'];
$productSalesForChart = $dashboardData['productSalesForChart'];
$serviceSalesForChart = $dashboardData['serviceSalesForChart'];
$topProducts = $dashboardData['topProducts']; 
$topServices = $dashboardData['topServices'];
$pendingAppointmentsDetails = $dashboardData['pendingAppointmentsDetails']; 
$pendingAppointmentsTodayCount = $dashboardData['pendingAppointmentsTodayCount']; // This is for the alert

// Prepare data for Chart.js
$productChartLabels = [];
$productChartData = [];
$totalProductSales = 0;
if (is_array($productSalesForChart)) {
    foreach ($productSalesForChart as $item) {
        $productChartLabels[] = htmlspecialchars($item['prod_name']);
        $productChartData[] = $item['total_sales_amount'];
        $totalProductSales += $item['total_sales_amount'];
    }
}
$serviceChartLabels = [];
$serviceChartData = [];
$totalServiceSales = 0;
if (is_array($serviceSalesForChart)) {
    foreach ($serviceSalesForChart as $item) {
        $serviceChartLabels[] = htmlspecialchars($item['service_name']);
        $serviceChartData[] = $item['total_service_amount'];
        $totalServiceSales += $item['total_service_amount'];
    }
}

function getFilterDisplayName($filterValue) {
    switch ($filterValue) {
        case 'today': return 'Today';
        case 'yesterday': return 'Yesterday';
        case 'this_week': return 'This Week';
        case 'this_month': return 'This Month';
        case 'this_year': return 'This Year';
        case 'all_time': return 'All Time';
        default: return 'Today';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard</title>
  <link rel="stylesheet" href="css/staff_dashboard.css?v=<?= time(); ?>">
  <script src="/../../utils/jquery-3.7.1.js"></script>
  <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
  <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
<div class="sidebar">
  <div class="logo_details">
    <i class='bx bx-code-alt'></i>
    <div class="logo_name">VetSync</div>
  </div>
  
  <ul>
    <li><a href="staff_dashboard.php" class="active"><i class='bx bxs-dashboard'></i><span class="links_name">Dashboard</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="staff_appointment.php"><i class='bx bxs-calendar'></i><span class="links_name">Appointments</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="staff_petProfile.php"><i class='bx bxs-dog'></i><span class="links_name">Pet History & Profiles</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="staff_POS.php"><i class='bx bx-line-chart'></i><span class="links_name">Point of Sale</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="staff_transactionHistory.php"><i class='bx bxs-report'></i><span class="links_name">Transaction History</span></a></li>
    <div class="sidebar-line"></div>
    <br><br><br>
    <li class="login"><a href="../logout.php"><span class="links_name login_out">Logout</span><i class='bx bx-log-out' id="log_out"></i></a></li>
  </ul>
</div>
<section class="home_section">
        <div class="topbar">
            <div class="toggle"><i class="bx bx-menu" id="btn"></i></div>
                 <div class="user_wrapper">
                     <div class="user-menu" id="user-toggle"><i class='bx bx-user' id="profile-icon"></i><span class="user-name">   Receptionist</span>
                 </div>
            </div>
        </div>

    <div class="container">
        <div id="dashboardAppointmentsTodayNotificationBar" class="top-notification-bar">
            <span class="message" id="dashboardNotificationBarMessage"></span>
            <span class="close-notification" id="dashboardCloseTopNotification">&times;</span>
        </div>

        <div class="dashboard_container">
            <div class="card"> 
                <div class="filter-dropdown-container">
                    <label for="dateFilter" style="color:white">View data for:</label>
                    <select id="dateFilter" onchange="location = this.value;">
                        <option value="?filter=today" <?= ($filter == 'today') ? 'selected' : ''; ?>>Today</option>
                        <option value="?filter=yesterday" <?= ($filter == 'yesterday') ? 'selected' : ''; ?>>Yesterday</option>
                        <option value="?filter=this_week" <?= ($filter == 'this_week') ? 'selected' : ''; ?>>This Week</option>
                        <option value="?filter=this_month" <?= ($filter == 'this_month') ? 'selected' : ''; ?>>This Month</option>
                        <option value="?filter=this_year" <?= ($filter == 'this_year') ? 'selected' : ''; ?>>This Year</option>
                        <option value="?filter=all_time" <?= ($filter == 'all_time') ? 'selected' : ''; ?>>All Time</option>
                    </select>
                </div>
                <div class="card-boxes">
                    <div class="box">
                        <div class="right_side">
                            <div class="numbers"><?= htmlspecialchars($completedAppointments); ?></div>
                            <div class="box_topic">Completed Appointments</div>
                        </div>
                        <i class='bx bxs-check-circle'></i>
                    </div>
                    <div class="box">
                        <div class="right_side">
                            <div class="numbers">₱ <?= htmlspecialchars(number_format($totalSales, 2)); ?></div>
                            <div class="box_topic">Total Sales</div>
                        </div>
                        <i class='bx bxs-cart-add'></i>
                    </div>
                    <div class="box">
                        <div class="right_side">
                            <div class="numbers"><?= htmlspecialchars($totalRegisteredPets); ?></div>
                            <div class="box_topic">Pet's Registered</div>
                        </div>
                        <i class='bx bxs-dog'></i>
                    </div>
                    <div class="box clickable" id="pendingAppointmentsBox">
                        <div class="right_side">
                            <div class="numbers"><?= htmlspecialchars($pendingAppointments); ?></div>
                            <div class="box_topic">Pending Appointments</div>
                        </div>
                        <i class='bx bxs-time-five'></i>
                    </div>
                </div>
                <div class="top-items-section">
                    <div class="table-section-row"> 
                        <div class="table-section">
                            <h2>Top Products</h2>
                            <div class="top-table-wrapper">
                                <table class="top-items-table">
                                    <thead><tr><th>Product</th><th>Total Sales (₱)</th></tr></thead>
                                    <tbody>
                                        <?php if (empty($topProducts)): ?>
                                            <tr><td colspan="2">No top products found for this period.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($topProducts as $item): ?>
                                                <tr><td><?= htmlspecialchars($item['prod_name']); ?></td><td>₱ <?= htmlspecialchars(number_format($item['total_sales_amount'], 2)); ?></td></tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="table-section">
                            <h2>Top Services</h2>
                            <div class="top-table-wrapper">
                                <table class="top-items-table">
                                     <thead><tr><th>Service</th><th>Total Sales (₱)</th></tr></thead>
                                     <tbody>
                                        <?php if (empty($topServices)): ?>
                                            <tr><td colspan="2">No top services found for this period.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($topServices as $item): ?>
                                                <tr><td><?= htmlspecialchars($item['service_name']); ?></td><td>₱ <?= htmlspecialchars(number_format($item['total_service_amount'], 2)); ?></td></tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                     </tbody>
                                 </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sales_panel">
                  <div class="header"><h2><a href="salesReport.php">Sales Report</a></h2></div>
                </div>
                <div class="charts">
                    <div class="chart-container">
                        <canvas id="productChart"></canvas>
                        <div class="total-sales-display">
                            <h3>Total Product Sales: ₱ <?= htmlspecialchars(number_format($totalProductSales, 2)); ?></h3>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="serviceChart"></canvas>
                        <div class="total-sales-display">
                            <h3>Total Service Sales: ₱ <?= htmlspecialchars(number_format($totalServiceSales, 2)); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pendingAppointmentsModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Pending Appointments Details (<?= getFilterDisplayName($filter) ?>)</h2>
          <span class="close-button" id="closePendingModal">&times;</span>
        </div>
        <div class="modal-body">
          <?php if (empty($pendingAppointmentsDetails)): ?>
              <p>No pending appointments for the selected period.</p>
          <?php else: ?>
              <table>
                  <thead>
                      <tr>
                          <th>Appt. ID</th> 
                          <th>Pet Name</th>
                          <th>Pet Owner</th>
                          <th>Contact No.</th>
                          <th>Service Type</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Remarks</th>
                          <th>Action</th> 
                      </tr>
                  </thead>
                  <tbody id="dashboardPendingAppointmentsTableBody">
                      <?php foreach ($pendingAppointmentsDetails as $appointment): ?>
                          <tr data-appointment-id="<?= htmlspecialchars($appointment['appointment_id']); ?>">
                              <td><?= htmlspecialchars($appointment['appointment_id']); ?></td> 
                              <td><?= htmlspecialchars($appointment['pet_name']); ?></td>
                              <td><?= htmlspecialchars($appointment['pet_owner_name']); ?></td>
                              <td><?= htmlspecialchars($appointment['contact_number']); ?></td>
                              <td><?= htmlspecialchars($appointment['type_of_service']); ?></td>
                              <td><?= htmlspecialchars(date("M d, Y", strtotime($appointment['appointment_date']))); ?></td>
                              <td><?= htmlspecialchars(date("h:i A", strtotime($appointment['appointment_time']))); ?></td>
                              <td><?= htmlspecialchars($appointment['remarks']); ?></td>
                              <td>
                                  <button class="action-btn-view dashboard-view-appointment-btn">View</button>
                                  </td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
          <?php endif; ?>
        </div>
         <div class="modal-footer">
              <p>Total Pending: <?= count($pendingAppointmentsDetails); ?></p>
         </div>
      </div>
    </div>

    <div id="dashboardViewAppointmentModal" class="dashboard-modal-overlay">
        <div class="modal-content">
            <span class="close-button" id="closeDashboardViewModal">&times;</span>
            <h2 class="section-title">View Appointment</h2>
            <form id="dashboardViewAppointmentForm">
                <input type="hidden" id="dashboardEditAppointmentId" name="appointment_id">
                <div class="form-group"><label>Pet's Owner Name</label><input type="text" id="dashboardEditOwnerNameInput" readonly></div>
                <div class="form-group"><label>Pet's Name</label><input type="text" id="dashboardEditPetNameInput" readonly></div>
                <div class="form-group"><label>Pet's Breed</label><input type="text" id="dashboardEditPetBreedInput" readonly></div>
                <div class="form-group"><label>Contact No.</label><input type="text" id="dashboardEditContactNo" readonly></div>
                <div class="form-group">
                    <label for="dashboardEditVisitPurposeSelect">Type of Service</label>
                    <select id="dashboardEditVisitPurposeSelect" name="visitPurpose" style="width:100%;" disabled></select>
                </div>
                <div class="form-group"><label>Appointment Date</label><input type="date" id="dashboardEditAppointmentDate" readonly></div>
                <div class="form-group"><label>Appointment Time</label><input type="time" id="dashboardEditAppointmentTime" readonly></div>
                <div class="form-group"><label>Remarks</label><textarea id="dashboardEditRemarks" readonly></textarea></div>
                <div style="display: flex; justify-content: space-around; margin-top: 20px;">
                    <button type="button" class="submit-btn" id="dashboardOpenCompleteModalBtn" style="display:none;">COMPLETE</button>
                    <button type="button" class="cancel-btn" id="dashboardOpenCancelConfirmModalBtn" style="display:none;">CANCEL</button>
                </div>
            </form>
        </div>
    </div>

    <div id="dashboardCompleteAppointmentModal" class="dashboard-modal-overlay">
        <div class="modal-content">
            <div class="modal-header1">
                <h4>Complete Appointment</h4>
                <button type="button" class="close-modal" id="dashboardCloseCompleteModalBtn">&times;</button>
            </div>
            <div class="modal-body1">
                <input type="hidden" id="dashboardCompleteAppointmentIdVal">
                <label for="dashboardApptTotal">Total Amount (₱)</label>
                <input type="number" id="dashboardApptTotal" readonly class="form-control">
                <label for="dashboardApptPaid">Amount Paid (₱)</label>
                <input type="number" id="dashboardApptPaid" step="0.01" min="0" class="form-control">
                <label for="dashboardApptChange">Change (₱)</label>
                <input type="number" id="dashboardApptChange" readonly class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="submit-btn" id="dashboardConfirmCompleteBtn">Confirm Completion</button>
            </div>
        </div>
    </div>

    <div id="dashboardConfirmCancelModal" class="dashboard-modal-overlay">
        <div class="modal-content confirmation-modal-content">
            <span class="close-button" id="dashboardCloseCancelConfirmModalBtn">&times;</span>
            <h2 class="section-title">Confirm Cancellation</h2>
            <p>Are you sure you want to cancel this appointment?</p>
            <input type="hidden" id="dashboardCancelAppointmentIdVal">
            <div class="confirmation-actions">
                <button type="button" class="confirm-btn yes-btn" id="dashboardConfirmCancelYesBtn">Yes</button>
                <button type="button" class="confirm-btn no-btn" id="dashboardConfirmCancelNoBtn">No</button>
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
  const pendingAppointmentsTodayForAlert = <?= intval($pendingAppointmentsTodayCount); ?>; 
  const appointmentControllerUrl = '/controllers/staff_appointmentController.php';
  let dashboardServicesData = []; 

  document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector(".sidebar");
    const toggleMenuBtn = document.querySelector("#btn"); 
    if(toggleMenuBtn && sidebar) {
       toggleMenuBtn.addEventListener("click", () => {
           sidebar.classList.toggle("open");
           if (sidebar.classList.contains("open")) {
                toggleMenuBtn.classList.replace("bx-menu", "bx-menu-alt-right");
            } else {
                toggleMenuBtn.classList.replace("bx-menu-alt-right", "bx-menu");
            }
       });
    }
    const userToggle = document.getElementById('user-toggle');
    if(userToggle) { 
      userToggle.addEventListener('click', (event) => { 
          event.stopPropagation(); 
      });
    }

    // --- Top Notification Bar for "Pending Appointments Today" ---
    const dashboardNotificationBar = document.getElementById("dashboardAppointmentsTodayNotificationBar");
    const dashboardNotificationMessageElem = document.getElementById("dashboardNotificationBarMessage");
    const dashboardCloseTopNotificationBtn = document.getElementById("dashboardCloseTopNotification");

    if (pendingAppointmentsTodayForAlert > 0) {
        let message = "You have " + pendingAppointmentsTodayForAlert + " pending appointment" + (pendingAppointmentsTodayForAlert > 1 ? "s" : "") + " today!";
        if (dashboardNotificationMessageElem) {
            dashboardNotificationMessageElem.textContent = message;
        }
        if (dashboardNotificationBar) {
            // This line makes the bar visible if there are pending appointments
            dashboardNotificationBar.style.display = "flex"; 
        }
    }
    if (dashboardCloseTopNotificationBtn) {
        dashboardCloseTopNotificationBtn.onclick = function() {
            if (dashboardNotificationBar) dashboardNotificationBar.style.display = "none";
        }
    }
    // --- End Top Notification Bar ---


    // --- Pending Appointments Modal (Existing) ---
    const pendingAppointmentsModal = document.getElementById("pendingAppointmentsModal");
    const pendingAppointmentsBox = document.getElementById("pendingAppointmentsBox");
    const closePendingModalButton = document.getElementById("closePendingModal");

    if (pendingAppointmentsBox) {
        pendingAppointmentsBox.onclick = function() {
            if (pendingAppointmentsModal) pendingAppointmentsModal.style.display = "block";
        }
    }
    if (closePendingModalButton) {
        closePendingModalButton.onclick = function() {
            if (pendingAppointmentsModal) pendingAppointmentsModal.style.display = "none";
        }
    }
    
    // --- New Modals for Dashboard ---
    const dashboardViewAppointmentModal = document.getElementById("dashboardViewAppointmentModal");
    const dashboardCompleteAppointmentModal = document.getElementById("dashboardCompleteAppointmentModal");
    const dashboardConfirmCancelModal = document.getElementById("dashboardConfirmCancelModal");

    const closeDashboardViewModalBtn = document.getElementById("closeDashboardViewModal");
    const dashboardCloseCompleteModalBtn = document.getElementById("dashboardCloseCompleteModalBtn");
    const dashboardCloseCancelConfirmModalBtn = document.getElementById("dashboardCloseCancelConfirmModalBtn");
    const dashboardConfirmCancelNoBtn = document.getElementById("dashboardConfirmCancelNoBtn");

    $('#dashboardEditVisitPurposeSelect').select2({
        dropdownParent: $('#dashboardViewAppointmentModal'), 
        width: '100%'
    });
    
    $.ajax({
        url: appointmentControllerUrl, type: 'GET', data: { action: 'getServices' }, dataType: 'json',
        success: function(services) {
            dashboardServicesData = services;
            const serviceSelect = $('#dashboardEditVisitPurposeSelect');
            serviceSelect.empty().append('<option value="">-- Service --</option>');
            if (services && services.length > 0) {
                services.forEach(service => {
                    serviceSelect.append(new Option(service.svc_name, service.svc_name));
                });
            }
        },
        error: function(xhr) { console.error("Dashboard: Error fetching services:", xhr.responseText); }
    });

    // Close Modals on Overlay Click
    window.addEventListener('click', function(event) {
        if (event.target == pendingAppointmentsModal) pendingAppointmentsModal.style.display = "none";
        if (event.target == dashboardViewAppointmentModal) dashboardViewAppointmentModal.style.display = "none";
        if (event.target == dashboardCompleteAppointmentModal) dashboardCompleteAppointmentModal.style.display = "none";
        if (event.target == dashboardConfirmCancelModal) dashboardConfirmCancelModal.style.display = "none";
    });
    
    // Event Delegation for "View" buttons in pending appointments list
    $('#dashboardPendingAppointmentsTableBody').on('click', '.dashboard-view-appointment-btn', function() {
        const row = $(this).closest('tr');
        const appointmentId = row.data('appointment-id');

        // For "View" button
        $.ajax({
            url: appointmentControllerUrl, type: 'GET',
            data: { action: 'getAppointmentDetails', appointment_id: appointmentId },
            dataType: 'json',
            success: function(appt) {
                if (appt) {
                    $('#dashboardEditAppointmentId').val(appt.appointment_id);
                    $('#dashboardEditOwnerNameInput').val(appt.owner_name);
                    $('#dashboardEditPetNameInput').val(appt.pet_name);
                    $('#dashboardEditPetBreedInput').val(appt.pet_breed);
                    $('#dashboardEditContactNo').val(appt.contact_number);
                    $('#dashboardEditVisitPurposeSelect').val(appt.purpose_of_visit).trigger('change.select2');
                    $('#dashboardEditAppointmentDate').val(appt.appointment_date);
                    $('#dashboardEditAppointmentTime').val(appt.appointment_time);
                    $('#dashboardEditRemarks').val(appt.remarks);

                    if (appt.status === 'Pending') { // Only show actions for Pending
                        $('#dashboardOpenCompleteModalBtn').show();
                        $('#dashboardOpenCancelConfirmModalBtn').show();
                    } else { 
                        $('#dashboardOpenCompleteModalBtn').hide();
                        $('#dashboardOpenCancelConfirmModalBtn').hide();
                    }
                    dashboardViewAppointmentModal.style.display = "flex";
                } else { alert("Could not fetch appointment details."); }
            },
            error: function(xhr) { alert("Failed to fetch appointment details: " + xhr.responseText); }
        });
    });

    if(closeDashboardViewModalBtn) closeDashboardViewModalBtn.onclick = () => dashboardViewAppointmentModal.style.display = "none";
    if(dashboardCloseCompleteModalBtn) dashboardCloseCompleteModalBtn.onclick = () => dashboardCompleteAppointmentModal.style.display = "none";
    if(dashboardCloseCancelConfirmModalBtn) dashboardCloseCancelConfirmModalBtn.onclick = () => dashboardConfirmCancelModal.style.display = "none";
    if(dashboardConfirmCancelNoBtn) dashboardConfirmCancelNoBtn.onclick = () => dashboardConfirmCancelModal.style.display = "none";

    $('#dashboardOpenCompleteModalBtn').on('click', function() {
        const appointmentId = $('#dashboardEditAppointmentId').val();
        $('#dashboardCompleteAppointmentIdVal').val(appointmentId); 
        const serviceName = $('#dashboardEditVisitPurposeSelect').val();
        const selectedService = dashboardServicesData.find(s => s.svc_name === serviceName);
        let totalAmount = 0.00;
        if (selectedService && selectedService.svc_price) {
            totalAmount = parseFloat(selectedService.svc_price);
        }
        $('#dashboardApptTotal').val(totalAmount.toFixed(2));
        $('#dashboardApptPaid').val('0.00');
        $('#dashboardApptChange').val(totalAmount > 0 ? (-totalAmount).toFixed(2) : '0.00'); 
        dashboardViewAppointmentModal.style.display = "none";
        dashboardCompleteAppointmentModal.style.display = "flex";
    });

    $('#dashboardApptPaid').on('input', function() {
        const total = parseFloat($('#dashboardApptTotal').val()) || 0;
        const paid = parseFloat($(this).val()) || 0;
        $('#dashboardApptChange').val((paid - total).toFixed(2));
    });

    $('#dashboardConfirmCompleteBtn').on('click', function() {
        const appointmentId = $('#dashboardCompleteAppointmentIdVal').val();
        const totalAmount = $('#dashboardApptTotal').val();
        const amountPaid = $('#dashboardApptPaid').val();
        if (parseFloat(amountPaid) < parseFloat(totalAmount)) {
            alert("Amount paid is less than the total amount."); return;
        }
        $.ajax({
            url: appointmentControllerUrl, type: 'POST',
            data: { action: 'complete', appointment_id: appointmentId, total_amount: totalAmount, amount_paid: amountPaid, change_amount: $('#dashboardApptChange').val() },
            success: function(response) {
                alert(response);
                if (response.toLowerCase().includes("success")) {
                    dashboardCompleteAppointmentModal.style.display = "none";
                    location.reload(); 
                }
            },
            error: function(xhr) { alert("Failed to complete appointment: " + xhr.responseText); }
        });
    });

    $('#dashboardOpenCancelConfirmModalBtn').on('click', function() {
        $('#dashboardCancelAppointmentIdVal').val($('#dashboardEditAppointmentId').val());
        dashboardViewAppointmentModal.style.display = "none";
        dashboardConfirmCancelModal.style.display = "flex";
    });

    $('#dashboardConfirmCancelYesBtn').on('click', function() {
        const appointmentId = $('#dashboardCancelAppointmentIdVal').val();
        $.ajax({
            url: appointmentControllerUrl, type: 'POST', 
            data: { action: 'cancel', appointment_id: appointmentId },
            success: function(response) {
                alert(response);
                if (response.toLowerCase().includes("success")) {
                    dashboardConfirmCancelModal.style.display = "none";
                    location.reload(); 
                }
            },
            error: function(xhr) { alert("Failed to cancel appointment: " + xhr.responseText); }
        });
    });

    // Chart initialization (condensed for brevity, ensure your original options are preserved)
    const productChartCtx = document.getElementById("productChart")?.getContext("2d");
    if (productChartCtx) {
        const productChartLabels = <?= json_encode($productChartLabels); ?>;
        const productChartData = <?= json_encode($productChartData); ?>;
        if (productChartLabels && productChartData && productChartLabels.length > 0) { 
            new Chart(productChartCtx, { type: "bar", data: { labels: productChartLabels, datasets: [{ label: "Product Sales (₱)", data: productChartData, backgroundColor: "#003249", borderColor: "#092D48", borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, type: 'logarithmic', ticks: { callback: function(value){ if(value === 0) return '0'; if(value >= 1000000) return (value/1000000).toFixed(1)+'M'; if(value >= 1000) return (value/1000).toFixed(1)+'K'; return value; }}}, x:{ticks:{autoSkip:true,maxRotation:0,minRotation:0}}}, plugins:{tooltip:{callbacks:{label:function(context){let label=context.dataset.label||''; if(label){label+=': ';} if(context.parsed.y!==null){label+='₱ '+context.parsed.y.toFixed(2);} return label;}}}} } });
        }
    }
    const serviceChartCtx = document.getElementById("serviceChart")?.getContext("2d");
    if (serviceChartCtx) {
        const serviceChartLabels = <?= json_encode($serviceChartLabels); ?>;
        const serviceChartData = <?= json_encode($serviceChartData); ?>;
        if (serviceChartLabels && serviceChartData && serviceChartLabels.length > 0) {
            new Chart(serviceChartCtx, { type: "bar", data: { labels: serviceChartLabels, datasets: [{ label: "Service Sales (₱)", data: serviceChartData, backgroundColor: "#003249", borderColor: "#092D48", borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins:{title:{display:true, text:"Veterinary Services Sales"}, tooltip:{callbacks:{label:function(context){let label=context.dataset.label||''; if(label){label+=': ';} if(context.parsed.y!==null){label+='₱ '+context.parsed.y.toFixed(2);} return label;}}}}, scales:{y:{beginAtZero:true}, x:{ticks:{autoSkip:true,maxRotation:0,minRotation:0}}} } });
        }
    }
  }); 
</script>
</body>
</html>