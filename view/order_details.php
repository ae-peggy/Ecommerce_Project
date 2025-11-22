<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    header('Location: orders.php');
    exit();
}

// Get order details
require_once '../controllers/order_controller.php';
$is_admin_view = is_admin();
$customer_id = get_user_id();

// Get order info
if ($is_admin_view) {
    // Admin can view any order
    require_once '../classes/order_class.php';
    $order_obj = new order_class();
    $order_info = $order_obj->get_all_orders();
    $order_info = array_filter($order_info, function($o) use ($order_id) {
        return $o['order_id'] == $order_id;
    });
    $order_info = reset($order_info);
} else {
    // Customer can only view their own orders
    $order_info = get_order_details_ctr($order_id, $customer_id);
}

if (!$order_info) {
    header('Location: orders.php');
    exit();
}

// Get order products
$order_products = get_order_products_ctr($order_id);

// Get customer info
require_once '../classes/customer_class.php';
$customer_obj = new customer_class();
$customer_info = $customer_obj->get_customer_by_id($order_info['customer_id'] ?? $customer_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Aya Crafts</title>
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
      margin-bottom: 2px;
      margin-top: -5px;
      letter-spacing: 1px;
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-decoration: none;
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
      border: 1.5px solid transparent;
    }

    .nav-btn.secondary {
      color: #6b7280;
      border: 1.5px solid #e5e7eb;
      background: transparent;
    }

    .nav-btn.secondary:hover {
      border-color: #dc2626;
      color: #dc2626;
    }

    .main-content {
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 60px;
    }

    .invoice-container {
      background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
      margin-bottom: 30px;
    }

    .invoice-header {
      text-align: center;
      margin-bottom: 40px;
      padding-bottom: 30px;
      border-bottom: 2px solid #e5e7eb;
    }

    .invoice-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 42px;
      color: #111827;
      margin-bottom: 10px;
    }

    .invoice-number {
      font-size: 18px;
      color: #6b7280;
      font-weight: 500;
    }

    .invoice-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      margin-bottom: 40px;
    }

    .info-section h3 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 24px;
      color: #111827;
      margin-bottom: 15px;
    }

    .info-section p {
      color: #6b7280;
      margin-bottom: 8px;
      line-height: 1.8;
    }

    .order-items {
      margin-bottom: 40px;
    }

    .order-items h3 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 28px;
      color: #111827;
      margin-bottom: 20px;
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .items-table th {
      background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
      padding: 15px;
      text-align: left;
      font-weight: 600;
      color: #374151;
      border-bottom: 2px solid #e5e7eb;
    }

    .items-table td {
      padding: 15px;
      border-bottom: 1px solid #f3f4f6;
      color: #6b7280;
    }

    .items-table tr:last-child td {
      border-bottom: none;
    }

    .items-table .item-image {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }

    .invoice-total {
      text-align: right;
      padding-top: 20px;
      border-top: 2px solid #e5e7eb;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      font-size: 16px;
    }

    .total-row.grand-total {
      font-size: 24px;
      font-weight: 700;
      color: #dc2626;
      margin-top: 10px;
      padding-top: 20px;
      border-top: 2px solid #e5e7eb;
    }

    .status-badge {
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 13px;
      font-weight: 600;
      display: inline-block;
    }

    .status-pending {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      color: #92400e;
    }

    .status-processing {
      background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
      color: #1e40af;
    }

    .status-completed {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      color: #065f46;
    }

    .status-cancelled {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      color: #991b1b;
    }

    .invoice-actions {
      display: flex;
      gap: 12px;
      justify-content: center;
      margin-top: 30px;
    }

    .btn {
      padding: 14px 32px;
      border-radius: 50px;
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      border: 1.5px solid transparent;
      cursor: pointer;
    }

    .btn-primary {
      background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(220, 38, 38, 0.25);
    }

    .btn-secondary {
      background: white;
      color: #6b7280;
      border: 1.5px solid #e5e7eb;
    }

    .btn:hover {
      transform: translateY(-2px);
    }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
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
                <a href="orders.php" class="nav-btn secondary">Back to Orders</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="invoice-container">
            <div class="invoice-header">
                <h1 class="invoice-title">Order Invoice</h1>
                <div class="invoice-number"><?php echo htmlspecialchars($order_info['invoice_no']); ?></div>
            </div>

            <div class="invoice-info">
                <div class="info-section">
                    <h3>Order Information</h3>
                    <p><strong>Order ID:</strong> #<?php echo str_pad($order_info['order_id'], 6, '0', STR_PAD_LEFT); ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i A', strtotime($order_info['order_date'])); ?></p>
                    <?php
                    $status = strtolower(trim($order_info['order_status'] ?? 'pending'));
                    $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
                    if (!in_array($status, $valid_statuses)) {
                        $status = 'pending';
                    }
                    ?>
                    <p><strong>Status:</strong> 
                        <span class="status-badge status-<?php echo $status; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </p>
                </div>

                <div class="info-section">
                    <h3>Customer Information</h3>
                    <?php if ($customer_info): ?>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_info['customer_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_info['customer_email']); ?></p>
                        <?php if (!empty($customer_info['customer_contact'])): ?>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer_info['customer_contact']); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="order-items">
                <h3>Order Items</h3>
                <?php if ($order_products && count($order_products) > 0): ?>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            foreach ($order_products as $item): 
                                $subtotal = $item['qty'] * $item['product_price'];
                                $total += $subtotal;
                                $image_path = !empty($item['product_image']) ? '../' . ltrim($item['product_image'], '/') : 'https://via.placeholder.com/80x80?text=No+Image';
                            ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                             class="item-image"
                                             onerror="this.src='https://via.placeholder.com/80x80?text=No+Image';">
                                    </td>
                                    <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                                    <td><?php echo $item['qty']; ?></td>
                                    <td>GHS <?php echo number_format($item['product_price'], 2); ?></td>
                                    <td><strong>GHS <?php echo number_format($subtotal, 2); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #9ca3af; padding: 40px;">No items found in this order.</p>
                <?php endif; ?>

                <div class="invoice-total">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>GHS <?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span>GHS 0.00</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total:</span>
                        <span>GHS <?php echo number_format($order_info['total_amount'] ?? $total, 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="invoice-actions">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
                <a href="../actions/export_pdf_action.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export as PDF
                </a>
                <a href="orders.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </main>
</body>
</html>

