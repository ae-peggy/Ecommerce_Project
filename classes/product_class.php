<?php
// Include the database connection file
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Product Class - handles all product-related database operations
 */
class product_class extends db_connection {
    
    /**
     * Add a new product to the database
     */
    public function add_product($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by) {
        error_log("=== ADD_PRODUCT METHOD CALLED ===");
        try {
            $conn = $this->db_conn();
            
            if (!$conn) {
                error_log("Failed to obtain database connection when adding product");
                return false;
            }
            
            $cat_id = (int)$cat_id;
            $brand_id = (int)$brand_id;
            $price = (float)$price;
            $created_by = (int)$created_by;
            $title = trim($title);
            $desc = trim($desc);
            $image = trim($image);
            $keywords = trim($keywords);
            
            $sql = "INSERT INTO products (
                        product_cat,
                        product_brand,
                        product_title,
                        product_price,
                        product_desc,
                        product_image,
                        product_keywords,
                        created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt === false) {
                error_log("Failed to prepare add_product statement: " . mysqli_error($conn));
                return false;
            }
            
            mysqli_stmt_bind_param(
                $stmt,
                'iisdsssi',
                $cat_id,
                $brand_id,
                $title,
                $price,
                $desc,
                $image,
                $keywords,
                $created_by
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                error_log("Failed to execute add_product statement: " . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
                return false;
            }
            
            $product_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            if ($product_id > 0) {
                error_log("Product added successfully with ID: $product_id");
                return $product_id;
            }
            
            error_log("Add product succeeded but returned invalid insert ID");
            return false;
            
        } catch (Exception $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all products created by a specific user
     */
    public function get_products_by_user($created_by) {
        try {
            $created_by = (int)$created_by;
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.created_by = $created_by 
                    ORDER BY p.created_date DESC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting products by user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all products (for customer view)
     */
    public function get_all_products(array $filters = []) {
        return $this->get_products_with_filters($filters);
    }
    
    /**
     * Advanced product filtering with optional search, price range, sorting, and best-seller support
     */
    public function get_products_with_filters(array $filters = []) {
        try {
            $conn = $this->db_conn();
            
            if (!$conn) {
                return false;
            }
            
            $sql = "SELECT 
                        p.*,
                        c.cat_name,
                        b.brand_name,
                        a.artisan_id,
                        a.business_name AS artisan_name,
                        COALESCE(od.total_qty, 0) AS sold_qty
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    LEFT JOIN artisans a ON p.artisan_id = a.artisan_id
                    LEFT JOIN (
                        SELECT product_id, SUM(qty) AS total_qty
                        FROM orderdetails
                        GROUP BY product_id
                    ) od ON od.product_id = p.product_id";
            
            $conditions = [];
            $types = '';
            $params = [];
            
            if (!empty($filters['search'])) {
                $searchTerm = '%' . trim($filters['search']) . '%';
                $conditions[] = "(p.product_title LIKE ? OR p.product_keywords LIKE ? OR p.product_desc LIKE ?)";
                $types .= 'sss';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($filters['category'])) {
                $conditions[] = "p.product_cat = ?";
                $types .= 'i';
                $category = (int)$filters['category'];
                $params[] = $category;
            }
            
            if (!empty($filters['brand'])) {
                $conditions[] = "p.product_brand = ?";
                $types .= 'i';
                $brand = (int)$filters['brand'];
                $params[] = $brand;
            }
            
            if (!empty($filters['artisan'])) {
                $conditions[] = "p.artisan_id = ?";
                $types .= 'i';
                $artisan = (int)$filters['artisan'];
                $params[] = $artisan;
            }
            
            if (isset($filters['min_price']) && $filters['min_price'] !== '' && $filters['min_price'] !== null) {
                $conditions[] = "p.product_price >= ?";
                $types .= 'd';
                $params[] = (float)$filters['min_price'];
            }
            
            if (isset($filters['max_price']) && $filters['max_price'] !== '' && $filters['max_price'] !== null) {
                $conditions[] = "p.product_price <= ?";
                $types .= 'd';
                $params[] = (float)$filters['max_price'];
            }
            
            if (!empty($conditions)) {
                $sql .= ' WHERE ' . implode(' AND ', $conditions);
            }
            
            $sort = $filters['sort'] ?? 'newest';
            switch ($sort) {
                case 'price_asc':
                    $sql .= ' ORDER BY p.product_price ASC, p.product_title ASC';
                    break;
                case 'price_desc':
                    $sql .= ' ORDER BY p.product_price DESC, p.product_title ASC';
                    break;
                case 'best_sellers':
                    $sql .= ' ORDER BY sold_qty DESC, p.product_title ASC';
                    break;
                case 'newest':
                default:
                    $sql .= ' ORDER BY p.created_date DESC, p.product_title ASC';
                    break;
            }
            
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt === false) {
                error_log("Failed to prepare product filter statement: " . mysqli_error($conn));
                return false;
            }
            
            if (!empty($params)) {
                $bindParams = [];
                $bindParams[] = &$stmt;
                $bindParams[] = &$types;
                foreach ($params as $key => $value) {
                    $bindParams[] = &$params[$key];
                }
                call_user_func_array('mysqli_stmt_bind_param', $bindParams);
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                error_log("Failed to execute product filter statement: " . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
                return false;
            }
            
            $result = mysqli_stmt_get_result($stmt);
            $products = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
            mysqli_stmt_close($stmt);
            
            return $products;
            
        } catch (Exception $e) {
            error_log("Error getting products with filters: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single product by ID
     */
    public function get_product_by_id($product_id) {
        try {
            $product_id = (int)$product_id;
            $sql = "SELECT p.*, c.cat_name, b.brand_name, a.artisan_id, a.business_name as artisan_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    LEFT JOIN artisans a ON p.artisan_id = a.artisan_id
                    WHERE p.product_id = $product_id";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting product by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single product by ID with user check (for edit/delete security)
     */
    public function get_product_by_id_and_user($product_id, $created_by) {
        try {
            $product_id = (int)$product_id;
            $created_by = (int)$created_by;
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_id = $product_id AND p.created_by = $created_by";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting product by ID and user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update product information
     */
    public function update_product($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by) {
        try {
            $product_id = (int)$product_id;
            $cat_id = (int)$cat_id;
            $brand_id = (int)$brand_id;
            $title = mysqli_real_escape_string($this->db_conn(), $title);
            $price = (float)$price;
            $desc = mysqli_real_escape_string($this->db_conn(), $desc);
            $keywords = mysqli_real_escape_string($this->db_conn(), $keywords);
            $created_by = (int)$created_by;
            
            // Handle image update - only update if new image provided
            if (!empty($image)) {
                $image = mysqli_real_escape_string($this->db_conn(), $image);
                $sql = "UPDATE products SET 
                        product_cat = $cat_id,
                        product_brand = $brand_id,
                        product_title = '$title',
                        product_price = $price,
                        product_desc = '$desc',
                        product_image = '$image',
                        product_keywords = '$keywords'
                        WHERE product_id = $product_id AND created_by = $created_by";
            } else {
                // Don't update image if not provided
                $sql = "UPDATE products SET 
                        product_cat = $cat_id,
                        product_brand = $brand_id,
                        product_title = '$title',
                        product_price = $price,
                        product_desc = '$desc',
                        product_keywords = '$keywords'
                        WHERE product_id = $product_id AND created_by = $created_by";
            }
            
            error_log("Executing update SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a product
     */
    public function delete_product($product_id, $created_by) {
        try {
            $product_id = (int)$product_id;
            $created_by = (int)$created_by;
            
            $sql = "DELETE FROM products WHERE product_id = $product_id AND created_by = $created_by";
            
            error_log("Executing delete SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search products by title or keywords
     */
    public function search_products($query) {
        return $this->get_products_with_filters(['search' => $query]);
    }
    
    /**
     * Filter products by category
     */
    public function filter_products_by_category($cat_id) {
        return $this->get_products_with_filters(['category' => (int)$cat_id]);
     }
    
    /**
     * Filter products by brand
     */
    public function filter_products_by_brand($brand_id) {
        return $this->get_products_with_filters(['brand' => (int)$brand_id]);
     }
    
    /**
     * Filter products by artisan ID
     */
    public function filter_products_by_artisan($artisan_id) {
        return $this->get_products_with_filters(['artisan' => (int)$artisan_id]);
     }
    
    /**
     * Get product count for a user
     */
    public function get_product_count($created_by) {
        try {
            $created_by = (int)$created_by;
            $sql = "SELECT COUNT(*) as count FROM products WHERE created_by = $created_by";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['count'] : 0;
        } catch (Exception $e) {
            error_log("Error getting product count: " . $e->getMessage());
            return 0;
        }
    }

    public function get_all_categories() {
        try {
            $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return false;
        }
    }

    public function get_all_brands() {
        try {
            $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting brands: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reduce product stock quantity
     * @param int $product_id - Product ID
     * @param int $quantity - Quantity to reduce
     * @return bool - Returns true if successful, false if failed
     */
    public function reduce_stock($product_id, $quantity) {
        try {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($quantity <= 0) {
                return false;
            }
            
            $sql = "UPDATE products SET product_qty = product_qty - $quantity 
                    WHERE product_id = $product_id AND product_qty >= $quantity";
            
            error_log("Reducing stock - Product: $product_id, Qty: $quantity");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error reducing stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if product has sufficient stock
     * @param int $product_id - Product ID
     * @param int $quantity - Required quantity
     * @return bool - Returns true if stock is available, false otherwise
     */
    public function check_stock_availability($product_id, $quantity) {
        try {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            $sql = "SELECT product_qty FROM products WHERE product_id = $product_id";
            $result = $this->db_fetch_one($sql);
            
            if (!$result) {
                return false;
            }
            
            $available_stock = (int)$result['product_qty'];
            return $available_stock >= $quantity;
            
        } catch (Exception $e) {
            error_log("Error checking stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get product stock quantity
     * @param int $product_id - Product ID
     * @return int - Returns stock quantity or 0 if not found
     */
    public function get_product_stock($product_id) {
        try {
            $product_id = (int)$product_id;
            $sql = "SELECT product_qty FROM products WHERE product_id = $product_id";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['product_qty'] : 0;
        } catch (Exception $e) {
            error_log("Error getting product stock: " . $e->getMessage());
            return 0;
        }
    }
}

?>