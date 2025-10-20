<?php
include_once(__DIR__ . '/../../controllers/admin_petProfileController.php');

$controller = new PetProController();
$pets = $controller->getAllPets();
$owners = $controller->getAllOwners();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet & Owner Profiles</title>
    <link rel="stylesheet" href="css/petProfile.css?v=<?php echo time(); ?>">
    <script src="/../../utils/jquery-3.7.1.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
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
    <li><a href="petProfile.php" class="active"><i class='bx bxs-dog'></i><span class="links_name">Pet Profile</span></a></li>
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

        <div class="main-profile-area">
            <div class="profile-section patient-profile-section">
                <div class="profile-section-header">
                    <h2>Pet Profiles</h2>
                    <div class="header-actions">
                        <div class="search-wrapper">
                            <label>
                                <input type="text" id="petSearchInput" placeholder="Search Pets..." class="search-input">
                                <span><i class='bx bx-search'></i></span>
                            </label>
                        </div>
                        <button type="button" class="register-btn register-patient-btn" id="openAddPetModalBtn">
                            <i class='bx bx-plus'></i> Register New Pet
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="profile-data-table">
                        <thead>
                            <tr>
                                <th>Pet ID</th>
                                <th>Pet Name</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Breed</th>
                                <th>Color</th>
                                <th>Markings</th>
                                <th>Date of Birth</th>
                                <th>Medical Notes</th>
                                <th>Registration Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="petTableBody">
                            <?php foreach ($pets as $index => $pet): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pet['pet_id']); ?></td>
                                    <td><?= htmlspecialchars($pet['pet_name']); ?></td>
                                    <td><?= htmlspecialchars($pet['owner_name']); ?></td>
                                    <td><?= htmlspecialchars($pet['species']); ?></td>
                                    <td><?= htmlspecialchars($pet['breed']); ?></td>
                                    <td><?= htmlspecialchars($pet['color']); ?></td>
                                    <td><?= htmlspecialchars($pet['markings']); ?></td>
                                    <td><?=
                                        // Format date if it's a valid date string
                                        ($timestamp = strtotime($pet['dob'])) ? date('Y-m-d', $timestamp) : htmlspecialchars($pet['dob']);
                                    ?></td>
                                    <td>
                                        <?php if (!empty($pet['medical_notes'])): ?>
                                            <button class="view-notes-btn" data-pet-id="<?= htmlspecialchars($pet['pet_id']); ?>">View Notes</button>
                                        <?php else: ?>
                                            No Notes
                                        <?php endif; ?>
                                    </td>
                                    <td><?=
                                        // Format date if it's a valid date string
                                        ($timestamp = strtotime($pet['registration_date'])) ? date('Y-m-d', $timestamp) : htmlspecialchars($pet['registration_date']);
                                    ?></td>
                                    <td class="action-icons">
                                        <button class="edit-btn pet-edit-btn" data-pet-id="<?= htmlspecialchars($pet['pet_id']); ?>"><i class="bx bxs-edit-alt"></i></button>
                                        <button class="delete-btn pet-delete-btn" data-pet-id="<?= htmlspecialchars($pet['pet_id']); ?>"><i class="bx bx-trash"></i></button>
                                        <button class="view-btn pet-history-btn" data-pet-id="<?= htmlspecialchars($pet['pet_id']); ?>"><i class='bx bx-history'></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="profile-section client-profile-section">
                <div class="profile-section-header">
                    <h2>Owner Profiles</h2>
                    <div class="header-actions">
                        <div class="search-wrapper">
                            <label>
                                <input type="text" id="ownerSearchInput" placeholder="Search Owners..." class="search-input">
                                <span><i class='bx bx-search'></i></span>
                            </label>
                        </div>
                        <button type="button" class="register-btn register-client-btn" id="openOwnerModalBtn">
                            <i class='bx bx-plus'></i> Register New Client
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="profile-data-table" id="ownerTable">
                        <thead>
                            <tr>
                                <th>Owner ID</th>
                                <th>Owner Name</th>
                                <th>Contact No.</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Registered Pets</th>
                                <th>Registration Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="ownerTableBody">
                             <?php foreach ($owners as $index => $owner): ?>
                                <tr>
                                    <td><?= htmlspecialchars($owner['owner_id']); ?></td>
                                    <td><?= htmlspecialchars($owner['owner_name']); ?></td>
                                    <td><?= htmlspecialchars($owner['contact_number']); ?></td>
                                    <td><?= htmlspecialchars($owner['address']); ?></td>
                                    <td><?= htmlspecialchars($owner['email']); ?></td>
                                    <td>
                                        <button class="view-pets-btn" data-client-id="<?= htmlspecialchars($owner['owner_id']); ?>" data-client-name="<?= htmlspecialchars($owner['owner_name']); ?>">
                                            View Pets
                                        </button>
                                    </td>
                                    <td><?=
                                        // Format date if it's a valid date string
                                        ($timestamp = strtotime($owner['registration_date'])) ? date('Y-m-d', $timestamp) : htmlspecialchars($owner['registration_date']);
                                    ?></td>
                                    <td class="action-icons">
                                        <button class="edit-btn owner-edit-btn" data-owner-id="<?= htmlspecialchars($owner['owner_id']); ?>"><i class="bx bxs-edit-alt"></i></button>
                                        <button class="delete-btn owner-delete-btn" data-owner-id="<?= htmlspecialchars($owner['owner_id']); ?>"><i class="bx bx-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div id="addPetModal" class="modal">
          <div class="modal-content">
              <span class="close-btn" id="closeAddPetModalBtn">×</span>
              <h2 class="modal-section-title">Register New Pet</h2>
              <form id="petForm" class="modal-profile-form">

                  <div class="form-group">
                      <label for="petOwnerId">Owner: <span class="required-asterisk">*</span></label>
                       <select id="petOwnerId" name="ownerId" required>
                           <option value="">Select Owner</option>
                           <?php foreach ($owners as $owner): ?>
                               <option value="<?= htmlspecialchars($owner['owner_id']); ?>"><?= htmlspecialchars($owner['owner_name']); ?> (ID: <?= htmlspecialchars($owner['owner_id']); ?>)</option>
                           <?php endforeach; ?>
                       </select>
                  </div>

                  <div class="form-group">
                      <label for="petName">Pet's Name: <span class="required-asterisk">*</span></label>
                      <input type="text" id="petName" name="petName" required>
                  </div>

                  <div class="form-group">
                      <label for="petSpecies">Species: <span class="required-asterisk">*</span></label>
                      <select id="petSpecies" name="species" required>
                        <option value="">Select Species</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        </select>
                  </div>

                  <div class="form-group">
                      <label for="petBreed">Breed: <span class="required-asterisk">*</span></label>
                      <input type="text" id="petBreed" name="breed" placeholder="Enter breed" required>
                  </div>

                  <div class="form-group">
                      <label for="petColor">Color:</label>
                      <input type="text" id="petColor" name="color" placeholder="e.g., Black, Tabby">
                  </div>

                  <div class="form-group">
                      <label for="petMarkings">Markings:</label>
                      <input type="text" id="petMarkings" name="markings" placeholder="e.g., White socks, Blaze">
                  </div>

                  <div class="form-group">
                      <label for="petDOB">Date of Birth: <span class="required-asterisk">*</span></label>
                      <input type="date" id="petDOB" name="dob" required>
                  </div>

                  <div class="form-group full-width">
                      <label for="petMedicalNotes">Medical Notes:</label>
                      <textarea id="petMedicalNotes" name="medicalNotes" placeholder="Any relevant medical history..."></textarea>
                  </div>

                  <div class="form-group full-width">
                      <button type="submit" class="submit-btn">Register Pet</button>
                  </div>
              </form>
          </div>
      </div>

        <div id="ownerModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="closeOwnerModalBtn">×</span>
                <h2 class="modal-section-title">Register New Client</h2>
                <form id="ownerForm" class="modal-profile-form">
                    <div class="form-group">
                        <label for="ownerFullName">Full Name: <span class="required-asterisk">*</span></label>
                        <input type="text" id="ownerFullName" name="ownerName" placeholder="Enter owner's full name" required>
                    </div>
                    <div class="form-group">
                        <label for="ownerContact">Contact Number: <span class="required-asterisk">*</span></label>
                        <input type="tel" id="ownerContact" name="contactNumber" pattern="[0-9]{10,15}" placeholder="e.g., 09123456789" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="ownerAddress">Address: <span class="required-asterisk">*</span></label>
                        <input type="text" id="ownerAddress" name="address" placeholder="Enter full address" required>
                    </div>
                    <div class="form-group full-width">
                        <label for="ownerEmail">Email (Optional):</label>
                        <input type="email" id="ownerEmail" name="email" placeholder="example@domain.com">
                    </div>
                    <button type="submit" class="submit-btn">Register Client</button>
                </form>
            </div>
        </div>

        <div id="editPetModal" class="modal">
          <div class="modal-content">
              <span class="close-btn" id="closeEditPetModalBtn">×</span>
              <h2 class="modal-section-title">Edit Pet</h2>
              <form id="editPetForm" class="modal-profile-form edit-layout-form">
                  <input type="hidden" id="editPetIdField" name="petId">

                  <div class="form-group">
                      <label for="editPetOwnerNameDisplay">Owner:</label>
                      <input type="text" id="editPetOwnerNameDisplay" value="" readonly class="readonly-field">
                      <input type="hidden" id="editPetOwnerIdField" name="ownerId">
                  </div>

                  <div class="form-group">
                      <label for="editPetName">Pet's Name: <span class="required-asterisk">*</span></label>
                      <input type="text" id="editPetName" name="petName" required>
                  </div>

                  <div class="form-group">
                      <label for="editPetSpecies">Species: <span class="required-asterisk">*</span></label>
                      <input type="text" id="editPetSpecies" name="species" required>
                  </div>

                  <div class="form-group">
                      <label for="editPetBreed">Breed: <span class="required-asterisk">*</span></label>
                      <input type="text" id="editPetBreed" name="breed" required>
                  </div>

                  <div class="form-group">
                      <label for="editPetColor">Color:</label>
                      <input type="text" id="editPetColor" name="color" placeholder="e.g., Black, Tabby">
                  </div>

                  <div class="form-group">
                      <label for="editPetMarkings">Markings:</label>
                      <input type="text" id="editPetMarkings" name="markings" placeholder="e.g., White socks, Blaze">
                  </div>

                  <div class="form-group">
                      <label for="editPetDOB">Date of Birth: <span class="required-asterisk">*</span></label>
                      <input type="date" id="editPetDOB" name="dob" required>
                  </div>

                  <div class="form-group full-width">
                      <label for="editPetMedicalNotes">Medical Notes:</label>
                      <textarea id="editPetMedicalNotes" name="medicalNotes" placeholder="Any relevant medical history..."></textarea>
                  </div>

                  <div class="form-group full-width">
                      <button type="submit" class="submit-btn update-btn">Update Pet</button>
                  </div>
              </form>
          </div>
      </div>

        <div id="editOwnerModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="closeEditOwnerModalBtn">×</span>
                <h2 class="modal-section-title">Edit Client Profile</h2>
                <form id="editOwnerForm" class="modal-profile-form edit-layout-form">
                    <input type="hidden" id="editOwnerIdField" name="ownerId">
                    <div class="form-row-edit">
                        <div class="form-label-edit"><label for="editOwnerDisplayID">Owner ID</label></div>
                        <div class="form-field-edit">
                            <input type="text" id="editOwnerDisplayID" name="ownerIdDisplay" readonly class="readonly-field">
                        </div>
                    </div>

                    <div class="form-row-edit">
                        <div class="form-label-edit"><label for="editOwnerFullName">Full Name <span class="required-asterisk">*</span></label></div>
                        <div class="form-field-edit">
                            <input type="text" id="editOwnerFullName" name="ownerName" required>
                        </div>
                    </div>

                    <div class="form-row-edit">
                        <div class="form-label-edit"><label for="editOwnerContact">Contact Number <span class="required-asterisk">*</span></label></div>
                        <div class="form-field-edit">
                            <input type="tel" id="editOwnerContact" name="contactNumber" required>
                        </div>
                    </div>

                    <div class="form-row-edit">
                        <div class="form-label-edit"><label for="editOwnerAddress">Address <span class="required-asterisk">*</span></label></div>
                        <div class="form-field-edit">
                            <input type="text" id="editOwnerAddress" name="address" required>
                        </div>
                    </div>

                    <div class="form-row-edit">
                        <div class="form-label-edit"><label for="editOwnerEmail">Email (Optional):</label></div>
                        <div class="form-field-edit">
                            <input type="email" id="editOwnerEmail" name="email">
                        </div>
                    </div>

                    <div class="form-row-edit">
                        <div class="form-label-edit"></div>
                        <div class="form-field-edit">
                            <button type="submit" class="submit-btn update-btn">Update Client</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="ownerPetsModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="closeOwnerPetsModalBtn">×</span>
                <h2 class="modal-section-title" id="ownerPetsModalTitle">Registered Pets</h2>
                <div class="owner-pets-list-container">
                    <ul id="ownerPetsList">
                        </ul>
                </div>
            </div>
        </div>

        <div id="medicalNotesModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="closeMedicalNotesModalBtn">×</span>
                <h2 class="modal-section-title" id="medicalNotesModalTitle">Medical Notes</h2>
                <div id="medicalNotesContent">
                    </div>
            </div>
        </div>

        <div id="petHistoryModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="closePetHistoryModalBtn">×</span>
                <h2 class="modal-section-title">Pet History</h2>

                <div id="petHistoryDetails">
                    <p><strong>Pet ID:</strong> <span id="historyPetId"></span></p>
                    <p><strong>Pet Name:</strong> <span id="historyPetName"></span></p>
                    <p><strong>Owner:</strong> <span id="historyPetOwner"></span> (<span id="historyOwnerContact"></span>)</p>
                    <p><strong>Species:</strong> <span id="historyPetSpecies"></span></p>
                    <p><strong>Breed:</strong> <span id="historyPetBreed"></span></p>
                    <p><strong>Date of Birth:</strong> <span id="historyPetDOB"></span></p>
                    <hr> </div>

                <h3 class="modal-subsection-title">Consultation History</h3>
                <div id="petTransactionsHistory">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Purpose of Visit</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <tr>
                                <td colspan="3" class="no-history-message">Loading history...</td>
                            </tr>
                        </tbody>
                    </table>
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

            const petProfileControllerUrl = '/controllers/admin_petProfileController.php';
            // --- Common Functions ---
            function clearAddPetForm() {
                $('#petForm')[0].reset();
                $('#petOwnerId').val('');
                $('#petSpecies').val('');
            }

            function clearOwnerForm() {
                $('#ownerForm')[0].reset();
            }

            function fillEditPetModal(data) {
                $('#editPetIdField').val(data.pet_id);
                $('#editPetOwnerNameDisplay').val(data.owner_name);
                $('#editPetOwnerIdField').val(data.owner_id);
                $('#editPetName').val(data.pet_name);
                $('#editPetSpecies').val(data.species);
                $('#editPetBreed').val(data.breed);
                $('#editPetColor').val(data.color);
                $('#editPetMarkings').val(data.markings);
                $('#editPetDOB').val(data.dob);
                $('#editPetMedicalNotes').val(data.medical_notes);
            }

            function fillEditOwnerModal(data) {
                $('#editOwnerIdField').val(data.owner_id);
                $('#editOwnerDisplayID').val(data.owner_id);
                $('#editOwnerFullName').val(data.owner_name);
                $('#editOwnerContact').val(data.contact_number);
                $('#editOwnerAddress').val(data.address);
                $('#editOwnerEmail').val(data.email);
            }

             // Function to format date string toYYYY-MM-DD
            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date)) return dateString; // Return original if invalid date
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // --- Modal Setup ---
            function setupModal(modalId, openBtnId, closeBtnId, formId, successMessage) {
                const modal = $('#' + modalId);
                const openBtn = openBtnId ? $('#' + openBtnId) : null;
                const closeBtn = $('#' + closeBtnId);
                const form = formId ? $('#' + formId) : null;

                // Open modal button click
                if (openBtn && openBtn.length) {
                    openBtn.on('click', function() {
                        // Use addClass to make the modal visible and centered
                        modal.addClass('modal-visible');
                        if (modalId === 'addPetModal') {
                            clearAddPetForm();
                        } else if (modalId === 'ownerModal') {
                             clearOwnerForm();
                        }
                    });
                }

                // Close modal button click
                if (closeBtn.length) {
                    closeBtn.on('click', function() {
                        // Use removeClass to hide the modal
                        modal.removeClass('modal-visible');
                         if (form && form.length) {
                            form[0].reset();
                        }
                         if (modalId === 'addPetModal') {
                             clearAddPetForm();
                         } else if (modalId === 'ownerModal') {
                             clearOwnerForm();
                         }
                         // Clear pet history modal content when closed
                         if (modalId === 'petHistoryModal') {
                             $('#historyPetId').text('');
                             $('#historyPetName').text('');
                             $('#historyPetOwner').text('');
                             $('#historyOwnerContact').text(''); // Clear contact
                             $('#historyPetSpecies').text('');
                             $('#historyPetBreed').text('');
                             $('#historyPetDOB').text('');
                             // No need to clear data attribute for removed button
                             $('#historyTableBody').empty();
                             // Add back the loading message placeholder
                             $('#historyTableBody').append('<tr><td colspan="3" class="no-history-message">Loading history...</td></tr>');
                         }
                         // Clear medical notes modal content when closed
                         if (modalId === 'medicalNotesModal') {
                              $('#medicalNotesModalTitle').text('Medical Notes');
                             $('#medicalNotesContent').text('');
                         }
                    });
                }

                // Close modal when clicking outside of the modal-content
                modal.on('click', function(event) {
                    // Check if the click target is the modal background itself
                    if ($(event.target).is(modal)) {
                         // Use removeClass to hide the modal
                        modal.removeClass('modal-visible');
                         if (form && form.length) {
                            form[0].reset();
                        }
                         if (modalId === 'addPetModal') {
                             clearAddPetForm();
                         } else if (modalId === 'ownerModal') {
                             clearOwnerForm();
                         }
                         // Clear pet history modal content when closed by outside click
                         if (modalId === 'petHistoryModal') {
                             $('#historyPetId').text('');
                             $('#historyPetName').text('');
                             $('#historyPetOwner').text('');
                             $('#historyOwnerContact').text(''); // Clear contact
                             $('#historyPetSpecies').text('');
                             $('#historyPetBreed').text('');
                             $('#historyPetDOB').text('');
                             // No need to clear data attribute for removed button
                             $('#historyTableBody').empty();
                             // Add back the loading message placeholder
                             $('#historyTableBody').append('<tr><td colspan="3" class="no-history-message">Loading history...</td></tr>');
                         }
                         // Clear medical notes modal content when closed by outside click
                         if (modalId === 'medicalNotesModal') {
                              $('#medicalNotesModalTitle').text('Medical Notes');
                             $('#medicalNotesContent').text('');
                         }
                    }
                });
            }

            // --- Pet CRUD Operations ---
            $('#petForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: petProfileControllerUrl,
                    type: 'POST',
                    dataType: 'text',
                    data: $(this).serialize() + '&action=addPet&type=pet',
                    success: function(response) {
                        alert(response);
                        $('#addPetModal').removeClass('modal-visible');
                        clearAddPetForm();
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            $('#editPetForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: petProfileControllerUrl,
                    type: 'POST',
                    dataType: 'text',
                    data: $(this).serialize() + '&action=updatePet&type=pet',
                    success: function(response) {
                        alert(response);
                        $('#editPetModal').removeClass('modal-visible');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('An error occurred while updating.');
                    }
                });
            });

            $(document).on('click', '.pet-delete-btn', function() {
                const petId = $(this).data('pet-id');
                if (confirm('Are you sure you want to delete this pet?')) {
                    $.ajax({
                        url: petProfileControllerUrl,
                        type: 'POST',
                        dataType: 'text',
                        data: {
                            action: 'deletePet',
                            petId: petId,
                            type: 'pet'
                        },
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error, xhr.responseText);
                            alert('Error deleting pet.');
                        }
                    });
                }
            });

            $(document).on('click', '.pet-edit-btn', function() {
                const petId = $(this).data('pet-id');
                 $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'getPetDetails',
                        petId: petId
                    },
                    success: function(petData) {
                        if (petData) {
                             fillEditPetModal(petData);
                             $('#editPetModal').addClass('modal-visible');
                        } else {
                            alert('Could not fetch pet details.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Error fetching pet details for editing.');
                    }
                });
            });

            // --- Owner CRUD Operations ---
            $('#ownerForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: petProfileControllerUrl,
                    type: 'POST',
                    dataType: 'text',
                    data: $(this).serialize() + '&action=addOwner&type=owner',
                    success: function(response) {
                        alert(response);
                        $('#ownerModal').removeClass('modal-visible');
                        clearOwnerForm();
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Error adding owner.');
                    }
                });
            });

            $('#editOwnerForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: petProfileControllerUrl,
                    type: 'POST',
                    dataType: 'text',
                    data: $(this).serialize() + '&action=updateOwner&type=owner',
                    success: function(response) {
                        alert(response);
                        $('#editOwnerModal').removeClass('modal-visible');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Error updating owner.');
                    }
                });
            });

            $(document).on('click', '.owner-delete-btn', function() {
                const ownerId = $(this).data('owner-id');
                $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'getOwnerPets',
                        clientId: ownerId
                    },
                    success: function(pets) {
                        if (pets.length > 0) {
                            alert("Please remove existing pets before removing owner.");
                        } else {
                            if (confirm('Are you sure you want to delete this owner?')) {
                                deleteOwnerConfirmed(ownerId);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Could not verify if the owner has pets. Please try again.');
                    }
                });
            });

             function deleteOwnerConfirmed(ownerId) {
                  $.ajax({
                        url: petProfileControllerUrl,
                        type: 'POST',
                        dataType: 'text',
                        data: {
                            action: 'deleteOwner',
                            ownerId: ownerId,
                            type: 'owner'
                        },
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error, xhr.responseText);
                            alert('Error deleting owner.');
                        }
                    });
             }


            $(document).on('click', '.owner-edit-btn', function() {
                const ownerId = $(this).data('owner-id');
                 $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'getOwnerDetails',
                        ownerId: ownerId
                    },
                    success: function(ownerData) {
                        if (ownerData) {
                             fillEditOwnerModal(ownerData);
                             $('#editOwnerModal').addClass('modal-visible');
                        } else {
                            alert('Could not fetch owner details.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Error fetching owner details for editing.');
                    }
                });
            });


            // --- Search Functionality ---
            $('#petSearchInput').on('input', function() {
                let query = $(this).val();
                 $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'searchPets',
                        query: query
                    },
                    success: function(results) {
                        populatePetTableFromSearch(results);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Error searching pets.');
                    }
                });
            });

            $('#ownerSearchInput').on('input', function() {
                let query = $(this).val();
                 $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'searchOwners',
                        query: query
                    },
                    success: function(results) {
                        populateOwnerTableFromSearch(results);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Error searching owners.');
                    }
                });
            });


            // --- Populate Tables from Search Results (Javascript) ---
            function populatePetTableFromSearch(pets) {
                let rows = '';
                $.each(pets, function(i, pet) {
                     const medicalNotesCell = pet.medical_notes ?
                                             `<button class="view-notes-btn" data-pet-id="${htmlspecialchars(pet.pet_id)}">View Notes</button>` :
                                             'No Notes';
                    rows += `
                        <tr>
                            <td>${htmlspecialchars(pet.pet_id)}</td>
                            <td>${htmlspecialchars(pet.pet_name)}</td>
                            <td>${htmlspecialchars(pet.owner_name)}</td>
                            <td>${htmlspecialchars(pet.species)}</td>
                            <td>${htmlspecialchars(pet.breed)}</td>
                            <td>${htmlspecialchars(pet.color)}</td>
                            <td>${htmlspecialchars(pet.markings)}</td>
                            <td>${formatDate(pet.dob)}</td>
                            <td>${medicalNotesCell}</td>
                            <td>${formatDate(pet.registration_date)}</td>
                            <td class="action-icons">
                                <button class="edit-btn pet-edit-btn" data-pet-id="${htmlspecialchars(pet.pet_id)}"><i class="bx bxs-edit-alt"></i></button>
                                <button class="delete-btn pet-delete-btn" data-pet-id="${htmlspecialchars(pet.pet_id)}"><i class="bx bx-trash"></i></button>
                                 <button class="view-btn pet-history-btn" data-pet-id="${htmlspecialchars(pet.pet_id)}"><i class='bx bx-history'></i></button>
                            </td>
                        </tr>
                    `;
                });
                $('#petTableBody').html(rows);
            }

             function populateOwnerTableFromSearch(owners) {
                let rows = '';
                $.each(owners, function(i, owner) {
                    rows += `
                        <tr>
                            <td>${htmlspecialchars(owner.owner_id)}</td>
                            <td>${htmlspecialchars(owner.owner_name)}</td>
                            <td>${htmlspecialchars(owner.contact_number)}</td>
                            <td>${htmlspecialchars(owner.address)}</td>
                            <td>${htmlspecialchars(owner.email)}</td>
                            <td>
                                <button class="view-pets-btn" data-client-id="${htmlspecialchars(owner.owner_id)}" data-client-name="${htmlspecialchars(owner.owner_name)}">
                                    View Pets
                                </button>
                            </td>
                            <td>${formatDate(owner.registration_date)}</td>
                            <td class="action-icons">
                                <button class="edit-btn owner-edit-btn" data-owner-id="${htmlspecialchars(owner.owner_id)}"><i class="bx bxs-edit-alt"></i></button>
                                <button class="delete-btn owner-delete-btn" data-owner-id="${htmlspecialchars(owner.owner_id)}"><i class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
                $('#ownerTableBody').html(rows);
            }


            // --- View Owner Pets ---
            $(document).on('click', '.view-pets-btn', function() {
                const ownerId = $(this).data('client-id');
                const ownerName = $(this).data('client-name');
                $('#ownerPetsModalTitle').text(`Registered Pets for ${htmlspecialchars(ownerName)}`);
                $('#ownerPetsList').empty();

                $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'getOwnerPets',
                        clientId: ownerId
                    },
                    success: function(pets) {
                        if (pets.length > 0) {
                            $.each(pets, function(i, pet) {
                                $('#ownerPetsList').append(`<li><strong>${htmlspecialchars(pet.pet_name)}</strong> (${htmlspecialchars(pet.species)}, ${htmlspecialchars(pet.breed)})</li>`);
                            });
                        } else {
                            $('#ownerPetsList').append('<li>No pets registered for this owner.</li>');
                        }
                        $('#ownerPetsModal').addClass('modal-visible');
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Error fetching owner pets.');
                    }
                });
            });

            // --- View Medical Notes ---
            $(document).on('click', '.view-notes-btn', function() {
                const petId = $(this).data('pet-id');

                $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'getPetDetails',
                        petId: petId
                    },
                    success: function(petData) {
                        if (petData) {
                            $('#medicalNotesModalTitle').text(`Medical Notes for ${htmlspecialchars(petData.pet_name)}`);
                            $('#medicalNotesContent').text(petData.medical_notes || 'No medical notes available.'); // Use .text() for safety
                            $('#medicalNotesModal').addClass('modal-visible');
                        } else {
                             $('#medicalNotesModalTitle').text(`Medical Notes`);
                            $('#medicalNotesContent').text('Could not fetch medical notes.');
                             $('#medicalNotesModal').addClass('modal-visible');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error, xhr.responseText);
                        alert('Error fetching medical notes.');
                    }
                });
            });

            // --- View Pet History ---
            $(document).on('click', '.pet-history-btn', function() {
                const petId = $(this).data('pet-id');

                // Clear previous history data
                $('#historyPetId').text('');
                $('#historyPetName').text('');
                $('#historyPetOwner').text('');
                $('#historyOwnerContact').text(''); // Clear contact
                $('#historyPetSpecies').text('');
                $('#historyPetBreed').text('');
                $('#historyPetDOB').text('');
                // No need to clear data attribute for removed button
                $('#historyTableBody').empty();
                 $('#historyTableBody').append('<tr><td colspan="3" class="no-history-message">Loading history...</td></tr>'); // Add loading message

                // Fetch pet details and transaction history concurrently
                const petDetailsRequest = $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'getPetDetails',
                        petId: petId
                    }
                });

                const petHistoryRequest = $.ajax({
                    url: petProfileControllerUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'getPetHistory',
                        petId: petId
                    }
                });

                // Use $.when to handle both requests completing
                $.when(petDetailsRequest, petHistoryRequest)
                    .done(function(petDataResponse, petHistoryResponse) {
                        const petData = petDataResponse[0]; // Data is the first element in the response array
                        const petHistory = petHistoryResponse[0];

                        // Populate pet details section
                        if (petData) {
                            $('#historyPetId').text(htmlspecialchars(petData.pet_id));
                            $('#historyPetName').text(htmlspecialchars(petData.pet_name));
                            $('#historyPetOwner').text(htmlspecialchars(petData.owner_name));
                            // Populate owner contact number
                            $('#historyOwnerContact').text(htmlspecialchars(petData.contact_number || 'N/A'));
                            $('#historyPetSpecies').text(htmlspecialchars(petData.species));
                            $('#historyPetBreed').text(htmlspecialchars(petData.breed));
                            $('#historyPetDOB').text(formatDate(petData.dob));
                            // No need to set data attribute for removed button
                        } else {
                            // Handle case where pet details couldn't be fetched
                            $('#petHistoryDetails').html('<p>Error loading pet details.</p>');
                        }

                        // Populate transaction history table
                        $('#historyTableBody').empty(); // Clear loading message

                        if (petHistory && petHistory.length > 0) {
                            $.each(petHistory, function(i, historyItem) {
                                const row = `
                                    <tr>
                                        <td>${formatDate(historyItem.appointment_date || historyItem.transaction_date)}</td>
                                        <td>${htmlspecialchars(historyItem.purpose_of_visit || 'N/A')}</td>
                                         <td>${htmlspecialchars(historyItem.remarks || 'No notes')}</td>
                                    </tr>
                                `;
                                $('#historyTableBody').append(row);
                            });
                        } else {
                            $('#historyTableBody').append('<tr><td colspan="3" class="no-history-message">No transaction history found for this pet.</td></tr>');
                        }

                        // Show the history modal
                        $('#petHistoryModal').addClass('modal-visible');
                    })
                    .fail(function(xhr, status, error) {
                        console.error("AJAX Error fetching pet history:", status, error, xhr.responseText);
                         $('#historyTableBody').empty(); // Clear loading message
                        $('#historyTableBody').append('<tr><td colspan="3" class="no-history-message">Error loading history.</td></tr>');
                        alert('Error loading pet history.');
                         // Still show the modal even if there's an error, but with error message
                        $('#petHistoryModal').addClass('modal-visible');
                    });
            });

            // --- Modal Setup Calls ---
            setupModal('addPetModal', 'openAddPetModalBtn', 'closeAddPetModalBtn', 'petForm', 'Pet Registered Successfully!');
            setupModal('ownerModal', 'openOwnerModalBtn', 'closeOwnerModalBtn', 'ownerForm', 'Owner Registered Successfully!');
            setupModal('editPetModal', null, 'closeEditPetModalBtn', 'editPetForm', 'Pet details updated!');
            setupModal('editOwnerModal', null, 'closeEditOwnerModalBtn', 'editOwnerForm', 'Owner details updated!');
            setupModal('ownerPetsModal', null, 'closeOwnerPetsModalBtn', null, null);
            setupModal('medicalNotesModal', null, 'closeMedicalNotesModalBtn', null, null);
            // Setup call for the new Pet History Modal
            setupModal('petHistoryModal', null, 'closePetHistoryModalBtn', null, null);


            // --- Sidebar and User Dropdown ---
             let btn = document.querySelector("#btn");
            let sidebar = document.querySelector(".sidebar");

             btn.onclick = function() {
                 sidebar.classList.toggle("open");
             }

            // User dropdown logic
            const userToggle = document.getElementById('user-toggle');
            // Check if userToggle exists before adding event listener
            if (userToggle) {
                userToggle.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const dropdownMenu = userToggle.nextElementSibling;
                    if (dropdownMenu) {
                        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
                    }
                });

                 // Close the dropdown if the user clicks outside of it
                 document.addEventListener('click', function(event) {
                     const userToggle = document.getElementById('user-toggle'); // Re-fetch to be safe
                     const dropdownMenu = userToggle ? userToggle.nextElementSibling : null;

                    if (userToggle && dropdownMenu && !userToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                        dropdownMenu.style.display = 'none';
                    }
                });
            }

            // Helper function for HTML escaping (basic)
            function htmlspecialchars(str) {
                if (typeof str != 'string') return str;
                return str.replace(/&/g, '&amp;')
                          .replace(/</g, '&lt;')
                          .replace(/>/g, '&gt;')
                          .replace(/"/g, '&quot;')
                          .replace(/'/g, '&#039;');
            }
        });
    </script>
</body>
</html>