<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'settings/core.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aya Crafts | Authentic African Artistry</title>
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
  color: #1a1a1a;
  line-height: 1.6;
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

.navbar {
  background: rgba(255, 255, 255, 0.95);
  padding: 25px 0;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
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
  padding: 0 60px;
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 15px;
}

.logo-symbol {
  width: 45px;
  height: 45px;
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(220, 38, 38, 0.2);
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
  width: 28px;
  height: 28px;
  fill: white;
  position: relative;
  z-index: 1;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo {
  font-family: 'Cormorant Garamond', serif;
  font-size: 32px;
  font-weight: 500;
  letter-spacing: 1px;
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
  background-size: 200% auto;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
  0%, 100% { background-position: 0% center; }
  50% { background-position: 100% center; }
}

.logo-subtitle {
  font-size: 10px;
  color: #9ca3af;
  letter-spacing: 2px;
  text-transform: uppercase;
  font-weight: 500;
  margin-top: -5px;
}

.nav-buttons {
  display: flex;
  gap: 12px;
  align-items: center;
}

.nav-btn {
  color: #374151;
  padding: 12px 32px;
  text-decoration: none;
  border-radius: 50px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 14px;
  font-weight: 500;
  letter-spacing: 0.3px;
  border: 1.5px solid transparent;
  position: relative;
  overflow: hidden;
}

.nav-btn::before {
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

.nav-btn:hover::before {
  width: 300px;
  height: 300px;
}

.nav-btn.secondary {
  color: #6b7280;
  border: 1.5px solid #e5e7eb;
  background: transparent;
}

.nav-btn.secondary:hover {
  border-color: #dc2626;
  color: #dc2626;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(220, 38, 38, 0.12);
}

.nav-btn:not(.secondary) {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(220, 38, 38, 0.25);
}

.nav-btn:not(.secondary):hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(220, 38, 38, 0.35);
}

.nav-btn span {
  position: relative;
  z-index: 1;
}

.main-content {
  max-width: 1400px;
  margin: 100px auto;
  padding: 0 60px;
}

.welcome-section {
  background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
  padding: 100px 80px;
  border-radius: 30px;
  box-shadow: 
    0 20px 60px rgba(0, 0, 0, 0.08),
    0 0 0 1px rgba(220, 38, 38, 0.05);
  text-align: center;
  position: relative;
  overflow: hidden;
}

/* Decorative kente-inspired border */
.welcome-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 12px;
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
  box-shadow: 0 2px 10px rgba(220, 38, 38, 0.2);
}

.welcome-section::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 12px;
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
  box-shadow: 0 -2px 10px rgba(220, 38, 38, 0.2);
}

/* Floating geometric shapes */
.geometric-shape {
  position: absolute;
  opacity: 0.1;
  z-index: 0;
  animation: float 20s ease-in-out infinite;
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

.welcome-content {
  position: relative;
  z-index: 1;
}

.accent-bar {
  height: 4px;
  width: 80px;
  background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
  margin: 0 auto 40px;
  border-radius: 2px;
  box-shadow: 0 2px 10px rgba(220, 38, 38, 0.3);
  animation: expandContract 2s ease-in-out infinite;
}

@keyframes expandContract {
  0%, 100% { width: 80px; }
  50% { width: 100px; }
}

.welcome-section h1 {
  font-family: 'Cormorant Garamond', serif;
  color: #111827;
  font-size: 64px;
  margin-bottom: 30px;
  font-weight: 400;
  letter-spacing: -1.5px;
  line-height: 1.2;
}

.welcome-section h1 strong {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
  background-size: 200% auto;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 600;
  animation: gradientShift 4s ease infinite;
  position: relative;
  display: inline-block;
}

.welcome-section h1 strong::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, transparent, #dc2626, transparent);
  opacity: 0.3;
}

.welcome-section p {
  color: #6b7280;
  font-size: 20px;
  margin-bottom: 50px;
  line-height: 1.8;
  max-width: 750px;
  margin-left: auto;
  margin-right: auto;
  font-weight: 300;
}

/* Search Box */
.search-box {
  max-width: 600px;
  margin: 40px auto;
  position: relative;
}

.search-box input {
  width: 100%;
  padding: 18px 60px 18px 24px;
  border: 2px solid #e5e7eb;
  border-radius: 50px;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-box input:focus {
  outline: none;
  border-color: #dc2626;
  box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
}

.search-box button {
  position: absolute;
  right: 6px;
  top: 50%;
  transform: translateY(-50%);
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 50px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.search-box button:hover {
  transform: translateY(-50%) scale(1.05);
  box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
}
.cta-buttons {
  display: flex;
  gap: 20px;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 50px;
}

.cta-btn {
  padding: 18px 50px;
  text-decoration: none;
  border-radius: 50px;
  font-size: 16px;
  font-weight: 500;
  letter-spacing: 0.5px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.cta-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.6s;
}

.cta-btn:hover::before {
  left: 100%;
}

.cta-btn:not(.secondary) {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  color: white;
  box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
}

.cta-btn:not(.secondary):hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 40px rgba(220, 38, 38, 0.4);
}

.cta-btn.secondary {
  background: white;
  color: #374151;
  border: 2px solid #e5e7eb;
}

.cta-btn.secondary:hover {
  border-color: #dc2626;
  color: #dc2626;
  transform: translateY(-4px);
  box-shadow: 0 15px 40px rgba(220, 38, 38, 0.15);
}

/* Floating particles */
.particles {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  z-index: 0;
  overflow: hidden;
}

.particle {
  position: absolute;
  width: 6px;
  height: 6px;
  background: #dc2626;
  border-radius: 50%;
  opacity: 0.4;
  animation: rise 15s infinite ease-in;
}

.particle:nth-child(1) { left: 10%; animation-delay: 0s; }
.particle:nth-child(2) { left: 20%; animation-delay: 2s; }
.particle:nth-child(3) { left: 30%; animation-delay: 4s; }
.particle:nth-child(4) { left: 40%; animation-delay: 6s; }
.particle:nth-child(5) { left: 50%; animation-delay: 8s; }
.particle:nth-child(6) { left: 60%; animation-delay: 10s; }
.particle:nth-child(7) { left: 70%; animation-delay: 12s; }
.particle:nth-child(8) { left: 80%; animation-delay: 1s; }
.particle:nth-child(9) { left: 90%; animation-delay: 3s; }

@keyframes rise {
  0% {
    bottom: -10%;
    transform: translateX(0) scale(1);
    opacity: 0;
  }
  10% {
    opacity: 0.2;
  }
  90% {
    opacity: 0.2;
  }
  100% {
    bottom: 110%;
    transform: translateX(20px) scale(0.5);
    opacity: 0;
  }
}

@media (max-width: 768px) {
  .nav-container {
    padding: 0 30px;
  }
  
  .logo {
    font-size: 26px;
  }
  
  .welcome-section {
    padding: 70px 40px;
  }
  
  .welcome-section h1 {
    font-size: 42px;
  }
  
  .welcome-section p {
    font-size: 18px;
  }
  
  .main-content {
    margin: 50px auto;
    padding: 0 30px;
  }
  
  .cta-buttons {
    flex-direction: column;
  }
  
  .cta-btn {
    width: 100%;
  }
}
</style>
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar">
  <div class="nav-container">
    <div class="logo-container">
      <div class="logo-symbol">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
        </svg>
      </div>
      <div class="logo-text">
        <div class="logo">Aya Crafts</div>
        <span class="logo-subtitle">Authentic Artistry</span>
      </div>
    </div>
    
    <div class="nav-buttons">
      <?php if (is_logged_in()): ?>
        <?php if (is_admin()): ?>
          <!-- Admin Navigation -->
          <a href="admin/category.php" class="nav-btn secondary"><span>Categories</span></a>
          <a href="admin/brand.php" class="nav-btn secondary"><span>Brands</span></a>
          <a href="admin/product.php" class="nav-btn secondary"><span>Products</span></a>
          <a href="admin/artisans.php" class="nav-btn secondary"><span>Artisans</span></a>
        <?php elseif (is_artisan()): ?>
          <!-- Artisan Navigation -->
          <a href="artisan/dashboard.php" class="nav-btn secondary"><span>Dashboard</span></a>
          <a href="artisan/my_products.php" class="nav-btn secondary"><span>My Products</span></a>
          <a href="artisan/orders.php" class="nav-btn secondary"><span>Orders</span></a>
          <a href="artisan/analytics.php" class="nav-btn secondary"><span>Analytics</span></a>
          <a href="view/all_product.php" class="nav-btn secondary"><span>View Store</span></a>
        <?php else: ?>
          <!-- Customer Navigation -->
          <a href="view/all_product.php" class="nav-btn secondary"><span>Shop</span></a>
          <a href="view/cart.php" class="nav-btn secondary"><span>Cart</span></a>
          <a href="view/orders.php" class="nav-btn secondary"><span>My Orders</span></a>
        <?php endif; ?>
        
        <a href="login/logout.php" class="nav-btn"><span>Logout</span></a>
      <?php else: ?>
        <!-- Guest Navigation -->
        <a href="login/login.php" class="nav-btn secondary"><span>Login</span></a>
        <a href="login/register.php" class="nav-btn"><span>Register</span></a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main class="main-content">
  <div class="welcome-section">
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
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
    </div>
    <div class="welcome-content">
      <div class="accent-bar"></div>
      <h1>Welcome to <strong>Aya Crafts</strong>, <?php echo htmlspecialchars(get_user_name() ?? ''); ?></h1>
      <p>Discover authentic African artistry and craftsmanship. Each piece tells a story, 
      woven with tradition and brought to life by skilled artisans. Join our community 
      and explore a world of unique, handcrafted treasures.</p>

      <!-- Search Box -->
      <div class="search-box">
        <form action="view/search.php" method="GET">
          <input type="text" name="search" placeholder="🔍 Search for products..." required>
          <button type="submit">Search</button>
        </form>
      </div>
      
      <div class="cta-buttons">
        <a href="view/all_product.php" class="cta-btn">Explore Collection</a>
        <a href="view/about_us.php" class="cta-btn secondary">Learn Our Story</a>
      </div>
    </div>
  </div>
</main>
</body>
</html>