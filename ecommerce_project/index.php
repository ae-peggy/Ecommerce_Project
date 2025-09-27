<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Platform</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .navbar {
            background-color: #333;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .nav-buttons {
            display: flex;
            gap: 15px;
        }
        
        .nav-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-btn:hover {
            background-color: #45a049;
        }
        
        .nav-btn.secondary {
            background-color: transparent;
            border: 2px solid #4CAF50;
        }
        
        .nav-btn.secondary:hover {
            background-color: #4CAF50;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
            text-align: center;
        }
        
        .welcome-section {
            background: white;
            padding: 60px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .welcome-section h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 20px;
        }
        
        .welcome-section p {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .cta-btn {
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .cta-btn:hover {
            background-color: #45a049;
        }
        
        .cta-btn.secondary {
            background-color: white;
            color: #4CAF50;
            border: 2px solid #4CAF50;
        }
        
        .cta-btn.secondary:hover {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">E-Commerce</div>
            
            <!-- Navigation buttons - only show when customer is NOT logged in -->
            <div class="nav-buttons">
                <a href="login/login.php" class="nav-btn secondary">Login</a>
                <a href="login/register.php" class="nav-btn">Register</a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="welcome-section">
            <h1>Welcome to Our E-Commerce Platform</h1>
            <p>Discover amazing products and enjoy a seamless shopping experience. 
               Join our community today and start exploring!</p>
            
        </div>
    </main>
</body>
</html>