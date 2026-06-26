<?php
/**
 * VHRC Manage Homepage Posters Panel (manage-gallery.php)
 * Dynamic CRUD version (Add / Delete uploaded flyers)
 */

// Include header layout (handles security and database connection)
include_once __DIR__ . '/includes/header.php';

$message = "";
$error = "";

// 1. Handle Poster Upload (POST Add)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_poster') {
    try {
        if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['poster_file']['tmp_name'];
            $file_name = basename($_FILES['poster_file']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate extension (must be PNG, JPG, JPEG, or WEBP)
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                // Generate a unique filename to prevent browser caching issues and overlaps
                $unique_name = "poster_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
                $relative_path = "uploads/gallery/" . $unique_name;
                $target_file = dirname(__DIR__) . "/" . $relative_path;
                
                if (move_uploaded_file($file_tmp, $target_file)) {
                    // Insert into SQLite database
                    $insert_stmt = $pdo->prepare("INSERT INTO gallery_posters (image_path) VALUES (?)");
                    $insert_stmt->execute([$relative_path]);
                    
                    $message = "New clinical graphic poster uploaded successfully.";
                } else {
                    $error = "Failed to save the graphic file on the server.";
                }
            } else {
                $error = "Invalid file format. Only JPG, JPEG, PNG, and WEBP files are allowed.";
            }
        } else {
            $error = "Please select a valid image file to upload.";
        }
    } catch (PDOException $e) {
        $error = "Database operation failed: " . $e->getMessage();
    }
}

// 2. Handle Poster Deletion (POST Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_poster') {
    $id = intval($_POST['poster_id'] ?? 0);
    
    if ($id <= 0) {
        $error = "Invalid poster ID selection.";
    } else {
        try {
            // Retrieve image path to delete file from disk
            $stmt = $pdo->prepare("SELECT image_path FROM gallery_posters WHERE id = ?");
            $stmt->execute([$id]);
            $poster = $stmt->fetch();
            
            if ($poster) {
                $file_path = dirname(__DIR__) . "/" . $poster['image_path'];
                
                // Remove record from database
                $delete_stmt = $pdo->prepare("DELETE FROM gallery_posters WHERE id = ?");
                $delete_stmt->execute([$id]);
                
                // Delete physical file from disk if it exists
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                
                $message = "Clinical graphic poster deleted successfully.";
            } else {
                $error = "Selected poster record was not found.";
            }
        } catch (PDOException $e) {
            $error = "Failed to delete poster: " . $e->getMessage();
        }
    }
}

// 3. Fetch all active posters from database
try {
    $stmt = $pdo->query("SELECT * FROM gallery_posters ORDER BY id DESC");
    $posters = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching gallery configurations: " . $e->getMessage());
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

<!-- Uploader Panel -->
<div class="panel-card" style="margin-bottom: 2.5rem;">
    <div class="panel-header">
        <h3>Add New Clinical Graphic Poster</h3>
    </div>
    <div class="panel-body">
        <form action="manage-gallery.php" method="POST" enctype="multipart/form-data" style="max-width: 500px; display: flex; flex-direction: column; gap: 1.25rem;">
            <input type="hidden" name="action" value="add_poster">
            
            <div class="login-group">
                <label>Select Poster Image</label>
                <div class="file-input-wrapper">
                    <span class="custom-file-btn" style="padding: 0.75rem 1rem; font-size: 0.85rem;">Select JPG/PNG/WEBP Image File</span>
                    <input type="file" name="poster_file" accept="image/*" required>
                </div>
                <small style="color: var(--clr-admin-text-muted); font-size: 0.75rem; margin-top: 0.25rem;">For best display symmetry, please upload square aspect ratio images (e.g. 800x800 px).</small>
            </div>

            <button type="submit" class="btn-add-primary" style="align-self: flex-start;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                <span>Upload & Add Poster</span>
            </button>
        </form>
    </div>
</div>

<!-- Active Grid List -->
<div class="panel-card">
    <div class="panel-header">
        <h3>Currently Active Homepage Posters</h3>
    </div>
    <div class="panel-body">
        <?php if (empty($posters)): ?>
            <div style="text-align: center; color: var(--clr-admin-text-muted); padding: 4rem 2rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem; opacity: 0.6;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                <p style="font-weight: 600; font-size: 1rem;">No posters added yet.</p>
                <p style="font-size: 0.85rem; margin-top: 0.25rem;">Use the upload field above to display clinical graphic posters on the homepage.</p>
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($posters as $poster): ?>
                    <?php 
                    $id = intval($poster['id']);
                    $image_file = "../" . $poster['image_path'];
                    $has_image = file_exists($image_file);
                    ?>
                    
                    <div class="gallery-slot-card">
                        <div class="slot-preview-wrap">
                            <?php if ($has_image): ?>
                                <img src="<?php echo htmlspecialchars($image_file); ?>" alt="Poster" class="slot-img">
                            <?php else: ?>
                                <div class="slot-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                    <span>File Missing</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="slot-info-box">
                            <span class="slot-number-badge">ID Reference #<?php echo $id; ?></span>
                            <small style="color: var(--clr-admin-text-muted); display: block; margin-top: 0.25rem;">
                                Added: <?php echo htmlspecialchars($poster['created_at']); ?>
                            </small>
                            
                            <form action="manage-gallery.php" method="POST" class="slot-upload-form" style="margin-top: 1rem;">
                                <input type="hidden" name="action" value="delete_poster">
                                <input type="hidden" name="poster_id" value="<?php echo $id; ?>">
                                
                                <button type="submit" class="confirm-action" data-confirm-message="Are you sure you want to delete this poster? This will remove it from the homepage grid permanently." style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; background-color: var(--clr-danger-bg); color: var(--clr-danger); font-size: 0.8rem; font-weight: 700; padding: 0.6rem 1rem; border-radius: var(--border-radius-sm); border: 1px solid rgba(239, 68, 68, 0.15);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    <span>Delete Poster</span>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer layout
include_once __DIR__ . '/includes/footer.php';
?>
