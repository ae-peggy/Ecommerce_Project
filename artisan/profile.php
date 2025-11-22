<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();
$artisan_info = get_artisan_details($artisan_id);

// Safely get customer info - use user_id as customer_id
$customer_id = $_SESSION['user_id'] ?? null;
if ($customer_id) {
    $customer_info = get_customer_details($customer_id);
} else {
    $customer_info = []; // fallback
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Aya Crafts Artisan Portal</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="artisan-main-content">
        <div class="artisan-section-card">
            <h1>My Profile</h1>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
                <!-- Business Information -->
            <div class="artisan-section-card">
                    <h2><i class="fas fa-store"></i> Business Information</h2>
                    <form id="businessInfoForm">
                        <div class="artisan-form-group">
                            <label for="business_name">Business Name *</label>
                            <input type="text" id="business_name" name="business_name" 
                                   class="artisan-form-control" 
                                   value="<?php echo htmlspecialchars($artisan_info['business_name'] ?? ''); ?>" required>
                        </div>

                        <div class="artisan-form-group">
                            <label for="business_desc">Business Description</label>
                            <textarea id="business_desc" name="business_desc" 
                                      class="artisan-form-control" rows="4"><?php echo htmlspecialchars($artisan_info['business_desc'] ?? ''); ?></textarea>
                        </div>

                        <div class="artisan-form-group">
                            <label for="business_phone">Business Phone</label>
                            <input type="tel" id="business_phone" name="business_phone" 
                                   class="artisan-form-control" 
                                   value="<?php echo htmlspecialchars($artisan_info['business_phone'] ?? ''); ?>">
                        </div>

                        <div class="artisan-form-group">
                            <label for="business_address">Business Address</label>
                            <textarea id="business_address" name="business_address" 
                                      class="artisan-form-control" rows="3"><?php echo htmlspecialchars($artisan_info['business_address'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="artisan-btn artisan-btn-primary">
                            <i class="fas fa-save"></i> Update Business Info
                        </button>
                    </form>
                </div>

                <!-- Personal Information -->
                <div class="artisan-section-card">
                    <h2><i class="fas fa-user"></i> Personal Information</h2>
                    <form id="personalInfoForm">
                        <div class="artisan-form-group">
                            <label for="customer_name">Full Name *</label>
                            <input type="text" id="customer_name" name="customer_name" 
                                   class="artisan-form-control" 
                                   value="<?php echo htmlspecialchars($customer_info['customer_name'] ?? ''); ?>" required>
                        </div>

                        <div class="artisan-form-group">
                            <label for="customer_email">Email *</label>
                            <input type="email" id="customer_email" name="customer_email" 
                                   class="artisan-form-control" 
                                   value="<?php echo htmlspecialchars($customer_info['customer_email'] ?? ''); ?>" required>
                        </div>

                        <div class="artisan-form-group">
                            <label for="customer_contact">Contact *</label>
                            <input type="tel" id="customer_contact" name="customer_contact" 
                                   class="artisan-form-control" 
                                   value="<?php echo htmlspecialchars($customer_info['customer_contact'] ?? ''); ?>" required>
                        </div>

                        <div class="artisan-form-group">
                            <label for="customer_country">Country</label>
                            <input type="text" id="customer_country" name="customer_country" 
                                   class="artisan-form-control" 
                                   value="<?php echo htmlspecialchars($customer_info['customer_country'] ?? ''); ?>">
                        </div>

                        <div class="artisan-form-group">
                            <label for="customer_city">City</label>
                            <input type="text" id="customer_city" name="customer_city" 
                                   class="artisan-form-control" 
                                   value="<?php echo htmlspecialchars($customer_info['customer_city'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="artisan-btn artisan-btn-primary">
                            <i class="fas fa-save"></i> Update Personal Info
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="artisan-section-card">
                    <h2><i class="fas fa-lock"></i> Change Password</h2>
                    <form id="changePasswordForm">
                        <div class="artisan-form-group">
                            <label for="current_password">Current Password *</label>
                            <div style="position: relative;">
                            <input type="password" id="current_password" name="current_password" 
                                       class="artisan-form-control" required style="padding-right: 40px;">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('current_password', this)" 
                                   style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280;"></i>
                            </div>
                        </div>

                        <div class="artisan-form-group">
                            <label for="new_password">New Password *</label>
                            <div style="position: relative;">
                            <input type="password" id="new_password" name="new_password" 
                                       class="artisan-form-control" required minlength="6" style="padding-right: 40px;">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password', this)" 
                                   style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280;"></i>
                            </div>
                        </div>

                        <div class="artisan-form-group">
                            <label for="confirm_password">Confirm New Password *</label>
                            <div style="position: relative;">
                            <input type="password" id="confirm_password" name="confirm_password" 
                                       class="artisan-form-control" required minlength="6" style="padding-right: 40px;">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password', this)" 
                                   style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280;"></i>
                            </div>
                        </div>

                        <button type="submit" class="artisan-btn artisan-btn-primary">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>

                <!-- Account Status -->
                <div class="artisan-section-card">
                    <h2><i class="fas fa-info-circle"></i> Account Status</h2>
                    <div class="status-info">
                        <div class="status-item">
                            <span class="status-label">Account Type:</span>
                            <span class="artisan-badge artisan-badge-info">Tier 1 Artisan</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Status:</span>
                            <span class="artisan-badge artisan-badge-success">Active</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Member Since:</span>
                            <span><?php echo date('F Y', strtotime($artisan_info['created_date'] ?? 'now')); ?></span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Commission Rate:</span>
                            <span><?php echo ($artisan_info['commission_rate'] ?? 15); ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        // Update Business Info
        document.getElementById('businessInfoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_business');
            
            fetch('../actions/artisan_profile_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.success ? 'Business info updated successfully!' : 'Error: ' + data.message);
            });
        });

        // Update Personal Info
        document.getElementById('personalInfoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_personal');
            
            fetch('../actions/artisan_profile_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.success ? 'Personal info updated successfully!' : 'Error: ' + data.message);
            });
        });

        // Change Password
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match!');
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'change_password');
            
            fetch('../actions/artisan_profile_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password changed successfully!');
                    this.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
        
        // Mobile menu toggle
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });
        
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