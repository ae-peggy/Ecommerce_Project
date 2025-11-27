<?php
/**
 * Analytics Export Action
 * Exports artisan analytics report as printable HTML
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

// Calculate commission (20% platform, 80% artisan)
$platform_fee = $total_sales * 0.20;
$artisan_earnings = $total_sales * 0.80;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Report - <?php echo htmlspecialchars($artisan_info['business_name'] ?? 'Artisan'); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            background: #f5f5f5;
            line-height: 1.5;
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
        }
        .report-container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 50px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 3px solid #dc2626;
        }
        .report-title {
            font-size: 32px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }
        .report-subtitle {
            font-size: 16px;
            color: #666;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-box {
            padding: 20px;
            text-align: center;
            border: 1px solid #eee;
            background: #f9fafb;
            border-radius: 8px;
        }
        .stat-box.highlight {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #059669;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 5px;
        }
        .stat-box.highlight .stat-value {
            color: #059669;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-title {
            font-size: 20px;
            color: #dc2626;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #fee2e2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #dc2626;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        tr:nth-child(even) {
            background: #fef7f7;
        }
        .text-right {
            text-align: right;
        }
        .report-footer {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .commission-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }
        .commission-box {
            padding: 25px;
            border-radius: 8px;
            text-align: center;
        }
        .commission-box.earnings {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid #059669;
        }
        .commission-box.fees {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #dc2626;
        }
        .commission-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .commission-box.earnings .commission-value { color: #059669; }
        .commission-box.fees .commission-value { color: #dc2626; }
        .commission-label {
            font-size: 14px;
            font-weight: 600;
        }
        .commission-box.earnings .commission-label { color: #065f46; }
        .commission-box.fees .commission-label { color: #991b1b; }
        
        @media print {
            body { background: white; }
            .print-controls { display: none !important; }
            .report-container { 
                box-shadow: none; 
                margin: 0; 
                padding: 20px;
                max-width: 100%;
            }
            .stats-grid { grid-template-columns: repeat(4, 1fr); }
        }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .commission-section { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <p>💡 To save as PDF: Click "Print" then select "Save as PDF" as your printer</p>
        <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>

    <div class="report-container">
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

        <!-- Commission Breakdown -->
        <h3 class="section-title">Earnings Breakdown</h3>
        <div class="commission-section">
            <div class="commission-box earnings">
                <div class="commission-value">GHS <?php echo number_format($artisan_earnings, 2); ?></div>
                <div class="commission-label">Your Earnings (80%)</div>
            </div>
            <div class="commission-box fees">
                <div class="commission-value">GHS <?php echo number_format($platform_fee, 2); ?></div>
                <div class="commission-label">Platform Fee (20%)</div>
            </div>
        </div>

        <div class="report-footer">
            <p><strong>Thank you for being part of Aya Crafts!</strong></p>
            <p>Generated on <?php echo date('F j, Y, g:i A'); ?></p>
            <p style="margin-top: 10px; color: #999;">Aya Crafts - Artisan Portal</p>
        </div>
    </div>
</body>
</html>

