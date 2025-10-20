<?php
// Ensure session_start() is called only once and at the very beginning.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . '/../../controllers/staff_posController.php');

// Use the $posPageController instance created at the end of posController.php
// This instance's getAllProducts() will use the model that filters out 0-stock items.
$products = $posPageController->getAllProducts(); 
$categories = $posPageController->getAllCategories();

$pos_message = null;
if (isset($_SESSION['pos_message'])) {
    $pos_message = $_SESSION['pos_message'];
    unset($_SESSION['pos_message']); 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Point of Sale - VetSync</title>
  <link rel="stylesheet" href="css/pos.css?v=<?= time(); ?>" /> 
  <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet" />
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
        	<li><a href="staff_POS.php" class="active"><i class='bx bx-line-chart'></i><span class="links_name">Point of Sale</span></a></li>
        	<div class="sidebar-line"></div>
        	<li><a href="staff_transactionHistory.php"><i class='bx bxs-report' ></i><span class="links_name">Transaction History</span></a></li>
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
                <div class="user-menu" id="user-toggle">
                    <i class='bx bx-user' id="profile-icon"></i>
                    <span class="user-name">   Receptionist</span>
                </div>
            </div>
        </div>
        
        <?php if ($pos_message): ?>
            <div class="pos-message-bar <?= htmlspecialchars($pos_message['type']); ?>" style="display:flex;"> 
                <span><?= htmlspecialchars($pos_message['text']); ?></span>
                <span class="close-pos-message">&times;</span>
            </div>
        <?php endif; ?>

    <div class="container"> 
        <div class="product-panel"> 
          <div class="search-bar">
            <input type="text" id="searchProduct" placeholder="Search Product Name">
            <select name="category" id="categorySort">
                <option value="">--Select Category--</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['cat_id']) ?>"><?= htmlspecialchars($cat['cat_name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
          </div>

          <div class="staff_table_wrapper"> 
            <table class="staff_table"> 
            <thead>
            <tr>
                <th class="prod_id">ID</th>
                <th>Name</th>
                <th class="stocks">Stocks</th>
                <th class="prod_price">Price</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody id="productsTableBody">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?> 
                      <tr data-cat-id="<?= htmlspecialchars($product['cat_id']) ?>">
                          <td><?= htmlspecialchars($product['prod_id']) ?></td>
                          <td><?= htmlspecialchars($product['prod_name']); ?></td>
                          <td><?= htmlspecialchars($product['prod_qty']); ?></td>
                          <td>₱ <?= number_format($product['prod_price'], 2); ?></td>
                          <td class="action-icons">
                            <form method="POST" action="/controllers/staff_posController.php"> 
                                <input type="hidden" name="action" value="addToCart"> 
                                <input type="hidden" name="prod_id" value="<?= htmlspecialchars($product['prod_id']) ?>">
                                <input type="hidden" name="prod_name" value="<?= htmlspecialchars($product['prod_name']) ?>">
                                <input type="hidden" name="prod_price" value="<?= htmlspecialchars($product['prod_price']) ?>">
                                <button type="submit">Add to Cart</button>
                            </form>
                           </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px;">No products currently in stock or matching your criteria.</td></tr>
                <?php endif; ?>
            </tbody>
            </table>
           </div>
        </div>

        <div class="right-panel"> 
            <div class="cart-panel">
                <h3>Cart Details:</h3><br>
                <div class="cart-items-scroll">
                    <?php
                    $total = 0;
                    if (!empty($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $id => $item):
                            $subtotal = $item['prod_price'] * $item['prod_qty'];
                            $total += $subtotal;
                    ?>
                    <div class="cart-item">
                        <span class="item-name"><?= htmlspecialchars($item['prod_name']) ?></span>
                        <div class="item-details">
                            <span class="item-price">₱<?= number_format($subtotal, 2) ?></span>
                            <div class="qty_container"> 
                                <form method="POST" action="/controllers/staff_posController.php" style="display:inline;">
                                    <input type="hidden" name="action" value="changeQty">
                                    <input type="hidden" name="change_qty_id" value="<?= $id ?>">
                                    <input type="hidden" name="change_type" value="decrease">
                                    <button class="qty-btn" type="submit">−</button>
                                </form>
                                <span class="qty-num"><?= $item['prod_qty'] ?></span>
                                <form method="POST" action="/controllers/staff_posController.php" style="display:inline;">
                                    <input type="hidden" name="action" value="changeQty">
                                    <input type="hidden" name="change_qty_id" value="<?= $id ?>">
                                    <input type="hidden" name="change_type" value="increase">
                                    <button class="qty-btn" type="submit">+</button>
                                </form>
                                <form method="POST" action="/controllers/staff_posController.php" style="display:inline;">
                                    <input type="hidden" name="action" value="removeItem">
                                    <input type="hidden" name="remove_id" value="<?= $id ?>">
                                    <button class="remove-btn" title="Remove"><i class='bx bx-trash'></i></button>
                                </form>
                            </div> 
                        </div>
                        <div class="sidebar-line1"></div> 
                    </div>
                    <?php endforeach; } else { echo "<p>No items in cart.</p>"; } ?>
                </div>
                <div class="group">
                    <div class="total">Total: <div class="total_amount">₱<?= number_format($total, 2) ?></div></div>
                    <button class="pay-btn" onclick="window.location.href='staff_pos_payment.php'" <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>>Proceed to Payment</button>
                </div>
            </div> 
        </div>
    </div>
  </section>
  <footer class="footer">
    <div class="footer-content">
      <p>© 2025 VetSync. All rights reserved. Software developed by <span class="fusion">COGTAS, LINGO & SEGOVIA</span></p>
    </div>
  </footer>

  <script>
    $(document).ready(function() {
        // Sidebar Toggle (using your original JS)
        let sidebar = document.querySelector(".sidebar");
        let closeBtn = document.querySelector("#btn"); 
        if (closeBtn && sidebar) { 
            closeBtn.addEventListener("click", () => {
                sidebar.classList.toggle("open");
                changeBtn();
            });
        }
        function changeBtn() { 
            if(sidebar && closeBtn && sidebar.classList.contains("open")) {
                closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
            } else if (sidebar && closeBtn) {
                closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
            }
        }

        const userToggle = document.getElementById('user-toggle');
        if(userToggle) { 
          userToggle.addEventListener('click', (event) => { 
              event.stopPropagation(); 
          });
        }

        // Client-side category filter (using your original JS)
        $('#categorySort').change(function() {
            const selectedCategory = $(this).val();
            $('#productsTableBody tr').each(function() {
                // Ensure data-cat-id attribute is on the <tr> element
                const productCategory = $(this).data('cat-id'); 
                if (selectedCategory === "" || String(selectedCategory) == String(productCategory)) { 
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Product Search via AJAX (using your original JS)
        $('#searchProduct').on('input', function() { 
            const searchQuery = $(this).val();
            $.ajax({
                url: '/controllers/staff_posController.php', 
                type: 'GET',
                data: {
                    action: 'search',
                    query: searchQuery
                },
                success: function(response) {
                    $('#productsTableBody').html(response);
                },
                error: function(xhr, status, error) {
                    console.error("Search AJAX error:", status, error, xhr.responseText);
                    $('#productsTableBody').html("<tr><td colspan='5' style='text-align:center; padding:10px;'>Error during search. Please try again.</td></tr>");
                }
            });
        });    

        // POS Message Bar Close
        $('.close-pos-message').on('click', function() {
            $(this).parent('.pos-message-bar').fadeOut('fast');
        });

        // Auto-hide POS message if it was displayed by PHP
        // Check if the element exists and is visible (style.display is not 'none')
        const messageBar = document.querySelector('.pos-message-bar');
        if (messageBar && getComputedStyle(messageBar).display !== 'none') {
             setTimeout(function() {
                $(messageBar).fadeOut('slow');
            }, 4000); 
        }
    }); 
  </script>
</body>
</html>