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
    <title>Brand Management - Aya Crafts</title>
    <link rel="stylesheet" href="../css/admin_pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../admin/includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Brand Management</h1>
            <p class="page-subtitle">Manage your product brands - independent across all categories</p>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number" id="brandCount">0</div>
                    <div class="stat-label">Total Brands</div>
                </div>
            </div>
        </div>

        <!-- Add Brand Section -->
        <div class="section">
            <h2 class="section-title">Add New Brand</h2>
            
            <form id="addBrandForm">
                <div class="form-group">
                    <label for="brandName">Brand Name</label>
                    <input type="text" id="brandName" name="brand_name" placeholder="e.g., Kente Masters, Adinkra Artisans..." required>
                    <div class="error-message" id="brandName-error"></div>
                </div>
                
                <button type="submit" id="addBrandBtn" class="btn btn-primary">Add Brand</button>
            </form>
        </div>

        <!-- Edit Brand Section (Hidden by default) -->
        <div class="section edit-section" id="editBrandSection">
            <h2 class="section-title">Edit Brand</h2>
            
            <form id="editBrandForm">
                <input type="hidden" id="editBrandId" name="brand_id">
                
                <div class="form-group">
                    <label for="editBrandName">Brand Name</label>
                    <input type="text" id="editBrandName" name="brand_name" placeholder="Enter brand name" required>
                    <div class="error-message" id="editBrandName-error"></div>
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <button type="submit" id="updateBrandBtn" class="btn btn-success">Update Brand</button>
                    <button type="button" onclick="cancelEdit()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Brands List Section -->
        <div class="section">
            <h2 class="section-title">Your Brands</h2>
            
            <div id="noBrandsMessage" class="no-data" style="display: none;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🏷️</div>
                <h3>No Brands Yet</h3>
                <p>Start by adding your first product brand above.</p>
            </div>
            
            <table class="table" id="brandsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Brand Name</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="brandsTableBody">
                    <!-- Brands will be loaded here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include the JavaScript file -->
    <script src="../js/brand.js"></script>
</body>
</html>
 