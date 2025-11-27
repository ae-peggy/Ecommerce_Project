<?php
require_once __DIR__ . '/../settings/db_class.php';

class admin_dashboard_class extends db_connection {

    public function get_tier2_summary($low_stock_threshold = 10) {
        $summarySql = "SELECT 
                            COUNT(DISTINCT a.artisan_id) AS total_artisans,
                            COALESCE(SUM(p.product_qty), 0) AS total_stock_units,
                            COALESCE(SUM(p.product_qty * p.product_price), 0) AS stock_value,
                            COALESCE(SUM(od.qty), 0) AS units_sold,
                            COALESCE(SUM(od.qty * p.product_price), 0) AS gross_sales
                        FROM artisans a
                        LEFT JOIN products p ON p.artisan_id = a.artisan_id
                        LEFT JOIN orderdetails od ON od.product_id = p.product_id
                        WHERE a.tier = 2";

        $summary = $this->db_fetch_one($summarySql) ?? [
            'total_artisans' => 0,
            'total_stock_units' => 0,
            'stock_value' => 0,
            'units_sold' => 0,
            'gross_sales' => 0,
        ];

        $threshold = max(0, (int)$low_stock_threshold);
        $lowStockSql = "SELECT COUNT(*) AS low_stock_count
                        FROM products p
                        INNER JOIN artisans a ON p.artisan_id = a.artisan_id
                        WHERE a.tier = 2 AND p.product_qty <= ?";
        $stmt = $this->prepare_statement($lowStockSql);
        mysqli_stmt_bind_param($stmt, 'i', $threshold);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $lowStock = $result ? mysqli_fetch_assoc($result) : ['low_stock_count' => 0];
        mysqli_stmt_close($stmt);

        return array_merge($summary, [
            'low_stock_count' => (int)($lowStock['low_stock_count'] ?? 0),
        ]);
    }

    public function get_top_tier2_artisans($limit = 5) {
        $limit = max(1, min((int)$limit, 20));
        $sql = "SELECT 
                    a.artisan_id,
                    a.business_name,
                    COALESCE(SUM(od.qty * p.product_price), 0) AS total_sales,
                    COALESCE(SUM(od.qty), 0) AS units_sold,
                    COALESCE(SUM(p.product_qty), 0) AS stock_on_hand
                FROM artisans a
                LEFT JOIN products p ON p.artisan_id = a.artisan_id
                LEFT JOIN orderdetails od ON od.product_id = p.product_id
                WHERE a.tier = 2
                GROUP BY a.artisan_id, a.business_name
                ORDER BY total_sales DESC, a.business_name ASC
                LIMIT $limit";

        return $this->db_fetch_all($sql) ?? [];
    }

    public function get_tier2_low_stock_products($threshold = 10, $limit = 10) {
        $threshold = max(0, (int)$threshold);
        $limit = max(1, min((int)$limit, 25));

        $sql = "SELECT 
                    p.product_id,
                    p.product_title,
                    p.product_qty,
                    a.business_name
                FROM products p
                INNER JOIN artisans a ON p.artisan_id = a.artisan_id
                WHERE a.tier = 2 AND p.product_qty <= ?
                ORDER BY p.product_qty ASC, p.product_title ASC
                LIMIT $limit";

        $stmt = $this->prepare_statement($sql);
        mysqli_stmt_bind_param($stmt, 'i', $threshold);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($stmt);

        return $rows;
    }

    public function get_tier2_monthly_sales($months = 6) {
        $months = max(1, min((int)$months, 12));
        $sql = "SELECT 
                    DATE_FORMAT(o.order_date, '%Y-%m') AS month_label,
                    COALESCE(SUM(od.qty * p.product_price), 0) AS revenue,
                    COALESCE(SUM(od.qty), 0) AS units
                FROM orders o
                JOIN orderdetails od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                JOIN artisans a ON p.artisan_id = a.artisan_id
                WHERE a.tier = 2
                  AND o.order_date >= DATE_SUB(CURDATE(), INTERVAL $months MONTH)
                GROUP BY DATE_FORMAT(o.order_date, '%Y-%m')
                ORDER BY DATE_FORMAT(o.order_date, '%Y-%m') ASC";

        return $this->db_fetch_all($sql) ?? [];
    }

    public function get_recent_tier2_orders($limit = 5) {
        $limit = max(1, min((int)$limit, 20));
        $sql = "SELECT 
                    o.order_id,
                    o.invoice_no,
                    o.order_date,
                    o.order_status,
                    COALESCE(SUM(od.qty * p.product_price), 0) AS total_amount,
                    COALESCE(SUM(od.qty), 0) AS total_units
                FROM orders o
                JOIN orderdetails od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                JOIN artisans a ON p.artisan_id = a.artisan_id
                WHERE a.tier = 2
                GROUP BY o.order_id, o.invoice_no, o.order_date, o.order_status
                ORDER BY o.order_date DESC
                LIMIT $limit";

        return $this->db_fetch_all($sql) ?? [];
    }

    public function get_tier2_artisan_overview() {
        $sql = "SELECT 
                    a.artisan_id,
                    a.business_name,
                    a.commission_rate,
                    c.customer_name,
                    c.customer_email,
                    c.customer_contact,
                    COUNT(DISTINCT p.product_id) AS product_count,
                    COALESCE(SUM(p.product_qty), 0) AS stock_units,
                    COALESCE(SUM(p.product_qty * p.product_price), 0) AS stock_value,
                    COALESCE(SUM(od.qty), 0) AS units_sold,
                    COALESCE(SUM(od.qty * p.product_price), 0) AS revenue
                FROM artisans a
                JOIN customer c ON a.customer_id = c.customer_id
                LEFT JOIN products p ON p.artisan_id = a.artisan_id
                LEFT JOIN orderdetails od ON od.product_id = p.product_id
                WHERE a.tier = 2
                GROUP BY 
                    a.artisan_id,
                    a.business_name,
                    a.commission_rate,
                    c.customer_name,
                    c.customer_email,
                    c.customer_contact
                ORDER BY revenue DESC, a.business_name ASC";

        return $this->db_fetch_all($sql) ?? [];
    }
}
?>

