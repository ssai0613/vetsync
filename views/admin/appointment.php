<?php

include_once(__DIR__ . '/../../controllers/admin_appointmentController.php');
include_once(__DIR__ . '/../../controllers/admin_petProfileController.php');

$controller = new AppointmentController();
// $controller2 = new PetProController(); // This was for add modal, can be removed if not used elsewhere
// $owners = $controller2->getAllOwners(); // This was for add modal

$appointments = $controller->getAllAppointments();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="css/appointment.css?v=<?= time(); ?>">
    <script src="/../../utils/jquery-3.7.1.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
    <li><a href="appointment.php" class="active"><i class='bx bxs-calendar'></i><span class="links_name">Appointments</span></a></li>
    <div class="sidebar-line"></div>
    <li><a href="inventory.php"><i class='bx bx-cart'></i><span class="links_name">Inventory</span></a></li>
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
                     <div class="user-menu" id="user-toggle"><i class='bx bx-user' id="profile-icon"></i><span class="user-name">   Admin</span>
                 </div>
            </div>
        </div>
        <div class="main_content">
            <div class="calendar-panel">
                <div class="lowbar">
                    <div class="table_wrapper">
                        
                            <div class="app_panel">
                                <div class="header">
                                    <h2>Appointments</h2>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <div class="search-wrapper">
                                    <label>
                                        <input type="text" id="searchInput" placeholder="Search Owner, Pet, or Appt ID...." class="search-input">
                                        <span>
                                            <i class='bx bx-search'></i>
                                        </span>
                                    </label>
                                </div>
                                <div class="appointment-header">
                                    <select id="sortAppointments" class="sortDropdown">
                                        <option value="all">All Appointments</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                        </select>
                                </div>
                            </div>
                            <div class="table_list">
                            <table class="appointment_list" id="appointmentTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Pet Owner Name</th>
                                        <th>Pet Breed</th>
                                        <th>Contact #</th>
                                        <th>Type of Service</th>
                                        <th>Appointment Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="appointmentTableBody">
                                    <?php
                                    if (!empty($appointments)) {
                                        $rowNumber = 1;
                                        foreach ($appointments as $appointment) {
                                            // Add data-appointment-id to the <tr> for easier selection by JavaScript
                                            echo "<tr data-appointment-id='" . htmlspecialchars($appointment['appointment_id']) . "'>";
                                            echo "<td>" . $rowNumber++ . "</td>";
                                            echo "<td>" . htmlspecialchars($appointment['appointment_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($appointment['owner_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($appointment['pet_breed']) . "</td>";
                                            echo "<td>" . htmlspecialchars($appointment['contact_number']) . "</td>";
                                            echo "<td>" . htmlspecialchars($appointment['purpose_of_visit']) . "</td>";
                                            echo "<td>" . htmlspecialchars($appointment['appointment_date']) . "</td>";
                                            echo "<td><span class='status-" . strtolower(htmlspecialchars($appointment['status'])) . "'>" . htmlspecialchars($appointment['status']) . "</span></td>";
                                            echo "<td class='action-icons'>";
                                            echo "<button class='edit_btn'><i class='bx bx-show'></i> View</button>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9'>No appointments found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="editAppointmentModal" class="modal-overlay1" style="display: none;">
            <div class="modal-content">
                <span class="close-btn" id="closeEditAppointmentModal">&times;</span>
                <h2 class="section-title">View/Edit Appointment</h2>
                <form id="editAppointmentForm">
                    <input type="hidden" id="editAppointmentId" name="appointment_id">
                    <div class="form-group">
                        <label for="editOwnerNameInput">Pet's Owner Name</label>
                        <input type="text" id="editOwnerNameInput" name="ownerName" readonly>
                    </div>
                     <div class="form-group">
                         <label for="editPetNameInput">Pet's Name</label>
                         <input type="text" id="editPetNameInput" name="petName" readonly>
                     </div>
                     <div class="form-group">
                         <label for="editPetBreedInput">Pet's Breed</label>
                         <input type="text" id="editPetBreedInput" name="petBreed" readonly>
                     </div>
                    <div class="form-group">
                        <label for="editContactNo">Contact No.</label>
                        <input type="text" id="editContactNo" name="contactNo" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editVisitPurposeSelect">Type of Service <span style="color: red;">*</span></label>
                         <select name="visitPurpose" id="editVisitPurposeSelect" required style="width: 100%;">
                             <option value="">-- Select One --</option>
                         </select>
                    </div>
                    <div class="form-group">
                        <label for="editAppointmentDate">Appointment Date <span style="color: red;">*</span></label>
                        <input type="date" id="editAppointmentDate" name="appointmentDate" required>
                    </div>
                    <div class="form-group">
                        <label for="editAppointmentTime">Appointment Time <span style="color: red;">*</span></label>
                        <input type="time" id="editAppointmentTime" name="appointmentTime" required>
                    </div>
                    <div class="form-group">
                        <label for="editRemarks">Remarks</label>
                        <textarea id="editRemarks" name="remarks" placeholder="Update notes if needed"></textarea>
                    </div>
                    <div class="update_cancel">
                        <button type="button" class="submit-btn" id="openCompleteModalFromEdit">COMPLETE APPOINTMENT</button>
                         <button type="button" class="cancel-btn" id="openCancelConfirmModal"> CANCEL APPOINTMENT</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="completeAppointmentModal" class="modal-overlay1" style="display: none;">
            <div class="modal-content">
                <div class="modal-header1">
                    <h4>Complete Appointment</h4>
                    <button class="close-modal" id="closeCompleteModal">&times;</button>
                </div>
                <div class="modal-body1">
                    <input type="hidden" id="completeAppointmentId">
                    <label>Total Amount</label>
                    <input type="number" id="appt-total" class="form-control" value="0.00" readonly>
                    <label>Amount Paid</label>
                    <input type="number" id="appt-paid" class="form-control" value="0.00">
                    <label>Change</label>
                    <input type="number" id="appt-change" class="form-control" value="0.00" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="submit-btn" id="confirmCompleteBtn">Confirm Completion</button>
                </div>
            </div>
        </div>

        <div id="confirmCancelModal" class="modal-overlay1" style="display: none;">
            <div class="modal-content confirmation-modal-content">
                <h2 class="section-title">Confirm Cancellation</h2>
                <p id="confirmCancelMessage">Are you sure you want to cancel this appointment?</p>
                <input type="hidden" id="cancelAppointmentId">
                <div class="confirmation-actions">
                    <button type="button" class="confirm-btn yes-btn" id="confirmCancelYesBtn">Yes</button>
                    <button type="button" class="confirm-btn no-btn" id="confirmCancelNoBtn">No</button>
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
        $(document).ready(function() {
            const appointmentControllerUrl = '/controllers/admin_appointmentController.php';
            let servicesData = []; 

            // --- Initialize Select2 for edit modal ---
            $('#editVisitPurposeSelect').select2({
                dropdownParent: $('#editAppointmentModal'), 
                width: '100%'
            });
            
            // Fetch Services for Edit Modal
            $.ajax({
                url: appointmentControllerUrl, type: 'GET', data: { action: 'getServices' }, dataType: 'json',
                success: function(services) {
                    servicesData = services; 
                    const serviceSelectEdit = $('#editVisitPurposeSelect');
                    serviceSelectEdit.empty().append('<option value="">-- Select One --</option>');
                    if (services && services.length > 0) {
                        services.forEach(service => {
                            serviceSelectEdit.append(new Option(service.svc_name, service.svc_name));
                        });
                    }
                    serviceSelectEdit.trigger('change.select2'); 
                },
                error: function(xhr) { console.error("Error fetching services:", xhr.responseText); }
            });

            // --- Sidebar Toggle ---
            const sidebar = document.querySelector(".sidebar");
            const toggleMenuBtn = document.querySelector("#btn");
            if(toggleMenuBtn && sidebar) {
                toggleMenuBtn.addEventListener("click", (e) => {
                    e.stopPropagation(); sidebar.classList.toggle("open");
                    if (sidebar.classList.contains("open")) { toggleMenuBtn.classList.replace("bx-menu", "bx-menu-alt-right"); } 
                    else { toggleMenuBtn.classList.replace("bx-menu-alt-right", "bx-menu"); }
                });
            }
            const userToggle = document.getElementById('user-toggle');
            if(userToggle) { userToggle.addEventListener('click', (event) => { event.stopPropagation(); }); }


            // --- Modal General Close Logic ---
            function closeModal(modalElement) {
                if (modalElement) modalElement.style.display = "none";
            }
            function openModal(modalElement) {
                if (modalElement) modalElement.style.display = "flex";
            }
            [document.getElementById('closeEditAppointmentModal'), document.getElementById('closeCompleteModal'), 
             document.getElementById("confirmCancelNoBtn")].forEach(btn => btn?.addEventListener("click", function() {
                const modalToClose = this.closest('.modal-overlay1');
                closeModal(modalToClose);
                if (modalToClose && modalToClose.id === 'editAppointmentModal') {
                     $(modalToClose.querySelector('form'))[0]?.reset();
                     $('#editVisitPurposeSelect').val(null).trigger('change.select2');
                     $('#editVisitPurposeSelect').prop('disabled', false); 
                     $('#editAppointmentDate, #editAppointmentTime, #editRemarks').prop('readonly', false);
                }
            }));
            $('.modal-overlay1').on('click', function(event) {
                if (event.target === this) { 
                    const formInModal = this.querySelector('form');
                    if (formInModal) $(formInModal)[0]?.reset();
                    $(this.querySelectorAll('select.select2-hidden-accessible')).val(null).trigger('change.select2');
                    if(this.id === 'editAppointmentModal'){
                        $('#editVisitPurposeSelect').prop('disabled', false);
                        $('#editAppointmentDate, #editAppointmentTime, #editRemarks').prop('readonly', false);
                    }
                    closeModal(this);
                }
            });
            
            // --- Highlight Appointment from URL ---
            const urlParams = new URLSearchParams(window.location.search);
            const highlightId = urlParams.get('highlight_id');

            if (highlightId) {
                // Escape the ID for use in a jQuery selector if it might contain special characters
                const escapedHighlightId = highlightId.replace(/([ #;&,.+*~\':"!^$\[\]()=>|\/@])/g,'\\$1');
                const targetRow = $(`#appointmentTableBody tr[data-appointment-id="${escapedHighlightId}"]`);
                
                if (targetRow.length) {
                    // Scroll to the row
                    $('html, body').animate({
                        scrollTop: targetRow.offset().top - 100 // Adjust 100 for fixed header height
                    }, 500);

                    // Apply highlight
                    targetRow.addClass('highlighted-appointment');
                    
                    // Remove highlight after a few seconds
                    setTimeout(() => {
                        targetRow.removeClass('highlighted-appointment');
                    }, 3000); // Highlight for 3 seconds
                }
            }

            // --- View/Edit Appointment (from original appointment.php) ---
            $('#appointmentTableBody').on('click', '.edit_btn', function() {
                const row = $(this).closest('tr');
                const appointmentId = row.data('appointment-id');
                $.ajax({
                    url: appointmentControllerUrl, type: 'GET', data: { action: 'getAppointmentDetails', appointment_id: appointmentId }, dataType: 'json',
                    success: function(appt) {
                        if (appt) {
                            $('#editAppointmentId').val(appt.appointment_id);
                            $('#editOwnerNameInput').val(appt.owner_name);
                            $('#editPetNameInput').val(appt.pet_name);
                            $('#editPetBreedInput').val(appt.pet_breed);
                            $('#editContactNo').val(appt.contact_number);
                            $('#editVisitPurposeSelect').val(appt.purpose_of_visit).trigger('change.select2');
                            $('#editAppointmentDate').val(appt.appointment_date);
                            $('#editAppointmentTime').val(appt.appointment_time);
                            $('#editRemarks').val(appt.remarks);

                            // Determine if fields should be editable based on status
                            const isEditable = (appt.status === 'Pending' || appt.status === 'Confirmed'); // Add other editable statuses if any
                            $('#editVisitPurposeSelect').prop('disabled', !isEditable);
                            $('#editAppointmentDate').prop('readonly', !isEditable);
                            $('#editAppointmentTime').prop('readonly', !isEditable);
                            $('#editRemarks').prop('readonly', !isEditable);
                            
                            if (isEditable) {
                                $('#openCompleteModalFromEdit').show();
                                $('#openCancelConfirmModal').show();
                            } else {
                                $('#openCompleteModalFromEdit').hide();
                                $('#openCancelConfirmModal').hide();
                            }
                            openModal(document.getElementById("editAppointmentModal"));
                        } else { alert("Could not fetch appointment details."); }
                    },
                    error: function(xhr) { alert("Failed to fetch details: " + xhr.responseText); }
                });
            });
            
            // --- Complete Appointment Flow (from original appointment.php) ---
            $('#openCompleteModalFromEdit').on('click', function() {
                const appointmentId = $('#editAppointmentId').val();
                $('#completeAppointmentId').val(appointmentId);
                const serviceName = $('#editVisitPurposeSelect').val();
                const selectedService = servicesData.find(s => s.svc_name === serviceName);
                let totalAmount = 0.00;
                if (selectedService && selectedService.svc_price) {
                    totalAmount = parseFloat(selectedService.svc_price);
                }
                $('#appt-total').val(totalAmount.toFixed(2));
                $('#appt-paid').val('0.00').trigger('input'); 
                closeModal(document.getElementById("editAppointmentModal"));
                openModal(document.getElementById("completeAppointmentModal"));
            });

            $('#appt-paid').on('input', function() {
                const total = parseFloat($('#appt-total').val()) || 0;
                const paid = parseFloat($(this).val()) || 0;
                $('#appt-change').val((paid - total).toFixed(2));
            });

            $('#confirmCompleteBtn').on('click', function() {
                const appointmentId = $('#completeAppointmentId').val();
                const totalAmount = $('#appt-total').val();
                const amountPaid = $('#appt-paid').val();
                if (parseFloat(amountPaid) < parseFloat(totalAmount)) {
                    alert("Amount paid is less than the total amount."); return;
                }
                $.ajax({
                    url: appointmentControllerUrl, type: 'POST',
                    data: { action: 'complete', appointment_id: appointmentId, total_amount: totalAmount, amount_paid: amountPaid, change_amount: $('#appt-change').val() },
                    success: function(response) {
                        alert(response);
                        if (response.toLowerCase().includes("success")) {
                            closeModal(document.getElementById("completeAppointmentModal")); location.reload();
                        }
                    },
                    error: function(xhr) { alert("Failed to complete: " + xhr.responseText); }
                });
            });

            // --- Cancel Appointment Flow (from original appointment.php) ---
            $('#openCancelConfirmModal').on('click', function() {
                $('#cancelAppointmentId').val($('#editAppointmentId').val());
                closeModal(document.getElementById("editAppointmentModal"));
                openModal(document.getElementById("confirmCancelModal"));
            });

            $('#confirmCancelYesBtn').on('click', function() {
                const appointmentId = $('#cancelAppointmentId').val();
                $.ajax({
                    url: appointmentControllerUrl, type: 'POST', data: { action: 'cancel', appointment_id: appointmentId },
                    success: function(response) {
                        alert(response);
                        if (response.toLowerCase().includes("success")) {
                            closeModal(document.getElementById("confirmCancelModal")); location.reload();
                        }
                    },
                    error: function(xhr) { alert("Failed to cancel: " .concat(xhr.responseText)); }
                });
            });
            $('#confirmCancelNoBtn').on('click', function() { 
                closeModal(document.getElementById("confirmCancelModal"));
            });

            // --- Search & Sort (from original appointment.php) ---
            $('#searchInput').on('keyup', function() { 
                const query = $(this).val();
                $.ajax({
                    url: appointmentControllerUrl, type: 'GET', data: { action: 'search', query: query },
                    success: function(htmlRows) { $('#appointmentTableBody').html(htmlRows); },
                    error: function(xhr) { $('#appointmentTableBody').html("<tr><td colspan='9'>Error searching.</td></tr>"); console.error(xhr.responseText); }
                });
            });

            $('#sortAppointments').on('change', function() {
                const status = $(this).val();
                $.ajax({
                    url: appointmentControllerUrl, type: 'GET', data: { action: 'sortByStatus', status: status },
                    success: function(htmlRows) { $('#appointmentTableBody').html(htmlRows); },
                    error: function(xhr) { $('#appointmentTableBody').html("<tr><td colspan='9'>Error sorting.</td></tr>"); console.error(xhr.responseText); }
                });
            });

        }); // End of document.ready
    </script>

</body>
</html>