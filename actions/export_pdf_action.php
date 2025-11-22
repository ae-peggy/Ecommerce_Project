<?php
/**
 * PDF Export Action
 * Exports order invoice as PDF
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

// Generate HTML for PDF
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 40px;
            background: #fff;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc2626;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 14px;
            color: #666;
        }
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
        }
        .info-section h3 {
            font-size: 16px;
            color: #dc2626;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-section p {
            margin: 5px 0;
            font-size: 12px;
        }
        .invoice-items {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #dc2626;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .invoice-total {
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #dc2626;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
        }
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        @media print {
            body { padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="invoice-title">Aya Crafts</div>
        <div class="invoice-number">Invoice: <?php echo htmlspecialchars($order_info['invoice_no']); ?></div>
    </div>

    <div class="invoice-info">
        <div class="info-section">
            <h3>Order Information</h3>
            <p><strong>Order ID:</strong> #<?php echo str_pad($order_info['order_id'], 6, '0', STR_PAD_LEFT); ?></p>
            <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i A', strtotime($order_info['order_date'])); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($order_info['order_status'] ?? 'Pending'); ?></p>
        </div>
        <div class="info-section">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_info['customer_name'] ?? 'N/A'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_info['customer_email'] ?? 'N/A'); ?></p>
            <?php if (!empty($customer_info['customer_contact'])): ?>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer_info['customer_contact']); ?></p>
            <?php endif; ?>
            <?php if (!empty($order_info['delivery_location'])): ?>
            <p><strong>Delivery Location:</strong> <?php echo htmlspecialchars($order_info['delivery_location']); ?></p>
            <?php endif; ?>
            <?php if (!empty($order_info['recipient_name'])): ?>
            <p><strong>Recipient:</strong> <?php echo htmlspecialchars($order_info['recipient_name']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="invoice-items">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                foreach ($order_products as $item):
                    $item_total = (float)$item['qty'] * (float)$item['product_price'];
                    $subtotal += $item_total;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                    <td class="text-right"><?php echo $item['qty']; ?></td>
                    <td class="text-right">GHS <?php echo number_format($item['product_price'], 2); ?></td>
                    <td class="text-right"><strong>GHS <?php echo number_format($item_total, 2); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="invoice-total">
        <div class="total-amount">
            Total: GHS <?php echo number_format($order_info['total_amount'] ?? $subtotal, 2); ?>
        </div>
    </div>

    <div class="invoice-footer">
        <p>Thank you for your purchase!</p>
        <p>Aya Crafts - Authentic Artistry</p>
        <p>Generated on <?php echo date('F j, Y, g:i A'); ?></p>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

// Use browser's print to PDF or download as HTML
// For a proper PDF library, you would use TCPDF, FPDF, or DomPDF here
// For now, we'll provide a downloadable HTML file that can be printed to PDF

header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="invoice_' . $order_info['invoice_no'] . '.html');
echo $html;
exit();
?>

