<?php
require_once '../settings/core.php';
require_login('../login/login.php');
require_admin('../index.php');

require_once '../classes/admin_dashboard_class.php';

$dashboard = new admin_dashboard_class();
$summary = $dashboard->get_tier2_summary();
$top_artisans = $dashboard->get_top_tier2_artisans(5);
$low_stock_products = $dashboard->get_tier2_low_stock_products(10, 8);
$sales_trend = $dashboard->get_tier2_monthly_sales(6);
$recent_orders = $dashboard->get_recent_tier2_orders(5);
$tier2_artisans = $dashboard->get_tier2_artisan_overview();

$maxRevenue = 0;
foreach ($sales_trend as $row) {
    $maxRevenue = max($maxRevenue, (float)($row['revenue'] ?? 0));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tier 2 Artisan Dashboard - Aya Crafts</title>
    <link rel="stylesheet" href="../css/admin_pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-grid {
            display: grid;
            gap: 24px;
        }
        .summary-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(220, 38, 38, 0.08);
        }
        .summary-card h3 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6b7280;
        }
        .summary-card .value {
            font-size: 32px;
            font-weight: 600;
            margin: 10px 0 4px 0;
            color: #111827;
        }
        .summary-card .subtext {
            font-size: 13px;
            color: #9ca3af;
        }
        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #f3f4f6;
        }
        .card h2 {
            margin-top: 0;
            font-size: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        table th {
            text-align: left;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            color: #9ca3af;
        }
        .trend-row {
            margin-top: 16px;
        }
        .trend-label {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #6b7280;
        }
        .trend-bar {
            height: 8px;
            border-radius: 999px;
            background: linear-gradient(90deg, #fde68a, #f97316);
            margin-top: 6px;
            position: relative;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>
    <?php include '../admin/includes/nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Tier 2 Artisan Control Center</h1>
            <p class="page-subtitle">Track sales, stock health, and fulfillment velocity for every Tier 2 artisan.</p>
        </div>

        <div class="dashboard-grid">
            <div class="summary-grid">
                <div class="summary-card">
                    <h3>Tier 2 Artisans</h3>
                    <div class="value"><?php echo number_format($summary['total_artisans'] ?? 0); ?></div>
                    <div class="subtext">Approved sellers in growth tier</div>
                </div>
                <div class="summary-card">
                    <h3>Units On Hand</h3>
                    <div class="value"><?php echo number_format($summary['total_stock_units'] ?? 0); ?></div>
                    <div class="subtext">Current stock across catalog</div>
                </div>
                <div class="summary-card">
                    <h3>Units Sold</h3>
                    <div class="value"><?php echo number_format($summary['units_sold'] ?? 0); ?></div>
                    <div class="subtext">All-time fulfillment volume</div>
                </div>
                <div class="summary-card">
                    <h3>Gross Sales (GHS)</h3>
                    <div class="value">₵<?php echo number_format($summary['gross_sales'] ?? 0, 2); ?></div>
                    <div class="subtext">Revenue attributed to tier 2</div>
                </div>
                <div class="summary-card">
                    <h3>Inventory Value (GHS)</h3>
                    <div class="value">₵<?php echo number_format($summary['stock_value'] ?? 0, 2); ?></div>
                    <div class="subtext">Retail value of stocked units</div>
                </div>
                <div class="summary-card">
                    <h3>Low Stock Alerts</h3>
                    <div class="value"><?php echo number_format($summary['low_stock_count'] ?? 0); ?></div>
                    <div class="subtext">Products at or below threshold</div>
                </div>
            </div>

            <div class="summary-grid">
                <div class="card">
                    <h2>Top Performing Artisans</h2>
                    <?php if (!empty($top_artisans)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Artisan</th>
                                    <th>Sales (GHS)</th>
                                    <th>Units Sold</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_artisans as $artisan): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($artisan['business_name']); ?></td>
                                        <td>₵<?php echo number_format($artisan['total_sales'] ?? 0, 2); ?></td>
                                        <td><?php echo number_format($artisan['units_sold'] ?? 0); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                $stock = (int)($artisan['stock_on_hand'] ?? 0);
                                                echo $stock > 20 ? 'badge-success' : ($stock > 5 ? 'badge-warning' : 'badge-danger');
                                            ?>">
                                                <?php echo number_format($stock); ?> in stock
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No Tier 2 artisan data yet.</p>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2>Sales Momentum (Last 6 Months)</h2>
                    <?php if (!empty($sales_trend)): ?>
                        <?php foreach ($sales_trend as $row): 
                            $revenue = (float)($row['revenue'] ?? 0);
                            $percent = $maxRevenue > 0 ? max(($revenue / $maxRevenue) * 100, 6) : 6;
                        ?>
                            <div class="trend-row">
                                <div class="trend-label">
                                    <span><?php echo htmlspecialchars($row['month_label']); ?></span>
                                    <span>₵<?php echo number_format($revenue, 2); ?></span>
                                </div>
                                <div class="trend-bar" style="width: <?php echo $percent; ?>%;"></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No recent sales to display.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="summary-grid">
                <div class="card">
                    <h2>Low Stock Watchlist</h2>
                    <?php if (!empty($low_stock_products)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Artisan</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock_products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['product_title']); ?></td>
                                        <td><?php echo htmlspecialchars($product['business_name']); ?></td>
                                        <td>
                                            <span class="badge <?php echo ($product['product_qty'] ?? 0) > 5 ? 'badge-warning' : 'badge-danger'; ?>">
                                                <?php echo number_format($product['product_qty'] ?? 0); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Stock levels look healthy. No alerts right now.</p>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h2>Latest Tier 2 Orders</h2>
                    <?php if (!empty($recent_orders)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                $status = strtolower($order['order_status'] ?? '');
                                                echo $status === 'completed' ? 'badge-success' : ($status === 'pending' ? 'badge-warning' : 'badge-danger');
                                            ?>">
                                                <?php echo ucfirst($order['order_status'] ?? ''); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                        <td>₵<?php echo number_format($order['total_amount'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No recent orders from tier 2 artisans.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card full-width">
                <h2>Tier 2 Artisan Directory</h2>
                <p style="color:#6b7280; margin-top:-10px; margin-bottom:16px;">Live data pulled directly from the artisans table so you can audit who has inventory and sales momentum.</p>
                <?php if (!empty($tier2_artisans)): ?>
                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Business</th>
                                    <th>Owner</th>
                                    <th>Contact</th>
                                    <th>Products</th>
                                    <th>Stock Units</th>
                                    <th>Revenue (GHS)</th>
                                    <th>Units Sold</th>
                                    <th>Commission %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tier2_artisans as $artisan): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($artisan['business_name']); ?></td>
                                        <td><?php echo htmlspecialchars($artisan['customer_name']); ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($artisan['customer_email']); ?></div>
                                            <?php if (!empty($artisan['customer_contact'])): ?>
                                                <small style="color:#6b7280;"><?php echo htmlspecialchars($artisan['customer_contact']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo number_format($artisan['product_count'] ?? 0); ?></td>
                                        <td><?php echo number_format($artisan['stock_units'] ?? 0); ?></td>
                                        <td>₵<?php echo number_format($artisan['revenue'] ?? 0, 2); ?></td>
                                        <td><?php echo number_format($artisan['units_sold'] ?? 0); ?></td>
                                        <td><?php echo number_format($artisan['commission_rate'] ?? 0, 2); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No Tier 2 artisans have been onboarded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

