<?php
require_once '../settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Tiers - Aya Crafts</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .tiers-hero {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .tiers-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
        }

        .tiers-hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 56px;
            font-weight: 500;
            color: #111827;
            margin-bottom: 16px;
        }

        .tiers-hero p {
            font-size: 20px;
            color: #6b7280;
            max-width: 700px;
            margin: 0 auto;
        }

        .pricing-container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 40px;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }

        .pricing-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(220, 38, 38, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 30px 80px rgba(220, 38, 38, 0.15),
                0 0 0 1px rgba(220, 38, 38, 0.1);
        }

        .pricing-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
        }

        .pricing-card.featured {
            border: 2px solid rgba(220, 38, 38, 0.2);
            transform: scale(1.05);
        }

        .pricing-card.featured::after {
            content: 'POPULAR';
            position: absolute;
            top: 24px;
            right: -40px;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 8px 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            transform: rotate(45deg);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        .tier-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .tier-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 500;
            color: #111827;
            margin-bottom: 12px;
        }

        .tier-subtitle {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .tier-price {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .price-amount {
            font-size: 48px;
            font-weight: 700;
            color: #dc2626;
        }

        .price-unit {
            font-size: 20px;
            color: #6b7280;
        }

        .price-note {
            font-size: 14px;
            color: #9ca3af;
            text-align: center;
        }

        .tier-features {
            margin: 32px 0;
        }

        .tier-features h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px;
            font-weight: 500;
            color: #111827;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tier-features h3 i {
            color: #dc2626;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list li {
            padding: 12px 0;
            padding-left: 32px;
            position: relative;
            font-size: 16px;
            color: #374151;
            line-height: 1.6;
        }

        .feature-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
            font-size: 20px;
        }

        .feature-list li.artisan-responsibility {
            color: #6b7280;
            font-style: italic;
        }

        .feature-list li.artisan-responsibility::before {
            content: '→';
            color: #dc2626;
        }

        .tier-cta {
            margin-top: 32px;
            text-align: center;
        }

        .tier-btn {
            display: inline-block;
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .tier-btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
        }

        .tier-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.4);
        }

        .tier-btn-secondary {
            background: white;
            color: #374151;
            border-color: #e5e7eb;
        }

        .tier-btn-secondary:hover {
            border-color: #dc2626;
            color: #dc2626;
            transform: translateY(-2px);
        }

        .comparison-section {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            border-radius: 24px;
            padding: 48px;
            margin-top: 60px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(220, 38, 38, 0.05);
        }

        .comparison-section h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 500;
            color: #111827;
            text-align: center;
            margin-bottom: 40px;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
        }

        .comparison-table th {
            padding: 20px;
            text-align: left;
            font-weight: 600;
            color: #111827;
            border-bottom: 2px solid #e5e7eb;
        }

        .comparison-table td {
            padding: 20px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }

        .comparison-table tr:last-child td {
            border-bottom: none;
        }

        .comparison-table .tier-col {
            font-weight: 600;
            color: #dc2626;
            width: 30%;
        }

        @media (max-width: 968px) {
            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .pricing-card.featured {
                transform: scale(1);
            }

            .pricing-container {
                padding: 0 20px;
            }

            .tiers-hero h1 {
                font-size: 42px;
            }

            .comparison-table {
                font-size: 14px;
            }

            .comparison-table th,
            .comparison-table td {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <?php 
    // Use artisan navigation if logged in as artisan, otherwise use public nav
    if (is_artisan()) {
        include '../artisan/includes/nav.php';
    } else {
    ?>
    <!-- Public Navigation -->
    <nav class="artisan-navbar">
        <div class="artisan-nav-container">
            <div class="artisan-logo-container">
                <a href="../index.php" class="artisan-logo-link">
                    <div class="artisan-logo-symbol">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
                        </svg>
                    </div>
                    <div class="artisan-logo-text">
                        <div class="artisan-logo">Aya Crafts</div>
                        <span class="artisan-logo-subtitle">AUTHENTIC ARTISTRY</span>
                    </div>
                </a>
            </div>
            <div class="artisan-nav-menu">
                <a href="all_product.php" class="artisan-nav-link">
                    <i class="fas fa-store"></i>
                    <span>Shop</span>
                </a>
                <a href="about_us.php" class="artisan-nav-link">
                    <i class="fas fa-info-circle"></i>
                    <span>About</span>
                </a>
                <?php if (is_logged_in()): ?>
                    <a href="../login/logout.php" class="artisan-nav-link logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a href="../login/login.php" class="artisan-nav-link">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    <a href="../login/register.php" class="artisan-nav-link">
                        <i class="fas fa-user-plus"></i>
                        <span>Register</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <?php } ?>

    <!-- Hero Section -->
    <div class="tiers-hero">
        <div class="pricing-container">
            <h1>Choose Your Artisan Tier</h1>
            <p>Select the tier that best fits your digital literacy and business needs. Both tiers offer fair commission rates and comprehensive platform support.</p>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="pricing-container">
        <div class="pricing-grid">
            <!-- Tier 1 -->
            <div class="pricing-card">
                <div class="tier-header">
                    <div class="tier-name">Tier 1</div>
                    <div class="tier-subtitle">Digitally Literate Artisans</div>
                    <div class="tier-price">
                        <span class="price-amount">20%</span>
                        <span class="price-unit">commission</span>
                    </div>
                    <div class="price-note">You keep 80% of each sale</div>
                </div>

                <div class="tier-features">
                    <h3><i class="fas fa-user-cog"></i> Your Responsibilities</h3>
                    <ul class="feature-list">
                        <li class="artisan-responsibility">Manage your own product listings</li>
                        <li class="artisan-responsibility">Update inventory and stock levels</li>
                        <li class="artisan-responsibility">Handle product updates and descriptions</li>
                        <li class="artisan-responsibility">Upload your own product photos</li>
                    </ul>

                    <h3 style="margin-top: 32px;"><i class="fas fa-hands-helping"></i> Platform Provides</h3>
                    <ul class="feature-list">
                        <li>Secure payment processing</li>
                        <li>Customer service support</li>
                        <li>Logistics and shipping coordination</li>
                        <li>Marketing and promotion</li>
                        <li>Global marketplace access</li>
                        <li>Analytics and sales reports</li>
                    </ul>
                </div>

                <div class="tier-cta">
                    <?php if (is_logged_in() && is_artisan()): ?>
                        <a href="../artisan/profile.php" class="tier-btn tier-btn-secondary">
                            <i class="fas fa-check"></i> Current Tier
                        </a>
                    <?php else: ?>
                        <a href="../login/register.php" class="tier-btn tier-btn-primary">
                            <i class="fas fa-arrow-right"></i> Get Started
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tier 2 -->
            <div class="pricing-card featured">
                <div class="tier-header">
                    <div class="tier-name">Tier 2</div>
                    <div class="tier-subtitle">Non-Digitally Literate Artisans</div>
                    <div class="tier-price">
                        <span class="price-amount">30%</span>
                        <span class="price-unit">commission</span>
                    </div>
                    <div class="price-note">You keep 70% of each sale</div>
                    <div style="margin-top: 16px; padding: 12px; background: rgba(220, 38, 38, 0.1); border-radius: 8px;">
                        <strong style="color: #dc2626;">Setup Fee: GH₵250</strong>
                        <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0 0;">One-time fee for initial setup</p>
                    </div>
                </div>

                <div class="tier-features">
                    <h3><i class="fas fa-user-cog"></i> Your Responsibilities</h3>
                    <ul class="feature-list">
                        <li class="artisan-responsibility">Provide product information</li>
                        <li class="artisan-responsibility">Coordinate with platform team</li>
                        <li class="artisan-responsibility">Fulfill orders when received</li>
                    </ul>

                    <h3 style="margin-top: 32px;"><i class="fas fa-hands-helping"></i> Platform Provides</h3>
                    <ul class="feature-list">
                        <li>Professional product photography</li>
                        <li>Complete listing creation and management</li>
                        <li>Inventory tracking and updates</li>
                        <li>Logistics coordination</li>
                        <li>Marketing & promotional support</li>
                        <li>Customer service handling</li>
                        <li>Secure payment processing</li>
                        <li>Global marketplace access</li>
                        <li>Regular sales reports</li>
                    </ul>
                </div>

                <div class="tier-cta">
                    <?php if (is_logged_in() && is_artisan()): ?>
                        <a href="../artisan/profile.php" class="tier-btn tier-btn-secondary">
                            <i class="fas fa-check"></i> Current Tier
                        </a>
                    <?php else: ?>
                        <a href="../login/register.php" class="tier-btn tier-btn-primary">
                            <i class="fas fa-arrow-right"></i> Get Started
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Comparison Table -->
        <div class="comparison-section">
            <h2>Side-by-Side Comparison</h2>
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th class="tier-col">Tier 1</th>
                        <th class="tier-col">Tier 2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Commission Rate</strong></td>
                        <td>20% per sale</td>
                        <td>30% per sale</td>
                    </tr>
                    <tr>
                        <td><strong>Artisan Keeps</strong></td>
                        <td>80% of revenue</td>
                        <td>70% of revenue</td>
                    </tr>
                    <tr>
                        <td><strong>Setup Fee</strong></td>
                        <td>Free</td>
                        <td>GH₵250 (one-time)</td>
                    </tr>
                    <tr>
                        <td><strong>Product Photography</strong></td>
                        <td>Self-managed</td>
                        <td>Platform provides</td>
                    </tr>
                    <tr>
                        <td><strong>Listing Creation</strong></td>
                        <td>Self-managed</td>
                        <td>Platform provides</td>
                    </tr>
                    <tr>
                        <td><strong>Inventory Management</strong></td>
                        <td>Self-managed</td>
                        <td>Platform manages</td>
                    </tr>
                    <tr>
                        <td><strong>Payment Processing</strong></td>
                        <td>Platform provides</td>
                        <td>Platform provides</td>
                    </tr>
                    <tr>
                        <td><strong>Customer Service</strong></td>
                        <td>Platform provides</td>
                        <td>Platform provides</td>
                    </tr>
                    <tr>
                        <td><strong>Logistics & Shipping</strong></td>
                        <td>Platform coordinates</td>
                        <td>Platform coordinates</td>
                    </tr>
                    <tr>
                        <td><strong>Marketing Support</strong></td>
                        <td>Platform provides</td>
                        <td>Platform provides</td>
                    </tr>
                    <tr>
                        <td><strong>Best For</strong></td>
                        <td>Artisans comfortable with technology</td>
                        <td>Artisans who need full support</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Additional Info -->
        <div class="artisan-section-card" style="margin-top: 60px; text-align: center;">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 36px; margin-bottom: 24px;">
                <i class="fas fa-question-circle" style="color: var(--primary);"></i> 
                Not Sure Which Tier?
            </h2>
            <p style="font-size: 18px; color: var(--text-secondary); margin-bottom: 32px; max-width: 700px; margin-left: auto; margin-right: auto;">
                If you're comfortable using smartphones, computers, and managing your own online listings, Tier 1 is perfect for you. 
                If you prefer hands-on support and want the platform to handle the technical aspects, Tier 2 is your best choice.
            </p>
            <a href="about_us.php" class="artisan-btn artisan-btn-secondary">
                <i class="fas fa-info-circle"></i> Learn More About Aya Crafts
            </a>
        </div>
    </div>

    <script>
        // Mobile menu toggle (if hamburger exists)
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });
    </script>
</body>
</html>

