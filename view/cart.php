<?php
// Include core session management functions
require_once '../settings/core.php';

// Check if user is logged in
require_login('../login/login.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Aya Crafts</title>
    <link rel="stylesheet" href="../css/aya_styles.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 20px 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
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
            text-decoration: none;
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
            position: relative;
        }

        .nav-link:hover {
            background: rgba(220, 38, 38, 0.05);
            color: #dc2626;
        }

        .cart-icon {
            position: relative;
        }

        .cart-count-badge {
            position: absolute;
            top: -5px;
            right: 5px;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            z-index: 10;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 500;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .cart-section {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            margin-bottom: 30px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th {
            background: #f9fafb;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cart-table td {
            padding: 20px 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .cart-summary {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            padding: 30px;
            border-radius: 16px;
            border: 2px solid rgba(220, 38, 38, 0.2);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .summary-total {
            font-size: 24px;
            font-weight: 700;
            color: #dc2626;
            padding-top: 15px;
            border-top: 2px solid rgba(220, 38, 38, 0.3);
        }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
            color: white;
        }

        .empty-cart-state {
            text-align: center;
            padding: 80px 20px;
            display: none;
        }

        .empty-cart-state h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            color: #4b5563;
            margin-bottom: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .cart-table {
                font-size: 14px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">Aya Crafts</a>
            <div class="nav-links">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="all_product.php" class="nav-link">Shop</a>
                <a href="cart.php" class="nav-link cart-icon">
                    🛒 Cart
                    <span class="cart-count-badge" style="display: flex; text-align: center;">0</span>
                </a>
                <?php if (is_logged_in()): ?>
                    <a href="../login/logout.php" class="nav-link">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Shopping Cart</h1>
            <p style="color: #6b7280; font-size: 16px;">Review your items before checkout</p>
        </div>

        <!-- Empty Cart State -->
        <div id="emptyCartState" class="empty-cart-state cart-section">
            <div style="font-size: 4rem; margin-bottom: 20px;">🛒</div>
            <h3>Your Cart is Empty</h3>
            <p style="color: #6b7280; margin-bottom: 30px;">Add some amazing products to get started!</p>
            <a href="all_product.php" class="btn btn-primary">Browse Products</a>
        </div>

        <!-- Cart Items -->
        <div class="cart-section" id="cartItemsSection">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 20px;">Cart Items</h2>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cartItemsContainer">
                    <!-- Cart items loaded by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Cart Summary -->
        <div class="cart-summary" id="cartSummary" style="display: none;">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; margin-bottom: 20px;">Order Summary</h2>
            
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="cartTotal">GHS 0.00</span>
            </div>
            
            <div class="summary-row">
                <span style="color: #6b7280; font-size: 14px;">Shipping:</span>
                <span style="color: #6b7280; font-size: 14px;">Calculated at checkout</span>
            </div>
            
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span id="cartTotalFinal">GHS 0.00</span>
            </div>
            
            <div class="action-buttons">
                <button onclick="emptyCart()" class="btn btn-secondary">Empty Cart</button>
                <a href="all_product.php" class="btn btn-secondary">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout →</a>
            </div>
        </div>
    </div>

    <script src="../js/cart.js"></script>
    <script>
        // Update total display
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new MutationObserver(function() {
                const total = document.getElementById('cartTotal').textContent;
                document.getElementById('cartTotalFinal').textContent = total;
            });
            
            const cartTotal = document.getElementById('cartTotal');
            if (cartTotal) {
                observer.observe(cartTotal, { childList: true, characterData: true, subtree: true });
            }
        });
    </script>
</body>
</html>