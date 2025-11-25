<?php
require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();
$artisan_tier = $_SESSION['artisan_tier'] ?? 1;
$is_tier2 = ($artisan_tier == 2);

// Fetch orders containing artisan's products
$orders = get_artisan_orders($artisan_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Aya Crafts Artisan Portal</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="artisan-main-content">
        <div class="artisan-section-card">
            <h1>Orders</h1>
        </div>

            <!-- Order Stats -->
        <div class="artisan-stats-grid">
                <?php
                    $normalizeStatus = function ($status) {
                        return strtolower(trim($status ?? ''));
                    };
                    $statusCounts = [
                        'pending' => 0,
                        'processing' => 0,
                        'completed' => 0,
                        'cancelled' => 0
                    ];
                    foreach ($orders as $order) {
                        $status = $normalizeStatus($order['order_status'] ?? '');
                        if (isset($statusCounts[$status])) {
                            $statusCounts[$status]++;
                        }
                    }
                ?>
                <div class="artisan-stat-card">
                    <div class="artisan-stat-icon blue">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="artisan-stat-details">
                        <h3><?php echo count($orders); ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>

                <div class="artisan-stat-card">
                    <div class="artisan-stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="artisan-stat-details">
                        <h3><?php echo $statusCounts['pending']; ?></h3>
                        <p>Pending</p>
                    </div>
                </div>

                <div class="artisan-stat-card">
                    <div class="artisan-stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="artisan-stat-details">
                        <h3><?php echo $statusCounts['completed']; ?></h3>
                        <p>Completed</p>
                    </div>
                </div>

                <div class="artisan-stat-card">
                    <div class="artisan-stat-icon purple">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="artisan-stat-details">
                        <h3><?php echo $statusCounts['processing']; ?></h3>
                        <p>Processing</p>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="artisan-section-card">
                <div style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
                    <button class="filter-btn active" onclick="filterOrders('all', this)">
                        All Orders
                    </button>
                    <button class="filter-btn" onclick="filterOrders('pending', this)">
                        Pending
                    </button>
                    <button class="filter-btn" onclick="filterOrders('processing', this)">
                        Processing
                    </button>
                    <button class="filter-btn" onclick="filterOrders('completed', this)">
                        Completed
                    </button>
                    <button class="filter-btn" onclick="filterOrders('cancelled', this)">
                        Cancelled
                    </button>
                </div>

                <style>
                    .filter-btn {
                        padding: 10px 24px;
                        border: 2px solid #e5e7eb;
                        background: white;
                        border-radius: 50px;
                        cursor: pointer;
                        font-weight: 500;
                        font-size: 14px;
                        color: #374151;
                        transition: all 0.3s ease;
                    }
                    .filter-btn:hover {
                        border-color: #dc2626;
                        color: #dc2626;
                    }
                    .filter-btn.active {
                        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
                        color: white;
                        border-color: transparent;
                    }
                </style>

                <!-- Orders Table -->
                <div class="artisan-table-responsive">
                    <table class="artisan-data-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product(s)</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Order Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-shopping-cart"></i>
                                            <h3>No Orders Yet</h3>
                                            <p>Orders containing your products will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                <?php $normalizedStatus = $normalizeStatus($order['order_status'] ?? ''); ?>
                                <tr data-status="<?php echo $normalizedStatus; ?>">
                                    <td><strong>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                            <small style="color: #6b7280;"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $product_names = explode(',', $order['products']);
                                        echo htmlspecialchars($product_names[0]);
                                        if (count($product_names) > 1) {
                                            echo '<br><small style="color: #6b7280;">+' . (count($product_names) - 1) . ' more</small>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="artisan-badge artisan-badge-info"><?php echo $order['total_qty']; ?></span>
                                    </td>
                                    <td>
                                        <strong>GHS <?php echo number_format($order['total_amount'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($order['order_date'])); ?><br>
                                        <small style="color: #6b7280;"><?php echo date('h:i A', strtotime($order['order_date'])); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($is_tier2): ?>
                                        <!-- Tier 2: View only, no editing -->
                                        <span class="artisan-badge <?php 
                                            echo ($normalizedStatus == 'completed') ? 'artisan-badge-success' : 
                                                 (($normalizedStatus == 'processing') ? 'artisan-badge-info' : 
                                                 (($normalizedStatus == 'cancelled') ? 'artisan-badge-danger' : 'artisan-badge-warning')); 
                                        ?>">
                                            <?php echo ucfirst($normalizedStatus); ?>
                                        </span>
                                        <?php else: ?>
                                        <!-- Tier 1: Can update status -->
                                        <select class="artisan-form-control" 
                                                style="min-width: 140px; padding: 6px 12px; font-size: 13px;"
                                                onchange="updateOrderStatus(<?php echo $order['order_id']; ?>, this.value)"
                                                data-order-id="<?php echo $order['order_id']; ?>">
                                            <option value="pending" <?php echo ($normalizedStatus == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo ($normalizedStatus == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                            <option value="completed" <?php echo ($normalizedStatus == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo ($normalizedStatus == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2>Order Details</h2>
                <span class="close" onclick="closeOrderModal()">&times;</span>
            </div>
            <div id="orderDetailsContent" style="padding: 24px;">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        function filterOrders(status, el) {
            const table = document.getElementById('ordersTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            if (el) {
                el.classList.add('active');
            }
            
            // Filter rows
            for (let row of rows) {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    const rowStatus = row.getAttribute('data-status');
                    row.style.display = rowStatus === status ? '' : 'none';
                }
            }
        }

        function viewOrderDetails(orderId) {
            document.getElementById('orderDetailsModal').style.display = 'block';
            
            // Fetch order details via AJAX
            fetch(`../actions/get_order_details.php?order_id=${orderId}&artisan_id=<?php echo $artisan_id; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayOrderDetails(data.order);
                    } else {
                        alert('Error loading order details');
                    }
                });
        }

        function displayOrderDetails(order) {
            const content = document.getElementById('orderDetailsContent');
            
            let html = `
                <div style="display: grid; gap: 24px;">
                    <div>
                        <h3 style="font-family: 'Cormorant Garamond', serif; color: #dc2626; margin-bottom: 16px;">
                            Order #${String(order.order_id).padStart(6, '0')}
                        </h3>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                            <div>
                                <strong>Customer:</strong><br>
                                ${order.customer_name}<br>
                                ${order.customer_email}<br>
                                ${order.customer_contact}
                            </div>
                            <div>
                                <strong>Order Date:</strong><br>
                                ${new Date(order.order_date).toLocaleDateString()}<br>
                                <strong>Status:</strong> 
                                <span class="badge badge-${order.order_status === 'completed' ? 'success' : 'warning'}">
                                    ${order.order_status}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="margin-bottom: 12px;">Your Products in this Order</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>`;
            
            order.items.forEach(item => {
                html += `
                    <tr>
                        <td>${item.product_title}</td>
                        <td>${item.qty}</td>
                        <td>GHS ${parseFloat(item.product_price).toFixed(2)}</td>
                        <td><strong>GHS ${(item.qty * item.product_price).toFixed(2)}</strong></td>
                    </tr>`;
            });
            
            html += `
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="text-align: right; padding-top: 16px; border-top: 2px solid #e5e7eb;">
                        <h3 style="font-family: 'Cormorant Garamond', serif; color: #dc2626;">
                            Total: GHS ${order.total.toFixed(2)}
                        </h3>
                    </div>
                </div>
            `;
            
            content.innerHTML = html;
        }

        function closeOrderModal() {
            document.getElementById('orderDetailsModal').style.display = 'none';
        }

        function printInvoice(orderId) {
            window.open(`../actions/print_invoice.php?order_id=${orderId}`, '_blank');
        }
        
        function updateOrderStatus(orderId, newStatus) {
            if (!orderId || !newStatus) {
                alert('Invalid order or status');
                return;
            }
            
            if (!confirm('Are you sure you want to update this order status to "' + newStatus + '"?')) {
                // Reset select to previous value
                location.reload();
                return;
            }
            
            fetch('../actions/update_order_status_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'order_id=' + orderId + '&order_status=' + encodeURIComponent(newStatus)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.success === true) {
                    // Show success message
                    const message = document.createElement('div');
                    message.style.cssText = 'position: fixed; top: 20px; right: 20px; padding: 16px 24px; background: #d1fae5; color: #065f46; border-radius: 8px; z-index: 10000; box-shadow: 0 4px 20px rgba(0,0,0,0.15);';
                    message.textContent = 'Order status updated successfully';
                    document.body.appendChild(message);
                    setTimeout(() => message.remove(), 3000);
                    
                    // Reload page to update status counts
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to update order status'));
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating order status');
                location.reload();
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('orderDetailsModal');
            if (event.target == modal) {
                closeOrderModal();
            }
        }
        
        // Mobile menu toggle
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });
    </script>
</body>
</html>