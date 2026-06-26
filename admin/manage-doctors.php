<?php
/**
 * VHRC Manage Doctors Panel (manage-doctors.php)
 */

// Include header layout (handles security and database connection)
include_once __DIR__ . '/includes/header.php';

$message = "";
$error = "";

// Handle file uploads directory
$upload_dir = dirname(__DIR__) . '/uploads/doctors';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 1. Handle Add Doctor Submit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_doctor') {
    $name = trim($_POST['name'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $qualifications = trim($_POST['qualifications'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $social_fb = trim($_POST['social_fb'] ?? '');
    $social_tw = trim($_POST['social_tw'] ?? '');
    $social_ig = trim($_POST['social_ig'] ?? '');
    $social_in = trim($_POST['social_in'] ?? '');
    
    $image_path = 'images/doctor_default.png'; // default fallback
    
    if (empty($name) || empty($specialty) || empty($qualifications)) {
        $error = "Please fill in all required fields (Name, Specialty, and Qualifications).";
    } else {
        // Handle Photo Upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['photo']['tmp_name'];
            $file_name = basename($_FILES['photo']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate extension
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($file_ext, $allowed_exts)) {
                $new_file_name = 'doc_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                $target_path = $upload_dir . '/' . $new_file_name;
                
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $image_path = 'uploads/doctors/' . $new_file_name;
                } else {
                    $error = "Failed to move uploaded photo.";
                }
            } else {
                $error = "Invalid photo format. Only JPG, JPEG, PNG, and WEBP files are allowed.";
            }
        }
        
        // Insert into database if no errors
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO doctors (name, specialty, qualifications, experience, image_path, social_fb, social_tw, social_ig, social_in) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $specialty, $qualifications, $experience, $image_path, $social_fb, $social_tw, $social_ig, $social_in]);
                $message = "New doctor profile created successfully.";
            } catch (PDOException $e) {
                $error = "Failed to save doctor details: " . $e->getMessage();
            }
        }
    }
}

// 1b. Handle Edit Doctor Submit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_doctor') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $qualifications = trim($_POST['qualifications'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $social_fb = trim($_POST['social_fb'] ?? '');
    $social_tw = trim($_POST['social_tw'] ?? '');
    $social_ig = trim($_POST['social_ig'] ?? '');
    $social_in = trim($_POST['social_in'] ?? '');
    
    if (empty($id) || empty($name) || empty($specialty) || empty($qualifications)) {
        $error = "Please fill in all required fields (Name, Specialty, and Qualifications).";
    } else {
        try {
            // Fetch current doctor info to retain/replace photo
            $stmt = $pdo->prepare("SELECT image_path FROM doctors WHERE id = ?");
            $stmt->execute([$id]);
            $current_image = $stmt->fetchColumn();
            $image_path = $current_image ? $current_image : 'images/doctor_default.png';
            
            // Handle Photo Upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['photo']['tmp_name'];
                $file_name = basename($_FILES['photo']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Validate extension
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($file_ext, $allowed_exts)) {
                    $new_file_name = 'doc_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                    $target_path = $upload_dir . '/' . $new_file_name;
                    
                    if (move_uploaded_file($file_tmp, $target_path)) {
                        $image_path = 'uploads/doctors/' . $new_file_name;
                        
                        // Delete old custom photo if it exists
                        if ($current_image && strpos($current_image, 'uploads/doctors/') === 0) {
                            $physical_path = dirname(__DIR__) . '/' . $current_image;
                            if (file_exists($physical_path)) {
                                unlink($physical_path);
                            }
                        }
                    } else {
                        $error = "Failed to move uploaded photo.";
                    }
                } else {
                    $error = "Invalid photo format. Only JPG, JPEG, PNG, and WEBP files are allowed.";
                }
            }
            
            // Update database if no errors
            if (empty($error)) {
                $stmt = $pdo->prepare("UPDATE doctors SET name = ?, specialty = ?, qualifications = ?, experience = ?, image_path = ?, social_fb = ?, social_tw = ?, social_ig = ?, social_in = ? WHERE id = ?");
                $stmt->execute([$name, $specialty, $qualifications, $experience, $image_path, $social_fb, $social_tw, $social_ig, $social_in, $id]);
                $message = "Doctor profile updated successfully.";
            }
        } catch (PDOException $e) {
            $error = "Failed to update doctor details: " . $e->getMessage();
        }
    }
}

// 2. Handle Delete Doctor (GET)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // Fetch doctor image path to delete the physical file
        $stmt = $pdo->prepare("SELECT image_path FROM doctors WHERE id = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetchColumn();
        
        // Delete row
        $del_stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
        $del_stmt->execute([$id]);
        
        // Delete image file if it is a user-uploaded file inside uploads/
        if ($img && strpos($img, 'uploads/doctors/') === 0) {
            $physical_path = dirname(__DIR__) . '/' . $img;
            if (file_exists($physical_path)) {
                unlink($physical_path);
            }
        }
        
        $message = "Doctor profile deleted successfully.";
    } catch (PDOException $e) {
        $error = "Failed to delete doctor: " . $e->getMessage();
    }
}

// Fetch all doctors from database
try {
    $stmt = $pdo->query("SELECT * FROM doctors ORDER BY id DESC");
    $doctors = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching doctor listings: " . $e->getMessage());
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

<!-- Section Control Header -->
<div class="doctors-control-header">
    <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.15rem; color: var(--clr-admin-brand);">Registered Medical Consultants</h3>
    <button class="btn-add-primary" id="btnOpenAddModal">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        <span>Add Doctor</span>
    </button>
</div>

<!-- Doctors Grid Panel -->
<div class="doctors-grid-panel">
    <?php if (empty($doctors)): ?>
        <div style="grid-column: 1 / -1; background-color: var(--clr-admin-bg-card); padding: 5rem; text-align: center; border: 1px dashed var(--clr-admin-border); border-radius: var(--border-radius-md); color: var(--clr-admin-text-muted);">
            No doctors have been registered yet. Click "Add Doctor" to create a profile.
        </div>
    <?php else: ?>
        <?php foreach ($doctors as $doctor): ?>
            <!-- Doctor Card -->
            <div class="doctor-admin-card">
                <div class="doctor-card-photo-wrap">
                    <img src="../<?php echo htmlspecialchars($doctor['image_path'] ?: 'images/doctor_default.png'); ?>" alt="Doctor Photo" class="doctor-card-photo" onerror="this.src='../images/doctor_default.png';">
                </div>
                <div class="doctor-card-details">
                    <h4 class="doctor-card-name"><?php echo htmlspecialchars($doctor['name']); ?></h4>
                    <span class="doctor-card-specialty"><?php echo htmlspecialchars($doctor['specialty']); ?></span>
                    <span class="doctor-card-meta">🎓 <?php echo htmlspecialchars($doctor['qualifications']); ?></span>
                    <?php if (!empty($doctor['experience'])): ?>
                        <span class="doctor-card-meta">💼 <?php echo htmlspecialchars($doctor['experience']); ?> Years Exp.</span>
                    <?php endif; ?>
                    
                    <!-- Social Config Previews -->
                    <div class="doctor-admin-socials" style="margin-top: 0.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <?php if (!empty($doctor['social_fb'])): ?>
                            <span style="font-size: 0.7rem; background: rgba(30, 41, 93, 0.1); color: var(--clr-admin-brand); padding: 0.15rem 0.4rem; border-radius: 4px;">FB</span>
                        <?php endif; ?>
                        <?php if (!empty($doctor['social_tw'])): ?>
                            <span style="font-size: 0.7rem; background: rgba(30, 41, 93, 0.1); color: var(--clr-admin-brand); padding: 0.15rem 0.4rem; border-radius: 4px;">X</span>
                        <?php endif; ?>
                        <?php if (!empty($doctor['social_ig'])): ?>
                            <span style="font-size: 0.7rem; background: rgba(30, 41, 93, 0.1); color: var(--clr-admin-brand); padding: 0.15rem 0.4rem; border-radius: 4px;">IG</span>
                        <?php endif; ?>
                        <?php if (!empty($doctor['social_in'])): ?>
                            <span style="font-size: 0.7rem; background: rgba(30, 41, 93, 0.1); color: var(--clr-admin-brand); padding: 0.15rem 0.4rem; border-radius: 4px;">IN</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="doctor-card-actions">
                        <button class="btn-edit-link btn-open-edit-modal" 
                                data-id="<?php echo $doctor['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($doctor['name']); ?>" 
                                data-specialty="<?php echo htmlspecialchars($doctor['specialty']); ?>" 
                                data-qualifications="<?php echo htmlspecialchars($doctor['qualifications']); ?>" 
                                data-experience="<?php echo htmlspecialchars($doctor['experience'] ?? ''); ?>" 
                                data-social-fb="<?php echo htmlspecialchars($doctor['social_fb'] ?? ''); ?>" 
                                data-social-tw="<?php echo htmlspecialchars($doctor['social_tw'] ?? ''); ?>" 
                                data-social-ig="<?php echo htmlspecialchars($doctor['social_ig'] ?? ''); ?>" 
                                data-social-in="<?php echo htmlspecialchars($doctor['social_in'] ?? ''); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/></svg>
                            <span>Edit</span>
                        </button>
                        <a href="manage-doctors.php?action=delete&id=<?php echo $doctor['id']; ?>" class="btn-delete-link confirm-action" data-confirm-message="Are you sure you want to delete this doctor profile?">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                            <span>Delete</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Add Doctor Modal -->
<div class="admin-modal" id="addDoctorModal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="admin-modal-overlay"></div>
    <div class="admin-modal-content">
        <div class="modal-head">
            <h3>Add New Consultant Profile</h3>
            <button class="modal-close-x btn-close-modal" aria-label="Close Modal">&times;</button>
        </div>
        
        <form action="manage-doctors.php" method="POST" enctype="multipart/form-data" class="modal-form">
            <input type="hidden" name="action" value="add_doctor">
            
            <div class="login-group">
                <label for="docName">Doctor Name <span class="required">*</span></label>
                <input type="text" id="docName" name="name" placeholder="Dr. Vikram Sharma" required>
            </div>

            <div class="login-group">
                <label for="docSpecialty">Clinical Specialty <span class="required">*</span></label>
                <input type="text" id="docSpecialty" name="specialty" placeholder="Cardiologist, Neurologist, Orthopedic" required>
            </div>

            <div class="login-group">
                <label for="docQuals">Qualifications <span class="required">*</span></label>
                <input type="text" id="docQuals" name="qualifications" placeholder="M.B.B.S., M.D. (Cardiology)" required>
            </div>

            <div class="login-group">
                <label for="docExp">Experience (Years)</label>
                <input type="number" id="docExp" name="experience" placeholder="10" min="0">
            </div>

            <!-- Social Media Handles Grid -->
            <div class="login-group-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 0.75rem;">
                <div class="login-group">
                    <label for="docFb">Facebook URL</label>
                    <input type="url" id="docFb" name="social_fb" placeholder="https://facebook.com/doctor">
                </div>
                <div class="login-group">
                    <label for="docTw">Twitter/X URL</label>
                    <input type="url" id="docTw" name="social_tw" placeholder="https://x.com/doctor">
                </div>
            </div>

            <div class="login-group-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 0.75rem;">
                <div class="login-group">
                    <label for="docIg">Instagram URL</label>
                    <input type="url" id="docIg" name="social_ig" placeholder="https://instagram.com/doctor">
                </div>
                <div class="login-group">
                    <label for="docIn">LinkedIn URL</label>
                    <input type="url" id="docIn" name="social_in" placeholder="https://linkedin.com/in/doctor">
                </div>
            </div>

            <div class="login-group">
                <label>Profile Picture</label>
                <div class="file-input-wrapper">
                    <span class="custom-file-btn">Choose Photo</span>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <small style="color: var(--clr-admin-text-muted); font-size: 0.75rem; margin-top: 0.25rem;">Supports JPG, JPEG, PNG, or WEBP.</small>
            </div>

            <div class="modal-form-actions">
                <button type="button" class="btn-secondary btn-close-modal">Cancel</button>
                <button type="submit" class="btn-add-primary" style="box-shadow: none;">Create Profile</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Doctor Modal -->
<div class="admin-modal" id="editDoctorModal" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="admin-modal-overlay"></div>
    <div class="admin-modal-content">
        <div class="modal-head">
            <h3>Edit Consultant Profile</h3>
            <button class="modal-close-x btn-close-modal" aria-label="Close Modal">&times;</button>
        </div>
        
        <form action="manage-doctors.php" method="POST" enctype="multipart/form-data" class="modal-form">
            <input type="hidden" name="action" value="edit_doctor">
            <input type="hidden" name="id" id="editDocId">
            
            <div class="login-group">
                <label for="editDocName">Doctor Name <span class="required">*</span></label>
                <input type="text" id="editDocName" name="name" placeholder="Dr. Vikram Sharma" required>
            </div>

            <div class="login-group">
                <label for="editDocSpecialty">Clinical Specialty <span class="required">*</span></label>
                <input type="text" id="editDocSpecialty" name="specialty" placeholder="Cardiologist, Neurologist, Orthopedic" required>
            </div>

            <div class="login-group">
                <label for="editDocQuals">Qualifications <span class="required">*</span></label>
                <input type="text" id="editDocQuals" name="qualifications" placeholder="M.B.B.S., M.D. (Cardiology)" required>
            </div>

            <div class="login-group">
                <label for="editDocExp">Experience (Years)</label>
                <input type="number" id="editDocExp" name="experience" placeholder="10" min="0">
            </div>

            <!-- Social Media Handles Grid -->
            <div class="login-group-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 0.75rem;">
                <div class="login-group">
                    <label for="editDocFb">Facebook URL</label>
                    <input type="url" id="editDocFb" name="social_fb" placeholder="https://facebook.com/doctor">
                </div>
                <div class="login-group">
                    <label for="editDocTw">Twitter/X URL</label>
                    <input type="url" id="editDocTw" name="social_tw" placeholder="https://x.com/doctor">
                </div>
            </div>

            <div class="login-group-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 0.75rem;">
                <div class="login-group">
                    <label for="editDocIg">Instagram URL</label>
                    <input type="url" id="editDocIg" name="social_ig" placeholder="https://instagram.com/doctor">
                </div>
                <div class="login-group">
                    <label for="editDocIn">LinkedIn URL</label>
                    <input type="url" id="editDocIn" name="social_in" placeholder="https://linkedin.com/in/doctor">
                </div>
            </div>

            <div class="login-group">
                <label>Profile Picture (Leave empty to keep current photo)</label>
                <div class="file-input-wrapper">
                    <span class="custom-file-btn" id="editDocPhotoLabel">Choose Photo</span>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <small style="color: var(--clr-admin-text-muted); font-size: 0.75rem; margin-top: 0.25rem;">Supports JPG, JPEG, PNG, or WEBP.</small>
            </div>

            <div class="modal-form-actions">
                <button type="button" class="btn-secondary btn-close-modal">Cancel</button>
                <button type="submit" class="btn-add-primary" style="box-shadow: none;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer layout
include_once __DIR__ . '/includes/footer.php';
?>
