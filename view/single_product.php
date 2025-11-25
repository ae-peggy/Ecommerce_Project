<?php
// Include core session management functions
require_once '../settings/core.php';

// Include product controller
require_once '../controllers/product_controller.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: all_product.php');
    exit();
}

// Get product details
$product = get_product_by_id_ctr($product_id);

if (!$product) {
    header('Location: all_product.php');
    exit();
}

// Get artisan about data if product has an artisan
$artisan_about = null;
if (!empty($product['artisan_id'])) {
    require_once '../classes/artisan_class.php';
    $artisan = new artisan_class();
    $artisan_about = $artisan->get_artisan_about($product['artisan_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Aya Crafts</title>
    <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
  @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #ffffff;
        line-height: 1.6;
        position: relative;
        overflow-x: hidden;
    }

    /* Animated gradient background */
    body::before {
        content: '';
        position: fixed;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: 
            radial-gradient(circle at 20% 50%, rgba(220, 38, 38, 0.04) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(239, 68, 68, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 40% 20%, rgba(185, 28, 28, 0.03) 0%, transparent 50%);
        animation: drift 20s ease-in-out infinite;
        z-index: -1;
    }

    @keyframes drift {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(1deg); }
        66% { transform: translate(-20px, 20px) rotate(-1deg); }
    }

    /* Floating geometric shapes */
    .geometric-shape {
        position: fixed;
        opacity: 0.3;
        z-index: -1;
        animation: float 25s ease-in-out infinite;
        pointer-events: none;
    }

    .shape-1 {
        top: 15%;
        right: 10%;
        width: 180px;
        height: 180px;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        animation-delay: 0s;
    }

    .shape-2 {
        bottom: 20%;
        left: 8%;
        width: 160px;
        height: 160px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-radius: 63% 37% 54% 46% / 55% 48% 52% 45%;
        animation-delay: -8s;
    }

    .shape-3 {
        top: 50%;
        left: 50%;
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
        border-radius: 41% 59% 51% 49% / 38% 45% 55% 62%;
        transform: translate(-50%, -50%);
        animation-delay: -15s;
    }

    @keyframes float {
        0%, 100% { 
            transform: translate(0, 0) rotate(0deg) scale(1);
        }
        25% { 
            transform: translate(30px, -30px) rotate(5deg) scale(1.05);
        }
        50% { 
            transform: translate(-20px, 20px) rotate(-5deg) scale(0.95);
        }
        75% { 
            transform: translate(20px, 30px) rotate(3deg) scale(1.02);
        }
    }

    /* Floating particles */
    .particles {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: -1;
        overflow: hidden;
        pointer-events: none;
    }

    .particle {
        position: absolute;
        width: 3px;
        height: 3px;
        background: #dc2626;
        border-radius: 50%;
        opacity: 0.2;
        animation: rise 20s infinite ease-in;
    }

    .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
    .particle:nth-child(2) { left: 25%; animation-delay: 3s; }
    .particle:nth-child(3) { left: 40%; animation-delay: 6s; }
    .particle:nth-child(4) { left: 55%; animation-delay: 9s; }
    .particle:nth-child(5) { left: 70%; animation-delay: 12s; }
    .particle:nth-child(6) { left: 85%; animation-delay: 2s; }

    @keyframes rise {
        0% {
            bottom: -10%;
            transform: translateX(0) scale(1);
            opacity: 0;
        }
        10% {
            opacity: 0.15;
        }
        90% {
            opacity: 0.15;
        }
        100% {
            bottom: 110%;
            transform: translateX(30px) scale(0.5);
            opacity: 0;
        }
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        padding: 80px 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 8px;
        background: linear-gradient(90deg, 
            #dc2626 0%, #dc2626 8%,
            #991b1b 8%, #991b1b 16%,
            #ef4444 16%, #ef4444 24%,
            #dc2626 24%, #dc2626 32%,
            #b91c1c 32%, #b91c1c 40%,
            #dc2626 40%, #dc2626 48%,
            #991b1b 48%, #991b1b 56%,
            #ef4444 56%, #ef4444 64%,
            #dc2626 64%, #dc2626 72%,
            #b91c1c 72%, #b91c1c 80%,
            #dc2626 80%, #dc2626 88%,
            #991b1b 88%, #991b1b 96%,
            #ef4444 96%, #ef4444 100%
        );
        z-index: 2;
        box-shadow: 0 2px 10px rgba(220, 38, 38, 0.15);
    }

    .hero h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 3.5rem;
        margin-bottom: 15px;
        font-weight: 500;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradientShift 4s ease infinite;
        letter-spacing: -1px;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% center; }
        50% { background-position: 100% center; }
    }

    .hero p {
        font-size: 1.2rem;
        color: #6b7280;
        opacity: 0.95;
    }

    /* Search Header for search results page */
    .search-header {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        padding: 60px 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    }

    .search-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, 
            #dc2626 0%, #dc2626 15%,
            #991b1b 15%, #991b1b 30%,
            #ef4444 30%, #ef4444 45%,
            #dc2626 45%, #dc2626 60%,
            #b91c1c 60%, #b91c1c 75%,
            #ef4444 75%, #ef4444 100%
        );
    }

    .search-header h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 3rem;
        margin-bottom: 15px;
        font-weight: 500;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .search-header p {
        font-size: 1.1rem;
        color: #6b7280;
    }

    /* Breadcrumb */
    .breadcrumb {
        max-width: 1400px;
        margin: 30px auto;
        padding: 0 40px;
        color: #6b7280;
        font-size: 14px;
    }

    .breadcrumb a {
        color: #6b7280;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb a:hover {
        color: #dc2626;
    }

    /* Filters Section */
    .filters-section {
        max-width: 1400px;
        margin: 30px auto;
        padding: 0 40px;
    }

    .filters-bar {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        padding: 25px 30px;
        border-radius: 16px;
        box-shadow: 
            0 10px 40px rgba(0, 0, 0, 0.06),
            0 0 0 1px rgba(220, 38, 38, 0.05);
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
    }

    .filters-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, 
            #dc2626 0%, #ef4444 50%, #dc2626 100%
        );
        background-size: 200% auto;
        animation: gradientShift 4s ease infinite;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-group label {
        font-weight: 600;
        color: #374151;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    .filter-group select,
    .search-box input {
        padding: 12px 16px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        min-width: 180px;
        transition: all 0.3s ease;
        background: white;
    }

    .filter-group select:focus,
    .search-box input:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.08);
        transform: translateY(-1px);
    }

    .search-box {
        flex: 1;
        min-width: 250px;
    }

    .search-box input {
        width: 100%;
    }

    .btn-filter,
    .btn-clear {
        padding: 12px 28px;
        border: none;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        letter-spacing: 0.3px;
    }

    .btn-filter::before,
    .btn-clear::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
    }

    .btn-filter:hover::before,
    .btn-clear:hover::before {
        left: 100%;
    }

    .btn-filter {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
    }

    .btn-clear {
        background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
        color: white;
        box-shadow: 0 6px 20px rgba(107, 114, 128, 0.2);
    }

    .btn-clear:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(107, 114, 128, 0.3);
    }

    /* Active Filters */
    .active-filters {
        max-width: 1400px;
        margin: 0 auto 30px;
        padding: 0 40px;
    }

    .active-filters-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-tag {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        border: 1.5px solid rgba(220, 38, 38, 0.2);
        box-shadow: 0 2px 10px rgba(220, 38, 38, 0.1);
        transition: all 0.3s ease;
    }

    .filter-tag:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.15);
    }

    .filter-tag button {
        background: none;
        border: none;
        color: #dc2626;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        line-height: 1;
        transition: transform 0.2s ease;
    }

    .filter-tag button:hover {
        transform: scale(1.2);
    }

    /* Products Section */
    .products-section,
    .results-section {
        max-width: 1400px;
        margin: 0 auto 60px;
        padding: 0 40px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
    }

    .section-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 2.2rem;
        color: #1a1a1a;
        font-weight: 500;
        letter-spacing: -0.5px;
    }

    .section-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #dc2626, #ef4444);
        margin-top: 10px;
        border-radius: 2px;
    }

    .product-count,
    .result-count {
        color: #6b7280;
        font-size: 1rem;
        font-weight: 500;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-card {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 
            0 10px 40px rgba(0, 0, 0, 0.06),
            0 0 0 1px rgba(220, 38, 38, 0.05);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
    }

    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, 
            #dc2626 0%, #ef4444 50%, #dc2626 100%
        );
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card:hover::before {
        opacity: 1;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 
            0 20px 60px rgba(0, 0, 0, 0.12),
            0 0 0 1px rgba(220, 38, 38, 0.1);
    }

    .product-image {
        width: 100%;
        height: 130px;
        object-fit: cover;
        background: #f3f4f6;
        transition: transform 0.4s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .product-info {
        padding: 24px;
    }

    .product-category {
        font-size: 11px;
        color: #dc2626;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 10px;
    }

    .product-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }

    .product-brand {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 15px;
    }

    .product-price {
        font-size: 1.6rem;
        font-weight: 700;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 18px;
    }

    .btn-add-cart {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 14px;
        letter-spacing: 0.3px;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
        position: relative;
        overflow: hidden;
    }

    .btn-add-cart::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
    }

    .btn-add-cart:hover::before {
        left: 100%;
    }

    .btn-add-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
    }

    /* Single Product Page */
    .product-section {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 40px;
    }

    .product-container {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        border-radius: 24px;
        box-shadow: 
            0 20px 60px rgba(0, 0, 0, 0.08),
            0 0 0 1px rgba(220, 38, 38, 0.05);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        padding: 60px;
        position: relative;
    }

    .product-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, 
            #dc2626 0%, #dc2626 15%,
            #991b1b 15%, #991b1b 30%,
            #ef4444 30%, #ef4444 45%,
            #dc2626 45%, #dc2626 60%,
            #b91c1c 60%, #b91c1c 75%,
            #ef4444 75%, #ef4444 100%
        );
    }

    .product-image-section {
        position: relative;
    }

    .product-image {
        width: 100%;
        height: 600px;
        object-fit: cover;
        border-radius: 16px;
        background: #f3f4f6;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .product-details-section {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .product-category {
        font-size: 12px;
        color: #dc2626;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 15px;
    }

    .product-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 3rem;
        color: #1a1a1a;
        line-height: 1.2;
        margin-bottom: -75px;
        font-weight: 500;
        letter-spacing: -1px;
    }

    .product-brand {
        font-size: 1.1rem;
        color: #6b7280;
        margin-bottom: 25px;
    }

    .product-price {
        font-size: 2.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 30px;
    }

    .product-description {
        color: #4b5563;
        font-size: 1.05rem;
        line-height: 1.8;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 2px solid #f3f4f6;
    }

    .product-meta {
        margin-bottom: 30px;
    }

    .meta-item {
        display: flex;
        margin-bottom: 12px;
        font-size: 15px;
    }

    .meta-label {
        font-weight: 600;
        color: #374151;
        min-width: 120px;
    }

    .meta-value {
        color: #6b7280;
    }

    .product-keywords {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 35px;
    }

    .keyword-tag {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid rgba(220, 38, 38, 0.2);
    }

    .product-actions {
        display: flex;
        gap: 15px;
    }

    .btn {
        padding: 10px 40px;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: inline-block;
        text-align: center;
        letter-spacing: 0.3px;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        flex: 1;
        height: 50%;
        box-shadow: 0 8px 15px rgba(220, 38, 38, 0.3);
        padding-top: 12px;
        margin-top: 31px;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(220, 38, 38, 0.4);
    }

    .btn-secondary {
        background: white;
        text-align: center;
        padding-top: 12px;
        margin-top: 31px;
        height: 50%;
        color: #374151;
        border: 2px solid #e5e7eb;
    }

    .btn-secondary:hover {
        border-color: #dc2626;
        color: #dc2626;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.15);
    }

    /* No Results/Products */
    .no-products,
    .no-results {
        text-align: center;
        padding: 100px 20px;
        color: #9ca3af;
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        border-radius: 20px;
        box-shadow: 
            0 10px 40px rgba(0, 0, 0, 0.06),
            0 0 0 1px rgba(220, 38, 38, 0.05);
        position: relative;
        overflow: hidden;
    }

    .no-products::before,
    .no-results::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, 
            #dc2626 0%, #ef4444 50%, #dc2626 100%
        );
    }

    .no-products-icon,
    .no-results-icon {
        font-size: 6rem;
        margin-bottom: 25px;
        opacity: 0.2;
        animation: float 3s ease-in-out infinite;
    }

    .no-products h3,
    .no-results h3 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 2rem;
        margin-bottom: 15px;
        color: #4b5563;
        font-weight: 500;
    }

    .no-products p,
    .no-results p {
        margin-bottom: 25px;
        font-size: 1.05rem;
        color: #6b7280;
    }

    .btn-browse {
        padding: 14px 36px;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
        letter-spacing: 0.3px;
        position: relative;
        overflow: hidden;
    }

    .btn-browse::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
    }

    .btn-browse:hover::before {
        left: 100%;
    }

    .btn-browse:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 50px;
        flex-wrap: wrap;
    }

    .page-btn {
        padding: 12px 20px;
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #374151;
        font-weight: 500;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .page-btn:hover {
        border-color: #dc2626;
        color: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.15);
    }

    .page-btn.active {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        border-color: #dc2626;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
    }

    /* Responsive Design */
    @media (max-width: 968px) {
        .product-container {
            grid-template-columns: 1fr;
            gap: 40px;
            padding: 40px 30px;
        }

        .product-image {
            height: 400px;
        }

        .product-title {
            font-size: 2.2rem;
        }

        .product-price {
            font-size: 2rem;
        }

        .product-actions {
            flex-direction: column;
        }

        .hero h1,
        .search-header h1 {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .nav-container {
            flex-direction: column;
            gap: 15px;
            padding: 0 20px;
        }

        .nav-links {
            flex-wrap: wrap;
            justify-content: center;
        }

        .filters-bar {
            flex-direction: column;
            align-items: stretch;
            padding: 20px;
        }

        .filter-group {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }
        
        /* Meet the Artisan responsive */
        .product-section .product-container[style*="grid-template-columns: 1fr"] {
            padding: 30px 20px !important;
        }
        
        .product-section .product-container[style*="grid-template-columns: 1fr"] > div[style*="grid-template-columns: 1fr 2fr"] {
            grid-template-columns: 1fr !important;
            gap: 30px !important;
        }

        .filter-group select,
        .search-box input {
            width: 100%;
        }

        .products-grid {
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .hero,
        .search-header {
            padding: 50px 25px;
        }

        .hero h1,
        .search-header h1 {
            font-size: 2rem;
        }

        .hero p,
        .search-header p {
            font-size: 1rem;
        }

        .section-title {
            font-size: 1.8rem;
        }

        .product-container {
            padding: 30px 20px;
        }

        .product-title {
            font-size: 1.8rem;
        }

        .no-products,
        .no-results {
            padding: 60px 20px;
        }

        .no-products-icon,
        .no-results-icon {
            font-size: 4rem;
        }
    }

    /* Loading Animation */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .loading {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Smooth Scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Selection Color */
    ::selection {
        background: rgba(220, 38, 38, 0.2);
        color: #dc2626;
    }

    ::-moz-selection {
        background: rgba(220, 38, 38, 0.2);
        color: #dc2626;
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 12px;
    }

    ::-webkit-scrollbar-track {
        background: #f3f4f6;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border-radius: 6px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
    }

    /* Focus Visible for Accessibility */
    *:focus-visible {
        outline: 2px solid #dc2626;
        outline-offset: 2px;
    }

    /* Meet the Artisan Section */
    .artisan-meet-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 40px;
        align-items: start;
    }

    @media (max-width: 968px) {
        .artisan-meet-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
    </style>
</head>
<body>
    <!-- Add these geometric shapes and particles -->
    <div class="geometric-shape shape-1"></div>
    <div class="geometric-shape shape-2"></div>
    <div class="geometric-shape shape-3"></div>
    
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-container">
                <a href="../index.php" class="logo-container" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                    <div class="logo-symbol">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <div class="logo">Aya Crafts</div>
            <span class="logo-subtitle">Authentic Artistry</span>
                    </div>
                </a>
            </div>
            <div class="nav-links">
                <a href="../index.php" class="nav-link secondary"><span>Home</span></a>
                <a href="all_product.php" class="nav-link secondary"><span>Shop</span></a>
                <a href="about_us.php" class="nav-link secondary"><span>About</span></a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a href="../admin/product.php" class="nav-link secondary"><span>Admin</span></a>
                    <?php endif; ?>
                    <a href="../view/cart.php" class="nav-link secondary" style="position: relative;">
                        <span>Cart</span>
                        <span class="cart-count-badge">0</span>
                </a>
                    <a href="../login/logout.php" class="nav-link"><span>Logout</span></a>
                <?php else: ?>
                    <a href="../login/login.php" class="nav-link secondary"><span>Login</span></a>
                    <a href="../login/register.php" class="nav-link"><span>Register</span></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="../index.php">Home</a> / 
        <a href="all_product.php">Shop</a> / 
        <span><?php echo htmlspecialchars($product['product_title']); ?></span>
    </div>

    <!-- Product Section -->
    <section class="product-section">
        <div class="product-container">
            <?php
                   // Properly construct image path
                    $imagePath = '';
                    if (!empty($product['product_image'])) {
                        // If the path already starts with '../', use it as is
                        if (strpos($product['product_image'], '../') === 0) {
                            $imagePath = $product['product_image'];
                        } 
                        // If it starts with 'uploads/', add '../' prefix
                        elseif (strpos($product['product_image'], 'uploads/') === 0) {
                            $imagePath = '../' . $product['product_image'];
                        }
                        // Otherwise use as-is (might be full URL)
                        else {
                            $imagePath = $product['product_image'];
                        }
                        
                        // Verify file exists
                        $fullPath = __DIR__ . '/' . $imagePath;
                        if (!file_exists($fullPath)) {
                            error_log("Image file not found: $fullPath");
                            $imagePath = ''; // Reset to empty if file doesn't exist
                        }
                    }
                    
                    // Use data URI placeholder for no image (prevents recursive loading)
                    if (empty($imagePath)) {
                        $displayImage = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="280" height="280"%3E%3Crect width="280" height="280" fill="%23fef2f2"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="%23dc2626"%3ENo Image%3C/text%3E%3C/svg%3E';
                    } else {
                        $displayImage = $imagePath;
                    }
                    ?>
            <!-- Product Image -->
            <div class="product-image-section">
                <img 
                    src="<?php echo htmlspecialchars($displayImage); ?>"
                    alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                    class="product-image"
                    <?php if (!empty($imagePath)): ?>
                    onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22280%22 height=%22280%22%3E%3Crect width=%22280%22 height=%22280%22 fill=%22%23fef2f2%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2218%22 fill=%22%23dc2626%22%3EImage Error%3C/text%3E%3C/svg%3E';"
                    <?php endif; ?>
                >
            </div>

            <!-- Product Details -->
            <div class="product-details-section">
                <div class="product-category">
                    <?php echo htmlspecialchars($product['cat_name'] ?? 'Uncategorized'); ?>
                </div>

                <h1 class="product-title">
                    <?php echo htmlspecialchars($product['product_title']); ?>
                </h1>

                <div class="product-brand">
                    by <?php if (!empty($product['artisan_id'])): ?>
                        <a href="artisan_about.php?id=<?php echo $product['artisan_id']; ?>" 
                           style="color: inherit; text-decoration: none; transition: color 0.3s ease;"
                           onmouseover="this.style.color='#dc2626';" 
                           onmouseout="this.style.color='inherit';">
                            <strong><?php echo htmlspecialchars($product['brand_name'] ?? 'Unknown'); ?></strong>
                        </a>
                    <?php else: ?>
                        <strong><?php echo htmlspecialchars($product['brand_name'] ?? 'Unknown'); ?></strong>
                    <?php endif; ?>
                </div>

                <div class="product-price">
                    GHS <?php echo number_format($product['product_price'], 2); ?>
                </div>

                <?php if (!empty($product['product_desc'])): ?>
                <div class="product-description">
                    <?php echo nl2br(htmlspecialchars($product['product_desc'])); ?>
                </div>
                <?php endif; ?>

                <div class="product-meta">
                    <div class="meta-item">
                        <span class="meta-label">Product ID:</span>
                        <span class="meta-value">#<?php echo $product['product_id']; ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Category:</span>
                        <span class="meta-value"><?php echo htmlspecialchars($product['cat_name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Brand:</span>
                        <span class="meta-value"><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <?php if (!empty($product['product_keywords'])): ?>
                <div class="product-keywords">
                    <?php 
                    $keywords = explode(',', $product['product_keywords']);
                    foreach($keywords as $keyword): 
                        $keyword = trim($keyword);
                        if ($keyword):
                    ?>
                        <span class="keyword-tag"><?php echo htmlspecialchars($keyword); ?></span>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <?php endif; ?>

               <?php 
               $product_stock = (int)($product['product_qty'] ?? 0);
               $is_sold_out = $product_stock == 0;
               ?>
               <div class="product-actions">
                    <?php if ($is_sold_out): ?>
                    <div style="padding: 16px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 2px solid #dc2626; border-radius: 12px; margin-bottom: 20px; text-align: center;">
                        <strong style="color: #991b1b; font-size: 18px; display: block; margin-bottom: 8px;">
                            <i class="fas fa-times-circle"></i> SOLD OUT
                        </strong>
                        <p style="color: #78350f; font-size: 14px; margin: 0;">This product is currently out of stock.</p>
                    </div>
                    <?php else: ?>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block;">Quantity:</label>
                        <input type="number" id="productQuantity" value="1" min="1" max="<?php echo min(99, $product_stock); ?>" 
                            style="width: 80px; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; text-align: center;">
                        <small style="display: block; color: #6b7280; margin-top: 8px;">
                            <?php echo $product_stock; ?> available in stock
                        </small>
                    </div>
                    <?php endif; ?>
                    <?php if ($is_sold_out): ?>
                    <button class="btn btn-primary" disabled style="opacity: 0.5; cursor: not-allowed;">
                        Sold Out
                    </button>
                    <?php else: ?>
                    <button class="btn btn-primary" onclick="addProductToCart()">
                        🛒 Add to Cart
                    </button>
                    <?php endif; ?>
                    <a href="all_product.php" class="btn btn-secondary">
                        ← Back to Shop
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Ratings & Reviews Section -->
    <section class="product-section" style="margin-top: 60px;">
        <div class="product-container" style="grid-template-columns: 1fr; padding: 50px 40px;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 42px; font-weight: 500; color: #111827; margin-bottom: 12px;">
                    <i class="fas fa-star" style="color: #dc2626; margin-right: 12px;"></i>
                    Ratings & Reviews
                </h2>
                <div style="height: 4px; width: 80px; background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%); margin: 0 auto; border-radius: 2px;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 40px; align-items: start;" class="artisan-meet-grid">
                <!-- Artisan Info Card (Keep on Left) -->
                <?php if ($artisan_about): ?>
                <div style="background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%); border-radius: 20px; padding: 32px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06); border: 1px solid rgba(220, 38, 38, 0.05);">
                    <div style="text-align: center; margin-bottom: 24px;">
                        <div style="width: 120px; height: 120px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 30px rgba(220, 38, 38, 0.3);">
                            <i class="fas fa-user" style="font-size: 48px; color: white;"></i>
                        </div>
                        <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 500; color: #111827; margin-bottom: 8px;">
                            <?php echo htmlspecialchars($artisan_about['business_name'] ?? $product['artisan_name'] ?? 'Artisan'); ?>
                        </h3>
                        <?php if (!empty($artisan_about['artisan_location'])): ?>
                        <p style="color: #6b7280; margin-bottom: 16px;">
                            <i class="fas fa-map-marker-alt" style="color: #dc2626;"></i>
                            <?php echo htmlspecialchars($artisan_about['artisan_location']); ?>
                        </p>
                        <?php endif; ?>
                        <div style="display: inline-block; padding: 6px 16px; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white; border-radius: 50px; font-size: 13px; font-weight: 600;">
                            <i class="fas fa-star"></i>
                            <?php echo ($artisan_about['tier'] == 1) ? 'Tier 1 - Digitally Literate' : 'Tier 2 - Non-Digitally Literate'; ?>
                        </div>
                    </div>
                    <a href="artisan_about.php?id=<?php echo $product['artisan_id']; ?>" 
                       class="btn btn-primary" 
                       style="width: 100%; display: block; text-align: center; text-decoration: none;">
                        <i class="fas fa-book-open"></i> Read Full Story
                    </a>
                </div>
                <?php endif; ?>

                <!-- Ratings & Reviews Content -->
                <div>
                    <!-- Overall Rating Summary -->
                            <?php 
                    require_once '../controllers/review_controller.php';
                    $review_stats = get_review_stats_ctr($product['product_id']);
                    $avg_rating = $review_stats ? (float)$review_stats['avg_rating'] : 0;
                    $total_reviews = $review_stats ? (int)$review_stats['total_reviews'] : 0;
                    $rating_5 = $review_stats ? (int)$review_stats['rating_5'] : 0;
                    $rating_4 = $review_stats ? (int)$review_stats['rating_4'] : 0;
                    $rating_3 = $review_stats ? (int)$review_stats['rating_3'] : 0;
                    $rating_2 = $review_stats ? (int)$review_stats['rating_2'] : 0;
                    $rating_1 = $review_stats ? (int)$review_stats['rating_1'] : 0;
                    $total_ratings = $rating_5 + $rating_4 + $rating_3 + $rating_2 + $rating_1;
                    $percent_5 = $total_ratings > 0 ? round(($rating_5 / $total_ratings) * 100) : 0;
                    $percent_4 = $total_ratings > 0 ? round(($rating_4 / $total_ratings) * 100) : 0;
                    $percent_3 = $total_ratings > 0 ? round(($rating_3 / $total_ratings) * 100) : 0;
                    $percent_1_2 = $total_ratings > 0 ? round((($rating_1 + $rating_2) / $total_ratings) * 100) : 0;
                    ?>
                    <div style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-radius: 16px; padding: 24px; margin-bottom: 32px; border: 1px solid #e5e7eb;">
                        <div style="display: flex; align-items: center; gap: 24px; flex-wrap: wrap;">
                            <div style="text-align: center;">
                                <div style="font-size: 48px; font-weight: 700; color: #dc2626; line-height: 1;">
                                    <?php echo number_format($avg_rating, 1); ?>
                    </div>
                                <div style="color: #6b7280; font-size: 14px; margin-top: 4px;">
                            <?php 
                                    $full_stars = floor($avg_rating);
                                    $has_half = ($avg_rating - $full_stars) >= 0.5;
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $full_stars) {
                                            echo '<i class="fas fa-star" style="color: #fbbf24;"></i>';
                                        } elseif ($i == $full_stars + 1 && $has_half) {
                                            echo '<i class="fas fa-star-half-alt" style="color: #fbbf24;"></i>';
                                        } else {
                                            echo '<i class="far fa-star" style="color: #d1d5db;"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <div style="color: #6b7280; font-size: 12px; margin-top: 8px;">
                                    Based on <?php echo $total_reviews; ?> review<?php echo $total_reviews != 1 ? 's' : ''; ?>
                                </div>
                            </div>
                            <div style="flex: 1; min-width: 200px;">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                    <span style="font-size: 13px; color: #6b7280; min-width: 60px;">5 stars</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; width: <?php echo $percent_5; ?>%; background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%);"></div>
                                    </div>
                                    <span style="font-size: 13px; color: #6b7280; min-width: 40px;"><?php echo $percent_5; ?>%</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                    <span style="font-size: 13px; color: #6b7280; min-width: 60px;">4 stars</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; width: <?php echo $percent_4; ?>%; background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%);"></div>
                                    </div>
                                    <span style="font-size: 13px; color: #6b7280; min-width: 40px;"><?php echo $percent_4; ?>%</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                    <span style="font-size: 13px; color: #6b7280; min-width: 60px;">3 stars</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; width: <?php echo $percent_3; ?>%; background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%);"></div>
                                    </div>
                                    <span style="font-size: 13px; color: #6b7280; min-width: 40px;"><?php echo $percent_3; ?>%</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 13px; color: #6b7280; min-width: 60px;">1-2 stars</span>
                                    <div style="flex: 1; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; width: <?php echo $percent_1_2; ?>%; background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%);"></div>
                                    </div>
                                    <span style="font-size: 13px; color: #6b7280; min-width: 40px;"><?php echo $percent_1_2; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Write Review Button (if logged in) -->
                    <?php if (is_logged_in()): ?>
                    <div style="margin-bottom: 32px;">
                        <button onclick="showReviewForm()" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-edit"></i> Write a Review
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Reviews List -->
                    <div id="reviewsList">
                        <?php
                        $reviews = get_product_reviews_ctr($product['product_id']);
                        if ($reviews && count($reviews) > 0):
                            foreach ($reviews as $review):
                                $customer_name = htmlspecialchars($review['customer_name']);
                                $initials = strtoupper(substr($customer_name, 0, 1));
                                $rating = (int)$review['rating'];
                                $review_text = htmlspecialchars($review['review_text'] ?? '');
                                $review_date = date('M d, Y', strtotime($review['review_date']));
                                $is_verified = (int)$review['is_verified_purchase'];
                                $review_id = (int)$review['review_id'];
                                $is_own_review = is_logged_in() && $review['customer_id'] == get_user_id();
                        ?>
                        <div class="review-item" data-review-id="<?php echo $review_id; ?>" style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; border: 1px solid #e5e7eb; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px;">
                                            <?php echo $initials; ?>
                                        </div>
                    <div>
                                            <div style="font-weight: 600; color: #111827;">
                                                <?php echo $customer_name; ?>
                                                <?php if ($is_verified): ?>
                                                    <span style="display: inline-block; margin-left: 8px; padding: 2px 8px; background: #d1fae5; color: #065f46; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                        <i class="fas fa-check-circle"></i> Verified Purchase
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="color: #6b7280; font-size: 13px; margin-top: 2px;">
                            <?php 
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $rating) {
                                                        echo '<i class="fas fa-star" style="color: #fbbf24;"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star" style="color: #d1d5db;"></i>';
                                                    }
                                                }
                                                ?>
                                                <span style="margin-left: 8px;"><?php echo $rating; ?>.0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="color: #6b7280; font-size: 13px;"><?php echo $review_date; ?></div>
                                    <?php if ($is_own_review): ?>
                                        <button onclick="editReview(<?php echo $review_id; ?>)" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: 14px; padding: 4px 8px;" title="Edit Review">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteReview(<?php echo $review_id; ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; padding: 4px 8px;" title="Delete Review">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($review_text)): ?>
                            <p style="color: #374151; line-height: 1.6; margin: 0;">
                                <?php echo nl2br($review_text); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <?php
                            endforeach;
                        else:
                        ?>
                        <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                            <i class="fas fa-comment-slash" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                            <p style="font-size: 16px; margin: 0;">No reviews yet. Be the first to review this product!</p>
                    </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Review Form Modal -->
    <div id="reviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; max-width: 600px; width: 90%; padding: 40px; border-radius: 20px; position: relative; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <span onclick="closeReviewModal()" style="position: absolute; top: 15px; right: 20px; font-size: 28px; cursor: pointer; color: #6b7280;">&times;</span>
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 28px; color: #dc2626; margin-bottom: 24px;">Write a Review</h2>
            
            <form id="reviewForm" onsubmit="submitReview(event); return false;">
                <input type="hidden" id="reviewProductId" value="<?php echo $product['product_id']; ?>">
                <input type="hidden" id="reviewId" value="">
                
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 12px;">Your Rating *</label>
                    <div id="ratingStars" style="display: flex; gap: 12px; font-size: 36px; cursor: pointer; align-items: center; min-height: 40px;">
                        <i class="far fa-star" data-rating="1" style="color: #d1d5db; transition: all 0.2s ease; display: inline-block; width: auto; height: auto;"></i>
                        <i class="far fa-star" data-rating="2" style="color: #d1d5db; transition: all 0.2s ease; display: inline-block; width: auto; height: auto;"></i>
                        <i class="far fa-star" data-rating="3" style="color: #d1d5db; transition: all 0.2s ease; display: inline-block; width: auto; height: auto;"></i>
                        <i class="far fa-star" data-rating="4" style="color: #d1d5db; transition: all 0.2s ease; display: inline-block; width: auto; height: auto;"></i>
                        <i class="far fa-star" data-rating="5" style="color: #d1d5db; transition: all 0.2s ease; display: inline-block; width: auto; height: auto;"></i>
                    </div>
                    <input type="hidden" id="reviewRating" name="rating" value="0" required>
                    <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 8px;">Click on the stars to rate (1-5 stars)</small>
                </div>
                
                <div style="margin-bottom: 24px;">
                    <label for="reviewText" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Your Review</label>
                    <textarea id="reviewText" name="review_text" rows="5" 
                              placeholder="Share your experience with this product..."
                              style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; resize: vertical; font-family: inherit;"
                              onfocus="this.style.borderColor='#dc2626';"
                              onblur="this.style.borderColor='#e5e7eb';"></textarea>
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <button type="button" onclick="closeReviewModal()" class="btn btn-secondary" style="flex: 1;">
                        Cancel
                    </button>
                    <button type="submit" id="submitReviewBtn" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-paper-plane"></i> Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/cart.js"></script>
    <script>
        const productId = <?php echo $product['product_id']; ?>;
        
        function addProductToCart() {
            const qty = parseInt(document.getElementById('productQuantity').value) || 1;
            addToCart(productId, qty);
        }
        
        function showReviewForm() {
            document.getElementById('reviewModal').style.display = 'flex';
            // Check if user already reviewed
            fetch('../actions/review_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get&product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.reviews) {
                    const ownReview = data.reviews.find(r => r.customer_id == <?php echo is_logged_in() ? get_user_id() : 0; ?>);
                    if (ownReview) {
                        // Pre-fill form for editing
                        document.getElementById('reviewId').value = ownReview.review_id;
                        document.getElementById('reviewRating').value = ownReview.rating;
                        setRatingStars(ownReview.rating);
                        document.getElementById('reviewText').value = ownReview.review_text || '';
                        document.getElementById('submitReviewBtn').innerHTML = '<i class="fas fa-save"></i> Update Review';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading review:', error);
            });
        }
        
        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
            document.getElementById('reviewForm').reset();
            document.getElementById('reviewId').value = '';
            document.getElementById('reviewRating').value = '0';
            setRatingStars(0);
            document.getElementById('submitReviewBtn').innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
        }
        
        function setRatingStars(rating) {
            const stars = document.querySelectorAll('#ratingStars i');
            stars.forEach((star, index) => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                if (starRating <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                    star.style.color = '#fbbf24';
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                    star.style.color = '#d1d5db';
                }
            });
        }
        
        // Rating star click handlers
        document.addEventListener('DOMContentLoaded', function() {
            const ratingStars = document.getElementById('ratingStars');
            if (ratingStars) {
                ratingStars.addEventListener('click', function(e) {
                    if (e.target.tagName === 'I') {
                        const rating = parseInt(e.target.getAttribute('data-rating'));
                        document.getElementById('reviewRating').value = rating;
                        setRatingStars(rating);
                    }
                });
                
                // Hover effect
                ratingStars.addEventListener('mouseover', function(e) {
                    if (e.target.tagName === 'I') {
                        const hoverRating = parseInt(e.target.getAttribute('data-rating'));
                        setRatingStars(hoverRating);
                    }
                });
                
                ratingStars.addEventListener('mouseleave', function() {
                    const currentRating = parseInt(document.getElementById('reviewRating').value) || 0;
                    setRatingStars(currentRating);
                });
            }
        });
        
        function submitReview(event) {
            event.preventDefault();
            const rating = parseInt(document.getElementById('reviewRating').value);
            
            if (rating < 1 || rating > 5) {
                alert('Please select a rating (1-5 stars)');
                return;
            }
            
            const formData = new FormData(document.getElementById('reviewForm'));
            formData.append('action', document.getElementById('reviewId').value ? 'update' : 'add');
            formData.append('product_id', productId);
            if (document.getElementById('reviewId').value) {
                formData.append('review_id', document.getElementById('reviewId').value);
            }
            
            const submitBtn = document.getElementById('submitReviewBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            
            fetch('../actions/review_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    closeReviewModal();
                    location.reload();
                } else {
                    alert(data.message || 'Failed to submit review');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
            });
        }
        
        function editReview(reviewId) {
            showReviewForm();
            // The form will be pre-filled by the showReviewForm function
        }
        
        function deleteReview(reviewId) {
            if (!confirm('Are you sure you want to delete your review?')) {
                return;
            }
            
            fetch('../actions/review_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=delete&review_id=' + reviewId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Failed to delete review');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>
</body>
</html>