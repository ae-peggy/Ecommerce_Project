<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../settings/core.php';
require_admin('../login/login.php');

$admin_id = get_user_id();
$admin_info = get_customer_details($admin_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Admin Portal</title>
    <link rel="stylesheet" href="../css/admin_pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-user-circle"></i> My Profile</h1>
            <p class="page-subtitle">Manage your account credentials and personal information</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
            <!-- Personal Information -->
            <div class="section">
                <h2 class="section-title"><i class="fas fa-user"></i> Personal Information</h2>
                <form id="personalInfoForm">
                    <div class="form-group">
                        <label for="customer_name">Full Name *</label>
                        <input type="text" id="customer_name" name="customer_name" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($admin_info['customer_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_email">Email *</label>
                        <input type="email" id="customer_email" name="customer_email" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($admin_info['customer_email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_contact">Contact *</label>
                        <input type="tel" id="customer_contact" name="customer_contact" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($admin_info['customer_contact'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_country">Country</label>
                        <input type="text" id="customer_country" name="customer_country" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($admin_info['customer_country'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="customer_city">City</label>
                        <input type="text" id="customer_city" name="customer_city" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($admin_info['customer_city'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Personal Info
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="section">
                <h2 class="section-title"><i class="fas fa-lock"></i> Change Password</h2>
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="new_password" 
                               class="form-control" 
                               minlength="6" required>
                        <small style="color: #6b7280; display: block; margin-top: 4px;">Minimum 6 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="form-control" 
                               minlength="6" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Personal Information Form
        document.getElementById('personalInfoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update_personal');
            
            fetch('../actions/admin_profile_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Personal information updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update personal information'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Change Password Form
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match!');
                return;
            }
            
            if (newPassword.length < 6) {
                alert('Password must be at least 6 characters long!');
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'change_password');
            
            fetch('../actions/admin_profile_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password changed successfully!');
                    this.reset();
                } else {
                    alert('Error: ' + (data.message || 'Failed to change password'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    </script>
</body>
</html>

