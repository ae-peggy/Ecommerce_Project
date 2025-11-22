<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();

// Fetch analytics data
$monthly_sales = get_artisan_monthly_sales($artisan_id);
$top_products = get_artisan_top_products($artisan_id, 5);
$sales_by_category = get_artisan_sales_by_category($artisan_id);
$revenue_stats = get_artisan_revenue_stats($artisan_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Aya Crafts Artisan Portal</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="artisan-main-content">
        <div class="artisan-section-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <h1>Analytics & Insights</h1>
                <select id="timeRange" class="artisan-form-control" style="width: auto; min-width: 200px;">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last Year</option>
                </select>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="artisan-stats-grid">
            <div class="artisan-stat-card">
                <div class="artisan-stat-icon green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="artisan-stat-details">
                    <h3>GHS <?php echo number_format($revenue_stats['total_revenue'] ?? 0, 2); ?></h3>
                    <p>Total Revenue</p>
                    <small style="color: #10b981; font-weight: 600; display: block; margin-top: 8px;">
                        <i class="fas fa-arrow-up"></i> <?php echo $revenue_stats['growth'] ?? '0'; ?>% from last period
                    </small>
                </div>
            </div>

            <div class="artisan-stat-card">
                <div class="artisan-stat-icon blue">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="artisan-stat-details">
                    <h3>GHS <?php echo number_format($revenue_stats['avg_order_value'] ?? 0, 2); ?></h3>
                    <p>Avg. Order Value</p>
                    <small style="color: #6b7280; display: block; margin-top: 8px;">Per transaction</small>
                </div>
            </div>

            <div class="artisan-stat-card">
                <div class="artisan-stat-icon orange">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="artisan-stat-details">
                    <h3><?php echo $revenue_stats['total_orders'] ?? 0; ?></h3>
                    <p>Total Orders</p>
                    <small style="color: #6b7280; display: block; margin-top: 8px;">All time</small>
                </div>
            </div>

            <div class="artisan-stat-card">
                <div class="artisan-stat-icon purple">
                    <i class="fas fa-box"></i>
                </div>
                <div class="artisan-stat-details">
                    <h3><?php echo $revenue_stats['products_sold'] ?? 0; ?></h3>
                    <p>Products Sold</p>
                    <small style="color: #6b7280; display: block; margin-top: 8px;">Total units</small>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
            <!-- Sales Trend Chart -->
            <div class="artisan-section-card">
                    <h2><i class="fas fa-chart-area" style="color: #dc2626;"></i> Sales Trend</h2>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="artisan-section-card">
                    <h2><i class="fas fa-chart-pie" style="color: #dc2626;"></i> Sales by Category</h2>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Products and Recent Activity -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- Top Products -->
                <div class="artisan-section-card">
                    <h2><i class="fas fa-fire" style="color: #dc2626;"></i> Top Selling Products</h2>
                    <div style="margin-top: 20px;">
                        <?php if (empty($top_products)): ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p>No sales data yet</p>
                            </div>
                        <?php else: ?>
                            <?php 
                                $top_product_max = $top_products[0]['total_sold'] ?? 0;
                                foreach ($top_products as $index => $product):
                                    $progress = ($top_product_max > 0)
                                        ? min(100, ($product['total_sold'] / $top_product_max) * 100)
                                        : 0;
                            ?>
                            <div style="display: flex; align-items: center; padding: 16px; border-bottom: 1px solid #f3f4f6; gap: 16px;">
                                <div style="
                                    width: 48px;
                                    height: 48px;
                                    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
                                    border-radius: 12px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: white;
                                    font-weight: 700;
                                    font-size: 18px;
                                    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
                                ">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div style="flex: 1;">
                                    <strong style="display: block; margin-bottom: 4px;"><?php echo htmlspecialchars($product['product_title']); ?></strong>
                                    <div style="display: flex; gap: 16px; font-size: 13px; color: #6b7280;">
                                        <span><i class="fas fa-box"></i> <?php echo $product['total_sold']; ?> sold</span>
                                        <span><i class="fas fa-dollar-sign"></i> GHS <?php echo number_format($product['revenue'], 2); ?></span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="
                                        width: 60px;
                                        height: 8px;
                                        background: #f3f4f6;
                                        border-radius: 4px;
                                        overflow: hidden;
                                    ">
                                        <div style="
                                            width: <?php echo $progress; ?>%;
                                            height: 100%;
                                            background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
                                        "></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="artisan-section-card">
                    <h2><i class="fas fa-tachometer-alt" style="color: #dc2626;"></i> Performance Metrics</h2>
                    <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 20px;">
                        <!-- Conversion Rate -->
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 500;">Conversion Rate</span>
                                <span style="font-weight: 700; color: #dc2626;">
                                    <?php echo number_format($revenue_stats['conversion_rate'] ?? 0, 1); ?>%
                                </span>
                            </div>
                            <div style="width: 100%; height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                <div style="
                                    width: <?php echo min(100, max(0, $revenue_stats['conversion_rate'] ?? 0)); ?>%;
                                    height: 100%;
                                    background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
                                    transition: width 0.6s ease;
                                "></div>
                            </div>
                        </div>

                        <!-- Average Rating -->
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 500;">Average Rating</span>
                                <span style="font-weight: 700; color: #f59e0b;">
                                    <?php echo number_format($revenue_stats['avg_rating'] ?? 0, 1); ?> 
                                    <i class="fas fa-star"></i>
                                </span>
                            </div>
                            <div style="width: 100%; height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                <div style="
                                    width: <?php echo min(100, max(0, (($revenue_stats['avg_rating'] ?? 0) / 5) * 100)); ?>%;
                                    height: 100%;
                                    background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
                                    transition: width 0.6s ease;
                                "></div>
                            </div>
                        </div>

                        <!-- Inventory Turnover -->
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 500;">Inventory Turnover</span>
                                <span style="font-weight: 700; color: #10b981;">
                                    <?php echo number_format($revenue_stats['turnover_rate'] ?? 0, 1); ?>x
                                </span>
                            </div>
                            <div style="width: 100%; height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                <div style="
                                    width: <?php echo min(100, max(0, ($revenue_stats['turnover_rate'] ?? 0) * 10)); ?>%;
                                    height: 100%;
                                    background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
                                    transition: width 0.6s ease;
                                "></div>
                            </div>
                        </div>

                        <!-- Customer Satisfaction -->
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 500;">Customer Satisfaction</span>
                                <span style="font-weight: 700; color: #3b82f6;">
                                    <?php echo number_format($revenue_stats['satisfaction'] ?? 0, 0); ?>%
                                </span>
                            </div>
                            <div style="width: 100%; height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                <div style="
                                    width: <?php echo min(100, max(0, $revenue_stats['satisfaction'] ?? 0)); ?>%;
                                    height: 100%;
                                    background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
                                    transition: width 0.6s ease;
                                "></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="artisan-section-card" style="margin-top: 24px; text-align: center;">
                <h2><i class="fas fa-download" style="color: #dc2626;"></i> Export Reports</h2>
                <p style="color: #6b7280; margin: 16px 0 24px;">Download detailed reports of your sales and performance</p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="../actions/export_analytics_pdf_action.php" class="artisan-btn artisan-btn-primary" target="_blank" style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-file-pdf"></i> Export as PDF
                    </a>
                    <button class="artisan-btn artisan-btn-secondary" onclick="exportReport('excel')">
                        <i class="fas fa-file-excel"></i> Export as Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: [<?php 
                    foreach ($monthly_sales as $sale) {
                        echo "'" . date('M d', strtotime($sale['date'])) . "',";
                    }
                ?>],
                datasets: [{
                    label: 'Sales (GHS)',
                    data: [<?php 
                        foreach ($monthly_sales as $sale) {
                            echo $sale['amount'] . ',';
                        }
                    ?>],
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#dc2626',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#111827',
                        bodyColor: '#6b7280',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'GHS ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'GHS ' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    foreach ($sales_by_category as $cat) {
                        echo "'" . $cat['cat_name'] . "',";
                    }
                ?>],
                datasets: [{
                    data: [<?php 
                        foreach ($sales_by_category as $cat) {
                            echo $cat['total'] . ',';
                        }
                    ?>],
                    backgroundColor: [
                        '#dc2626',
                        '#ef4444',
                        '#f87171',
                        '#fca5a5',
                        '#fecaca'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#111827',
                        bodyColor: '#6b7280',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': GHS ' + context.parsed.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        function exportReport(format) {
            window.location.href = `../actions/export_analytics.php?artisan_id=<?php echo $artisan_id; ?>&format=${format}&range=${document.getElementById('timeRange').value}`;
        }

        // Update charts when time range changes
        document.getElementById('timeRange').addEventListener('change', function() {
            window.location.href = `analytics.php?range=${this.value}`;
        });
        
        // Mobile menu toggle
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });
    </script>
</body>
</html>