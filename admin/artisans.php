<?php
require_once '../settings/core.php';
require_admin('../login/login.php');

$artisans = get_all_artisans();

// Calculate stats
$total_artisans = count($artisans);
$approved_count = count(array_filter($artisans, fn($a) => $a['approval_status'] == 'approved'));
$pending_count = count(array_filter($artisans, fn($a) => $a['approval_status'] == 'pending'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Management | Aya Crafts</title>
    <link rel="stylesheet" href="../css/admin_pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Artisan Management</h1>
            <p class="page-subtitle">Manage and oversee all registered artisans on the platform</p>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_artisans; ?></div>
                    <div class="stat-label">Total Artisans</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $approved_count; ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $pending_count; ?></div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>
            
            <?php if ($pending_count > 0): ?>
            <div style="margin-top: 20px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <button onclick="selectAllPending()" class="btn btn-sm" style="padding: 10px 20px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-check-square"></i> Select All Pending
                </button>
                <button onclick="bulkApprove()" id="bulkApproveBtn" class="btn btn-sm" style="padding: 10px 20px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: none;">
                    <i class="fas fa-check-double"></i> Approve Selected (<span id="selectedCount">0</span>)
                </button>
                <button onclick="clearSelection()" id="clearSelectionBtn" class="btn btn-sm" style="padding: 10px 20px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #374151; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: none;">
                    <i class="fas fa-times"></i> Clear Selection
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Add Artisan Section -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 class="section-title">Artisan List</h2>
                <button onclick="showAddArtisanModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Artisan
                </button>
            </div>

            <!-- Search Box -->
            <div class="filter-bar" style="margin-bottom: 25px;">
                <div class="search-box" style="flex: 1; min-width: 300px; position: relative;">
                    <input type="text" id="searchInput" placeholder="Search artisans by name, email, business name..." onkeyup="searchTable()" style="width: 100%; padding-right: 45px;">
                    <i class="fas fa-search" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none;"></i>
                </div>
            </div>

            <!-- Artisans Table -->
            <?php if (empty($artisans)): ?>
                <div class="no-data">
                    <div style="font-size: 4rem; margin-bottom: 20px;">👥</div>
                    <h3>No Artisans Yet</h3>
                    <p>Start by adding your first artisan above.</p>
                </div>
            <?php else: ?>
                <table class="table" id="artisansTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Business Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Tier</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($artisans as $artisan): ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php if ($artisan['approval_status'] == 'pending'): ?>
                                <input type="checkbox" class="artisan-checkbox" value="<?php echo $artisan['artisan_id']; ?>" onchange="updateBulkActions()" style="cursor: pointer;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $artisan['artisan_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($artisan['customer_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($artisan['business_name']); ?></td>
                            <td><?php echo htmlspecialchars($artisan['customer_email']); ?></td>
                            <td><?php echo htmlspecialchars($artisan['customer_contact'] ?? 'N/A'); ?></td>
                            <td>
                                <span style="display: inline-block; padding: 4px 20px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border-radius: 50px; font-size: 12px; font-weight: 600;">
                                    <?php echo $artisan['product_count'] ?? 0; ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $status = $artisan['approval_status'];
                                $status_class = $status == 'approved' ? 'success' : ($status == 'pending' ? 'warning' : 'danger');
                                $status_colors = [
                                    'success' => 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)',
                                    'warning' => 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)',
                                    'danger' => 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)'
                                ];
                                $status_text_colors = [
                                    'success' => '#065f46',
                                    'warning' => '#92400e',
                                    'danger' => '#991b1b'
                                ];
                                ?>
                                <span style="display: inline-block; padding: 6px 6px; background: <?php echo $status_colors[$status_class]; ?>; color: <?php echo $status_text_colors[$status_class]; ?>; border-radius: 50px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td>
                                <span style="display: inline-block; padding: 4px 10px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); color: #dc2626; border-radius: 50px; font-size: 12px; font-weight: 600;">
                                    Tier <?php echo $artisan['tier']; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($artisan['created_date'])); ?></td>
                            <td>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <a href="#" class="btn btn-sm" onclick="viewArtisan(<?php echo $artisan['artisan_id']; ?>); return false;" title="View" style="padding: 8px 12px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border: none;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm" onclick="editArtisan(<?php echo $artisan['artisan_id']; ?>); return false;" title="Edit" style="padding: 8px 12px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; border: none;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($artisan['tier'] == 2): ?>
                                    <a href="#" class="btn btn-sm" onclick="manageAbout(<?php echo $artisan['artisan_id']; ?>); return false;" title="Manage About Page" style="padding: 8px 12px; background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%); color: #6b21a8; border: none;">
                                        <i class="fas fa-book-open"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($artisan['approval_status'] == 'pending'): ?>
                                    <a href="#" class="btn btn-sm" onclick="approveArtisan(<?php echo $artisan['artisan_id']; ?>); return false;" title="Approve" style="padding: 8px 12px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: none;">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="#" class="btn btn-sm" onclick="deleteArtisan(<?php echo $artisan['artisan_id']; ?>); return false;" title="Delete" style="padding: 8px 12px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border: none;">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- View Artisan Modal -->
    <div id="viewArtisanModal" class="modal">
        <div class="modal-content" style="max-width: 700px;">
            <span class="modal-close" onclick="closeViewModal()">&times;</span>
            <h2 class="section-title">Artisan Details</h2>
            
            <div id="viewArtisanContent" style="padding: 20px 0;">
                <!-- Content will be loaded here -->
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 25px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Artisan Modal -->
    <div id="editArtisanModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeEditModal()">&times;</span>
            <h2 class="section-title">Edit Artisan</h2>
            
            <form id="editArtisanForm">
                <input type="hidden" id="edit_artisan_id" name="artisan_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_customer_name">Full Name *</label>
                        <input type="text" id="edit_customer_name" name="customer_name" required>
                        <div class="error-message" id="edit_customer_name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_customer_email">Email *</label>
                        <input type="email" id="edit_customer_email" name="customer_email" required>
                        <div class="error-message" id="edit_customer_email-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_customer_contact">Contact *</label>
                        <input type="tel" id="edit_customer_contact" name="customer_contact" required>
                        <div class="error-message" id="edit_customer_contact-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_business_name">Business Name *</label>
                        <input type="text" id="edit_business_name" name="business_name" required>
                        <div class="error-message" id="edit_business_name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_tier">Tier *</label>
                        <select id="edit_tier" name="tier" required>
                            <option value="1">Tier 1 - Digitally Literate</option>
                            <option value="2">Tier 2 - Non-Digitally Literate</option>
                        </select>
                        <div class="error-message" id="edit_tier-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_commission_rate">Commission Rate (%)</label>
                        <input type="number" id="edit_commission_rate" name="commission_rate" step="0.01" min="0" max="100">
                        <div class="error-message" id="edit_commission_rate-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_approval_status">Approval Status</label>
                        <select id="edit_approval_status" name="approval_status">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <div class="error-message" id="edit_approval_status-error"></div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">Update Artisan</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Manage About Modal -->
    <div id="manageAboutModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="modal-close" onclick="closeManageAboutModal()">&times;</span>
            <h2 class="section-title">Manage Artisan About Page</h2>
            
            <form id="manageAboutForm">
                <input type="hidden" id="about_artisan_id" name="artisan_id">
                
                <div class="form-group">
                    <label for="artisan_bio">Artisan Bio *</label>
                    <textarea id="artisan_bio" name="artisan_bio" rows="4" class="form-control" placeholder="Tell the story of this artisan..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="cultural_meaning">Cultural Meaning</label>
                    <textarea id="cultural_meaning" name="cultural_meaning" rows="4" class="form-control" placeholder="Describe the cultural significance of their work..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="crafting_method">Crafting Method</label>
                    <textarea id="crafting_method" name="crafting_method" rows="4" class="form-control" placeholder="Describe how they create their products..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="artisan_location">Location</label>
                    <input type="text" id="artisan_location" name="artisan_location" class="form-control" placeholder="e.g., Accra, Ghana">
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">Save About Page</button>
                    <button type="button" class="btn btn-secondary" onclick="closeManageAboutModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Artisan Modal -->
    <div id="addArtisanModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 class="section-title">Add New Artisan</h2>
            
            <form id="addArtisanForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="customer_name">Full Name *</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                        <div class="error-message" id="customer_name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="customer_email">Email *</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                        <div class="error-message" id="customer_email-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="customer_contact">Contact *</label>
                        <input type="tel" id="customer_contact" name="customer_contact" required>
                        <div class="error-message" id="customer_contact-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="business_name">Business Name *</label>
                        <input type="text" id="business_name" name="business_name" required>
                        <div class="error-message" id="business_name-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="tier">Tier *</label>
                        <select id="tier" name="tier" required>
                            <option value="1">Tier 1 - Digitally Literate</option>
                            <option value="2">Tier 2 - Non-Digitally Literate</option>
                        </select>
                        <div class="error-message" id="tier-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="commission_rate">Commission Rate (%)</label>
                        <input type="number" id="commission_rate" name="commission_rate" value="20" step="0.01" min="0" max="100">
                        <small style="color: #9ca3af; font-size: 13px; margin-top: 8px; display: block;">
                            Default: 20% for Tier 1, 30% for Tier 2
                        </small>
                        <div class="error-message" id="commission_rate-error"></div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">Add Artisan</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Credentials Modal -->
    <div id="credentialsModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="modal-close" onclick="closeCredentialsModal()">&times;</span>
            <h2 class="section-title" style="color: #dc2626; margin-bottom: 10px;">
                <i class="fas fa-key" style="margin-right: 10px;"></i>Login Credentials
            </h2>
            <p style="color: #6b7280; margin-bottom: 25px; font-size: 14px;">
                Artisan account created successfully! Please share these credentials with the artisan.
            </p>
            
            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0; color: #92400e; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>Important
                </p>
                <p style="margin: 8px 0 0 0; color: #78350f; font-size: 14px;">
                    Save these credentials now. They will not be shown again. The artisan should change their password after first login.
                </p>
            </div>
            
            <div style="display: grid; gap: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                        <i class="fas fa-user" style="margin-right: 8px;"></i>Artisan Name
                    </label>
                    <div id="credName" style="padding: 12px 16px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; color: #1a1a1a; font-weight: 500;"></div>
                </div>
                
                <div>
                    <label style="display: block; font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                        <i class="fas fa-envelope" style="margin-right: 8px;"></i>Email Address
                    </label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="text" id="credEmail" readonly style="flex: 1; padding: 12px 16px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; color: #1a1a1a; font-weight: 500; font-family: monospace;">
                        <button onclick="copyToClipboard('credEmail')" class="btn btn-sm" style="padding: 12px 16px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; white-space: nowrap;">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                        <i class="fas fa-lock" style="margin-right: 8px;"></i>Temporary Password
                    </label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="text" id="credPassword" readonly style="flex: 1; padding: 12px 16px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; color: #1a1a1a; font-weight: 600; font-family: monospace; letter-spacing: 2px;">
                        <button onclick="copyToClipboard('credPassword')" class="btn btn-sm" style="padding: 12px 16px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; white-space: nowrap;">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
                <button onclick="copyAllCredentials()" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-copy"></i> Copy All
                </button>
                <button onclick="closeCredentialsModal()" class="btn btn-secondary">
                    Done
                </button>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        function showAddArtisanModal() {
            document.getElementById('addArtisanModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addArtisanModal').style.display = 'none';
            document.getElementById('addArtisanForm').reset();
        }

        function showCredentialsModal(credentials) {
            document.getElementById('credName').textContent = credentials.name || 'N/A';
            document.getElementById('credEmail').value = credentials.email || '';
            document.getElementById('credPassword').value = credentials.password || '';
            document.getElementById('credentialsModal').style.display = 'block';
        }

        function closeCredentialsModal() {
            document.getElementById('credentialsModal').style.display = 'none';
            // Reload page to show new artisan in list
            setTimeout(() => location.reload(), 300);
        }

        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            element.select();
            element.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                document.execCommand('copy');
                
                // Show feedback - find the button that called this function
                const buttons = document.querySelectorAll('button[onclick*="copyToClipboard"]');
                buttons.forEach(btn => {
                    if (btn.getAttribute('onclick').includes(elementId)) {
                        const originalHtml = btn.innerHTML;
                        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                        btn.style.background = 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)';
                        btn.style.color = '#065f46';
                        
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.style.background = 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)';
                            btn.style.color = '#1e40af';
                        }, 2000);
                    }
                });
            } catch (err) {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard');
            }
        }

        function copyAllCredentials() {
            const email = document.getElementById('credEmail').value;
            const password = document.getElementById('credPassword').value;
            const name = document.getElementById('credName').textContent;
            
            const text = `Artisan Login Credentials\n\nName: ${name}\nEmail: ${email}\nPassword: ${password}\n\nPlease change your password after first login.`;
            
            // Create temporary textarea to copy
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                document.body.removeChild(textarea);
                
                // Show feedback - find the "Copy All" button
                const btn = document.querySelector('button[onclick="copyAllCredentials()"]');
                if (btn) {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> All Copied!';
                    btn.style.background = 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)';
                    
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.style.background = '';
                    }, 2000);
                }
            } catch (err) {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard');
                document.body.removeChild(textarea);
            }
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('artisansTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < td.length; j++) {
                    if (td[j] && td[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                tr[i].style.display = found ? '' : 'none';
            }
        }

        function viewArtisan(id) {
            fetch('../actions/admin_artisan_actions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get&artisan_id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const artisan = data.data;
                    const content = document.getElementById('viewArtisanContent');
                    
                    const statusColors = {
                        'approved': { bg: 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)', text: '#065f46' },
                        'pending': { bg: 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)', text: '#92400e' },
                        'suspended': { bg: 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)', text: '#991b1b' }
                    };
                    
                    const status = artisan.approval_status || 'pending';
                    const statusStyle = statusColors[status] || statusColors.pending;
                    
                    content.innerHTML = `
                        <div style="display: grid; gap: 20px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</label>
                                    <div style="margin-top: 8px; font-size: 16px; color: #1a1a1a;">${escapeHtml(artisan.customer_name || 'N/A')}</div>
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Email</label>
                                    <div style="margin-top: 8px; font-size: 16px; color: #1a1a1a;">${escapeHtml(artisan.customer_email || 'N/A')}</div>
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Contact</label>
                                    <div style="margin-top: 8px; font-size: 16px; color: #1a1a1a;">${escapeHtml(artisan.customer_contact || 'N/A')}</div>
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Business Name</label>
                                    <div style="margin-top: 8px; font-size: 16px; color: #1a1a1a;">${escapeHtml(artisan.business_name || 'N/A')}</div>
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Tier</label>
                                    <div style="margin-top: 8px;">
                                        <span style="display: inline-block; padding: 4px 12px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); color: #dc2626; border-radius: 50px; font-size: 12px; font-weight: 600;">
                                            Tier ${artisan.tier || 1}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Commission Rate</label>
                                    <div style="margin-top: 8px; font-size: 16px; color: #1a1a1a;">${parseFloat(artisan.commission_rate || 20).toFixed(2)}%</div>
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Status</label>
                                    <div style="margin-top: 8px;">
                                        <span style="display: inline-block; padding: 6px 14px; background: ${statusStyle.bg}; color: ${statusStyle.text}; border-radius: 50px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                            ${status.charAt(0).toUpperCase() + status.slice(1)}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Products</label>
                                    <div style="margin-top: 8px; font-size: 16px; color: #1a1a1a;">${artisan.product_count || 0}</div>
                                </div>
                                <div>
                                    <label style="font-weight: 600; color: #6b7280; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Joined</label>
                                    <div style="margin-top: 8px; font-size: 16px; color: #1a1a1a;">${artisan.created_date ? new Date(artisan.created_date).toLocaleDateString() : 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('viewArtisanModal').style.display = 'block';
                } else {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('Error: ' + data.message, 'error');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                    AdminUI.toast('An error occurred. Please try again.', 'error');
                } else {
                    alert('An error occurred. Please try again.');
                }
            });
        }

        function editArtisan(id) {
            fetch('../actions/admin_artisan_actions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get&artisan_id=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const artisan = data.data;
                    
                    document.getElementById('edit_artisan_id').value = artisan.artisan_id;
                    document.getElementById('edit_customer_name').value = artisan.customer_name || '';
                    document.getElementById('edit_customer_email').value = artisan.customer_email || '';
                    document.getElementById('edit_customer_contact').value = artisan.customer_contact || '';
                    document.getElementById('edit_business_name').value = artisan.business_name || '';
                    document.getElementById('edit_tier').value = artisan.tier || 1;
                    document.getElementById('edit_commission_rate').value = artisan.commission_rate || 20;
                    document.getElementById('edit_approval_status').value = artisan.approval_status || 'pending';
                    
                    document.getElementById('editArtisanModal').style.display = 'block';
                } else {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('Error: ' + data.message, 'error');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                    AdminUI.toast('An error occurred. Please try again.', 'error');
                } else {
                    alert('An error occurred. Please try again.');
                }
            });
        }

        function closeViewModal() {
            document.getElementById('viewArtisanModal').style.display = 'none';
        }

        function closeEditModal() {
            document.getElementById('editArtisanModal').style.display = 'none';
            document.getElementById('editArtisanForm').reset();
        }

        function manageAbout(artisanId) {
            // Load existing about data
            fetch('../actions/artisan_about_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=get&artisan_id=' + artisanId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.about) {
                    document.getElementById('about_artisan_id').value = artisanId;
                    document.getElementById('artisan_bio').value = data.about.artisan_bio || '';
                    document.getElementById('cultural_meaning').value = data.about.cultural_meaning || '';
                    document.getElementById('crafting_method').value = data.about.crafting_method || '';
                    document.getElementById('artisan_location').value = data.about.artisan_location || '';
                } else {
                    document.getElementById('about_artisan_id').value = artisanId;
                    document.getElementById('artisan_bio').value = '';
                    document.getElementById('cultural_meaning').value = '';
                    document.getElementById('crafting_method').value = '';
                    document.getElementById('artisan_location').value = '';
                }
                document.getElementById('manageAboutModal').style.display = 'flex';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('about_artisan_id').value = artisanId;
                document.getElementById('manageAboutModal').style.display = 'flex';
            });
        }

        function closeManageAboutModal() {
            document.getElementById('manageAboutModal').style.display = 'none';
            document.getElementById('manageAboutForm').reset();
        }

        // Manage About form submission
        document.getElementById('manageAboutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update');
            
            fetch('../actions/artisan_about_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('About page updated successfully!', 'success');
                    } else {
                        alert('About page updated successfully!');
                    }
                    closeManageAboutModal();
                } else {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('Error: ' + data.message, 'error');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Edit form submission
        document.getElementById('editArtisanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update');
            
            fetch('../actions/admin_artisan_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('Artisan updated successfully!', 'success');
                    } else {
                        alert('Artisan updated successfully!');
                    }
                    setTimeout(() => location.reload(), 1000);
                } else {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('Error: ' + data.message, 'error');
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                    AdminUI.toast('An error occurred. Please try again.', 'error');
                } else {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        document.getElementById('addArtisanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add');
            
            // Disable submit button during submission
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            
            fetch('../actions/admin_artisan_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the add modal
                    closeModal();
                    
                    // Show credentials modal if credentials are provided
                    if (data.credentials) {
                        showCredentialsModal(data.credentials);
                    } else {
                        // Fallback if no credentials (shouldn't happen)
                        if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                            AdminUI.toast('Artisan added successfully!', 'success');
                        } else {
                            alert('Artisan added successfully!');
                        }
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('Error: ' + data.message, 'error');
                    } else {
                        alert('Error: ' + data.message);
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                    AdminUI.toast('An error occurred. Please try again.', 'error');
                } else {
                    alert('An error occurred. Please try again.');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        function approveArtisan(id) {
            if (confirm('Approve this artisan?')) {
                fetch('../actions/admin_artisan_actions.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=approve&artisan_id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                            AdminUI.toast('Artisan approved!', 'success');
                        } else {
                            alert('Artisan approved!');
                        }
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                            AdminUI.toast('Error: ' + data.message, 'error');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('An error occurred. Please try again.', 'error');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        }

        function selectAllPending() {
            const checkboxes = document.querySelectorAll('.artisan-checkbox');
            checkboxes.forEach(cb => cb.checked = true);
            updateBulkActions();
        }
        
        function clearSelection() {
            const checkboxes = document.querySelectorAll('.artisan-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            document.getElementById('selectAllCheckbox').checked = false;
            updateBulkActions();
        }
        
        function toggleAllCheckboxes(checked) {
            const checkboxes = document.querySelectorAll('.artisan-checkbox');
            checkboxes.forEach(cb => cb.checked = checked);
            updateBulkActions();
        }
        
        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.artisan-checkbox:checked');
            const count = checkboxes.length;
            const bulkBtn = document.getElementById('bulkApproveBtn');
            const clearBtn = document.getElementById('clearSelectionBtn');
            const countSpan = document.getElementById('selectedCount');
            
            if (count > 0) {
                bulkBtn.style.display = 'inline-block';
                clearBtn.style.display = 'inline-block';
                countSpan.textContent = count;
            } else {
                bulkBtn.style.display = 'none';
                clearBtn.style.display = 'none';
            }
        }
        
        function bulkApprove() {
            const checkboxes = document.querySelectorAll('.artisan-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                alert('Please select at least one artisan to approve.');
                return;
            }
            
            if (!confirm(`Are you sure you want to approve ${ids.length} artisan(s)?`)) {
                return;
            }
            
            const bulkBtn = document.getElementById('bulkApproveBtn');
            bulkBtn.disabled = true;
            bulkBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
            
            fetch('../actions/admin_artisan_actions.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=bulk_approve&artisan_ids=' + ids.join(',')
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast(`Successfully approved ${data.approved_count} artisan(s)!`, 'success');
                    } else {
                        alert(`Successfully approved ${data.approved_count} artisan(s)!`);
                    }
                    setTimeout(() => location.reload(), 1000);
                } else {
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('Error: ' + (data.message || 'Failed to approve artisans'), 'error');
                    } else {
                        alert('Error: ' + (data.message || 'Failed to approve artisans'));
                    }
                    bulkBtn.disabled = false;
                    bulkBtn.innerHTML = '<i class="fas fa-check-double"></i> Approve Selected (<span id="selectedCount">' + ids.length + '</span>)';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                    AdminUI.toast('An error occurred. Please try again.', 'error');
                } else {
                    alert('An error occurred. Please try again.');
                }
                bulkBtn.disabled = false;
                bulkBtn.innerHTML = '<i class="fas fa-check-double"></i> Approve Selected (<span id="selectedCount">' + ids.length + '</span>)';
            });
        }
        
        function deleteArtisan(id) {
            if (confirm('Delete this artisan? This will also remove their products.')) {
                fetch('../actions/admin_artisan_actions.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=delete&artisan_id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                            AdminUI.toast('Artisan deleted!', 'success');
                        } else {
                            alert('Artisan deleted!');
                        }
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                            AdminUI.toast('Error: ' + data.message, 'error');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof AdminUI !== 'undefined' && AdminUI.toast) {
                        AdminUI.toast('An error occurred. Please try again.', 'error');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addArtisanModal');
            const viewModal = document.getElementById('viewArtisanModal');
            const editModal = document.getElementById('editArtisanModal');
            const credentialsModal = document.getElementById('credentialsModal');
            const manageAboutModal = document.getElementById('manageAboutModal');
            
            if (event.target == addModal) {
                closeModal();
            }
            if (event.target == viewModal) {
                closeViewModal();
            }
            if (event.target == editModal) {
                closeEditModal();
            }
            if (event.target == credentialsModal) {
                closeCredentialsModal();
            }
            if (event.target == manageAboutModal) {
                closeManageAboutModal();
            }
        }
    </script>
</body>
</html>
