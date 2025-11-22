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
    <link rel="stylesheet" href="../css/admin_pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../admin/includes/nav.php'; ?>

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