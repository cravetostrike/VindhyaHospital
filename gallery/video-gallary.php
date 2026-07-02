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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Gallery - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Explore our video gallery showcasing the best of Vindhya Hospital &amp; Research Centre, Rewa.">
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
    $active_page = 'video-gallary';
    require_once __DIR__ . '/../includes/header.php';
    ?>

    <main class="main-content" style="padding: 100px 0; min-height: 50vh; display: flex; align-items: center; justify-content: center; background-color: #f8fafc;">
        <div class="container" style="text-align: center;">
            <h1 style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 800; color: var(--clr-brand); margin-bottom: 15px;">Video Gallery</h1>
            <p style="font-size: 1.1rem; color: var(--clr-text-muted); max-width: 600px; margin: 0 auto 30px auto;">Explore our video gallery showcasing the best of Vindhya Hospital & Research Centre, Rewa.</p>
            <a href="video-gallary/" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:18px; height:18px;"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Video Gallery
            </a>
        </div>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>