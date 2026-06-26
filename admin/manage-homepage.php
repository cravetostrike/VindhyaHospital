<?php
/**
 * VHRC Manage Homepage CMS Panel (manage-homepage.php)
 */

// Include header layout (handles security and database connection)
include_once __DIR__ . '/includes/header.php';

$message = "";
$error = "";

// Handle Form Submissions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_slide') {
        // Update specific hero slide
        $id = intval($_POST['slide_id'] ?? 0);
        $tag = trim($_POST['tag'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $cta1_text = trim($_POST['cta1_text'] ?? '');
        $cta1_link = trim($_POST['cta1_link'] ?? '');
        $cta2_text = trim($_POST['cta2_text'] ?? '');
        $cta2_link = trim($_POST['cta2_link'] ?? '');

        if ($id < 1 || $id > 3) {
            $error = "Invalid slide ID selected.";
        } else {
            try {
                // Update Text Columns in SQLite
                $stmt = $pdo->prepare("UPDATE hero_slides SET tag = ?, title = ?, description = ?, cta1_text = ?, cta1_link = ?, cta2_text = ?, cta2_link = ? WHERE id = ?");
                $stmt->execute([$tag, $title, $description, $cta1_text, $cta1_link, $cta2_text, $cta2_link, $id]);
                
                $message = "Slide #{$id} text details updated successfully.";

                // Handle Image File Upload Replacement
                if (isset($_FILES['slide_file']) && $_FILES['slide_file']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['slide_file']['tmp_name'];
                    $file_name = basename($_FILES['slide_file']['name']);
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Validate extension (must be PNG, JPG, JPEG, or WEBP)
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                    if (in_array($file_ext, $allowed_exts)) {
                        $target_dir = dirname(__DIR__) . '/images';
                        $target_file = $target_dir . "/slide{$id}.png"; // Force slide[ID].png naming to match slider background links
                        
                        if (move_uploaded_file($file_tmp, $target_file)) {
                            $message = "Slide #{$id} texts and background image updated successfully.";
                        } else {
                            $error = "Slide texts updated, but failed to replace the background image file on the server.";
                        }
                    } else {
                        $error = "Invalid slide file format. Only JPG, JPEG, PNG, and WEBP files are allowed.";
                    }
                }
            } catch (PDOException $e) {
                $error = "Database update failed: " . $e->getMessage();
            }
        }
    } elseif ($action === 'update_settings') {
        // Update general settings
        $settings = $_POST['settings'] ?? [];
        if (empty($settings)) {
            $error = "No settings data submitted.";
        } else {
            try {
                $pdo->beginTransaction();
                $update_stmt = $pdo->prepare("UPDATE homepage_settings SET value = ? WHERE key = ?");
                foreach ($settings as $key => $value) {
                    $update_stmt->execute([trim($value), $key]);
                }
                $pdo->commit();
                $message = "Homepage configuration settings updated successfully.";
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Failed to update configurations: " . $e->getMessage();
            }
        }
    }
}

// Fetch all slides from SQLite
try {
    $slides_stmt = $pdo->query("SELECT * FROM hero_slides ORDER BY slide_order ASC");
    $slides = $slides_stmt->fetchAll();
    
    // Fetch settings and map to key-value array
    $settings_stmt = $pdo->query("SELECT * FROM homepage_settings");
    $raw_settings = $settings_stmt->fetchAll();
    $cms_settings = [];
    foreach ($raw_settings as $row) {
        $cms_settings[$row['key']] = $row['value'];
    }
} catch (PDOException $e) {
    die("Error fetching CMS configurations: " . $e->getMessage());
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
        <button class="cms-tab-btn active" data-tab="hero">Hero Slider</button>
        <button class="cms-tab-btn" data-tab="emergency">Emergency & Badges</button>
        <button class="cms-tab-btn" data-tab="stats">Stats Counters</button>
        <button class="cms-tab-btn" data-tab="contact">Contact & Socials</button>
    </div>

    <!-- Tab Content 1: Hero Slider -->
    <div class="cms-tab-content active" id="tab-hero">
        <div class="slides-editor-grid">
            <?php foreach ($slides as $index => $slide): ?>
                <?php 
                $id = $slide['id'];
                $image_file = "../images/slide{$id}.png";
                $has_image = file_exists($image_file);
                ?>
                <div class="panel-card slide-editor-card">
                    <div class="panel-header">
                        <h3>Slide Slot #<?php echo $id; ?></h3>
                    </div>
                    <div class="panel-body">
                        <!-- Image Preview -->
                        <div class="slide-preview-wrap">
                            <?php if ($has_image): ?>
                                <img src="<?php echo htmlspecialchars($image_file); ?>?v=<?php echo time(); ?>" alt="Slide Preview" class="slide-preview-img">
                            <?php else: ?>
                                <div class="slide-preview-placeholder">No Slide Image Found</div>
                            <?php endif; ?>
                        </div>

                        <!-- Form -->
                        <form action="manage-homepage.php" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                            <input type="hidden" name="action" value="update_slide">
                            <input type="hidden" name="slide_id" value="<?php echo $id; ?>">

                            <div class="login-group">
                                <label>Badge / Tagline</label>
                                <input type="text" name="tag" value="<?php echo htmlspecialchars($slide['tag'] ?? ''); ?>" required>
                            </div>

                            <div class="login-group">
                                <label>Slide Title (HTML `<br>` and `<span>` enabled)</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($slide['title'] ?? ''); ?>" required>
                            </div>

                            <div class="login-group">
                                <label>Description</label>
                                <textarea name="description" rows="3" required><?php echo htmlspecialchars($slide['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="login-group">
                                    <label>CTA Button 1 Text</label>
                                    <input type="text" name="cta1_text" value="<?php echo htmlspecialchars($slide['cta1_text'] ?? ''); ?>">
                                </div>
                                <div class="login-group">
                                    <label>CTA Button 1 Link</label>
                                    <input type="text" name="cta1_link" value="<?php echo htmlspecialchars($slide['cta1_link'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: -0.5rem;">
                                <div class="login-group">
                                    <label>CTA Button 2 Text</label>
                                    <input type="text" name="cta2_text" value="<?php echo htmlspecialchars($slide['cta2_text'] ?? ''); ?>">
                                </div>
                                <div class="login-group">
                                    <label>CTA Button 2 Link</label>
                                    <input type="text" name="cta2_link" value="<?php echo htmlspecialchars($slide['cta2_link'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="login-group">
                                <label>Replace Background Image</label>
                                <div class="file-input-wrapper">
                                    <span class="custom-file-btn" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Select Slider JPG/PNG</span>
                                    <input type="file" name="slide_file" accept="image/*">
                                </div>
                            </div>

                            <button type="submit" class="btn-add-primary confirm-action" data-confirm-message="Are you sure you want to update Slide #<?php echo $id; ?>?" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
                                Update Slide Slot #<?php echo $id; ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tab Content 2: Emergency & Badges -->
    <div class="cms-tab-content" id="tab-emergency">
        <div class="panel-card">
            <div class="panel-header">
                <h3>Emergency Service Card & Trust Badges CMS</h3>
            </div>
            <div class="panel-body">
                <form action="manage-homepage.php" method="POST" class="cms-editor-form">
                    <input type="hidden" name="action" value="update_settings">

                    <!-- Emergency Service -->
                    <h4 class="cms-form-section-title">🚨 24/7 Emergency Service Banner</h4>
                    <div class="login-group">
                        <label>Emergency Banner Title</label>
                        <input type="text" name="settings[emergency_title]" value="<?php echo htmlspecialchars($cms_settings['emergency_title'] ?? ''); ?>" required>
                    </div>
                    <div class="login-group">
                        <label>Emergency Banner Description</label>
                        <textarea name="settings[emergency_desc]" rows="3" required><?php echo htmlspecialchars($cms_settings['emergency_desc'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="login-group">
                            <label>Emergency Action Button Text</label>
                            <input type="text" name="settings[emergency_btn_text]" value="<?php echo htmlspecialchars($cms_settings['emergency_btn_text'] ?? ''); ?>" required>
                        </div>
                        <div class="login-group">
                            <label>Emergency Action Button Link</label>
                            <input type="text" name="settings[emergency_btn_link]" value="<?php echo htmlspecialchars($cms_settings['emergency_btn_link'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <hr class="cms-divider" style="margin: 2.5rem 0;">

                    <!-- Trust Badges -->
                    <h4 class="cms-form-section-title">🛡 Header Trust Badges</h4>
                    <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <!-- Badge 1 -->
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h5>Badge 1 (Gold Seal Accreditation)</h5>
                            <div class="login-group">
                                <label>Badge Label</label>
                                <input type="text" name="settings[accred1_title]" value="<?php echo htmlspecialchars($cms_settings['accred1_title'] ?? ''); ?>" required>
                            </div>
                            <div class="login-group">
                                <label>Badge Description Value</label>
                                <input type="text" name="settings[accred1_desc]" value="<?php echo htmlspecialchars($cms_settings['accred1_desc'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <!-- Badge 2 -->
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h5>Badge 2 (Certified Quality accreditation)</h5>
                            <div class="login-group">
                                <label>Badge Label</label>
                                <input type="text" name="settings[accred2_title]" value="<?php echo htmlspecialchars($cms_settings['accred2_title'] ?? ''); ?>" required>
                            </div>
                            <div class="login-group">
                                <label>Badge Description Value</label>
                                <input type="text" name="settings[accred2_desc]" value="<?php echo htmlspecialchars($cms_settings['accred2_desc'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-add-primary" style="margin-top: 1.5rem;">
                        Save Emergency & Badges Configurations
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab Content 3: Stats Counters -->
    <div class="cms-tab-content" id="tab-stats">
        <div class="panel-card">
            <div class="panel-header">
                <h3>Statistics Counter Cards CMS</h3>
            </div>
            <div class="panel-body">
                <form action="manage-homepage.php" method="POST" class="cms-editor-form">
                    <input type="hidden" name="action" value="update_settings">

                    <div class="stats-cms-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
                        <!-- Stat Card 1 -->
                        <div class="panel-card" style="box-shadow: none; border: 1px solid var(--clr-admin-border); padding: 1.5rem;">
                            <h4 style="margin-bottom: 1rem; color: var(--clr-admin-primary);">Card 1 (Stethoscope Icon)</h4>
                            <div class="login-group">
                                <label>Value Number</label>
                                <input type="text" name="settings[stat1_number]" value="<?php echo htmlspecialchars($cms_settings['stat1_number'] ?? ''); ?>" required>
                            </div>
                            <div class="login-group">
                                <label>Card Text Label</label>
                                <input type="text" name="settings[stat1_label]" value="<?php echo htmlspecialchars($cms_settings['stat1_label'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Stat Card 2 -->
                        <div class="panel-card" style="box-shadow: none; border: 1px solid var(--clr-admin-border); padding: 1.5rem;">
                            <h4 style="margin-bottom: 1rem; color: var(--clr-admin-primary);">Card 2 (Patients Icon)</h4>
                            <div class="login-group">
                                <label>Value Number</label>
                                <input type="text" name="settings[stat2_number]" value="<?php echo htmlspecialchars($cms_settings['stat2_number'] ?? ''); ?>" required>
                            </div>
                            <div class="login-group">
                                <label>Card Text Label</label>
                                <input type="text" name="settings[stat2_label]" value="<?php echo htmlspecialchars($cms_settings['stat2_label'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Stat Card 3 -->
                        <div class="panel-card" style="box-shadow: none; border: 1px solid var(--clr-admin-border); padding: 1.5rem;">
                            <h4 style="margin-bottom: 1rem; color: var(--clr-admin-primary);">Card 3 (Clinic Beds Icon)</h4>
                            <div class="login-group">
                                <label>Value Number</label>
                                <input type="text" name="settings[stat3_number]" value="<?php echo htmlspecialchars($cms_settings['stat3_number'] ?? ''); ?>" required>
                            </div>
                            <div class="login-group">
                                <label>Card Text Label</label>
                                <input type="text" name="settings[stat3_label]" value="<?php echo htmlspecialchars($cms_settings['stat3_label'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Stat Card 4 -->
                        <div class="panel-card" style="box-shadow: none; border: 1px solid var(--clr-admin-border); padding: 1.5rem;">
                            <h4 style="margin-bottom: 1rem; color: var(--clr-admin-primary);">Card 4 (Gold Seal Badge Icon)</h4>
                            <div class="login-group">
                                <label>Value Number</label>
                                <input type="text" name="settings[stat4_number]" value="<?php echo htmlspecialchars($cms_settings['stat4_number'] ?? ''); ?>" required>
                            </div>
                            <div class="login-group">
                                <label>Card Text Label</label>
                                <input type="text" name="settings[stat4_label]" value="<?php echo htmlspecialchars($cms_settings['stat4_label'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-add-primary" style="margin-top: 2rem;">
                        Save Statistics Configuration
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab Content 4: Contact & Socials -->
    <div class="cms-tab-content" id="tab-contact">
        <div class="panel-card">
            <div class="panel-header">
                <h3>Contact Details, Clinical Hours, & Social Profiles CMS</h3>
            </div>
            <div class="panel-body">
                <form action="manage-homepage.php" method="POST" class="cms-editor-form">
                    <input type="hidden" name="action" value="update_settings">

                    <h4 class="cms-form-section-title">📞 Clinic Contacts & Hours</h4>
                    <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="login-group">
                            <label>Clinical Timings Bar Text (e.g. Header Info)</label>
                            <input type="text" name="settings[header_hours]" value="<?php echo htmlspecialchars($cms_settings['header_hours'] ?? ''); ?>" required>
                        </div>
                        <div class="login-group">
                            <label>General Contact Phone (e.g. Header Bar)</label>
                            <input type="text" name="settings[header_phone]" value="<?php echo htmlspecialchars($cms_settings['header_phone'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: -0.5rem;">
                        <div class="login-group">
                            <label>General Contact Email</label>
                            <input type="email" name="settings[header_email]" value="<?php echo htmlspecialchars($cms_settings['header_email'] ?? ''); ?>" required>
                        </div>
                        <div class="login-group">
                            <label>24/7 Booking Info Hotline</label>
                            <input type="text" name="settings[booking_hotline]" value="<?php echo htmlspecialchars($cms_settings['booking_hotline'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: -0.5rem;">
                        <div class="login-group">
                            <label>Booking Section Clinical Hours Timings</label>
                            <input type="text" name="settings[booking_hours]" value="<?php echo htmlspecialchars($cms_settings['booking_hours'] ?? ''); ?>" required>
                        </div>
                        <div class="login-group">
                            <label>Hospital Address Blurb</label>
                            <input type="text" name="settings[booking_address]" value="<?php echo htmlspecialchars($cms_settings['booking_address'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <hr class="cms-divider" style="margin: 2.5rem 0;">

                    <h4 class="cms-form-section-title">🌐 Header Social Profile Link Targets</h4>
                    <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="login-group">
                            <label>Facebook Profile Target</label>
                            <input type="text" name="settings[social_fb]" value="<?php echo htmlspecialchars($cms_settings['social_fb'] ?? ''); ?>" required>
                        </div>
                        <div class="login-group">
                            <label>LinkedIn Profile Target</label>
                            <input type="text" name="settings[social_in]" value="<?php echo htmlspecialchars($cms_settings['social_in'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: -0.5rem;">
                        <div class="login-group">
                            <label>Pinterest Target</label>
                            <input type="text" name="settings[social_pin]" value="<?php echo htmlspecialchars($cms_settings['social_pin'] ?? ''); ?>" required>
                        </div>
                        <div class="login-group">
                            <label>Twitter/X Target</label>
                            <input type="text" name="settings[social_tw]" value="<?php echo htmlspecialchars($cms_settings['social_tw'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: -0.5rem;">
                        <div class="login-group">
                            <label>YouTube Channel Target</label>
                            <input type="text" name="settings[social_yt]" value="<?php echo htmlspecialchars($cms_settings['social_yt'] ?? ''); ?>" required>
                        </div>
                        <div class="login-group">
                            <label>Instagram Target</label>
                            <input type="text" name="settings[social_ig]" value="<?php echo htmlspecialchars($cms_settings['social_ig'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-add-primary" style="margin-top: 1.5rem;">
                        Save Contact & Social Details
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer layout
include_once __DIR__ . '/includes/footer.php';
?>
