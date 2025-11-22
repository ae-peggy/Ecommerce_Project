<?php
/**
 * Analytics PDF Export Action
 * Exports artisan analytics report as PDF
 */

require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();

// Get artisan analytics data
require_once '../classes/artisan_class.php';
$artisan_obj = new artisan_class();

// Get sales data
$sales_data = $artisan_obj->get_monthly_sales_data($artisan_id, 30);
$orders = $artisan_obj->get_orders_for_artisan($artisan_id);
$artisan_info = $artisan_obj->get_artisan_by_id($artisan_id);

// Calculate totals
$total_sales = 0;
$total_orders = count($orders);
foreach ($orders as $order) {
    $total_sales += (float)($order['total_amount'] ?? 0);
}

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
        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc2626;
        }
        .report-title {
            font-size: 28px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }
        .report-subtitle {
            font-size: 14px;
            color: #666;
        }
        .stats-section {
            margin-bottom: 30px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #eee;
            background: #f9fafb;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 18px;
            color: #dc2626;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #dc2626;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #dc2626;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .report-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="report-title">Sales Analytics Report</div>
        <div class="report-subtitle"><?php echo htmlspecialchars($artisan_info['business_name'] ?? 'Artisan'); ?> - <?php echo date('F Y'); ?></div>
    </div>

    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value"><?php echo $total_orders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">GHS <?php echo number_format($total_sales, 2); ?></div>
                <div class="stat-label">Total Sales</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo count($sales_data); ?></div>
                <div class="stat-label">Active Days</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">GHS <?php echo $total_orders > 0 ? number_format($total_sales / $total_orders, 2) : '0.00'; ?></div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>
    </div>

    <div class="section-title">Recent Orders</div>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $display_orders = array_slice($orders, 0, 10);
            foreach ($display_orders as $order):
            ?>
            <tr>
                <td>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td class="text-right">GHS <?php echo number_format($order['total_amount'], 2); ?></td>
                <td><?php echo ucfirst($order['order_status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="report-footer">
        <p>Generated on <?php echo date('F j, Y, g:i A'); ?></p>
        <p>Aya Crafts - Artisan Portal</p>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="analytics_report_' . date('Y-m-d') . '.html"');
echo $html;
exit();
?>

