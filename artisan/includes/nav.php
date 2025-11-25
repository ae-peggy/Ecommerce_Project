<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Determine if we're in the artisan folder or being included from elsewhere
// Check the directory of the currently executing script
$script_path = $_SERVER['PHP_SELF'];
$is_in_artisan_folder = (strpos($script_path, '/artisan/') !== false);

// Set base path for artisan pages
// If we're in view/ or another folder, we need to go up and into artisan/
$artisan_base = $is_in_artisan_folder ? '' : '../artisan/';
$view_base = $is_in_artisan_folder ? '../view/' : 'view/';
$login_base = $is_in_artisan_folder ? '../login/' : '../login/';
$index_base = $is_in_artisan_folder ? '../' : '../';
?>
<nav class="artisan-navbar">
  <div class="artisan-nav-container">
    <div class="artisan-logo-container">
      <a href="<?php echo $index_base; ?>index.php" class="artisan-logo-link">
        <div class="artisan-logo-symbol">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
          </svg>
        </div>
        <div class="artisan-logo-text">
          <div class="artisan-logo">Aya Crafts</div>
          <span class="artisan-logo-subtitle">ARTISAN PORTAL</span>
        </div>
      </a>
    </div>
    
    <!-- Desktop Navigation -->
    <div class="artisan-nav-menu" id="artisanNavMenu">
      <a href="<?php echo $artisan_base; ?>dashboard.php" class="artisan-nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
      </a>
      <a href="<?php echo $artisan_base; ?>my_products.php" class="artisan-nav-link <?php echo $current_page === 'my_products.php' ? 'active' : ''; ?>" title="My Products">
        <i class="fas fa-box"></i>
        <span class="nav-text-hide-md">Products</span>
      </a>
      <a href="<?php echo $artisan_base; ?>add_product.php" class="artisan-nav-link <?php echo $current_page === 'add_product.php' ? 'active' : ''; ?>" title="Add Product">
        <i class="fas fa-plus-circle"></i>
        <span class="nav-text-hide-md">Add</span>
      </a>
      <a href="<?php echo $artisan_base; ?>orders.php" class="artisan-nav-link <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>">
        <i class="fas fa-shopping-cart"></i>
        <span>Orders</span>
      </a>
      <a href="<?php echo $artisan_base; ?>profile.php" class="artisan-nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>
        <span>Profile</span>
      </a>
      <a href="<?php echo $artisan_base; ?>about.php" class="artisan-nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" title="My About Page">
        <i class="fas fa-book-open"></i>
        <span class="nav-text-hide-md">About</span>
      </a>
      <a href="<?php echo $artisan_base; ?>analytics.php" class="artisan-nav-link <?php echo $current_page === 'analytics.php' ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i>
        <span>Analytics</span>
      </a>
      <a href="<?php echo $view_base; ?>all_product.php" class="artisan-nav-link" title="View Store">
        <i class="fas fa-store"></i>
        <span class="nav-text-hide-md">Store</span>
      </a>
      <a href="<?php echo $login_base; ?>logout.php" class="artisan-nav-link logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
    
    <!-- Mobile Hamburger -->
    <button class="artisan-hamburger" id="artisanHamburger" aria-label="Toggle menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</nav>

