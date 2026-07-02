<?php
// Load configuration and DB connection from parent directory
require_once __DIR__ . '/../includes/db_connect.php';

// Fetch homepage settings
try {
    $settings_stmt = $pdo->query("SELECT * FROM homepage_settings");
    $raw_settings = $settings_stmt->fetchAll();
    $settings = [];
    foreach ($raw_settings as $row) {
        $settings[$row['key']] = $row['value'];
    }
} catch (PDOException $e) {
    $settings = []; // fallback
}

// Fetch all gallery posters
try {
    $posters_stmt = $pdo->query("SELECT * FROM gallery_posters ORDER BY id DESC");
    $posters = $posters_stmt->fetchAll();
} catch (PDOException $e) {
    $posters = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Explore our gallery showcasing the best of Vindhya Hospital &amp; Research Centre, Rewa.">
    <base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'; ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Website Favicon -->
    <?php if (!empty($settings['website_favicon']) && file_exists(__DIR__ . '/../' . $settings['website_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['website_favicon']); ?>" type="image/x-icon">
    <?php endif; ?>
</head>

<body>
    <?php
    $active_page = 'gallery';
    require_once __DIR__ . '/../includes/header.php';
    ?>

    <!-- Main Banner Header -->
    <section class="about-hero-section">
        <div class="about-hero-overlay"></div>
        <div class="container">
            <div class="about-hero-content">
                <span class="about-hero-tag">📷 OUR GALLERY</span>
                <h1 class="about-hero-title">Photo <span>Gallery</span></h1>
                <p class="about-hero-desc">Explore our state-of-the-art facilities, advanced medical equipment, clinical achievements, and dedicated staff members.</p>
            </div>
        </div>
    </section>

    <!-- Gallery Grid Section -->
    <main class="main-content" style="padding: 80px 0; background-color: #f8fafc;">
        <div class="container">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h2 style="font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800; color: #0f172a; line-height: 1.2;">Clinical Portfolio & Showcase</h2>
                    <p style="color: #64748b; font-size: 0.95rem; margin-top: 4px;">Click on any image to view it in full screen.</p>
                </div>
                <!-- CMS Manage Button -->
               
            </div>

            <?php if (empty($posters)): ?>
                <div style="text-align: center; color: #64748b; padding: 80px 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 64px; height: 64px; margin-bottom: 20px; opacity: 0.5;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b;">No Images Found</h3>
                    <p style="margin-top: 6px; font-size: 0.95rem;">Please log in to the CMS panel to upload photos to the gallery.</p>
                </div>
            <?php else: ?>
                <div class="graphics-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
                    <?php foreach ($posters as $poster): ?>
                        <div class="graphic-card">
                            <div class="graphic-image-wrap">
                                <img src="<?php echo htmlspecialchars($poster['image_path']); ?>" alt="Vindhya Gallery" class="graphic-img">
                                <div class="graphic-hover-overlay">
                                    <div class="zoom-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <circle cx="11" cy="11" r="8" />
                                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                            <line x1="11" y1="8" x2="11" y2="14" />
                                            <line x1="8" y1="11" x2="14" y2="11" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <!-- Lightbox Modal for Gallery Images -->
    <div class="lightbox-modal" id="lightboxModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="lightbox-overlay"></div>
        <div class="lightbox-container">
            <button class="lightbox-close" aria-label="Close Image">&times;</button>
            <div class="lightbox-content">
                <img src="" alt="High Resolution Gallery Image" class="lightbox-image">
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>