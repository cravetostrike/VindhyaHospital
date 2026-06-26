<?php
/**
 * VHRC Administrative Header Template
 */

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Route Security Check: Redirect to login if user session is not verified
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

// Load database connection
require_once dirname(dirname(__DIR__)) . '/includes/db_connect.php';

// Fetch admin favicon setting
$admin_favicon_path = '';
try {
    $fav_stmt = $pdo->prepare("SELECT value FROM homepage_settings WHERE key = 'admin_favicon'");
    $fav_stmt->execute();
    $admin_favicon_path = $fav_stmt->fetchColumn();
} catch (PDOException $e) {
    // ignore
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VHRC Admin Dashboard - Vindhya Hospital</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Separate Dedicated Admin Stylesheet -->
    <link rel="stylesheet" href="../css/admin.css">
    
    <!-- Admin Favicon -->
    <?php if (!empty($admin_favicon_path) && file_exists(dirname(dirname(__DIR__)) . '/' . $admin_favicon_path)): ?>
        <link rel="icon" href="../<?php echo htmlspecialchars($admin_favicon_path); ?>?v=<?php echo filemtime(dirname(dirname(__DIR__)) . '/' . $admin_favicon_path); ?>">
    <?php else: ?>
        <link rel="icon" href="../images/logo.png">
    <?php endif; ?>
</head>
<body>

    <?php if ($current_page !== 'login.php'): ?>
    <div class="admin-wrapper">
        
        <!-- Sidebar Navigation Included -->
        <?php include_once __DIR__ . '/sidebar.php'; ?>

        <!-- Content Panel Shell -->
        <div class="admin-container">
            
            <!-- Global Admin Top Header -->
            <header class="admin-header">
                <div class="header-title-area">
                    <h2>
                        <?php
                        switch ($current_page) {
                            case 'index.php': echo 'Dashboard Overview'; break;
                            case 'manage-appointments.php': echo 'Manage Bookings'; break;
                            case 'manage-doctors.php': echo 'Manage Medical Staff'; break;
                            case 'manage-gallery.php': echo 'Homepage Poster Graphics'; break;
                            case 'manage-homepage.php': echo 'Manage Homepage CMS'; break;
                            case 'settings.php': echo 'Site Settings'; break;
                            default: echo 'VHRC Admin Portal'; break;
                        }
                        ?>
                    </h2>
                </div>
                
                <div class="header-user">
                    <div class="user-avatar">A</div>
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator'); ?></span>
                        <small>Super Administrator</small>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Container Starts -->
            <main class="admin-main">
    <?php endif; ?>
