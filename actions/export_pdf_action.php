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

// Normalize products array
$order_products = $order_products ?: [];

// Load TCPDF
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$fallbackTcpdf = __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';

if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} elseif (file_exists($fallbackTcpdf)) {
    require_once $fallbackTcpdf;
} else {
    die('TCPDF library not found. Run "composer require tecnickcom/tcpdf" and try again.');
}

if (!class_exists('TCPDF')) {
    die('TCPDF is not available even after including the library. Verify your installation.');
}

$pdf = new TCPDF();
$pdf->SetCreator('Aya Crafts');
$pdf->SetAuthor('Aya Crafts');
$pdf->SetTitle('Invoice ' . $order_info['invoice_no']);
$pdf->SetSubject('Order Invoice');
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

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

$html = '
    <style>
    * { box-sizing: border-box; }
        .invoice-header {
            text-align: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
            border-bottom: 3px solid #dc2626;
        }
        .invoice-title {
        font-size: 26px;
            font-weight: bold;
            color: #dc2626;
        }
        .invoice-number {
        font-size: 12px;
        color: #555;
        margin-top: 5px;
        }
        .invoice-info {
            width: 100%;
        margin-bottom: 20px;
        }
        .info-section {
        width: 48%;
        display: inline-block;
            vertical-align: top;
        padding: 10px;
        }
        .info-section h3 {
        font-size: 14px;
            color: #dc2626;
        margin-bottom: 8px;
            border-bottom: 1px solid #eee;
        padding-bottom: 4px;
        }
        .info-section p {
        margin: 4px 0;
        font-size: 11px;
        color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        margin-bottom: 15px;
        }
        th {
            background: #dc2626;
        color: #fff;
        padding: 10px;
            text-align: left;
        font-size: 11px;
        }
        td {
        padding: 9px 10px;
        border-bottom: 1px solid #f2f2f2;
        font-size: 11px;
        }
        tr:nth-child(even) {
        background: #fdf5f5;
        }
        .text-right {
            text-align: right;
        }
        .invoice-total {
            text-align: right;
            border-top: 2px solid #dc2626;
        padding-top: 12px;
        margin-top: 5px;
    }
    .invoice-total p {
        margin: 2px 0;
        font-size: 12px;
        }
        .total-amount {
        font-size: 18px;
            font-weight: bold;
            color: #dc2626;
        }
        .invoice-footer {
            text-align: center;
        font-size: 10px;
            color: #666;
        margin-top: 25px;
        border-top: 1px solid #eee;
        padding-top: 10px;
        }
    </style>

    <div class="invoice-header">
        <div class="invoice-title">Aya Crafts</div>
    <div class="invoice-number">Invoice: ' . $invoiceNo . '</div>
    </div>

    <div class="invoice-info">
        <div class="info-section">
            <h3>Order Information</h3>
        <p><strong>Order ID:</strong> #' . $orderId . '</p>
        <p><strong>Order Date:</strong> ' . $orderDate . '</p>
        <p><strong>Status:</strong> ' . $orderStatus . '</p>
        </div>
        <div class="info-section">
            <h3>Customer Information</h3>
        <p><strong>Name:</strong> ' . $customerName . '</p>
        <p><strong>Email:</strong> ' . $customerEmail . '</p>';

if ($customerPhone !== '') {
    $html .= '<p><strong>Phone:</strong> ' . $customerPhone . '</p>';
}
if ($deliveryLocation !== '') {
    $html .= '<p><strong>Delivery Location:</strong> ' . $deliveryLocation . '</p>';
}
if ($recipientName !== '') {
    $html .= '<p><strong>Recipient:</strong> ' . $recipientName . '</p>';
}

$html .= '
        </div>
    </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
    <tbody>' . $rowsHtml . '</tbody>
        </table>

    <div class="invoice-total">
    <p>Subtotal: GHS ' . $subtotalFormatted . '</p>
    <p class="total-amount">Total Due: GHS ' . $grandTotal . '</p>
    <p style="font-size:10px;color:#777;">All prices include applicable taxes.</p>
    </div>

    <div class="invoice-footer">
    <p>Thank you for supporting authentic African artisans at Aya Crafts.</p>
    <p>Generated on ' . $generatedOn . '</p>
    </div>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->lastPage();
$filename = 'invoice_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $invoiceNo) . '.pdf';
$pdf->Output($filename, 'D');
exit;

