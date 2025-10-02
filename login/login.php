<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        input:focus {
            border-color: #4CAF50;
            outline: none;
        }
        
        .login-btn {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            background-color: #45a049;
        }
        
        .login-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: #4CAF50;
            text-decoration: none;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        
        .forgot-password a {
            color: #888;
            text-decoration: none;
            font-size: 14px;
        }
        
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Customer Login</h2>
        
        <form id="loginForm">
            
            <!-- Email Field -->
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <div class="error-message" id="email-error"></div>
            </div>
            
            <!-- Password Field -->
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <div class="error-message" id="password-error"></div>
            </div>
            
            <!-- Login Button -->
            <button type="submit" class="login-btn">Login</button>
            
        </form>
        
        <!-- Forgot Password Link -->
        <div class="forgot-password">
            <a href="#">Forgot Password?</a>
        </div>
        
        <!-- Link to Registration Page -->
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>

        <div class="index-link">
        <a href="../index.php">Home Page</a>
        </div>
    </div>
    
    <!-- Include the JavaScript file -->
    <script src="../js/login.js"></script>
</body>
</html>
