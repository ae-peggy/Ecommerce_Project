<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Aya Crafts</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', sans-serif;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #ffffff;
  padding: 20px;
  position: relative;
  overflow: hidden;
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

.form-container {
  background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
  padding: 50px 45px;
  border-radius: 24px;
  box-shadow: 
    0 20px 60px rgba(0, 0, 0, 0.08),
    0 0 0 1px rgba(220, 38, 38, 0.05);
  max-width: 450px;
  width: 100%;
  position: relative;
  overflow: hidden;
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Decorative kente border */
.form-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 8px;
  background: 
    linear-gradient(90deg, 
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

.logo-header {
  text-align: center;
  margin-bottom: 35px;
}

.logo-symbol {
  width: 55px;
  height: 55px;
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 15px;
  box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
  position: relative;
  overflow: hidden;
}

.logo-symbol::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
  animation: shimmer 3s infinite;
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.logo-symbol svg {
  width: 32px;
  height: 32px;
  fill: white;
  position: relative;
  z-index: 1;
}

h2 {
  font-family: 'Cormorant Garamond', serif;
  text-align: center;
  font-size: 36px;
  font-weight: 500;
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 10px;
  letter-spacing: -0.5px;
}

.subtitle {
  text-align: center;
  color: #9ca3af;
  font-size: 14px;
  margin-bottom: 35px;
  font-weight: 400;
}

.form-group {
  margin-bottom: 24px;
  position: relative;
}

label {
  display: block;
  margin-bottom: 8px;
  color: #374151;
  font-weight: 500;
  font-size: 14px;
  letter-spacing: 0.3px;
}

input[type="email"],
input[type="password"] {
  width: 100%;
  padding: 14px 16px;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  font-size: 15px;
  font-family: 'Inter', sans-serif;
  transition: all 0.3s ease;
  background: white;
}

input:focus {
  border-color: #dc2626;
  outline: none;
  box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.08);
  transform: translateY(-1px);
}

input::placeholder {
  color: #d1d5db;
}

.login-btn {
  width: 100%;
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  color: white;
  padding: 15px;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 10px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
  position: relative;
  overflow: hidden;
  letter-spacing: 0.3px;
}

.login-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.6s;
}

.login-btn:hover::before {
  left: 100%;
}

.login-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
}

.login-btn:active {
  transform: translateY(0);
}

.login-btn:disabled {
  background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}

.forgot-password {
  text-align: center;
  margin-top: 20px;
}

.forgot-password a {
  color: #6b7280;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s ease;
}

.forgot-password a:hover {
  color: #dc2626;
}

.divider {
  display: flex;
  align-items: center;
  margin: 30px 0 25px;
  color: #9ca3af;
  font-size: 13px;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(to right, transparent, #e5e7eb, transparent);
}

.divider span {
  padding: 0 15px;
}

.register-link {
  text-align: center;
  margin-top: 25px;
  padding-top: 25px;
  border-top: 1px solid #f3f4f6;
}

.register-link p {
  color: #6b7280;
  font-size: 14px;
  margin-bottom: 12px;
}

.register-link a {
  color: #dc2626;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  padding: 8px 20px;
  border-radius: 8px;
  display: inline-block;
}

.register-link a:hover {
  background: rgba(220, 38, 38, 0.05);
  transform: translateY(-1px);
}

.index-link {
  text-align: center;
  margin-top: 15px;
}

.index-link a {
  color: #9ca3af;
  text-decoration: none;
  font-size: 13px;
  transition: color 0.3s ease;
}

.index-link a:hover {
  color: #6b7280;
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

/* Floating geometric shapes */
.geometric-shape {
  position: fixed;
  opacity: 0.03;
  z-index: -1;
  animation: float 20s ease-in-out infinite;
  pointer-events: none;
}

.shape-1 {
  top: 10%;
  right: 8%;
  width: 200px;
  height: 200px;
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
  animation-delay: 0s;
}

.shape-2 {
  bottom: 15%;
  left: 5%;
  width: 180px;
  height: 180px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border-radius: 63% 37% 54% 46% / 55% 48% 52% 45%;
  animation-delay: -5s;
}

.shape-3 {
  top: 50%;
  left: 50%;
  width: 150px;
  height: 150px;
  background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
  border-radius: 41% 59% 51% 49% / 38% 45% 55% 62%;
  transform: translate(-50%, -50%);
  animation-delay: -10s;
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
  opacity: 0.15;
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

@media (max-width: 768px) {
  .form-container {
    padding: 40px 30px;
  }
  
  h2 {
    font-size: 32px;
  }
}
</style>
</head>
<body>
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

<div class="form-container">
  <div class="logo-header">
    <div class="logo-symbol">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
      </svg>
    </div>
  </div>
  
  <h2>Welcome Back</h2>
  <p class="subtitle">Sign in to continue your journey</p>
  
  <form id="loginForm">
    <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" placeholder="your@email.com" required>
      <div class="error-message" id="email-error"></div>
    </div>
    
    <div class="form-group">
      <label for="password">Password</label>
      <div style="position: relative;">
        <input type="password" id="password" name="password" placeholder="••••••••" required style="padding-right: 40px;">
        <i class="fas fa-eye password-toggle" onclick="togglePassword('password', this)" 
           style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280;"></i>
      </div>
      <div class="error-message" id="password-error"></div>
    </div>
    
    <button type="submit" class="login-btn">Sign In</button>
  </form>
  
  <div class="forgot-password">
    <a href="#">Forgot your password?</a>
  </div>
  
  <div class="divider">
    <span>or</span>
  </div>
  
  <div class="register-link">
    <p>New to Aya Crafts?</p>
    <a href="register.php">Create an account →</a>
  </div>
  
  <div class="index-link">
    <a href="../index.php">← Back to Home</a>
  </div>
</div>

<script src="../js/login.js"></script>
<script>
  // Password toggle function
  function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }
</script>
</body>
</html>