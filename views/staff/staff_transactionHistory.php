<?php
include_once(__DIR__ . '/../../controllers/staff_transactionHistoryController.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaction History</title>
  <link rel="stylesheet" href="css/staff_transactionHistory.css?v=<?= time(); ?>">
  <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="/../../utils/jquery-3.7.1.js"></script>
</head>
<body>
<!---SideBar--->
    <div class="sidebar">
        <div class="logo_details">
        	<i class='bx bx-code-alt'></i>
        	<div class="logo_name">VetSync</div>
        </div>

        <ul>
        	<li><a href="staff_dashboard.php"><i class='bx bxs-dashboard'></i><span class="links_name">Dashboard</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_appointment.php"><i class='bx bxs-calendar-event'></i><span class="links_name">Appointments</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_petProfile.php"><i class='bx bxs-dog'></i><span class="links_name">Pet History & Profiles</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_POS.php"><i class='bx bx-line-chart'></i><span class="links_name">Point of Sale</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_transactionHistory.php" class="active"><i class='bx bxs-report' ></i><span class="links_name">Transaction History</span></a></li>
        	<div class="sidebar-line"></div>
        	<br><br><br>
		<li class="login"><a href="../logout.php"><span class="links_name login_out">Logout</span><i class='bx bx-log-out' id="log_out"></i></a></li>      
        </ul>
    </div>
<!-- End Sideber -->
  <section class="home_section">
        <div class="topbar">
            <div class="toggle"><i class="bx bx-menu" id="btn"></i></div>
                <div class="user_wrapper">
                    <div class="user-menu" id="user-toggle"><i class='bx bx-user' id="profile-icon"></i><span class="user-name">   Receptionist</span>
                </div>
            </div>
        </div>
        <div class="tab-container">
      <div class="filter-bar">
        <label for="timeFilter">Filter by Date:</label>
        <select id="timeFilter">
          <option value="all">All Time</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
        </select>
      </div>

      <div class="tabs">
        <button class="tab-btn active" onclick="openTab(event, 'appointmentHistory')">Appointment History</button>
        <button class="tab-btn" onclick="openTab(event, 'salesHistory')">Sales History</button>
      </div>

      <div class="tab-content" id="appointmentHistory">
        <div class="card">
          <div class="staff_table_wrapper">
            <table class="staff_table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Appointment ID </th>
                  <th>Date & Time</th>
                  <th>Pet Name</th>
                  <th>Owner Name</th>
                  <th>Service</th>
                  <th>Total Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="appointmentHistoryTableBody">
                </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="tab-content" id="salesHistory" style="display: none;">
        <div class="card">
          <div class="staff_table_wrapper">
            <table class="staff_table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Date & Time</th>
                  <th>Total Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="salesHistoryTableBody">
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

    <div id="transactionDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Transaction Details</h2>
            <div id="modalTransactionDetails">
                </div>
             <button class="receipt-print-btn" id="printReceiptBtn">Print Receipt</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // ** IMPORTANT: Update this URL to your correct controller path **
            const transactionHistoryControllerUrl = '/controllers/admin_transactionHistoryController.php';

             // --- Sidebar Toggle ---
            // Renamed closeBtn to sidebarToggleBtn to avoid conflict
            let sidebar = document.querySelector(".sidebar");
            let sidebarToggleBtn = document.querySelector("#btn");

            sidebarToggleBtn.addEventListener("click", () => {
                sidebar.classList.toggle("open");
                changeBtn();
            });

            function changeBtn() {
                if(sidebar.classList.contains("open")) {
                    sidebarToggleBtn.classList.replace("bx-menu", "bx-menu-alt-right");
                } else {
                    sidebarToggleBtn.classList.replace("bx-menu-alt-right", "bx-menu");
                }
            }

            // --- Tab Functionality ---
            // Function to open a specific tab
             function openTab(evt, tabName) {
                const tabContents = document.querySelectorAll(".tab-content");
                const tabButtons = document.querySelectorAll(".tab-btn");

                // Hide all tab content and remove 'active' class from buttons
                tabContents.forEach(content => content.style.display = "none");
                tabButtons.forEach(btn => btn.classList.remove("active"));

                // Show the selected tab content and add 'active' class to the clicked button
                document.getElementById(tabName).style.display = "block";
                evt.currentTarget.classList.add("active");

                // Load data based on the active tab and current filter setting
                const selectedFilter = $('#timeFilter').val();
                if (tabName === 'appointmentHistory') {
                    loadAppointmentTransactions(selectedFilter);
                } else if (tabName === 'salesHistory') {
                    loadSalesTransactions(selectedFilter);
                }
             }
             // Make openTab globally accessible as it's used in onclick attributes in the HTML
             window.openTab = openTab;


            // --- Filter Functionality ---
            // Apply filter change to the currently active tab
            $('#timeFilter').on('change', function() {
                const selectedFilter = $(this).val();
                // Find the currently active tab button and get its associated tab name
                const activeTabButton = $('.tab-btn.active');
                 // Extract the tab name from the onclick attribute
                const activeTab = activeTabButton.attr('onclick').match(/'(.*?)'/)[1];


                if (activeTab === 'appointmentHistory') {
                    loadAppointmentTransactions(selectedFilter);
                } else if (activeTab === 'salesHistory') {
                    loadSalesTransactions(selectedFilter);
                }
            });


            // --- Function to Load Appointment Transactions ---
            function loadAppointmentTransactions(filter) {
                const tableBody = $('#appointmentHistoryTableBody');
                // Adjusted colspan to 8 to include the new 'Action' column
                tableBody.html('<tr><td colspan="8">Loading...</td></tr>'); // Show loading message

                $.ajax({
                    url: transactionHistoryControllerUrl, // URL to your PHP controller
                    type: 'GET', // Use GET method to fetch data
                    data: {
                        action: 'getAppointmentTransactions', // Specify the action to get details
                        filter: filter // Pass the selected filter
                    },
                    success: function(response) {
                        // Populate table body with the rendered rows received from the PHP controller
                        tableBody.html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching appointment transactions:", error);
                        // Adjusted colspan to 8 for the error message
                        tableBody.html('<tr><td colspan="8">Error loading transactions.</td></tr>');
                    }
                });
            }

             // --- Function to Load Sales Transactions ---
             function loadSalesTransactions(filter) {
                const tableBody = $('#salesHistoryTableBody');
                // Adjusted colspan to 4 for the sales table (removed 2 columns)
                tableBody.html('<tr><td colspan="4">Loading...</td></tr>'); // Show loading message

                $.ajax({
                    url: transactionHistoryControllerUrl, // URL to your PHP controller
                    type: 'GET', // Use GET method to fetch data
                    data: {
                        action: 'getSalesTransactions', // Specify the action to get sales
                        filter: filter // Pass the selected filter
                    },
                    success: function(response) {
                        // Populate table body with the rendered rows received from the PHP controller
                        tableBody.html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching sales transactions:", error);
                        // Adjusted colspan to 4 for the error message
                        tableBody.html('<tr><td colspan="4">Error loading sales transactions.</td></tr>');
                    }
                });
            }


            // --- Initial Load on Page Ready ---
             // Ensure the Appointment History tab is active and load its data when the page loads
             document.getElementById("appointmentHistory").style.display = "block";
             // Assuming the first button is for Appointment History, set it as active
             document.querySelector(".tab-btn").classList.add("active");

            // Load appointment transactions by default when the page is ready
            loadAppointmentTransactions('all');


            // --- User Dropdown Toggle (Copy from appointmentView.php if needed) ---
             // Assuming this is part of your standard layout, copy the JS for user dropdown
             const userToggleBtn = document.getElementById('user-toggle');
             const userDropdown = document.getElementById('user-dropdown');

             userToggleBtn?.addEventListener('click', function(e) {
                 e.stopPropagation(); // Prevent click from closing dropdown immediately
                 if (userDropdown) {
                     // Toggle display between 'block' and 'none'
                     userDropdown.style.display = (userDropdown.style.display === 'block') ? 'none' : 'block';
                 }
             });

             // Close dropdown when clicking outside of it
             document.addEventListener('click', function(e) {
                 if (userDropdown && userDropdown.style.display === 'block') {
                     // Check if the click target is outside the toggle button and the dropdown menu
                     if (userToggleBtn && !userToggleBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                         userDropdown.style.display = 'none';
                     }
                 }
             });
             // --- End User Dropdown Toggle ---


            // --- Modal Functionality ---
            const modal = $('#transactionDetailsModal'); // Select the modal element
            // This closeBtn is for the modal, distinct from the sidebar toggle
            const modalCloseBtn = $('.modal .close-btn'); // Select the close button inside the modal
            const modalDetailsDiv = $('#modalTransactionDetails'); // Div to populate with details
            const printReceiptBtn = $('#printReceiptBtn'); // Select the print button

            // When the user clicks on a "View Transaction" button, open the modal
            // Use event delegation on the tab content containers because buttons are added dynamically
            $('#appointmentHistory, #salesHistory').on('click', '.view-transaction-btn', function() {
                // Get the transaction ID and type from the data attributes
                const transactionId = $(this).data('transaction-id');
                const transactionType = $(this).data('transaction-type'); // 'appointment' or 'sale'

                // Call the appropriate function to fetch and display transaction details
                if (transactionType === 'appointment') {
                    fetchAppointmentDetails(transactionId);
                     // Hide print button for appointment transactions if not applicable
                    printReceiptBtn.hide();
                } else if (transactionType === 'sale') {
                    fetchSalesDetails(transactionId);
                    // Show print button for sales transactions
                    printReceiptBtn.show();
                }
            });

            // When the user clicks on <span> (x), close the modal
            modalCloseBtn.on('click', function() {
                modal.hide(); // Hide the modal
                modalDetailsDiv.empty(); // Clear previous details when closing
            });

            // When the user clicks anywhere outside of the modal content, close it
            $(window).on('click', function(event) {
                // Check if the clicked element is the modal background itself
                if ($(event.target).is(modal)) {
                    modal.hide(); // Hide the modal
                    modalDetailsDiv.empty(); // Clear previous details when closing
                }
            });

            // Handle print button click
            printReceiptBtn.on('click', function() {
                // Trigger the browser's print functionality
                window.print();
            });


            // Function to fetch appointment details via AJAX
            function fetchAppointmentDetails(transactionId) {
                modalDetailsDiv.html('<p>Loading details...</p>'); // Show loading message
                 // Set modal title for Appointment Details
                modal.find('h2').text('Appointment Details');

                $.ajax({
                    url: transactionHistoryControllerUrl, // URL to your PHP controller
                    type: 'GET', // Use GET method to fetch data
                    data: {
                        action: 'getTransactionDetails', // Specify the action to get details
                        transaction_id: transactionId // Pass the transaction ID
                    },
                    dataType: 'json', // Expect a JSON response from the server
                    success: function(response) {
                        // Check if the response is a valid object and not an error response
                        if (response && typeof response === 'object' && !response.error) {
                            // Populate the modal's elements with the received transaction details
                            let detailsHtml = `
                                <div class="appointment-details-content">
                                    <p><strong>Transaction ID:</strong> ${response.transaction_id ?? 'N/A'}</p>
                                    <p><strong>Appointment ID:</strong> ${response.appointment_id ?? 'N/A'}</p>
                                    <p><strong>Date & Time:</strong> ${response.transaction_date ?? 'N/A'}</p>
                                    <p><strong>Pet Name:</strong> ${response.pet_name ?? 'N/A'}</p>
                                    <p><strong>Owner Name:</strong> ${response.owner_name ?? 'N/A'}</p>
                                    <p><strong>Service:</strong> ${response.purpose_of_visit ?? 'N/A'}</p>
                                    <p><strong>Total Amount:</strong> ₱ ${parseFloat(response.total_amount ?? 0).toFixed(2)}</p>
                                    <p><strong>Amount Paid:</strong> ₱ ${parseFloat(response.amount_paid ?? 0).toFixed(2)}</p>
                                    <p><strong>Change Amount:</strong> ₱ ${parseFloat(response.change_amount ?? 0).toFixed(2)}</p>
                                </div>
                            `;
                            modalDetailsDiv.html(detailsHtml);
                            // Show the modal
                            modal.show();
                        } else {
                            // Handle cases where response is null, not an object, or contains an error
                            console.error("Error fetching appointment details:", response ? (response.error || "Unknown error format") : "Null or invalid response");
                            modalDetailsDiv.html('<p>Could not load appointment details.</p>');
                            alert("Could not load appointment details. " + (response ? (response.error || "Please check console for details.") : "Received empty or invalid response."));
                        }
                    },
                    error: function(xhr, status, error) {
                        // Log the AJAX error and show an alert
                        console.error("AJAX Error fetching appointment details:", status, error);
                        modalDetailsDiv.html('<p>Error loading appointment details.</p>');
                        alert("An error occurred while fetching appointment details. Please check the console for more info.");
                    }
                });
            }

            // Function to fetch sales details via AJAX and format as a receipt
            function fetchSalesDetails(saleId) {
                modalDetailsDiv.html('<p>Loading details...</p>'); // Show loading message
                // Set modal title for Sales Receipt
                modal.find('h2').text('Sales Receipt');

                $.ajax({
                    url: transactionHistoryControllerUrl, // URL to your PHP controller
                    type: 'GET', // Use GET method to fetch data
                    data: {
                        action: 'getSalesTransactionDetails', // Specify the action to get sales details
                        sale_id: saleId // Pass the sale ID
                    },
                    dataType: 'json', // Expect a JSON response from the server
                    success: function(response) {
                        // Check if the response is a valid object and not an error response
                        if (response && typeof response === 'object' && !response.error) {
                            // Format the response data into a receipt-like HTML structure
                            let receiptHtml = `
                                <div class="receipt-content">
                                    <div class="receipt-header">
                                        <h3>VetSync</h3>
                                        <p>Sales Receipt</p>
                                    </div>
                                    <div class="receipt-details">
                                        <p>Date: ${response.sale_date ? new Date(response.sale_date).toLocaleString() : 'N/A'}</p>
                                        <p>Receipt #: ${response.sale_id ?? 'N/A'}</p>
                                        ${response.customer_name ? `<p>Customer: ${response.customer_name}</p>` : ''} </div>
                                    <table class="receipt-items">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="item-qty">Qty</th>
                                                <th class="item-price">Price</th>
                                                <th class="item-total">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                            if (response.items && response.items.length > 0) {
                                response.items.forEach(item => {
                                    receiptHtml += `
                                        <tr>
                                            <td>${item.prod_name ?? 'N/A'}</td>
                                            <td class="item-qty">${item.sale_item_qty ?? 0}</td>
                                            <td class="item-price">₱ ${parseFloat(item.sale_unit_price ?? 0).toFixed(2)}</td>
                                            <td class="item-total">₱ ${parseFloat(item.sale_line_total ?? 0).toFixed(2)}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                receiptHtml += `<tr><td colspan="4">No items listed.</td></tr>`;
                            }

                            receiptHtml += `
                                        </tbody>
                                    </table>
                                    <div class="receipt-summary">
                                        <p><strong>Subtotal:</strong> ₱ ${parseFloat(response.sale_total_amount ?? 0).toFixed(2)}</p> <p><strong>Total:</strong> ₱ ${parseFloat(response.sale_total_amount ?? 0).toFixed(2)}</p>
                                        <p><strong>Amount Paid:</strong> ₱ ${parseFloat(response.sale_amount_paid ?? 0).toFixed(2)}</p>
                                        <p><strong>Change:</strong> ₱ ${parseFloat(response.sale_change_amount ?? 0).toFixed(2)}</p>
                                    </div>
                                    <div class="receipt-footer">
                                        <p>Thank you for your business!</p>
                                    </div>
                                </div>
                            `;

                            modalDetailsDiv.html(receiptHtml);
                            // Show the modal
                            modal.show();
                        } else {
                             // Handle cases where response is null, not an object, or contains an error
                            console.error("Error fetching sales details:", response ? (response.error || "Unknown error format") : "Null or invalid response");
                            modalDetailsDiv.html('<p>Could not load sales details.</p>');
                            alert("Could not load sales details. " + (response ? (response.error || "Please check console for details.") : "Received empty or invalid response."));
                        }
                    },
                    error: function(xhr, status, error) {
                        // Log the AJAX error and show an alert
                        console.error("AJAX Error fetching sales details:", status, error);
                        modalDetailsDiv.html('<p>Error loading sales details.</p>');
                        alert("An error occurred while fetching sales details. Please check the console for more info.");
                    }
                });
            }

        });
    </script>

</body>
</html>
