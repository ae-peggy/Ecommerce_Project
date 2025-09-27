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
    <title>Category Management - Heritage Market Ghana</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: #3498db;
            color: white;
            padding: 15px 25px;
            border-radius: 6px;
            text-align: center;
            min-width: 150px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #3498db;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .btn-success {
            background-color: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background-color: #229954;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .edit-section {
            display: none;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border: 2px solid #3498db;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .no-data img {
            width: 100px;
            opacity: 0.5;
            margin-bottom: 20px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .nav-link.logout {
            background-color: #e74c3c;
        }

        .nav-link.logout:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .stats {
                flex-direction: column;
            }

            .container {
                padding: 0 15px;
            }

            .table {
                font-size: 14px;
            }

            .btn-sm {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">Heritage Market Ghana - Admin</div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars(get_user_name()); ?>!</span>
                <div class="nav-links">
                    <a href="../index.php" class="nav-link">Home</a>
                    <a href="../login/logout.php" class="nav-link logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Category Management</h1>
            <p class="page-subtitle">Manage your Heritage Market Ghana product categories</p>
            
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
                    <input type="text" id="categoryName" name="cat_name" placeholder="Enter category name (e.g., Traditional Textiles)" required>
                    <div class="error-message" id="categoryName-error"></div>
                    <small style="color: #7f8c8d; font-size: 14px; margin-top: 5px; display: block;">
                        Suggested categories: Traditional Textiles, Wood Crafts, Pottery & Ceramics, Jewelry & Accessories, Woven Items, Home Decor
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
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" id="updateCategoryBtn" class="btn btn-success">Update Category</button>
                    <button type="button" onclick="cancelEdit()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Categories List Section -->
        <div class="section">
            <h2 class="section-title">Your Categories</h2>
            
            <div id="noCategoriesMessage" class="no-data" style="display: none;">
                <div style="font-size: 3rem; margin-bottom: 20px;">📦</div>
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