<?php
/**
 * VHRC Manage Site Settings CMS (settings.php)
 */

// Include header layout (handles security and database connection)
include_once __DIR__ . '/includes/header.php';

$message = "";
$error = "";

$admin_id = $_SESSION['admin_id'] ?? 0;

// Fetch current logged in admin user data
try {
    $admin_stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $admin_stmt->execute([$admin_id]);
    $admin_user = $admin_stmt->fetch();
} catch (PDOException $e) {
    $error = "Failed to load account details: " . $e->getMessage();
}

// Handle Form Submissions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_emails') {
        $header_email = trim($_POST['header_email'] ?? '');
        $notification_email = trim($_POST['notification_email'] ?? '');

        if (empty($header_email) || empty($notification_email)) {
            $error = "Both email addresses are required.";
        } elseif (!filter_var($header_email, FILTER_VALIDATE_EMAIL) || !filter_var($notification_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter valid email addresses.";
        } else {
            try {
                $pdo->beginTransaction();
                $update_stmt = $pdo->prepare("UPDATE homepage_settings SET value = ? WHERE key = ?");
                $update_stmt->execute([$header_email, 'header_email']);
                $update_stmt->execute([$notification_email, 'notification_email']);
                $pdo->commit();
                $message = "Email configurations updated successfully.";
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Failed to update emails: " . $e->getMessage();
            }
        }
    } elseif ($action === 'update_favicons') {
        try {
            // Get current favicon settings for deletion if replaced
            $stmt = $pdo->query("SELECT * FROM homepage_settings WHERE key IN ('website_favicon', 'admin_favicon')");
            $curr = [];
            foreach ($stmt->fetchAll() as $row) {
                $curr[$row['key']] = $row['value'];
            }

            $allowed_exts = ['ico', 'png', 'jpg', 'jpeg', 'gif'];
            $target_dir = dirname(__DIR__) . '/uploads/favicons';

            // 1. Handle Website Favicon Upload
            if (isset($_FILES['website_favicon_file']) && $_FILES['website_favicon_file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['website_favicon_file']['tmp_name'];
                $file_name = basename($_FILES['website_favicon_file']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if (in_array($file_ext, $allowed_exts)) {
                    $new_filename = 'favicon_website_' . time() . '.' . $file_ext;
                    $target_file = $target_dir . '/' . $new_filename;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        // Delete old file if exists
                        if (!empty($curr['website_favicon'])) {
                            $old_file = dirname(__DIR__) . '/' . $curr['website_favicon'];
                            if (file_exists($old_file)) {
                                @unlink($old_file);
                            }
                        }
                        // Update in DB
                        $db_path = 'uploads/favicons/' . $new_filename;
                        $update_stmt = $pdo->prepare("UPDATE homepage_settings SET value = ? WHERE key = ?");
                        $update_stmt->execute([$db_path, 'website_favicon']);
                        $message = "Website favicon updated successfully.";
                    } else {
                        $error = "Failed to move uploaded website favicon file.";
                    }
                } else {
                    $error = "Invalid website favicon format. Allowed types: " . implode(', ', $allowed_exts);
                }
            }

            // 2. Handle Admin Favicon Upload
            if (isset($_FILES['admin_favicon_file']) && $_FILES['admin_favicon_file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['admin_favicon_file']['tmp_name'];
                $file_name = basename($_FILES['admin_favicon_file']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if (in_array($file_ext, $allowed_exts)) {
                    $new_filename = 'favicon_admin_' . time() . '.' . $file_ext;
                    $target_file = $target_dir . '/' . $new_filename;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        // Delete old file if exists
                        if (!empty($curr['admin_favicon'])) {
                            $old_file = dirname(__DIR__) . '/' . $curr['admin_favicon'];
                            if (file_exists($old_file)) {
                                @unlink($old_file);
                            }
                        }
                        // Update in DB
                        $db_path = 'uploads/favicons/' . $new_filename;
                        $update_stmt = $pdo->prepare("UPDATE homepage_settings SET value = ? WHERE key = ?");
                        $update_stmt->execute([$db_path, 'admin_favicon']);
                        $message = "Admin favicon updated successfully.";
                    } else {
                        $error = "Failed to move uploaded admin favicon file.";
                    }
                } else {
                    $error = "Invalid admin favicon format. Allowed types: " . implode(', ', $allowed_exts);
                }
            }
        } catch (PDOException $e) {
            $error = "Database error updating favicons: " . $e->getMessage();
        }
    } elseif ($action === 'change_username') {
        $new_username = trim($_POST['new_username'] ?? '');

        if (empty($new_username)) {
            $error = "Username cannot be empty.";
        } else {
            try {
                // Check if username already exists for another account
                $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ? AND id != ?");
                $check_stmt->execute([$new_username, $admin_id]);
                if ($check_stmt->fetchColumn() > 0) {
                    $error = "Username '{$new_username}' is already in use by another administrator.";
                } else {
                    $update_stmt = $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?");
                    $update_stmt->execute([$new_username, $admin_id]);
                    
                    // Update current session
                    $_SESSION['admin_username'] = $new_username;
                    $message = "Username updated successfully.";
                    
                    // Refresh user profile details
                    $admin_stmt->execute([$admin_id]);
                    $admin_user = $admin_stmt->fetch();
                }
            } catch (PDOException $e) {
                $error = "Failed to update username: " . $e->getMessage();
            }
        }
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "All password fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New password and confirmation password do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = "New password must be at least 6 characters long.";
        } else {
            try {
                // Verify current password
                $check_stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
                $check_stmt->execute([$admin_id]);
                $db_hash = $check_stmt->fetchColumn();

                if ($db_hash && password_verify($current_password, $db_hash)) {
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                    $update_stmt->execute([$new_hash, $admin_id]);
                    $message = "Password changed successfully.";
                } else {
                    $error = "Invalid current password.";
                }
            } catch (PDOException $e) {
                $error = "Failed to update password: " . $e->getMessage();
            }
        }
    }
}

// Fetch all homepage_settings for display
try {
    $settings_stmt = $pdo->query("SELECT * FROM homepage_settings");
    $cms_settings = [];
    foreach ($settings_stmt->fetchAll() as $row) {
        $cms_settings[$row['key']] = $row['value'];
    }
} catch (PDOException $e) {
    $error = "Error loading CMS settings: " . $e->getMessage();
}
?>

<!-- Alert Banners -->
<?php if (!empty($message)): ?>
    <div class="login-alert" style="background-color: var(--clr-success-bg); color: var(--clr-success); border-color: rgba(16, 185, 129, 0.2); margin-bottom: 2rem;">
        ✔ <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="login-alert" style="margin-bottom: 2rem;">
        ✖ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- CMS Tab Headers -->
<div class="cms-tabs-container">
    <div class="cms-tabs-nav">
        <button class="cms-tab-btn active" data-tab="emails">Email Config</button>
        <button class="cms-tab-btn" data-tab="favicons">Favicon Settings</button>
        <button class="cms-tab-btn" data-tab="security">Account Security</button>
    </div>

    <!-- Tab Content 1: Email Configuration -->
    <div class="cms-tab-content active" id="tab-emails">
        <div class="panel-card">
            <div class="panel-header">
                <h3>Email Address Configuration</h3>
            </div>
            <div class="panel-body">
                <form action="settings.php" method="POST" class="cms-editor-form">
                    <input type="hidden" name="action" value="update_emails">

                    <div class="login-group">
                        <label>Public Contact Email (Displayed on Frontend Top Bar & Footer)</label>
                        <input type="email" name="header_email" value="<?php echo htmlspecialchars($cms_settings['header_email'] ?? ''); ?>" placeholder="vhrcrewa@gmail.com" required>
                        <small style="color: var(--clr-admin-text-muted); display: block; margin-top: 0.25rem;">This email is visible to all website users in the top bar contact links.</small>
                    </div>

                    <div class="login-group" style="margin-top: 1.5rem;">
                        <label>Admin Notification Email (Receives Appointment Requests & Inquiries)</label>
                        <input type="email" name="notification_email" value="<?php echo htmlspecialchars($cms_settings['notification_email'] ?? ''); ?>" placeholder="vhrcrewa@gmail.com" required>
                        <small style="color: var(--clr-admin-text-muted); display: block; margin-top: 0.25rem;">System notification messages and client bookings are queued to route here.</small>
                    </div>

                    <button type="submit" class="btn-add-primary" style="margin-top: 1.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <span>Save Email Settings</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab Content 2: Favicon Settings -->
    <div class="cms-tab-content" id="tab-favicons">
        <div class="slides-editor-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
            <!-- Website Favicon Card -->
            <div class="panel-card">
                <div class="panel-header">
                    <h3>Public Website Favicon</h3>
                </div>
                <div class="panel-body">
                    <div class="slide-preview-wrap" style="height: 120px; display: flex; align-items: center; justify-content: center; background-color: var(--clr-admin-bg-light); border-radius: 6px; margin-bottom: 1.5rem; border: 1px dashed var(--clr-admin-border);">
                        <?php 
                        $web_fav = $cms_settings['website_favicon'] ?? '';
                        if (!empty($web_fav) && file_exists(dirname(__DIR__) . '/' . $web_fav)): 
                        ?>
                            <img src="../<?php echo htmlspecialchars($web_fav); ?>?v=<?php echo time(); ?>" alt="Website Favicon" style="max-height: 48px; max-width: 48px; object-fit: contain;">
                        <?php else: ?>
                            <div style="color: var(--clr-admin-text-muted); font-size: 0.85rem; text-align: center;">
                                <img src="../images/logo.png" alt="Fallback Logo" style="max-height: 48px; max-width: 48px; opacity: 0.5; margin-bottom: 0.5rem; display: block; margin-left: auto; margin-right: auto;">
                                Fallback Logo Icon Used
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="settings.php" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                        <input type="hidden" name="action" value="update_favicons">
                        
                        <div class="login-group">
                            <label>Upload New Favicon (.ico, .png, .jpg, .gif)</label>
                            <div class="custom-file-upload-btn-wrap" style="position: relative; margin-top: 0.5rem;">
                                <label class="custom-file-btn" style="display: block; text-align: center; border: 2px dashed var(--clr-admin-border); padding: 0.8rem; border-radius: 6px; cursor: pointer; color: var(--clr-admin-text-muted); font-size: 0.9rem; font-weight: 500; transition: all 0.2s;">
                                    Choose website icon...
                                </label>
                                <input type="file" name="website_favicon_file" accept=".ico,.png,.jpg,.jpeg,.gif" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" required>
                            </div>
                        </div>

                        <button type="submit" class="btn-add-primary" style="margin-top: 1.5rem; width: 100%; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            <span>Upload Website Icon</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Admin Favicon Card -->
            <div class="panel-card">
                <div class="panel-header">
                    <h3>Admin Dashboard Favicon</h3>
                </div>
                <div class="panel-body">
                    <div class="slide-preview-wrap" style="height: 120px; display: flex; align-items: center; justify-content: center; background-color: var(--clr-admin-bg-light); border-radius: 6px; margin-bottom: 1.5rem; border: 1px dashed var(--clr-admin-border);">
                        <?php 
                        $adm_fav = $cms_settings['admin_favicon'] ?? '';
                        if (!empty($adm_fav) && file_exists(dirname(__DIR__) . '/' . $adm_fav)): 
                        ?>
                            <img src="../<?php echo htmlspecialchars($adm_fav); ?>?v=<?php echo time(); ?>" alt="Admin Favicon" style="max-height: 48px; max-width: 48px; object-fit: contain;">
                        <?php else: ?>
                            <div style="color: var(--clr-admin-text-muted); font-size: 0.85rem; text-align: center;">
                                <img src="../images/logo.png" alt="Fallback Logo" style="max-height: 48px; max-width: 48px; opacity: 0.5; margin-bottom: 0.5rem; display: block; margin-left: auto; margin-right: auto;">
                                Fallback Logo Icon Used
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="settings.php" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                        <input type="hidden" name="action" value="update_favicons">
                        
                        <div class="login-group">
                            <label>Upload New Favicon (.ico, .png, .jpg, .gif)</label>
                            <div class="custom-file-upload-btn-wrap" style="position: relative; margin-top: 0.5rem;">
                                <label class="custom-file-btn" style="display: block; text-align: center; border: 2px dashed var(--clr-admin-border); padding: 0.8rem; border-radius: 6px; cursor: pointer; color: var(--clr-admin-text-muted); font-size: 0.9rem; font-weight: 500; transition: all 0.2s;">
                                    Choose admin icon...
                                </label>
                                <input type="file" name="admin_favicon_file" accept=".ico,.png,.jpg,.jpeg,.gif" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" required>
                            </div>
                        </div>

                        <button type="submit" class="btn-add-primary" style="margin-top: 1.5rem; width: 100%; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            <span>Upload Admin Icon</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content 3: Account Security -->
    <div class="cms-tab-content" id="tab-security">
        <div class="slides-editor-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
            <!-- Username Change Card -->
            <div class="panel-card">
                <div class="panel-header">
                    <h3>Modify Administrator Username</h3>
                </div>
                <div class="panel-body">
                    <form action="settings.php" method="POST" class="cms-editor-form">
                        <input type="hidden" name="action" value="change_username">

                        <div class="login-group">
                            <label>Current Username</label>
                            <input type="text" value="<?php echo htmlspecialchars($admin_user['username'] ?? 'admin'); ?>" disabled style="background-color: var(--clr-admin-bg-light); cursor: not-allowed; border-color: var(--clr-admin-border);">
                        </div>

                        <div class="login-group" style="margin-top: 1rem;">
                            <label>New Administrative Username</label>
                            <input type="text" name="new_username" placeholder="Enter new username" required autocomplete="username">
                        </div>

                        <button type="submit" class="btn-add-primary" style="margin-top: 1.5rem; width: 100%; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Update Username</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Password Change Card -->
            <div class="panel-card">
                <div class="panel-header">
                    <h3>Change Administrator Password</h3>
                </div>
                <div class="panel-body">
                    <form action="settings.php" method="POST" class="cms-editor-form">
                        <input type="hidden" name="action" value="change_password">

                        <div class="login-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" placeholder="Enter current password" required autocomplete="current-password">
                        </div>

                        <div class="login-group" style="margin-top: 1rem;">
                            <label>New Password (min 6 characters)</label>
                            <input type="password" name="new_password" placeholder="Enter new password" required autocomplete="new-password">
                        </div>

                        <div class="login-group" style="margin-top: 1rem;">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Re-type new password" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn-add-primary" style="margin-top: 1.5rem; width: 100%; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span>Update Password</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer layout
include_once __DIR__ . '/includes/footer.php';
?>
