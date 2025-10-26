<?php
// Include core session management functions
require_once '../settings/core.php';

// Check if user is logged in
require_login('../login/login.php');

// Check if user is admin
require_admin('../index.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management | Aya Crafts</title>
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
            color: #1a1a1a;
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
                radial-gradient(circle at 20% 50%, rgba(220, 38, 38, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(239, 68, 68, 0.02) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(185, 28, 28, 0.02) 0%, transparent 50%);
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
            opacity: 0.02;
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

        .header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 20px 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(220, 38, 38, 0.08);
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                #dc2626 0%, #dc2626 10%,
                #991b1b 10%, #991b1b 20%,
                #ef4444 20%, #ef4444 30%,
                #dc2626 30%, #dc2626 40%,
                #b91c1c 40%, #b91c1c 50%,
                #dc2626 50%, #dc2626 60%,
                #991b1b 60%, #991b1b 70%,
                #ef4444 70%, #ef4444 80%,
                #dc2626 80%, #dc2626 90%,
                #b91c1c 90%, #b91c1c 100%
            );
        }

        .header-content {
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
            letter-spacing: 0.5px;
        }

        .logo-subtitle {
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: -3px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 25px;
            color: #374151;
            font-size: 14px;
        }

        .nav-links {
            display: flex;
            gap: 10px;
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
        }

        .nav-link:hover {
            background: rgba(220, 38, 38, 0.05);
            border-color: rgba(220, 38, 38, 0.2);
            color: #dc2626;
            transform: translateY(-1px);
        }

        .nav-link.logout {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.2);
        }

        .nav-link.logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 
                0 10px 40px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(220, 38, 38, 0.05);
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
            background: linear-gradient(90deg, 
                #dc2626 0%, #dc2626 15%,
                #991b1b 15%, #991b1b 30%,
                #ef4444 30%, #ef4444 45%,
                #dc2626 45%, #dc2626 60%,
                #b91c1c 60%, #b91c1c 75%,
                #ef4444 75%, #ef4444 100%
            );
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 500;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 16px;
            font-weight: 400;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 25px 35px;
            border-radius: 16px;
            text-align: center;
            min-width: 180px;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.25);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .stat-card:hover::before {
            left: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(220, 38, 38, 0.35);
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 500;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }

        .section {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 
                0 10px 40px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(220, 38, 38, 0.05);
            position: relative;
            height: fit-content;
            margin-bottom: 30px;
        }

        .section.sticky-form {
            position: sticky;
            top: 120px;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .quick-stat {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(220, 38, 38, 0.1);
        }

        .quick-stat-number {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .quick-stat-label {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
            font-weight: 500;
        }
        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 25px;
            font-weight: 500;
            letter-spacing: -0.3px;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #dc2626, #ef4444);
            margin-top: 12px;
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        input[type="text"] {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            background: white;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.08);
            transform: translateY(-1px);
        }

        input[type="text"]::placeholder {
            color: #d1d5db;
        }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 50px;
            font-size: 15px;
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
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(107, 114, 128, 0.2);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(107, 114, 128, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.25);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(5, 150, 105, 0.35);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
        }

        .btn-sm {
            padding: 10px 20px;
            font-size: 13px;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 25px;
        }

        .table th,
        .table td {
            padding: 16px;
            text-align: left;
        }

        .table thead {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }

        .table th {
            font-weight: 600;
            color: #374151;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .table th:first-child {
            border-top-left-radius: 12px;
        }

        .table th:last-child {
            border-top-right-radius: 12px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f3f4f6;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            transform: translateX(3px);
        }

        .table td {
            color: #4b5563;
            font-size: 14px;
        }

        .edit-section {
            display: none;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid rgba(220, 38, 38, 0.2);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .no-data {
            text-align: center;
            padding: 60px 40px;
            color: #9ca3af;
        }

        .no-data h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            color: #6b7280;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
            display: none;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 0 20px;
            }

            .user-info {
                flex-direction: column;
                gap: 10px;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .section.sticky-form {
                position: static;
            }

            .quick-stats {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 0 20px;
                margin: 20px auto;
            }

            .page-header,
            .section {
                padding: 30px 25px;
            }

            .page-title {
                font-size: 32px;
            }

            .table {
                font-size: 13px;
                display: block;
                overflow-x: auto;
            }

            .btn-sm {
                padding: 8px 14px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="geometric-shape shape-1"></div>
    <div class="geometric-shape shape-2"></div>

    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">Aya Crafts - Category Management</div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars(get_user_name()); ?>!</span>
                <div class="nav-links">
                    <a href="../index.php" class="nav-link">Home</a>
                    <a href="category.php" class="nav-link">Categories</a>
                    <a href="brand.php" class="nav-link">Brands</a>
                    <a href="product.php" class="nav-link" style="background-color: rgba(255,255,255,0.2);">Products</a>
                    <a href="../login/logout.php" class="nav-link">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Category Management</h1>
            <p class="page-subtitle">Organize and manage your authentic craft categories</p>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number" id="categoryCount">0</div>
                    <div class="stat-label">Total Categories</div>
                </div>
            </div>
        </div>

        <!-- Add Category Section -->
        <div class="section">
            <h2 class="section-title">Add New Category</h2>
            
            <form id="addCategoryForm">
                <div class="form-group">
                    <label for="categoryName">Category Name</label>
                    <input type="text" id="categoryName" name="cat_name" placeholder="e.g., Traditional Textiles, Wood Crafts..." required>
                    <div class="error-message" id="categoryName-error"></div>
                    <small style="color: #9ca3af; font-size: 13px; margin-top: 8px; display: block;">
                        Suggested: Traditional Textiles, Wood Crafts, Pottery & Ceramics, Jewelry & Accessories, Woven Items, Home Decor
                    </small>
                </div>
                
                <button type="submit" id="addCategoryBtn" class="btn btn-primary">Add Category</button>
            </form>
        </div>

        <!-- Edit Category Section (Hidden by default) -->
        <div class="section edit-section" id="editCategorySection">
            <h2 class="section-title">Edit Category</h2>
            
            <form id="editCategoryForm">
                <input type="hidden" id="editCategoryId" name="cat_id">
                
                <div class="form-group">
                    <label for="editCategoryName">Category Name</label>
                    <input type="text" id="editCategoryName" name="cat_name" placeholder="Enter category name" required>
                    <div class="error-message" id="editCategoryName-error"></div>
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <button type="submit" id="updateCategoryBtn" class="btn btn-success">Update Category</button>
                    <button type="button" onclick="cancelEdit()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Categories List Section -->
        <div class="section">
            <h2 class="section-title">Your Categories</h2>
            
            <div id="noCategoriesMessage" class="no-data" style="display: none;">
                <div style="font-size: 4rem; margin-bottom: 20px;">📦</div>
                <h3>No Categories Yet</h3>
                <p>Start by adding your first product category above.</p>
            </div>
            
            <table class="table" id="categoriesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTableBody">
                    <!-- Categories will be loaded here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include the JavaScript file -->
    <script src="../js/category.js"></script>
</body>
</html>