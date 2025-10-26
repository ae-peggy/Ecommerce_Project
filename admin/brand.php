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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 1.2rem 0;
            box-shadow: 0 4px 20px rgba(220, 38, 38, 0.3);
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
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 600;
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
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border-top: 4px solid #dc2626;
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 1rem;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.25);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.95;
            margin-top: 5px;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            color: #1a1a1a;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f3f4f6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .btn {
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.35);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 8px 18px;
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
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #f3f4f6;
        }

        .table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #fef2f2;
        }

        .edit-section {
            display: none;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            padding: 25px;
            border-radius: 10px;
            border: 2px solid #fecaca;
            margin-bottom: 30px;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .no-data-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
            display: none;
        }

        .nav-links {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.15);
        }

        .nav-link.logout {
            background-color: rgba(0,0,0,0.2);
        }

        .nav-link.logout:hover {
            background-color: rgba(0,0,0,0.3);
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
            <div class="logo">Aya Crafts - Admin Panel</div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars(get_user_name()); ?>!</span>
                <div class="nav-links">
                    <a href="../index.php" class="nav-link">Home</a>
                    <a href="category.php" class="nav-link">Categories</a>
                    <a href="brand.php" class="nav-link" style="background-color: rgba(255,255,255,0.2);">Brands</a>
                    <a href="../login/logout.php" class="nav-link logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

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
                    <input type="text" id="brandName" name="brand_name" placeholder="Enter brand name (e.g., Nike, Adidas, Apple)" required>
                    <div class="error-message" id="brandName-error"></div>
                    <small style="color: #6b7280; font-size: 13px; margin-top: 8px; display: block;">
                        💡 Tip: Brands work across all categories. Nike can be used for footwear, clothing, and accessories.
                    </small>
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
                <div class="no-data-icon">🏷️</div>
                <h3>No Brands Yet</h3>
                <p>Start by adding your first brand above.</p>
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