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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Aya Crafts</title>
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

    /* Navigation */
    .navbar {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        padding: 20px 0;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 100;
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(220, 38, 38, 0.08);
    }


    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 40px;
    }

    .logo {
        font-family: 'Cormorant Garamond', serif;
        font-size: 28px;
        font-weight: 500;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-decoration: none;
        letter-spacing: 0.5px;
        position: relative;
    }

    .logo-subtitle {
        font-size: 10px;
        color: #9ca3af;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-weight: 500;
        margin-top: -5px;
        margin-bottom: -10px;
        align-self: flex-end;
        position: absolute;
        }

    .logo::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
        animation: shimmer 3s infinite;
        top: 0;
        left: 0;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .nav-links {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .nav-link {
        color: #374151;
        text-decoration: none;
        padding: 10px 24px;
        border-radius: 50px;
        transition: all 0.3s ease;
        font-size: 14px;
        font-weight: 500;
        border: 1.5px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.1);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .nav-link:hover::before {
        width: 300px;
        height: 300px;
    }

    .nav-link:hover {
        background: rgba(220, 38, 38, 0.05);
        border-color: rgba(220, 38, 38, 0.2);
        color: #dc2626;
        transform: translateY(-1px);
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
        margin-bottom: 15px;
        line-height: 1.2;
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
        padding: 16px 40px;
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
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(220, 38, 38, 0.4);
    }

    .btn-secondary {
        background: white;
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
            <a href="../index.php" class="logo">Aya Crafts</a>
            <span class="logo-subtitle">Authentic Artistry</span>
            <div class="nav-links">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="all_product.php" class="nav-link">Shop</a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a href="../admin/product.php" class="nav-link">Admin</a>
                    <?php endif; ?>
                    <a href="../login/logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="login/login.php" class="nav-link">Login</a>
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
                    // FIX: Properly construct image path
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
                    
                    // Use placeholder only if no valid image
                    $displayImage = !empty($imagePath) ? $imagePath : 'https://via.placeholder.com/280x280/fef2f2/dc2626?text=No+Image';
                    ?>
            <!-- Product Image -->
            <div class="product-image-section">
                <img 
                    src="<?php echo htmlspecialchars($displayImage); ?>"
                    alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                    class="product-image"
                    onerror="this.src='https://via.placeholder.com/600x600/fef2f2/dc2626?text=Image+Error'; this.onerror=null;"
                    loading="lazy"
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
                    by <strong><?php echo htmlspecialchars($product['brand_name'] ?? 'Unknown'); ?></strong>
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

                <div class="product-actions">
                    <button class="btn btn-primary" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                        🛒 Add to Cart
                    </button>
                    <a href="all_product.php" class="btn btn-secondary">
                        ← Back to Shop
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        function addToCart(productId) {
            alert('Add to cart functionality will be implemented in future labs. Product ID: ' + productId);
            // TODO: Implement cart functionality
        }
    </script>
</body>
</html>