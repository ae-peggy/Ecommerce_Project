<?php
require_once dirname(__FILE__) . '/../settings/db_class.php';

class artisan_class extends db_connection {
    
    // Create new artisan
    public function create_artisan($customer_id, $business_name, $tier = 1, $commission_rate = 15.00) {
        $sql = "INSERT INTO artisans (customer_id, business_name, tier, commission_rate, approval_status) 
                VALUES (?, ?, ?, ?, 'approved')";
        $stmt = $this->prepare_statement($sql);
        $customer_id = (int)$customer_id;
        $tier = (int)$tier;
        $commission_rate = (float)$commission_rate;
        mysqli_stmt_bind_param($stmt, 'isid', $customer_id, $business_name, $tier, $commission_rate);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // Get artisan by customer ID
    public function get_artisan_by_customer($customer_id) {
        $sql = "SELECT a.*, c.customer_name, c.customer_email, c.customer_contact, c.customer_country, c.customer_city
                FROM artisans a
                JOIN customer c ON a.customer_id = c.customer_id
                WHERE a.customer_id = ?";
        $stmt = $this->prepare_statement($sql);
        $customer_id = (int)$customer_id;
        mysqli_stmt_bind_param($stmt, 'i', $customer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        return $data;
    }
    
    // Get artisan by ID
    public function get_artisan_by_id($artisan_id) {
        $sql = "SELECT a.*, c.customer_name, c.customer_email, c.customer_contact
                FROM artisans a
                JOIN customer c ON a.customer_id = c.customer_id
                WHERE a.artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        return $data;
    }
    
    // Get all artisans
    public function get_all_artisans() {
        $sql = "SELECT a.*, c.customer_name, c.customer_email, c.customer_contact,
                (SELECT COUNT(*) FROM products WHERE artisan_id = a.artisan_id) as product_count
                FROM artisans a
                JOIN customer c ON a.customer_id = c.customer_id
                ORDER BY a.created_date DESC";
        return $this->db_fetch_all($sql);
    }
    
    // Get approved artisans only
    public function get_approved_artisans() {
        $sql = "SELECT a.*, c.customer_name, c.customer_email
                FROM artisans a
                JOIN customer c ON a.customer_id = c.customer_id
                WHERE a.approval_status = 'approved'
                ORDER BY a.business_name ASC";
        return $this->db_fetch_all($sql);
    }
    
    // Update artisan business info
    public function update_artisan_business($artisan_id, $business_name, $business_desc, $business_phone, $business_address) {
        $sql = "UPDATE artisans SET 
                business_name = ?,
                business_desc = ?,
                business_phone = ?,
                business_address = ?
                WHERE artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'ssssi', $business_name, $business_desc, $business_phone, $business_address, $artisan_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // Update artisan approval status
    public function update_approval_status($artisan_id, $status) {
        $sql = "UPDATE artisans SET approval_status = ? WHERE artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'si', $status, $artisan_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // Update artisan details (business_name, tier, commission_rate, approval_status)
    public function update_artisan_details($artisan_id, $business_name, $tier, $commission_rate, $approval_status = null) {
        $artisan_id = (int)$artisan_id;
        $tier = (int)$tier;
        $commission_rate = (float)$commission_rate;
        
        $sql = "UPDATE artisans SET 
                business_name = ?,
                tier = ?,
                commission_rate = ?";
        
        if ($approval_status !== null && !empty($approval_status)) {
            $sql .= ", approval_status = ?";
        }
        
        $sql .= " WHERE artisan_id = ?";
        
        $stmt = $this->prepare_statement($sql);
        
        if ($approval_status !== null && !empty($approval_status)) {
            mysqli_stmt_bind_param($stmt, 'sidsi', $business_name, $tier, $commission_rate, $approval_status, $artisan_id);
        } else {
            mysqli_stmt_bind_param($stmt, 'sidi', $business_name, $tier, $commission_rate, $artisan_id);
        }
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // Delete artisan
    public function delete_artisan($artisan_id) {
        $artisan_id = (int)$artisan_id;

        $delete_products = "DELETE FROM products WHERE artisan_id = ?";
        $stmtProducts = $this->prepare_statement($delete_products);
        mysqli_stmt_bind_param($stmtProducts, 'i', $artisan_id);
        mysqli_stmt_execute($stmtProducts);
        mysqli_stmt_close($stmtProducts);
        
        $sql = "DELETE FROM artisans WHERE artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // Get artisan product count
    public function get_product_count($artisan_id) {
        $sql = "SELECT COUNT(*) as count FROM products WHERE artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        return $data['count'] ?? 0;
    }
    
    // Get artisan pending orders
    public function get_pending_orders($artisan_id) {
        $sql = "SELECT COUNT(DISTINCT o.order_id) as count 
                FROM orders o
                JOIN orderdetails od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                WHERE p.artisan_id = ?
                AND LOWER(o.order_status) = 'pending'";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        return $data['count'] ?? 0;
    }
    
    // Get artisan total sales
    public function get_total_sales($artisan_id) {
        $sql = "SELECT SUM(od.qty * p.product_price) as total
                FROM orderdetails od
                JOIN products p ON od.product_id = p.product_id
                WHERE p.artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        return $data['total'] ?? 0;
    }
    
    // Get artisan products
    public function get_artisan_products($artisan_id, $limit = null) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE p.artisan_id = ?
                ORDER BY p.product_id DESC";
        $artisan_id = (int)$artisan_id;
        $stmt = null;
        
        if ($limit !== null) {
            $limit = (int)$limit;
            $sql .= " LIMIT ?";
            $stmt = $this->prepare_statement($sql);
            mysqli_stmt_bind_param($stmt, 'ii', $artisan_id, $limit);
        } else {
            $stmt = $this->prepare_statement($sql);
            mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($stmt);
        return $data;
    }
    
    // Add product for artisan
    public function add_product($artisan_id, $cat, $brand, $title, $price, $desc, $image, $keywords, $qty, $created_by = null) {
        // If created_by not provided, get customer_id from artisan_id
        if ($created_by === null) {
            $artisan_data = $this->get_artisan_by_id($artisan_id);
            $created_by = $artisan_data ? (int)$artisan_data['customer_id'] : 0;
        }
        
        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, 
                product_desc, product_image, product_keywords, product_qty, artisan_id, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        $cat = (int)$cat;
        $brand = (int)$brand;
        $price = (float)$price;
        $qty = (int)$qty;
        $created_by = (int)$created_by;
        mysqli_stmt_bind_param(
            $stmt,
            'iisdsssiii',
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image,
            $keywords,
            $qty,
            $artisan_id,
            $created_by
        );
        $result = mysqli_stmt_execute($stmt);
        
        if ($result) {
            $product_id = mysqli_insert_id($this->db_conn());
            mysqli_stmt_close($stmt);
            return $product_id;
        } else {
            mysqli_stmt_close($stmt);
            return false;
        }
    }
    
    // Update product (only if owned by artisan)
    public function update_product($product_id, $artisan_id, $cat, $brand, $title, $price, $desc, $keywords, $qty) {
        $sql = "UPDATE products SET 
                product_cat = ?,
                product_brand = ?,
                product_title = ?,
                product_price = ?,
                product_desc = ?,
                product_keywords = ?,
                product_qty = ?
                WHERE product_id = ? AND artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $product_id = (int)$product_id;
        $artisan_id = (int)$artisan_id;
        $cat = (int)$cat;
        $brand = (int)$brand;
        $price = (float)$price;
        $qty = (int)$qty;
        mysqli_stmt_bind_param(
            $stmt,
            'iisdssiii',
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $keywords,
            $qty,
            $product_id,
            $artisan_id
        );
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // Delete product (only if owned by artisan)
    public function delete_product($product_id, $artisan_id) {
        $sql = "DELETE FROM products WHERE product_id = ? AND artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $product_id = (int)$product_id;
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'ii', $product_id, $artisan_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    
    // Check if artisan owns product
    public function verify_product_ownership($product_id, $artisan_id) {
        $sql = "SELECT product_id FROM products WHERE product_id = ? AND artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $product_id = (int)$product_id;
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'ii', $product_id, $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        return !empty($data);
    }

    public function get_orders_for_artisan($artisan_id) {
        $sql = "SELECT DISTINCT 
                    o.order_id,
                    o.order_date,
                    o.order_status,
                    c.customer_name,
                    c.customer_email,
                    c.customer_contact,
                    GROUP_CONCAT(p.product_title SEPARATOR ', ') as products,
                    SUM(od.qty) as total_qty,
                    SUM(od.qty * p.product_price) as total_amount
                FROM orders o
                JOIN orderdetails od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                JOIN customer c ON o.customer_id = c.customer_id
                WHERE p.artisan_id = ?
                GROUP BY o.order_id
                ORDER BY o.order_date DESC";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function get_monthly_sales_data($artisan_id, $days = 30) {
        $sql = "SELECT 
                    DATE(o.order_date) as date,
                    SUM(od.qty * p.product_price) as amount
                FROM orders o
                JOIN orderdetails od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                WHERE p.artisan_id = ?
                AND o.order_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(o.order_date)
                ORDER BY date ASC";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        $days = (int)$days;
        mysqli_stmt_bind_param($stmt, 'ii', $artisan_id, $days);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function get_top_products_for_artisan($artisan_id, $limit = 5) {
        $sql = "SELECT 
                    p.product_id,
                    p.product_title,
                    p.product_image,
                    SUM(od.qty) as total_sold,
                    SUM(od.qty * p.product_price) as revenue
                FROM products p
                JOIN orderdetails od ON p.product_id = od.product_id
                WHERE p.artisan_id = ?
                GROUP BY p.product_id
                ORDER BY total_sold DESC
                LIMIT ?";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        $limit = (int)$limit;
        mysqli_stmt_bind_param($stmt, 'ii', $artisan_id, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function get_sales_by_category($artisan_id) {
        $sql = "SELECT 
                    c.cat_name,
                    SUM(od.qty * p.product_price) as total
                FROM products p
                JOIN categories c ON p.product_cat = c.cat_id
                JOIN orderdetails od ON p.product_id = od.product_id
                WHERE p.artisan_id = ?
                GROUP BY c.cat_id
                ORDER BY total DESC";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function get_revenue_summary($artisan_id) {
        $artisan_id = (int)$artisan_id;

        $baseSql = "SELECT 
                        SUM(od.qty * p.product_price) as total_revenue,
                        COUNT(DISTINCT o.order_id) as total_orders,
                        SUM(od.qty) as products_sold,
                        SUM(CASE WHEN LOWER(o.order_status) = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                        SUM(CASE WHEN LOWER(o.order_status) = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                        SUM(CASE WHEN LOWER(o.order_status) = 'processing' THEN 1 ELSE 0 END) as processing_orders
                    FROM orders o
                    JOIN orderdetails od ON o.order_id = od.order_id
                    JOIN products p ON od.product_id = p.product_id
                    WHERE p.artisan_id = ?";
        $stmt = $this->prepare_statement($baseSql);
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $totals = $result ? mysqli_fetch_assoc($result) : [];
        mysqli_stmt_close($stmt);

        $currentSql = "SELECT SUM(od.qty * p.product_price) as revenue
                       FROM orders o
                       JOIN orderdetails od ON o.order_id = od.order_id
                       JOIN products p ON od.product_id = p.product_id
                       WHERE p.artisan_id = ?
                       AND o.order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmtCurrent = $this->prepare_statement($currentSql);
        mysqli_stmt_bind_param($stmtCurrent, 'i', $artisan_id);
        mysqli_stmt_execute($stmtCurrent);
        $currentResult = mysqli_stmt_get_result($stmtCurrent);
        $currentPeriod = $currentResult ? mysqli_fetch_assoc($currentResult) : ['revenue' => 0];
        mysqli_stmt_close($stmtCurrent);

        $previousSql = "SELECT SUM(od.qty * p.product_price) as revenue
                        FROM orders o
                        JOIN orderdetails od ON o.order_id = od.order_id
                        JOIN products p ON od.product_id = p.product_id
                        WHERE p.artisan_id = ?
                        AND o.order_date >= DATE_SUB(CURDATE(), INTERVAL 60 DAY)
                        AND o.order_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmtPrevious = $this->prepare_statement($previousSql);
        mysqli_stmt_bind_param($stmtPrevious, 'i', $artisan_id);
        mysqli_stmt_execute($stmtPrevious);
        $previousResult = mysqli_stmt_get_result($stmtPrevious);
        $previousPeriod = $previousResult ? mysqli_fetch_assoc($previousResult) : ['revenue' => 0];
        mysqli_stmt_close($stmtPrevious);

        $totalRevenue = (float)($totals['total_revenue'] ?? 0);
        $totalOrders = (int)($totals['total_orders'] ?? 0);
        $productsSold = (int)($totals['products_sold'] ?? 0);
        $completedOrders = (int)($totals['completed_orders'] ?? 0);
        $pendingOrders = (int)($totals['pending_orders'] ?? 0);
        $processingOrders = (int)($totals['processing_orders'] ?? 0);

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $growth = 0;
        $previousRevenue = (float)($previousPeriod['revenue'] ?? 0);
        $currentRevenue = (float)($currentPeriod['revenue'] ?? 0);

        if ($previousRevenue > 0) {
            $growth = (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
        } elseif ($currentRevenue > 0) {
            $growth = 100;
        }

        $conversionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
        $satisfaction = $totalOrders > 0 ? round((($completedOrders + $processingOrders) / $totalOrders) * 100) : 0;
        $productCount = max(1, $this->get_product_count($artisan_id));
        $turnoverRate = round($productsSold / $productCount, 1);
        $avgRating = $satisfaction > 0 ? round(($satisfaction / 20), 1) : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'products_sold' => $productsSold,
            'avg_order_value' => $avgOrderValue,
            'growth' => round($growth, 1),
            'conversion_rate' => $conversionRate,
            'avg_rating' => min(5, $avgRating),
            'turnover_rate' => $turnoverRate,
            'satisfaction' => min(100, $satisfaction),
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders,
            'completed_orders' => $completedOrders
        ];
    }
    
    // Get artisan about page data
    public function get_artisan_about($artisan_id) {
        $sql = "SELECT artisan_id, artisan_bio, cultural_meaning, crafting_method, 
                artisan_location, artisan_photos, business_name, tier
                FROM artisans 
                WHERE artisan_id = ? AND approval_status = 'approved'";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        mysqli_stmt_bind_param($stmt, 'i', $artisan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
        
        // Parse JSON photos if exists
        if ($data && !empty($data['artisan_photos'])) {
            $photos = json_decode($data['artisan_photos'], true);
            $data['artisan_photos'] = is_array($photos) ? $photos : [];
        } else if ($data) {
            $data['artisan_photos'] = [];
        }
        
        return $data;
    }
    
    // Update artisan about page data
    public function update_artisan_about($artisan_id, $artisan_bio, $cultural_meaning, $crafting_method, $artisan_location, $artisan_photos = []) {
        $sql = "UPDATE artisans SET 
                artisan_bio = ?,
                cultural_meaning = ?,
                crafting_method = ?,
                artisan_location = ?,
                artisan_photos = ?
                WHERE artisan_id = ?";
        $stmt = $this->prepare_statement($sql);
        $artisan_id = (int)$artisan_id;
        
        // Convert photos array to JSON
        $photos_json = !empty($artisan_photos) ? json_encode($artisan_photos) : null;
        
        mysqli_stmt_bind_param($stmt, 'sssssi', 
            $artisan_bio, 
            $cultural_meaning, 
            $crafting_method, 
            $artisan_location, 
            $photos_json, 
            $artisan_id
        );
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
}
?>