<?php
require_once '../settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Aya Crafts | Authentic African Artistry</title>
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
        max-width: 1200px;
        margin: 80px auto;
        padding: 0 60px;
    }

    .hero-section {
        text-align: center;
        margin-bottom: 80px;
        padding: 60px 0;
    }

    .hero-section h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 64px;
        font-weight: 400;
        color: #111827;
        margin-bottom: 24px;
        line-height: 1.2;
    }

    .hero-section h1 strong {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 600;
        animation: gradientShift 4s ease infinite;
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

    .content-section {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
        border-radius: 24px;
        padding: 60px;
        margin-bottom: 40px;
        box-shadow: 
            0 20px 60px rgba(0, 0, 0, 0.08),
            0 0 0 1px rgba(220, 38, 38, 0.05);
        position: relative;
        overflow: hidden;
    }

    .content-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
    }

    .content-section h2 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 42px;
        font-weight: 500;
        color: #111827;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .content-section h2 i {
        color: #dc2626;
        font-size: 36px;
    }

    .content-section p {
        font-size: 18px;
        line-height: 1.8;
        color: #6b7280;
        margin-bottom: 20px;
    }

    .content-section ul {
        list-style: none;
        padding: 0;
        margin: 24px 0;
    }

    .content-section ul li {
        font-size: 18px;
        line-height: 1.8;
        color: #6b7280;
        padding: 12px 0;
        padding-left: 32px;
        position: relative;
    }

    .content-section ul li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #dc2626;
        font-weight: bold;
        font-size: 20px;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-top: 32px;
    }

    .value-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        text-align: center;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(220, 38, 38, 0.05);
        transition: all 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(220, 38, 38, 0.15);
    }

    .value-card i {
        font-size: 48px;
        color: #dc2626;
        margin-bottom: 20px;
    }

    .value-card h3 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 28px;
        font-weight: 500;
        color: #111827;
        margin-bottom: 12px;
    }

    .value-card p {
        font-size: 16px;
        color: #6b7280;
        margin: 0;
    }

    @media (max-width: 768px) {
        .nav-container {
            padding: 0 30px;
        }
        
        .logo {
            font-size: 26px;
        }
        
        .main-content {
            padding: 0 30px;
            margin: 40px auto;
        }
        
        .hero-section h1 {
            font-size: 42px;
        }
        
        .content-section {
            padding: 40px 30px;
        }
        
        .content-section h2 {
            font-size: 32px;
        }
        
        .values-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-container">
                <a href="../index.php" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                    <div class="logo-symbol">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <div class="logo">Aya Crafts</div>
                        <span class="logo-subtitle">Authentic Artistry</span>
                    </div>
                </a>
            </div>
            
            <div class="nav-buttons">
                <a href="all_product.php" class="nav-btn secondary"><span>Shop</span></a>
                <a href="about_us.php" class="nav-btn secondary"><span>About</span></a>
                <?php if (is_logged_in()): ?>
                    <a href="../login/logout.php" class="nav-btn"><span>Logout</span></a>
                <?php else: ?>
                    <a href="../login/login.php" class="nav-btn secondary"><span>Login</span></a>
                    <a href="../login/register.php" class="nav-btn"><span>Register</span></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1>About <strong>Aya Crafts</strong></h1>
            <div class="accent-bar"></div>
            <p style="font-size: 22px; color: #6b7280; max-width: 800px; margin: 0 auto;">
                Connecting Ghanaian artisans with global customers through authentic, story-driven commerce
            </p>
        </div>

        <!-- Vision Statement -->
        <div class="content-section">
            <h2><i class="fas fa-eye"></i> Our Vision</h2>
            <p style="font-size: 24px; font-weight: 500; color: #111827; font-style: italic;">
                "To become Africa's leading digital marketplace for authentic handcrafted goods, empowering artisans to reach global markets."
            </p>
        </div>

        <!-- Mission Statement -->
        <div class="content-section">
            <h2><i class="fas fa-bullseye"></i> Our Mission</h2>
            <p>At Aya Crafts, we are dedicated to:</p>
            <ul>
                <li><strong>Connecting Ghanaian artisans with global customers</strong> – We bridge the gap between talented local craftspeople and international buyers who value authentic, handmade products.</li>
                <li><strong>Ensuring fairness</strong> – Artisans keep 70–80% of their sales revenue, ensuring they receive fair compensation for their craftsmanship and cultural heritage.</li>
                <li><strong>Accessibility</strong> – Our platform is mobile-first, supporting multiple payment methods including Mobile Money (MoMo), Stripe, and PayPal to make transactions seamless for everyone.</li>
                <li><strong>Authenticity</strong> – We combine storytelling with verified artisan profiles, allowing customers to connect with the cultural significance and personal stories behind each handcrafted item.</li>
            </ul>
        </div>

        <!-- Core Values -->
        <div class="content-section">
            <h2><i class="fas fa-heart"></i> Our Core Values</h2>
            <p style="margin-bottom: 32px;">These principles guide everything we do:</p>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-balance-scale"></i>
                    <h3>Fairness</h3>
                    <p>We ensure artisans receive fair compensation, keeping 70–80% of sales revenue while we handle the rest.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Accessibility</h3>
                    <p>Mobile-first design and multiple payment options make our platform accessible to artisans and customers worldwide.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-certificate"></i>
                    <h3>Authenticity</h3>
                    <p>Every product comes with verified artisan stories, preserving cultural heritage and meaning.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-hands-helping"></i>
                    <h3>Cultural Preservation</h3>
                    <p>We celebrate and preserve traditional crafting methods and cultural significance of handmade goods.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-globe"></i>
                    <h3>Digital Inclusivity</h3>
                    <p>We support both digitally literate and non-digitally literate artisans through tiered service levels.</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="content-section" style="text-align: center; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white;">
            <h2 style="color: white; margin-bottom: 24px;">
                <i class="fas fa-handshake"></i> Join Our Mission
            </h2>
            <p style="font-size: 20px; color: rgba(255, 255, 255, 0.95); margin-bottom: 32px;">
                Whether you're an artisan looking to reach global markets or a customer seeking authentic handcrafted goods, 
                Aya Crafts is here to connect you.
            </p>
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <a href="all_product.php" class="nav-btn" style="background: white; color: #dc2626; border: none;">
                    <span>Shop Now</span>
                </a>
                <?php if (!is_logged_in()): ?>
                <a href="../login/register.php" class="nav-btn secondary" style="background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid white;">
                    <span>Become an Artisan</span>
                </a>
                <?php endif; ?>
                <a href="tiers.php" class="nav-btn secondary" style="background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid white;">
                    <span>View Artisan Tiers</span>
                </a>
            </div>
        </div>
    </main>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>

