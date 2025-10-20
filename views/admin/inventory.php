<?php
include_once(__DIR__ . '/../../controllers/admin_inventoryController.php');

$controller = new InventoryController(); // Controller for initial page data
$products = $controller->getAllProducts();
$categories = $controller->getAllCategories();

// Fetch low stock products. Threshold is 2, so products with qty 0, 1, or 2 will be fetched
// due to the model change (prod_qty <= :threshold).
$lowStockThreshold = 2; 
$lowStockProducts = $controller->getLowStockItemsForAlert($lowStockThreshold);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="css/inventory.css?v=<?= time(); ?>">
    <script src="/../../utils/jquery-3.7.1.js"></script>
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
<div class="sidebar">
  <div class="logo_details"> <i class='bx bx-code-alt'></i> <div class="logo_name">VetSync</div> </div>
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
            <div class="user-menu" id="user-toggle"><i class='bx bx-user' id="profile-icon"></i><span class="user-name">   Admin</span></div>
        </div>
    </div>
    <div class="main_content">
        <div class="container">
            <div id="lowStockAlertBar" class="low-stock-notification-bar" style="display: none;">
                <span class="message" id="lowStockAlertMessage"></span>
                <span class="close-notification" id="closeLowStockAlert">&times;</span>
            </div>

            <h2>Product Management</h2>
            <div class="content">
                <form method="POST" class="form" id="productForm">
                    <input type="hidden" id="action" name="action" value="add">
                    <input type="hidden" id="productID" name="prodID">

                    <div class="form-group">
                        <label for="prodName">Product Name</label>
                        <input type="text" name="prodName" id="prodName" placeholder="e.g. Dog Shampoo" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <div class="category_filter">
                            <select name="category" id="category" required>
                                <option value="">-- Select Category --</option>
                                <?php if(!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['cat_id']) ?>"><?= htmlspecialchars($cat['cat_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="prodPrice">Product Price (₱)</label>
                        <input type="number" name="prodPrice" id="prodPrice" placeholder="e.g. 500.00" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="prodQty">Stocks</label>
                        <input type="number" name="prodQty" id="prodQty" placeholder="e.g. 10" min="0" required>
                    </div>
                    <div class="buttons">
                        <button class="add-btn" id="addProductBtn" type="submit">Add Product</button>
                        <button class="update-btn" id="updateProductBtn" type="submit" style="display: none;">Update Product</button>
                        <button type="button" class="remove-btn" id="removeProductBtn" style="display: none;">Remove Product</button>
                        <button type="button" class="clear-btn" id="clearProductBtn">Clear</button>
                    </div>
                </form>

                <div class="table-section">
                    <a href="productHistory.php" class="view-history-btn">View Product History</a>
                    <div class="table-controls">
                        <input type="text" id="searchProduct" placeholder="Search Product Name...">
                        <div class="sort_filter">
                            <label for="categorySort">Filter by Category:</label>
                            <select id="categorySort">
                                <option value="">All Categories</option>
                                <?php if(!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['cat_id']) ?>"><?= htmlspecialchars($cat['cat_name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div><br>

                    <div class="products_table_wrapper">
                        <table class="products_table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Stocks</th>
                                    <th>Price</th>
                                    <th>Category</th> 
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <?php if (empty($products)): ?>
                                     <tr><td colspan="5">No products found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr data-cat-id="<?= htmlspecialchars($product['cat_id']) ?>">
                                            <td><?= htmlspecialchars($product['prod_id']) ?></td>
                                            <td><?= htmlspecialchars($product['prod_name']) ?></td>
                                            <td><?= htmlspecialchars($product['prod_qty']) ?></td>
                                            <td>₱ <?= htmlspecialchars(number_format($product['prod_price'], 2)) ?></td>
                                            <td><?= htmlspecialchars($product['cat_name'] ?? 'N/A') ?></td> 
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

    <div id="confirmDeleteModal" class="modal confirmation-modal" style="display: none;">
        <div class="modal-content confirmation-modal-content">
            <h2 class="modal-section-title">Confirm Deletion</h2>
            <p id="confirmDeleteMessage">Are you sure you want to delete this product?</p>
            <div class="confirmation-actions">
                <button type="button" class="confirm-btn yes-btn" id="confirmDeleteYesBtn">Yes</button>
                <button type="button" class="confirm-btn no-btn" id="confirmDeleteNoBtn">No</button>
            </div>
        </div>
    </div>
</section>
<footer class="footer">
    <div class="footer-content"> <p>© 2025 VetSync. All rights reserved.Software developed by <span class="fusion">COGTAS, LINGO & SEGOVIA</span></p> </div>
</footer>

<script>
// Pass PHP data to JavaScript
const lowStockProductsData = <?= json_encode($lowStockProducts ?? []); ?>; 
const inventoryControllerUrl = '/controllers/admin_inventoryController.php';

$(document).ready(function() {
    // Initial button states
    $('#updateProductBtn, #removeProductBtn, #confirmDeleteModal').hide();
    $('#addProductBtn').show();
    $('#action').val('add'); 

    let productIdToRemove = null;

    $('#productForm').on('submit', function(e) {
        e.preventDefault(); 
        const action = $('#action').val();
        const prodID = $('#productID').val();
        const prodName = $('#prodName').val();
        const prodQty = $('#prodQty').val();
        const prodPrice = $('#prodPrice').val();
        const category = $('#category').val();

        if (!prodName || prodQty === '' || prodPrice === '' || !category) {
            alert("Please fill in all required fields."); return;
        }
        if (parseInt(prodQty) < 0) {
            alert("Stock quantity cannot be negative."); return;
        }
        if (parseFloat(prodPrice) < 0) {
            alert("Product price cannot be negative."); return;
        }
        const formData = { action: action, prodName: prodName, prodQty: prodQty, prodPrice: prodPrice, category: category };
        if (action === 'update') {
            if (!prodID) { alert("Product ID is missing for update."); return; }
            formData.prodID = prodID;
        }
        $.ajax({
            url: inventoryControllerUrl, type: 'POST', data: formData,
            success: function(response) {
                alert(response); 
                if (response.toLowerCase().includes("success")) { location.reload(); }
            },
            error: function(xhr) { alert('Error: ' + (xhr.responseText || 'An error occurred.')); }
        });
    });

    $('#removeProductBtn').click(function() {
        const productId = $('#productID').val();
        if (productId) { productIdToRemove = productId; $('#confirmDeleteModal').show(); } 
        else { alert("Please select a product to remove."); }
    });

    $('#confirmDeleteYesBtn').click(function() {
        if (productIdToRemove) {
            $.ajax({
                url: inventoryControllerUrl, type: 'POST', data: { action: 'remove', prodID: productIdToRemove },
                success: function(response) {
                    alert(response);
                    if (response.toLowerCase().includes("success")) { location.reload(); }
                },
                error: function(xhr) { alert('Error: ' + (xhr.responseText || 'An error occurred.')); }
            });
            $('#confirmDeleteModal').hide(); productIdToRemove = null;
        }
    });

    $('#confirmDeleteNoBtn').click(function() { $('#confirmDeleteModal').hide(); productIdToRemove = null; });

    $(document).on('click', '#productsTableBody tr', function() {
        if ($(this).find('td[colspan="5"]').length > 0) { return; }
        const catID = $(this).data('cat-id'); 
        const prodID = $(this).find('td:eq(0)').text().trim();
        const prodName = $(this).find('td:eq(1)').text().trim();
        const prodQty = $(this).find('td:eq(2)').text().trim();
        let prodPrice = $(this).find('td:eq(3)').text().trim(); 
        prodPrice = prodPrice.replace('₱', '').replace(/,/g, '').trim();
        $('#category').val(catID); $('#productID').val(prodID); $('#prodName').val(prodName);
        $('#prodQty').val(prodQty); $('#prodPrice').val(parseFloat(prodPrice) || '');
        $('#addProductBtn').hide(); $('#updateProductBtn').show(); $('#removeProductBtn').show();
        $('#action').val('update');
    });

    $('#searchProduct').on('keyup', function() { 
        const searchQuery = $(this).val();
        $.ajax({
            url: inventoryControllerUrl, type: 'GET', data: { action: 'search', query: searchQuery },
            success: function(response) { $('#productsTableBody').html(response); },
            error: function(xhr) {
                console.error('Search error:', xhr.responseText);
                $('#productsTableBody').html("<tr><td colspan='5'>Error searching products.</td></tr>");
            }
        });
    });

    $('#clearProductBtn').click(function() {
        $('#productForm')[0].reset(); $('#productID').val(''); 
        $('#addProductBtn').show(); $('#updateProductBtn').hide(); $('#removeProductBtn').hide();
        $('#action').val('add'); $('#productsTableBody tr').show(); $('#categorySort').val(''); 
    });

    $('#categorySort').change(function() {
        const selectedCategory = $(this).val();
        $('#productsTableBody tr').each(function() {
            const productCategory = $(this).data('cat-id'); 
            if (selectedCategory === "" || String(selectedCategory) === String(productCategory)) { $(this).show(); } 
            else { $(this).hide(); }
        });
    });

    // Low Stock Notification Bar
    const lowStockBar = $('#lowStockAlertBar');
    const lowStockMessageElem = $('#lowStockAlertMessage');
    const closeLowStockBtn = $('#closeLowStockAlert');

    if (lowStockProductsData && lowStockProductsData.length > 0) {
        // MODIFIED Message to reflect stocks of 2 or less (including 0)
        let lowStockMsg = "<strong>Low Stock/Out of Stock Warning:</strong>";
        const productNames = lowStockProductsData.map(p => {
            let stockLabel = `Qty: ${p.prod_qty}`;
            if (parseInt(p.prod_qty) === 0) {
                stockLabel = "Out of Stock";
            }
            return `${p.prod_name} (${stockLabel})`;
        });
        lowStockMsg += productNames.join(', ') + ". Please re-stock soon.";
        
        lowStockMessageElem.html(lowStockMsg); 
        lowStockBar.css('display', 'flex'); 
    }

    closeLowStockBtn.click(function() { lowStockBar.hide(); });

    // Sidebar Toggle
    const sidebar = document.querySelector(".sidebar");
    const toggleMenuBtn = document.querySelector("#btn"); 
    if(toggleMenuBtn && sidebar) {
       toggleMenuBtn.addEventListener("click", () => {
           sidebar.classList.toggle("open");
           if (sidebar.classList.contains("open")) { toggleMenuBtn.classList.replace("bx-menu", "bx-menu-alt-right"); } 
           else { toggleMenuBtn.classList.replace("bx-menu-alt-right", "bx-menu"); }
       });
    }
    const userToggle = document.getElementById('user-toggle');
    if(userToggle) { userToggle.addEventListener('click', (event) => { event.stopPropagation(); }); }
});
</script>
</body>
</html>