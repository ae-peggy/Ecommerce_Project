<?php
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
  header('Location: ../login/login.php');
  exit();
}

// Get orders (all orders for admin, user orders for customers)
require_once '../controllers/order_controller.php';
$is_admin_view = is_admin();
$customer_id = get_user_id();

// Debug logging
error_log("=== ORDERS PAGE DEBUG ===");
error_log("Is admin view: " . ($is_admin_view ? 'YES' : 'NO'));
error_log("Customer ID: " . $customer_id);

if ($is_admin_view) {
    $orders = get_all_orders_ctr();
    $page_title = "All Orders | Aya Crafts Admin";
    error_log("Admin: Fetched " . ($orders ? count($orders) : 0) . " orders");
} else {
    $orders = get_user_orders_ctr($customer_id);
    $page_title = "My Orders | Aya Crafts";
    error_log("User: Fetched " . ($orders ? count($orders) : 0) . " orders for customer ID: $customer_id");
}

// Log orders data for debugging
if ($orders) {
    error_log("Orders data: " . print_r($orders, true));
} else {
    error_log("Orders is FALSE or empty");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="../css/admin_pages.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #ffffff;
      color: #1a1a1a;
      line-height: 1.6;
      overflow-x: hidden;
      min-height: 100vh;
    }

    /* Animated gradient background */
    body::before {
      content: '';
      position: fixed;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background:
        radial-gradient(circle at 20% 50%, rgba(220, 38, 38, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(239, 68, 68, 0.02) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(185, 28, 28, 0.02) 0%, transparent 50%);
      animation: drift 20s ease-in-out infinite;
      z-index: -1;
    }

    @keyframes drift {

      0%,
      100% {
        transform: translate(0, 0) rotate(0deg);
      }

      33% {
        transform: translate(30px, -30px) rotate(1deg);
      }

      66% {
        transform: translate(-20px, 20px) rotate(-1deg);
      }
    }

    .navbar {
      background: rgba(255, 255, 255, 0.95);
      padding: 25px 0;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(220, 38, 38, 0.08);
    }

    .nav-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 60px;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .logo-symbol {
      width: 45px;
      height: 45px;
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(220, 38, 38, 0.2);
    }

    .logo-symbol::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
      animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
      0% {
        transform: translateX(-100%);
      }

      100% {
        transform: translateX(100%);
      }
    }

    .logo-symbol svg {
      width: 28px;
      height: 28px;
      fill: white;
      position: relative;
      z-index: 1;
    }

    .logo-text {
      display: flex;
      flex-direction: column;
    }

    .logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 32px;
      font-weight: 500;
      letter-spacing: 1px;
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: gradientShift 3s ease infinite;
      text-decoration: none;
    }

    @keyframes gradientShift {

      0%,
      100% {
        background-position: 0% center;
      }

      50% {
        background-position: 100% center;
      }
    }

    .logo-subtitle {
      font-size: 10px;
      color: #9ca3af;
      letter-spacing: 2px;
      text-transform: uppercase;
      font-weight: 500;
      margin-top: -5px;
    }

    .nav-buttons {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .nav-btn {
      color: #374151;
      padding: 12px 32px;
      text-decoration: none;
      border-radius: 50px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      font-size: 14px;
      font-weight: 500;
      letter-spacing: 0.3px;
      border: 1.5px solid transparent;
      position: relative;
      overflow: hidden;
    }

    .nav-btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(220, 38, 38, 0.1);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .nav-btn:hover::before {
      width: 300px;
      height: 300px;
    }

    .nav-btn.secondary {
      color: #6b7280;
      border: 1.5px solid #e5e7eb;
      background: transparent;
    }

    .nav-btn.secondary:hover {
      border-color: #dc2626;
      color: #dc2626;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(220, 38, 38, 0.12);
    }

    .nav-btn:not(.secondary) {
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(220, 38, 38, 0.25);
    }

    .nav-btn:not(.secondary):hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
    }

    .nav-btn span {
      position: relative;
      z-index: 1;
    }

    .main-content {
      max-width: 1400px;
      margin: 60px auto;
      padding: 0 60px;
      min-height: calc(100vh - 200px);
    }

    .page-header {
      text-align: center;
      margin-bottom: 60px;
      position: relative;
    }

    .accent-bar {
      height: 4px;
      width: 80px;
      background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
      margin: 0 auto 30px;
      border-radius: 2px;
      box-shadow: 0 2px 10px rgba(220, 38, 38, 0.3);
      animation: expandContract 2s ease-in-out infinite;
    }

    @keyframes expandContract {

      0%,
      100% {
        width: 80px;
      }

      50% {
        width: 100px;
      }
    }

    .page-header h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 52px;
      font-weight: 500;
      color: #111827;
      margin-bottom: 15px;
      letter-spacing: -1px;
    }

    .page-header p {
      color: #6b7280;
      font-size: 18px;
      font-weight: 300;
    }

    .orders-container {
      display: grid;
      gap: 30px;
    }

    .order-card {
      background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
      border-radius: 20px;
      padding: 35px;
      box-shadow:
        0 10px 40px rgba(0, 0, 0, 0.06),
        0 0 0 1px rgba(220, 38, 38, 0.05);
      position: relative;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
    }

    .order-card:hover {
      transform: translateY(-5px);
      box-shadow:
        0 20px 50px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(220, 38, 38, 0.1);
    }

    /* Kente-inspired top border */
    .order-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      background:
        linear-gradient(90deg,
          #dc2626 0%, #dc2626 10%,
          #991b1b 10%, #991b1b 20%,
          #ef4444 20%, #ef4444 30%,
          #dc2626 30%, #dc2626 40%,
          #b91c1c 40%, #b91c1c 50%,
          #dc2626 50%, #dc2626 60%,
          #991b1b 60%, #991b1b 70%,
          #ef4444 70%, #ef4444 80%,
          #dc2626 80%, #dc2626 90%,
          #b91c1c 90%, #b91c1c 100%);
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 25px;
      flex-wrap: wrap;
      gap: 20px;
    }

    .order-info {
      flex: 1;
    }

    .invoice-number {
      font-size: 24px;
      font-weight: 600;
      color: #111827;
      margin-bottom: 8px;
      font-family: 'Cormorant Garamond', serif;
    }

    .order-date {
      color: #6b7280;
      font-size: 14px;
      font-weight: 400;
    }

    .order-meta {
      display: flex;
      gap: 20px;
      align-items: center;
      flex-wrap: wrap;
    }

    .status-badge {
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .status-completed {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      color: #065f46;
    }

    .status-pending {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      color: #92400e;
    }

    .status-cancelled {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      color: #991b1b;
    }

    .status-processing {
      background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
      color: #1e40af;
    }

    .order-total {
      text-align: right;
    }

    .total-label {
      font-size: 12px;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 5px;
    }

    .total-amount {
      font-size: 28px;
      font-weight: 700;
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-family: 'Cormorant Garamond', serif;
    }

    .order-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.6);
      border-radius: 12px;
      margin-top: 20px;
      border: 1px solid rgba(220, 38, 38, 0.08);
    }

    .summary-item {
      text-align: center;
    }

    .summary-label {
      font-size: 12px;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 5px;
    }

    .summary-value {
      font-size: 18px;
      font-weight: 600;
      color: #374151;
    }

    .order-actions {
      display: flex;
      gap: 12px;
      margin-top: 25px;
      flex-wrap: wrap;
    }

    .action-btn {
      padding: 12px 28px;
      border-radius: 50px;
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      border: 1.5px solid transparent;
      cursor: pointer;
    }

    .action-btn.primary {
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(220, 38, 38, 0.25);
    }

    .action-btn.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(220, 38, 38, 0.35);
    }

    .action-btn.secondary {
      background: white;
      color: #6b7280;
      border: 1.5px solid #e5e7eb;
    }

    .action-btn.secondary:hover {
      border-color: #dc2626;
      color: #dc2626;
      transform: translateY(-2px);
    }

    .action-btn.danger {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      color: white;
      border: none;
      box-shadow: 0 4px 15px rgba(220, 38, 38, 0.25);
    }

    .action-btn.danger:hover {
      background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(220, 38, 38, 0.35);
    }

    .status-select {
      padding: 8px 16px;
      border-radius: 8px;
      border: 1.5px solid #e5e7eb;
      font-size: 13px;
      font-weight: 500;
      background: white;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-left: 12px;
    }
    .status-select:hover {
      border-color: #dc2626;
    }
    .status-select:focus {
      outline: none;
      border-color: #dc2626;
      box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .empty-state {
      text-align: center;
      padding: 100px 40px;
      background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    }

    .empty-icon {
      width: 120px;
      height: 120px;
      margin: 0 auto 30px;
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 60px;
    }

    .empty-state h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 36px;
      color: #374151;
      margin-bottom: 15px;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .empty-state p {
      color: #9ca3af;
      font-size: 16px;
      margin-bottom: 30px;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .empty-state .action-btn {
      display: inline-block;
      align-items: center;
      justify-content: center;
      text-align: center;
      margin: 0 auto;
    }
    
    .empty-state {
      text-align: center;
    }

    @media (max-width: 768px) {
      .nav-container {
        padding: 0 30px;
        flex-direction: column;
        gap: 20px;
      }

      .main-content {
        padding: 0 30px;
        margin: 40px auto;
      }

      .page-header h1 {
        font-size: 36px;
      }

      .order-card {
        padding: 25px;
      }

      .order-header {
        flex-direction: column;
      }

      .order-total {
        text-align: left;
      }

      .order-summary {
        grid-template-columns: 1fr 1fr;
      }
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <?php if ($is_admin_view): ?>
    <?php 
    // Set current page for nav highlighting
    $_SERVER['PHP_SELF'] = 'orders.php';
    include '../admin/includes/nav.php'; 
    ?>
  <?php else: ?>
    <!-- Navigation Bar -->
    <nav class="navbar">
      <div class="nav-container">
        <a href="../index.php" style="text-decoration: none;">
          <div class="logo-container">
            <div class="logo-symbol">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z" />
              </svg>
            </div>
            <div class="logo-text">
              <div class="logo">Aya Crafts</div>
              <span class="logo-subtitle">Authentic Artistry</span>
            </div>
          </div>
        </a>

        <div class="nav-buttons">
          <a href="all_product.php" class="nav-btn secondary"><span>Shop</span></a>
          <a href="cart.php" class="nav-btn secondary"><span>Cart</span></a>
          <a href="orders.php" class="nav-btn"><span>My Orders</span></a>
        </div>
      </div>
    </nav>
  <?php endif; ?>

  <!-- Main Content -->
  <main class="main-content">
    <div class="page-header">
      <div class="accent-bar"></div>
      <h1><?php echo $is_admin_view ? 'All Orders' : 'My Orders'; ?></h1>
      <p><?php echo $is_admin_view ? 'Manage and track all customer orders' : 'Track and manage your purchases'; ?></p>
    </div>

    <div class="orders-container">
      <?php if ($orders && count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
          <div class="order-card" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
            <div class="order-header">
              <div class="order-info">
                <div class="invoice-number"><?php echo htmlspecialchars($order['invoice_no']); ?></div>
                <div class="order-date">Ordered on <?php echo date('F j, Y', strtotime($order['order_date'])); ?></div>
                <?php if ($is_admin_view && !empty($order['customer_name'])): ?>
                  <div class="customer-info" style="margin-top: 8px; font-size: 14px; color: #6b7280;">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($order['customer_name']); ?>
                    <?php if (!empty($order['customer_email'])): ?>
                      <span style="margin-left: 12px;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($order['customer_email']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($order['has_tier2_artisan']) && $order['has_tier2_artisan'] == 2): ?>
                      <span style="margin-left: 12px; padding: 4px 12px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; border-radius: 12px; font-weight: 600; font-size: 12px;">
                        <i class="fas fa-star"></i> Tier 2
                      </span>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="order-meta">
                <?php
                $status = strtolower(trim($order['order_status'] ?? 'pending'));
                // Map status to valid values
                $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
                if (!in_array($status, $valid_statuses)) {
                    $status = 'pending'; // Default to pending if invalid
                }
                $status_class = 'status-' . $status;
                ?>
                <span class="status-badge <?php echo $status_class; ?>">
                  <?php echo ucfirst($status); ?>
                </span>
                <?php if ($is_admin_view && !empty($order['has_tier2_artisan']) && $order['has_tier2_artisan'] == 2): ?>
                  <select class="status-select" onchange="updateOrderStatus(<?php echo $order['order_id']; ?>, this.value)" onclick="event.stopPropagation();">
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $status === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                  </select>
                <?php endif; ?>

                <div class="order-total">
                  <div class="total-label">Total</div>
                  <div class="total-amount">
                    <?php echo htmlspecialchars($order['currency']); ?>
                    <?php echo number_format($order['total_amount'], 2); ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="order-summary">
              <div class="summary-item">
                <div class="summary-label">Items</div>
                <div class="summary-value"><?php echo $order['item_count']; ?></div>
              </div>
              <div class="summary-item">
                <div class="summary-label">Order ID</div>
                <div class="summary-value">#<?php echo $order['order_id']; ?></div>
              </div>
              <div class="summary-item">
                <div class="summary-label">Payment</div>
                <div class="summary-value">Completed</div>
              </div>
            </div>

            <div class="order-actions">
              <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="action-btn primary" onclick="event.stopPropagation();">View Details</a>
              <?php if (!$is_admin_view): ?>
                <a href="all_product.php" class="action-btn secondary" onclick="event.stopPropagation();">Shop Again</a>
              <?php else: ?>
                <button type="button" class="action-btn danger" onclick="event.stopPropagation(); deleteOrder(<?php echo $order['order_id']; ?>, '<?php echo htmlspecialchars($order['invoice_no'], ENT_QUOTES); ?>');">
                  <i class="fas fa-trash"></i> Delete
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <div class="empty-icon">📦</div>
          <h2>No Orders Yet</h2>
          <p>Start exploring our collection and place your first order!</p>
          <a href="all_product.php" class="action-btn primary" >Browse Products</a>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    // Debug: Log orders data from PHP
    console.log('=== ORDERS PAGE DEBUG ===');
    console.log('Is admin view:', <?php echo $is_admin_view ? 'true' : 'false'; ?>);
    console.log('Customer ID:', <?php echo $customer_id; ?>);
    console.log('Orders count:', <?php echo $orders ? count($orders) : 0; ?>);
    console.log('Orders data:', <?php echo json_encode($orders); ?>);
    
    // Check if orders container exists
    const ordersContainer = document.querySelector('.orders-container');
    console.log('Orders container found:', !!ordersContainer);
    
    // Check if empty state is showing
    const emptyState = document.querySelector('.empty-state');
    console.log('Empty state showing:', !!emptyState);
    
    // Check order cards
    const orderCards = document.querySelectorAll('.order-card');
    console.log('Order cards found:', orderCards.length);
    
    function viewOrderDetails(orderId) {
      console.log('Viewing order details for ID:', orderId);
      window.location.href = 'order_details.php?id=' + orderId;
    }

    function deleteOrder(orderId, invoiceNo) {
      if (!confirm('Are you sure you want to delete invoice ' + invoiceNo + '? This action cannot be undone.')) {
        return;
      }

      const formData = new FormData();
      formData.append('order_id', orderId);

      fetch('../actions/delete_order_action.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Show success message
          alert('Order deleted successfully');
          // Reload the page to reflect changes
          window.location.reload();
        } else {
          alert('Error: ' + (data.message || 'Failed to delete order'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the order. Please try again.');
      });
    }

    function updateOrderStatus(orderId, newStatus) {
      const formData = new FormData();
      formData.append('order_id', orderId);
      formData.append('order_status', newStatus);

      fetch('../actions/update_order_status_action.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Order status updated to ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
          window.location.reload();
        } else {
          alert('Error: ' + (data.message || 'Failed to update status'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the order status.');
      });
    }
  </script>

</body>

</html>