<?php
/**
 * Invoice Export Action
 * Exports order invoice as printable HTML (user can Print to PDF)
 */

require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: ../login/login.php');
    exit();
}

// Get order ID
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    die('Invalid order ID');
}

// Get order details
require_once '../controllers/order_controller.php';
$is_admin_view = is_admin();
$customer_id = get_user_id();

// Get order info
if ($is_admin_view) {
    require_once '../classes/order_class.php';
    $order_obj = new order_class();
    $all_orders = $order_obj->get_all_orders();
    $order_info = null;
    foreach ($all_orders as $o) {
        if ($o['order_id'] == $order_id) {
            $order_info = $o;
            break;
        }
    }
} else {
    $order_info = get_order_details_ctr($order_id, $customer_id);
}

if (!$order_info) {
    die('Order not found');
}

// Get order products
$order_products = get_order_products_ctr($order_id);

// Get customer info
require_once '../classes/customer_class.php';
$customer_obj = new customer_class();
$customer_info = $customer_obj->get_customer_by_id($order_info['customer_id'] ?? $customer_id);

// Normalize products array
$order_products = $order_products ?: [];

$orderId = str_pad($order_info['order_id'], 6, '0', STR_PAD_LEFT);
$orderDate = date('F j, Y, g:i A', strtotime($order_info['order_date']));
$orderStatus = ucfirst($order_info['order_status'] ?? 'Pending');
$invoiceNo = htmlspecialchars($order_info['invoice_no'], ENT_QUOTES, 'UTF-8');
$customerName = htmlspecialchars($customer_info['customer_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$customerEmail = htmlspecialchars($customer_info['customer_email'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$customerPhone = !empty($customer_info['customer_contact']) ? htmlspecialchars($customer_info['customer_contact'], ENT_QUOTES, 'UTF-8') : '';
$deliveryLocation = !empty($order_info['delivery_location']) ? htmlspecialchars($order_info['delivery_location'], ENT_QUOTES, 'UTF-8') : '';
$recipientName = !empty($order_info['recipient_name']) ? htmlspecialchars($order_info['recipient_name'], ENT_QUOTES, 'UTF-8') : '';

$subtotal = 0;
$rowsHtml = '';

if (!empty($order_products)) {
    foreach ($order_products as $product) {
        $title = htmlspecialchars($product['product_title'], ENT_QUOTES, 'UTF-8');
        $qty = (int)$product['qty'];
        $price = (float)$product['product_price'];
        $lineTotal = $qty * $price;
        $subtotal += $lineTotal;

        $rowsHtml .= '<tr>
            <td>' . $title . '</td>
            <td class="text-right">' . $qty . '</td>
            <td class="text-right">GHS ' . number_format($price, 2) . '</td>
            <td class="text-right">GHS ' . number_format($lineTotal, 2) . '</td>
        </tr>';
    }
} else {
    $rowsHtml = '<tr><td colspan="4" class="text-right">No products found for this order.</td></tr>';
}

$grandTotal = number_format($order_info['total_amount'] ?? $subtotal, 2);
$subtotalFormatted = number_format($subtotal, 2);
$generatedOn = date('F j, Y, g:i A');

// Output printable HTML invoice
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice <?php echo $invoiceNo; ?></title>
    <style>
        * { 
            box-sizing: border-box; 
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            background: #f5f5f5;
        }
        .print-controls {
            background: #dc2626;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .print-controls p {
            margin: 0;
            font-size: 14px;
        }
        .print-btn {
            background: white;
            color: #dc2626;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .print-btn:hover {
            background: #fee2e2;
            transform: translateY(-1px);
        }
        .invoice-container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 50px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 3px solid #dc2626;
        }
        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        .invoice-subtitle {
            font-size: 12px;
            color: #666;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .invoice-number {
            font-size: 16px;
            color: #555;
            margin-top: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        .info-section h3 {
            font-size: 14px;
            color: #dc2626;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #fee2e2;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-section p {
            margin: 8px 0;
            font-size: 14px;
            color: #333;
        }
        .info-section strong {
            color: #111;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .products-table th {
            background: #dc2626;
            color: #fff;
            padding: 14px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .products-table th.text-right {
            text-align: right;
        }
        .products-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        .products-table tr:nth-child(even) {
            background: #fef7f7;
        }
        .text-right {
            text-align: right;
        }
        .invoice-total {
            text-align: right;
            border-top: 3px solid #dc2626;
            padding-top: 20px;
            margin-top: 10px;
        }
        .invoice-total p {
            margin: 6px 0;
            font-size: 14px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin-top: 10px;
        }
        .tax-note {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
        }
        .invoice-footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }
        .invoice-footer p {
            margin: 5px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        
        /* Print styles */
        @media print {
            body { background: white; }
            .print-controls { display: none !important; }
            .invoice-container { 
                box-shadow: none; 
                margin: 0; 
                padding: 20px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <p>💡 To save as PDF: Click "Print" then select "Save as PDF" as your printer</p>
        <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="invoice-title">Aya Crafts</div>
            <div class="invoice-subtitle">Authentic African Artistry</div>
            <div class="invoice-number">Invoice: <?php echo $invoiceNo; ?></div>
        </div>

        <div class="info-grid">
            <div class="info-section">
                <h3>Order Information</h3>
                <p><strong>Order ID:</strong> #<?php echo $orderId; ?></p>
                <p><strong>Order Date:</strong> <?php echo $orderDate; ?></p>
                <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($orderStatus); ?>"><?php echo $orderStatus; ?></span></p>
            </div>
            <div class="info-section">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?php echo $customerName; ?></p>
                <p><strong>Email:</strong> <?php echo $customerEmail; ?></p>
                <?php if ($customerPhone !== ''): ?>
                    <p><strong>Phone:</strong> <?php echo $customerPhone; ?></p>
                <?php endif; ?>
                <?php if ($deliveryLocation !== ''): ?>
                    <p><strong>Delivery Location:</strong> <?php echo $deliveryLocation; ?></p>
                <?php endif; ?>
                <?php if ($recipientName !== ''): ?>
                    <p><strong>Recipient:</strong> <?php echo $recipientName; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Product</th>
                    <th class="text-right" style="width: 15%;">Quantity</th>
                    <th class="text-right" style="width: 17%;">Unit Price</th>
                    <th class="text-right" style="width: 18%;">Total</th>
                </tr>
            </thead>
            <tbody><?php echo $rowsHtml; ?></tbody>
        </table>

        <div class="invoice-total">
            <p>Subtotal: <strong>GHS <?php echo $subtotalFormatted; ?></strong></p>
            <p class="total-amount">Total Due: GHS <?php echo $grandTotal; ?></p>
            <p class="tax-note">All prices include applicable taxes.</p>
        </div>

        <div class="invoice-footer">
            <p><strong>Thank you for supporting authentic African artisans!</strong></p>
            <p>Aya Crafts - Preserving Heritage, Empowering Communities</p>
            <p style="margin-top: 15px; color: #999;">Generated on <?php echo $generatedOn; ?></p>
        </div>
    </div>
</body>
</html>

