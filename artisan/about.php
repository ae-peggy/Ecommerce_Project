<?php
require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();
$artisan_tier = $_SESSION['artisan_tier'] ?? 1;

// Block tier 2 artisans from accessing this page
if ($artisan_tier == 2) {
    header('Location: dashboard.php?error=access_denied');
    exit();
}

require_once '../classes/artisan_class.php';
$artisan = new artisan_class();
$about_data = $artisan->get_artisan_about($artisan_id);

// Get artisan info for display
$artisan_info = $artisan->get_artisan_by_id($artisan_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My About Page - Aya Crafts Artisan Portal</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .photo-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
        }
        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-item .remove-photo {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .photo-item .remove-photo:hover {
            background: #dc2626;
            transform: scale(1.1);
        }
        .upload-area {
            border: 2px dashed #e5e7eb;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background: #fafafa;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: var(--primary);
            background: rgba(220, 38, 38, 0.02);
        }
        .upload-area.dragover {
            border-color: var(--primary);
            background: rgba(220, 38, 38, 0.05);
        }
        .preview-section {
            margin-top: 24px;
            padding: 24px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="artisan-main-content">
        <div class="artisan-section-card">
            <h1>My About Page</h1>
            <p style="color: var(--text-secondary); margin-top: 8px;">
                Share your story and connect with customers through authentic storytelling
            </p>
        </div>

        <form id="aboutForm" class="artisan-section-card">
            <input type="hidden" name="action" value="save">
            
            <!-- Artisan Biography -->
            <div class="artisan-form-group">
                <label for="artisan_bio">
                    <i class="fas fa-user-circle" style="color: var(--primary);"></i> 
                    Your Biography & Story *
                </label>
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">
                    Tell customers about yourself, your background, and what drives your passion for crafting.
                </p>
                <textarea id="artisan_bio" name="artisan_bio" 
                          class="artisan-form-control" 
                          rows="6" 
                          placeholder="Share your personal story, background, and what inspired you to become an artisan..."
                          required><?php echo htmlspecialchars($about_data['artisan_bio'] ?? ''); ?></textarea>
            </div>

            <!-- Cultural Meaning -->
            <div class="artisan-form-group">
                <label for="cultural_meaning">
                    <i class="fas fa-heart" style="color: var(--primary);"></i> 
                    Cultural Significance *
                </label>
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">
                    Explain the cultural meaning, traditions, and symbolism behind your craft.
                </p>
                <textarea id="cultural_meaning" name="cultural_meaning" 
                          class="artisan-form-control" 
                          rows="5" 
                          placeholder="Describe the cultural traditions, meanings, and significance of your craft..."
                          required><?php echo htmlspecialchars($about_data['cultural_meaning'] ?? ''); ?></textarea>
            </div>

            <!-- Crafting Method -->
            <div class="artisan-form-group">
                <label for="crafting_method">
                    <i class="fas fa-tools" style="color: var(--primary);"></i> 
                    Crafting Method & Process *
                </label>
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">
                    Describe how you create your products, the techniques you use, and your creative process.
                </p>
                <textarea id="crafting_method" name="crafting_method" 
                          class="artisan-form-control" 
                          rows="5" 
                          placeholder="Explain your crafting techniques, materials used, and step-by-step process..."
                          required><?php echo htmlspecialchars($about_data['crafting_method'] ?? ''); ?></textarea>
            </div>

            <!-- Location -->
            <div class="artisan-form-group">
                <label for="artisan_location">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> 
                    Location
                </label>
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">
                    Your city, region, or community where you craft (e.g., "Accra, Ghana" or "Kumasi, Ashanti Region").
                </p>
                <input type="text" id="artisan_location" name="artisan_location" 
                       class="artisan-form-control" 
                       placeholder="e.g., Accra, Ghana"
                       value="<?php echo htmlspecialchars($about_data['artisan_location'] ?? ''); ?>">
            </div>

            <!-- Photo Gallery -->
            <div class="artisan-form-group">
                <label>
                    <i class="fas fa-images" style="color: var(--primary);"></i> 
                    Artisan Photos
                </label>
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px;">
                    Upload high-quality photos of yourself, your workspace, or your crafting process (up to 10 photos).
                </p>
                
                <!-- Photo Upload Area -->
                <div class="upload-area" id="uploadArea">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--primary); margin-bottom: 12px;"></i>
                    <div style="font-weight: 500; margin-bottom: 4px;">Click to upload or drag and drop photos</div>
                    <small style="color: #6b7280;">PNG, JPG, JPEG, WEBP (MAX. 5MB each)</small>
                    <input type="file" id="photoUpload" multiple accept="image/*" style="display: none;">
                </div>

                <!-- Photo Gallery Display -->
                <div class="photo-gallery" id="photoGallery">
                    <?php 
                    $photos = $about_data['artisan_photos'] ?? [];
                    foreach ($photos as $index => $photo): 
                        $photo_url = strpos($photo, 'uploads/') === 0 ? '../' . ltrim($photo, '/') : '../images/artisans/' . ltrim($photo, '/');
                    ?>
                        <div class="photo-item" data-photo-index="<?php echo $index; ?>">
                            <img src="<?php echo htmlspecialchars($photo_url); ?>" 
                                 alt="Artisan photo"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=Photo';">
                            <button type="button" class="remove-photo" onclick="removePhoto(<?php echo $index; ?>)">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="hidden" name="artisan_photos[]" value="<?php echo htmlspecialchars($photo); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="preview-section">
                <h3 style="margin-bottom: 16px; font-family: 'Cormorant Garamond', serif;">
                    <i class="fas fa-eye" style="color: var(--primary);"></i> Preview Your About Page
                </h3>
                <a href="../view/artisan_about.php?id=<?php echo $artisan_id; ?>" 
                   target="_blank" 
                   class="artisan-btn artisan-btn-secondary">
                    <i class="fas fa-external-link-alt"></i> View Public Page
                </a>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="artisan-btn artisan-btn-primary">
                    <i class="fas fa-save"></i> Save About Page
                </button>
                <button type="button" class="artisan-btn artisan-btn-secondary" onclick="window.location.reload()">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </form>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });

        const uploadArea = document.getElementById('uploadArea');
        const photoUpload = document.getElementById('photoUpload');
        const photoGallery = document.getElementById('photoGallery');
        let photoFiles = [];

        // Click to upload
        uploadArea.addEventListener('click', () => photoUpload.click());

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        photoUpload.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            const maxPhotos = 10;
            const currentCount = photoGallery.querySelectorAll('.photo-item').length;
            
            if (currentCount + files.length > maxPhotos) {
                AdminUI.toast(`Maximum ${maxPhotos} photos allowed. Please remove some photos first.`, 'error');
                return;
            }

            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) {
                    AdminUI.toast(`${file.name} is not an image file.`, 'error');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    AdminUI.toast(`${file.name} is too large (max 5MB).`, 'error');
                    return;
                }

                uploadPhoto(file);
            });
        }

        function uploadPhoto(file) {
            const formData = new FormData();
            formData.append('action', 'upload_artisan_photo');
            formData.append('photo', file);

            fetch('../actions/upload_artisan_photo_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addPhotoToGallery(data.path);
                    AdminUI.toast('Photo uploaded successfully', 'success');
                } else {
                    AdminUI.toast(data.message || 'Failed to upload photo', 'error');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                AdminUI.toast('Error uploading photo', 'error');
            });
        }

        function addPhotoToGallery(photoPath) {
            const photoItem = document.createElement('div');
            photoItem.className = 'photo-item';
            photoItem.innerHTML = `
                <img src="../${photoPath}" alt="Artisan photo" 
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=Photo';">
                <button type="button" class="remove-photo" onclick="removePhotoFromGallery(this)">
                    <i class="fas fa-times"></i>
                </button>
                <input type="hidden" name="artisan_photos[]" value="${photoPath}">
            `;
            photoGallery.appendChild(photoItem);
        }

        function removePhoto(index) {
            const photoItem = photoGallery.querySelector(`[data-photo-index="${index}"]`);
            if (photoItem) {
                photoItem.remove();
            }
        }

        function removePhotoFromGallery(button) {
            button.closest('.photo-item').remove();
        }

        // Form submission
        document.getElementById('aboutForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            
            // Collect photo paths
            const photoInputs = photoGallery.querySelectorAll('input[name="artisan_photos[]"]');
            const photos = Array.from(photoInputs).map(input => input.value);
            formData.append('artisan_photos', JSON.stringify(photos));

            fetch('../actions/artisan_about_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    AdminUI.toast('About page saved successfully!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    AdminUI.toast(data.message || 'Failed to save about page', 'error');
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                AdminUI.toast('Error saving about page', 'error');
            });
        });
    </script>
</body>
</html>

