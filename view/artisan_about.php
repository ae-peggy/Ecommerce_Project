<?php
require_once '../settings/core.php';

$artisan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$artisan_id) {
    header('Location: ../index.php');
    exit;
}

require_once '../classes/artisan_class.php';
$artisan = new artisan_class();
$about_data = $artisan->get_artisan_about($artisan_id);

if (!$about_data) {
    header('Location: ../index.php');
    exit;
}

// Get full artisan info
$artisan_info = $artisan->get_artisan_by_id($artisan_id);
$tier_label = ($about_data['tier'] == 1) ? 'Tier 1 - Digitally Literate' : 'Tier 2 - Non-Digitally Literate';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($about_data['business_name'] ?? 'Artisan'); ?> - Aya Crafts</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .artisan-about-hero {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .artisan-about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 50%, #dc2626 100%);
        }
        .artisan-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 48px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 16px;
        }
        .artisan-location {
            font-size: 18px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }
        .artisan-tier-badge {
            display: inline-block;
            padding: 8px 20px;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 40px;
        }
        .story-section {
            max-width: 900px;
            margin: 0 auto;
            padding: 60px 0;
        }
        .story-section h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .story-section h2 i {
            color: var(--primary);
        }
        .story-content {
            font-size: 18px;
            line-height: 1.8;
            color: var(--text-secondary);
            margin-bottom: 40px;
        }
        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }
        .photo-gallery-item {
            aspect-ratio: 1;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .photo-gallery-item:hover {
            transform: translateY(-8px);
        }
        .photo-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 40px;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            gap: 12px;
        }
        @media (max-width: 768px) {
            .artisan-name {
                font-size: 36px;
            }
            .story-section {
                padding: 40px 0;
            }
            .story-content {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
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
                <a href="../index.php" class="artisan-nav-link">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="all_product.php" class="artisan-nav-link">
                    <i class="fas fa-store"></i>
                    <span>Shop</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="artisan-about-hero">
        <div class="artisan-main-content" style="max-width: 1200px;">
            <a href="javascript:history.back()" class="back-link">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h1 class="artisan-name"><?php echo htmlspecialchars($about_data['business_name'] ?? 'Artisan'); ?></h1>
            <?php if (!empty($about_data['artisan_location'])): ?>
                <div class="artisan-location">
                    <i class="fas fa-map-marker-alt"></i> 
                    <?php echo htmlspecialchars($about_data['artisan_location']); ?>
                </div>
            <?php endif; ?>
            <div class="artisan-tier-badge">
                <i class="fas fa-star"></i> <?php echo htmlspecialchars($tier_label); ?>
            </div>
            
            <!-- Profile Picture -->
            <div style="margin-top: 40px; display: flex; justify-content: center;">
                <?php
                // Get profile picture (first photo from artisan_photos or placeholder)
                $profile_picture = null;
                if (!empty($about_data['artisan_photos']) && is_array($about_data['artisan_photos']) && count($about_data['artisan_photos']) > 0) {
                    $profile_picture = $about_data['artisan_photos'][0];
                }
                
                $profile_picture_url = '';
                if ($profile_picture) {
                    $profile_picture_url = strpos($profile_picture, 'uploads/') === 0 ? '../' . ltrim($profile_picture, '/') : '../images/artisans/' . ltrim($profile_picture, '/');
                } else {
                    $profile_picture_url = 'https://via.placeholder.com/400x400?text=No+Profile+Picture';
                }
                ?>
                <div style="width: 300px; height: 300px; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); border: 4px solid rgba(220, 38, 38, 0.1);">
                    <img src="<?php echo htmlspecialchars($profile_picture_url); ?>" 
                         alt="Profile Picture"
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x400?text=Profile+Picture';">
                </div>
            </div>
        </div>
    </div>

    <!-- Story Sections -->
    <div class="artisan-main-content" style="max-width: 1200px;">
        <!-- Biography -->
        <?php if (!empty($about_data['artisan_bio'])): ?>
        <div class="story-section">
            <h2>
                <i class="fas fa-user-circle"></i>
                My Story
            </h2>
            <div class="story-content">
                <?php echo nl2br(htmlspecialchars($about_data['artisan_bio'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Cultural Meaning -->
        <?php if (!empty($about_data['cultural_meaning'])): ?>
        <div class="story-section">
            <h2>
                <i class="fas fa-heart"></i>
                Cultural Significance
            </h2>
            <div class="story-content">
                <?php echo nl2br(htmlspecialchars($about_data['cultural_meaning'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Crafting Method -->
        <?php if (!empty($about_data['crafting_method'])): ?>
        <div class="story-section">
            <h2>
                <i class="fas fa-tools"></i>
                My Crafting Process
            </h2>
            <div class="story-content">
                <?php echo nl2br(htmlspecialchars($about_data['crafting_method'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Photo Gallery -->
        <?php if (!empty($about_data['artisan_photos']) && is_array($about_data['artisan_photos']) && count($about_data['artisan_photos']) > 0): ?>
        <div class="story-section">
            <h2>
                <i class="fas fa-images"></i>
                Gallery
            </h2>
            <div class="photo-gallery">
                <?php foreach ($about_data['artisan_photos'] as $photo): 
                    $photo_url = strpos($photo, 'uploads/') === 0 ? '../' . ltrim($photo, '/') : '../images/artisans/' . ltrim($photo, '/');
                ?>
                    <div class="photo-gallery-item">
                        <img src="<?php echo htmlspecialchars($photo_url); ?>" 
                             alt="Artisan photo"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400?text=Photo';">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- View Products CTA -->
        <div class="artisan-section-card" style="text-align: center; margin-top: 60px;">
            <h2 style="margin-bottom: 24px;">Explore Their Products</h2>
            <p style="color: var(--text-secondary); margin-bottom: 32px;">
                Discover the beautiful handcrafted items created by <?php echo htmlspecialchars($about_data['business_name'] ?? 'this artisan'); ?>
            </p>
            <a href="all_product.php?artisan_id=<?php echo $artisan_id; ?>" 
               class="artisan-btn artisan-btn-primary">
                <i class="fas fa-shopping-bag"></i> View Products
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

