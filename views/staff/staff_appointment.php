<?php
include_once(__DIR__ . '/../../controllers/staff_appointmentController.php');
include_once(__DIR__ . '/../../controllers/staff_petProfileController.php');

$controller = new AppointmentController();
$controller2 = new PetProController();

$appointments = $controller->getAllAppointments();
$owners = $controller2->getAllOwners();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="css/staff_appointment.css?v=<?= time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="/../../utils/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
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
        	<li><a href="staff_dashboard.php"><i class='bx bxs-dashboard'></i><span class="links_name">Dashboard</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_appointment.php" class="active"><i class='bx bxs-calendar-event'></i><span class="links_name">Appointments</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_petProfile.php"><i class='bx bxs-dog'></i><span class="links_name">Pet History & Profiles</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_POS.php"><i class='bx bx-line-chart'></i><span class="links_name">Point of Sale</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_transactionHistory.php"><i class='bx bxs-report' ></i><span class="links_name">Transaction History</span></a></li>
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

                                <div class="right-bttn">
                                    <button type="button" class="add-appointment-btn">
                                        <i class='bx bx-plus'></i>Add Appointment
                                    </button>
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
                                            // MODIFIED: Changed button to match admin style
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


        <div id="addAppointmentModal" class="modal-overlay1" style="display: none;">
            <div class="modal-content">
                <span class="close-btn" id="closeAddAppointmentModal">&times;</span>
                <h2 class="section-title">Add New Appointment</h2>

                <form id="addAppointmentForm">
                    <div class="form-group">
                        <label for="ownerSelect">Pet's Owner Name <span style="color: red;">*</span></label>
                        <select id="ownerSelect" name="owner_id" required style="width: 100%;">
                            <option value="">-- Select Owner --</option>
                            <?php foreach ($owners as $owner) : ?>
                                <option value="<?= htmlspecialchars($owner['owner_id']) ?>"><?= htmlspecialchars($owner['owner_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                         <label for="petSelect">Pet's Name and Breed <span style="color: red;">*</span></label>
                         <select id="petSelect" name="pet_id" required style="width: 100%;">
                             <option value="">-- Select Pet --</option>
                             </select>
                     </div>


                    <div class="form-group">
                        <label for="contactNoInput">Contact No. <span style="color: red;">*</span></label>
                        <input type="text" id="contactNoInput" name="contactNo" placeholder="Enter contact number" readonly>
                    </div>

                    <div class="form-group">
                        <label for="visitPurposeInput">Type of Service <span style="color: red;">*</span></label>
                         <select name="visitPurpose" id="visitPurposeInput" required style="width: 100%;">
                             <option value="">-- Select One --</option>
                             </select>
                    </div>

                    <div class="form-group">
                        <label for="appointmentDateInput">Appointment Date <span style="color: red;">*</span></label>
                        <input type="date" id="appointmentDateInput" name="appointmentDate" required>
                    </div>

                    <div class="form-group">
                        <label for="appointmentTimeInput">Appointment Time <span style="color: red;">*</span></label>
                        <input type="time" id="appointmentTimeInput" name="appointmentTime" required>
                    </div>

                    <div class="form-group">
                        <label for="remarksInput">Remarks</label>
                        <textarea id="remarksInput" name="remarks" placeholder="Anything you want us to know?"></textarea>
                    </div>

                    <button type="submit" class="submit-btn">ADD APPOINTMENT</button>
                </form>
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
            // ** IMPORTANT: Update this URL to your correct controller path **
            const appointmentControllerUrl = '/controllers/staff_appointmentController.php';
             let servicesData = []; // Variable to store services data including prices

            // --- Get modal element references ---
            const addAppointmentModal = document.getElementById('addAppointmentModal');
            const editAppointmentModal = document.getElementById("editAppointmentModal");
            const completeAppointmentModal = document.getElementById("completeAppointmentModal");
            const confirmCancelModal = document.getElementById("confirmCancelModal");


            // --- Get button and close button references ---
            const addAppointmentBtn = document.querySelector('.add-appointment-btn');
            const closeAddAppointmentModalBtn = document.getElementById('closeAddAppointmentModal');
            const closeEditAppointmentModalBtn = document.getElementById("closeEditAppointmentModal");
            const closeCompleteModalBtn = document.getElementById("closeCompleteModal");
            // const closeCancelConfirmModalBtn = document.getElementById("closeCancelConfirmModal"); // No longer needed for specific close button

            // --- Get form and table body references ---
            const addAppointmentForm = document.getElementById('addAppointmentForm');
            const editAppointmentForm = document.getElementById("editAppointmentForm");
            const appointmentTableBody = document.getElementById("appointmentTableBody");

            // --- Get specific button references in modals ---
            const openCompleteModalFromEditBtn = document.getElementById("openCompleteModalFromEdit");
            const openCancelConfirmModalBtn = document.getElementById("openCancelConfirmModal");
            const confirmCompleteBtn = document.getElementById("confirmCompleteBtn");
            const confirmCancelYesBtn = document.getElementById("confirmCancelYesBtn");
            const confirmCancelNoBtn = document.getElementById("confirmCancelNoBtn");


            // Initialize Select2 for dropdowns
            $('#ownerSelect').select2({
                 dropdownParent: $('#addAppointmentModal')
            });
             $('#petSelect').select2({
                dropdownParent: $('#addAppointmentModal')
             });
             $('#visitPurposeInput').select2({
                dropdownParent: $('#addAppointmentModal')
             });
             $('#editVisitPurposeSelect').select2({
                dropdownParent: $('#editAppointmentModal')
             });


            // --- Fetch Services on Page Load and Populate Dropdowns ---
            $.ajax({
                url: appointmentControllerUrl,
                type: 'GET',
                data: { action: 'getServices' },
                dataType: 'json',
                success: function(services) {
                    servicesData = services;
                    const serviceSelectAdd = $('#visitPurposeInput');
                    const serviceSelectEdit = $('#editVisitPurposeSelect');

                    serviceSelectAdd.empty().append('<option value="">-- Select One --</option>');
                    serviceSelectEdit.empty().append('<option value="">-- Select One --</option>');

                    if (services.length > 0) {
                        services.forEach(service => {
                            serviceSelectAdd.append(new Option(service.svc_name, service.svc_name));
                            serviceSelectEdit.append(new Option(service.svc_name, service.svc_name));
                        });
                    }

                    serviceSelectAdd.trigger('change');
                    serviceSelectEdit.trigger('change');
                },
                error: function(xhr) {
                    console.error("Error fetching services:", xhr.responseText);
                }
            });

            // ADDED: Highlight Appointment from URL (from Admin)
            const urlParams = new URLSearchParams(window.location.search);
            const highlightId = urlParams.get('highlight_id');

            if (highlightId) {
                const escapedHighlightId = highlightId.replace(/([ #;&,.+*~\':"!^$\[\]()=>|\/@])/g,'\\$1');
                const targetRow = $(`#appointmentTableBody tr[data-appointment-id="${escapedHighlightId}"]`);

                if (targetRow.length) {
                    $('html, body').animate({
                        scrollTop: targetRow.offset().top - 100
                    }, 500);
                    targetRow.addClass('highlighted-appointment');
                    setTimeout(() => {
                        targetRow.removeClass('highlighted-appointment');
                    }, 3000);
                }
            }


            // --- Sidebar Toggle ---
            const sidebar = document.querySelector(".sidebar");
            const closeBtn = document.querySelector("#btn");
            closeBtn?.addEventListener("click", (e) => {
                e.stopPropagation();
                sidebar.classList.toggle("open");
                changeBtn();
            });

            function changeBtn() {
                if (sidebar && closeBtn) {
                    if (sidebar.classList.contains("open")) {
                        closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
                    } else {
                        closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
                    }
                }
            }

            // --- User Dropdown Toggle ---
            const userToggleBtn = document.getElementById('user-toggle');
            if(userToggleBtn) { userToggleBtn.addEventListener('click', (event) => { event.stopPropagation(); }); }


            // --- General Modal Close Logic ---
            function closeModal(modalElement) {
                if (modalElement) modalElement.style.display = "none";
            }
            function openModal(modalElement) {
                if (modalElement) modalElement.style.display = "flex";
            }
            // Close buttons
            closeAddAppointmentModalBtn?.addEventListener("click", () => closeModal(addAppointmentModal));
            closeEditAppointmentModalBtn?.addEventListener("click", () => closeModal(editAppointmentModal));
            closeCompleteModalBtn?.addEventListener("click", () => closeModal(completeAppointmentModal));
            confirmCancelNoBtn?.addEventListener("click", () => closeModal(confirmCancelModal));
            // Close on overlay click
            $('.modal-overlay1').on('click', function(event) {
                if (event.target === this) {
                    closeModal(this);
                }
            });


            // --- Add Appointment Modal ---
            addAppointmentBtn?.addEventListener("click", () => openModal(addAppointmentModal));

            // Reset add form when modal is closed
            $('#addAppointmentModal').on('hidden.bs.modal', function () {
                 addAppointmentForm.reset();
                 $('#ownerSelect').val('').trigger('change');
                 $('#petSelect').empty().append('<option value="">-- Select Pet --</option>').trigger('change');
                 $('#visitPurposeInput').val('').trigger('change');
                 $('#contactNoInput').val('');
            });

            // Handle owner selection change
            $('#ownerSelect').on('change', function() {
                const ownerId = $(this).val();
                const petSelect = $('#petSelect');
                petSelect.empty().append('<option value="">-- Select Pet --</option>');
                $('#contactNoInput').val('');

                 if (ownerId) {
                     const selectedOwner = <?php echo json_encode($owners); ?>.find(owner => owner.owner_id == ownerId);
                     if (selectedOwner) {
                         $('#contactNoInput').val(selectedOwner.contact_number);
                     }
                     $.ajax({
                         url: appointmentControllerUrl,
                         type: 'GET',
                         data: { action: 'getOwnerPets', owner_id: ownerId },
                         dataType: 'json',
                         success: function(pets) {
                             if (pets.length > 0) {
                                 pets.forEach(pet => {
                                     petSelect.append(new Option(pet.pet_name + ' (' + pet.breed + ')', pet.pet_id));
                                 });
                             }
                              petSelect.trigger('change');
                         },
                         error: function(xhr) {
                             console.error("Error fetching pets:", xhr.responseText);
                             petSelect.trigger('change');
                         }
                     });
                 } else {
                     petSelect.trigger('change');
                 }
            });


            // Handle Add Appointment Form Submission
            $('#addAppointmentForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: appointmentControllerUrl,
                    type: 'POST',
                    data: $(this).serialize() + '&action=add',
                    success: function(response) {
                        alert(response);
                        closeModal(addAppointmentModal);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert("Failed to add appointment: " + xhr.responseText);
                    }
                });
            });


            // --- Edit Appointment Modal (View) ---
            $('#appointmentTableBody').on('click', '.edit_btn', function() {
                const row = $(this).closest('tr');
                const appointmentId = row.data('appointment-id');

                $.ajax({
                    url: appointmentControllerUrl,
                    type: 'GET',
                    data: { action: 'getAppointmentDetails', appointment_id: appointmentId },
                    dataType: 'json',
                    success: function(appt) {
                        if (appt) {
                            $('#editAppointmentId').val(appt.appointment_id);
                            $('#editOwnerNameInput').val(appt.owner_name);
                            $('#editPetNameInput').val(appt.pet_name);
                            $('#editPetBreedInput').val(appt.pet_breed);
                            $('#editContactNo').val(appt.contact_number);
                            $('#editVisitPurposeSelect').val(appt.purpose_of_visit).trigger('change');
                            $('#editAppointmentDate').val(appt.appointment_date);
                            $('#editAppointmentTime').val(appt.appointment_time);
                            $('#editRemarks').val(appt.remarks);

                            const isEditable = (appt.status === 'Pending' || appt.status === 'Confirmed');
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
                            openModal(editAppointmentModal);
                        } else {
                            alert("Could not fetch appointment details.");
                        }
                    },
                    error: function(xhr) {
                        alert("Failed to fetch details: " + xhr.responseText);
                    }
                });
            });

            // --- Complete Appointment Flow ---
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
                closeModal(editAppointmentModal);
                openModal(completeAppointmentModal);
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
                    alert("Amount paid is less than the total amount.");
                    return;
                }
                $.ajax({
                    url: appointmentControllerUrl, type: 'POST',
                    data: {
                        action: 'complete',
                        appointment_id: appointmentId,
                        total_amount: totalAmount,
                        amount_paid: amountPaid,
                        change_amount: $('#appt-change').val()
                    },
                    success: function(response) {
                        alert(response);
                        if (response.toLowerCase().includes("success")) {
                            closeModal(completeAppointmentModal);
                            location.reload();
                        }
                    },
                    error: function(xhr) { alert("Failed to complete: " + xhr.responseText); }
                });
            });


            // --- Cancel Appointment Flow ---
            $('#openCancelConfirmModal').on('click', function() {
                $('#cancelAppointmentId').val($('#editAppointmentId').val());
                closeModal(editAppointmentModal);
                openModal(confirmCancelModal);
            });

            $('#confirmCancelYesBtn').on('click', function() {
                const appointmentId = $('#cancelAppointmentId').val();
                $.ajax({
                    url: appointmentControllerUrl,
                    type: 'POST',
                    data: { action: 'cancel', appointment_id: appointmentId },
                    success: function(response) {
                        alert(response);
                        if (response.toLowerCase().includes("success")) {
                            closeModal(confirmCancelModal);
                            location.reload();
                        }
                    },
                    error: function(xhr) { alert("Failed to cancel: " + xhr.responseText); }
                });
            });


            // --- Search & Sort Functionality ---
            $('#searchInput').on('keyup', function() {
                const query = $(this).val();
                $.ajax({
                    url: appointmentControllerUrl, type: 'GET', data: { action: 'search', query: query },
                    success: function(htmlRows) { $('#appointmentTableBody').html(htmlRows); },
                    error: function(xhr) {
                        $('#appointmentTableBody').html("<tr><td colspan='9'>Error searching.</td></tr>");
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#sortAppointments').on('change', function() {
                const status = $(this).val();
                $.ajax({
                    url: appointmentControllerUrl, type: 'GET', data: { action: 'sortByStatus', status: status },
                    success: function(htmlRows) { $('#appointmentTableBody').html(htmlRows); },
                    error: function(xhr) {
                        $('#appointmentTableBody').html("<tr><td colspan='9'>Error sorting.</td></tr>");
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

</body>

</html>