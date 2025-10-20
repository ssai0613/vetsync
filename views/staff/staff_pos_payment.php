<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment - VetSync</title>
  <link rel="stylesheet" href="css/staff_pos_payment.css?v=<?= time(); ?>" />
  <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet" />
  <script src="/../../utils/jquery-3.7.1.js"></script>
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
        	<li><a href="staff_appointment.php"><i class='bx bxs-calendar-event'></i><span class="links_name">Appointments</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_petProfile.php"><i class='bx bxs-dog'></i><span class="links_name">Pet History & Profiles</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_POS.php" class="active"><i class='bx bx-line-chart'></i><span class="links_name">Point of Sale</span></a></li>
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
        
    <div class="pos_container">
        <div class="left_panel">
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <a href="staff_POS.php" class="btn-back" title="Back to POS"><i class='bx bx-arrow-back'></i></a>
                <h2 style="margin-left: 1rem; color: #333;">Complete Payment</h2>
            </div>
            <h4 style="margin-bottom: 1rem; color: #555;">Order Summary</h4>
            <div class="staff_table_wrapper">
                <table class="staff_table">
                    <thead>
                        <tr>
                            <th class="th">Item Name</th>
                            <th class="th">Qty</th>
                            <th class="th">Unit Price</th>
                            <th class="th">Total</th>
                        </tr>
                    </thead>
                    <tbody class="tableBody">
                        <?php
                        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                echo "<tr>";
                                echo "<td class='td'>" . htmlspecialchars($item['prod_name']) . "</td>";
                                echo "<td class='td'>" . htmlspecialchars($item['prod_qty']) . "</td>";
                                echo "<td class='td'>₱ " . number_format($item['prod_price'], 2) . "</td>";
                                echo "<td class='td'>₱ " . number_format($item['prod_price'] * $item['prod_qty'], 2) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Your cart is empty.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-inner">
                <h3 style="text-align: center; color: #1e3d58; margin-bottom: 15px; font-weight: 600;">Payment Details</h3>

                <div class="form-row">
                    <div class="form-group"><span class="label">Discount</span></div>
                    <div class="form-group"><span class="amount-value" style="font-size: 18px;" id="discountAmountDisplay">₱ 0.00</span></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><span class="label">Vatable Sales</span></div>
                    <div class="form-group"><span class="amount-value" style="font-size: 18px;" id="vatableSalesDisplay">₱ 0.00</span></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><span class="label">VAT Amount (12%)</span></div>
                    <div class="form-group"><span class="amount-value" style="font-size: 18px;" id="vatAmountDisplay">₱ 0.00</span></div>
                </div>
                
                <hr style="border: 1px dashed #ccc; margin: 10px 0;">

                <div class="form-row">
                    <div class="form-group"><strong class="label" style="font-size: 1.1em; color: #111827;">Amount Due</strong></div>
                    <div class="form-group"><strong class="amount-value" style="font-size: 1.6em; color: #003249;" id="amountDueDisplay">₱ 0.00</strong></div>
                </div>

                <hr style="border-top: 2px solid #ccc; margin: 15px 0;">

                <div style="display:flex; gap: 15px; margin-bottom: 15px;">
                    <label class="form-group" style="flex:1;">
                        <span class="form-label">Discount (%)</span>
                        <input type="number" id="discountInput" class="input" value="0" style="text-align:center;">
                    </label>
                    <label class="form-group" style="flex:1;">
                        <span class="form-label">Cash Tendered</span>
                        <input type="text" id="cashInput" class="input" readonly>
                    </label>
                </div>
                
                <div class="keypad">
                    <button class="btn" data-value="1">1</button>
                    <button class="btn" data-value="2">2</button>
                    <button class="btn" data-value="3">3</button>
                    <button class="btn" data-value="4">4</button>
                    <button class="btn" data-value="5">5</button>
                    <button class="btn" data-value="6">6</button>
                    <button class="btn" data-value="7">7</button>
                    <button class="btn" data-value="8">8</button>
                    <button class="btn" data-value="9">9</button>
                    <button class="btn" data-value=".">.</button>
                    <button class="btn" data-value="0">0</button>
                    <button id="clearBtn" class="btn clear">Del</button>
                </div>
                
                <label class="form-group" style="margin-top: 15px;">
                    <span class="form-label">Change</span>
                    <input type="text" id="changeOutput" class="input disabled" readonly style="font-weight: bold; font-size: 1.2em; color: #2d8a39;">
                </label>

                <button id="payBtn" class="pay-btn">Confirm Payment</button>
            </div>
        </div>
    </div>
  </section>

  <footer class="footer">
    <div class="footer-content">
      <p>© 2025 VetSync. All rights reserved.Software developed by <span class="fusion">COGTAS, LINGO & SEGOVIA</span></p>
    </div>
  </footer>

  <div id="receiptModal" class="modal">
    <div class="modal-content">
      <p style="font-size: 1.2em;">Payment successful!</p>
      <p>Would you like to print a receipt?</p>
      <div class="modal-buttons">
        <button id="printYes" class="btn-yes">YES</button>
        <button id="printNo" class="btn-no">NO</button>
      </div>
    </div>
  </div>

  <script>
    //toggle
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

    // --- PAYMENT CALCULATION SCRIPT ---
    document.addEventListener('DOMContentLoaded', function () {
        const cartTotal = <?php
            $total = 0;
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $total += $item['prod_price'] * $item['prod_qty'];
                }
            }
            echo $total;
        ?>;

        const vatDivisor = 1.12;
        const cashInput = document.getElementById("cashInput");
        const discountInput = document.getElementById("discountInput");
        const changeOutput = document.getElementById("changeOutput");
        const keypadButtons = document.querySelectorAll(".keypad .btn");

        const vatableSalesDisplay = document.getElementById("vatableSalesDisplay");
        const vatAmountDisplay = document.getElementById("vatAmountDisplay");
        const discountAmountDisplay = document.getElementById("discountAmountDisplay");
        const amountDueDisplay = document.getElementById("amountDueDisplay");

        function calculateTotals() {
            const discountPercentage = parseFloat(discountInput.value) || 0;
            const discountAmount = cartTotal * (discountPercentage / 100);
            const finalAmountDue = cartTotal - discountAmount;
            
            const vatableSales = finalAmountDue / vatDivisor;
            const vatAmount = finalAmountDue - vatableSales;

            const cash = parseFloat(cashInput.value) || 0;
            const change = cash - finalAmountDue;

            discountAmountDisplay.textContent = `₱ ${discountAmount.toFixed(2)}`;
            vatableSalesDisplay.textContent = `₱ ${vatableSales.toFixed(2)}`;
            vatAmountDisplay.textContent = `₱ ${vatAmount.toFixed(2)}`;
            amountDueDisplay.textContent = `₱ ${finalAmountDue.toFixed(2)}`;
            changeOutput.value = change >= 0 ? `₱ ${change.toFixed(2)}` : "₱ 0.00";
        }

        keypadButtons.forEach(button => {
            if (button.id === 'clearBtn') {
                button.addEventListener("click", () => {
                    cashInput.value = cashInput.value.slice(0, -1);
                    calculateTotals();
                });
            } else {
                button.addEventListener("click", () => {
                    // Prevent multiple decimals
                    if (button.dataset.value === '.' && cashInput.value.includes('.')) {
                        return;
                    }
                    cashInput.value += button.dataset.value;
                    calculateTotals();
                });
            }
        });

        discountInput.addEventListener("input", calculateTotals);

        document.getElementById("payBtn").addEventListener("click", () => {
            const discountPercentage = parseFloat(discountInput.value) || 0;
            const discountAmount = cartTotal * (discountPercentage / 100);
            const finalAmountDue = cartTotal - discountAmount;
            const cash = parseFloat(cashInput.value) || 0;
            const change = cash - finalAmountDue;

            if (cash < finalAmountDue) {
                alert("Insufficient cash amount!");
                return;
            }

            $.post("/controllers/staff_paymentController.php", {
                amount_due: finalAmountDue.toFixed(2),
                cash: cash,
                change: change.toFixed(2)
            }, function(response) {
                const res = JSON.parse(response);
                if (res.status === "success") {
                    window.saleIdForReceipt = res.sale_id;
                    document.getElementById("receiptModal").style.display = "block";
                } else {
                    alert("Payment failed. Please try again.");
                }
            });
        });

        document.getElementById("printYes").addEventListener("click", () => {
            document.getElementById("receiptModal").style.display = "none";
            if (window.saleIdForReceipt) {
                window.location.href = "staff_receipt.php?sale_id=" + window.saleIdForReceipt;
            }
        });

        document.getElementById("printNo").addEventListener("click", () => {
            document.getElementById("receiptModal").style.display = "none";
            window.location.href = "staff_POS.php";
        });
        
        // Initial calculation on page load
        calculateTotals();
    });
    </script>
</body>
</html>