<?php
// Load configuration and DB connection
require_once __DIR__ . '/includes/db_connect.php';

$booking_success = false;
$booking_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_appointment') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($phone) || empty($date)) {
        $booking_error = "Please fill in all required fields (Name, Phone, and Date).";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO appointments (patient_name, patient_phone, patient_email, appointment_date, department, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, $date, $department, $message]);
            $booking_success = true;
        } catch (PDOException $e) {
            $booking_error = "Booking failed. Please try again. " . $e->getMessage();
        }
    }
}

// Fetch clinical graphics posters titles
try {
    $posters_stmt = $pdo->query("SELECT * FROM gallery_posters ORDER BY id ASC");
    $posters = $posters_stmt->fetchAll();
} catch (PDOException $e) {
    $posters = []; // fallback
}

// Helper function to return specialty icons
function getSpecialtyIcon($specialty) {
    $spec = strtolower($specialty);
    if (strpos($spec, 'cardio') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>';
    } else if (strpos($spec, 'neuro') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>';
    } else if (strpos($spec, 'ortho') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><rect x="8" y="2" width="8" height="20" rx="2"/><path d="M20 14h-4m-8 0H4"/></svg>';
    } else if (strpos($spec, 'pediatric') !== false || strpos($spec, 'child') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v2m0 0v2m0-2h-2m2 0h2"/></svg>';
    } else if (strpos($spec, 'surg') !== false || strpos($spec, 'operation') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M12 2v20M2 12h20"/></svg>';
    } else {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6h2a6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg>';
    }
}

// Fetch registered doctors list
try {
    $doctors_stmt = $pdo->query("SELECT * FROM doctors ORDER BY id ASC");
    $doctors = $doctors_stmt->fetchAll();
} catch (PDOException $e) {
    $doctors = []; // fallback
}

// Fetch hero slides
try {
    $slides_stmt = $pdo->query("SELECT * FROM hero_slides ORDER BY slide_order ASC");
    $slides = $slides_stmt->fetchAll();
} catch (PDOException $e) {
    $slides = []; // fallback
}

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
    <title>VHRC - Vindhya Hospital & Research Centre</title>
    <meta name="description" content="Welcome to Vindhya Hospital & Research Centre (VHRC). Rewa's best healthcare facilities open 24 hours. Book your appointment today.">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Website Favicon -->
    <?php if (!empty($settings['website_favicon']) && file_exists(__DIR__ . '/' . $settings['website_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['website_favicon']); ?>?v=<?php echo filemtime(__DIR__ . '/' . $settings['website_favicon']); ?>">
    <?php else: ?>
        <link rel="icon" href="images/logo.png">
    <?php endif; ?>
</head>
<body>

    <!-- PHP Submission Alert Toasts -->
    <?php if ($booking_success): ?>
        <div class="booking-toast-wrap" id="bookingToast">
            <div class="booking-toast">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <div class="toast-content">
                    <h4>Appointment Submitted!</h4>
                    <p>Your booking request was registered. Admin will review and update status.</p>
                </div>
                <button class="toast-close" onclick="document.getElementById('bookingToast').remove();">&times;</button>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($booking_error)): ?>
        <div class="booking-toast-wrap error" id="bookingToast">
            <div class="booking-toast">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="toast-content">
                    <h4>Booking Failed</h4>
                    <p><?php echo htmlspecialchars($booking_error); ?></p>
                </div>
                <button class="toast-close" onclick="document.getElementById('bookingToast').remove();">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Infinite Scrolling Alert Ticker -->
    <div class="ticker-wrap">
        <div class="ticker">
            <div class="ticker__group">
                <span>👉 Your Health, Our Priority &bull; 24&times;7 Emergency Services &bull; VHRC Rewa: Committed to Medical Excellence &bull; Emergency Hotline: +91 9589899826</span>
                <span>👉 Your Health, Our Priority &bull; 24&times;7 Emergency Services &bull; VHRC Rewa: Committed to Medical Excellence &bull; Emergency Hotline: +91 9589899826</span>
            </div>
            <div class="ticker__group" aria-hidden="true">
                <span>👉 Your Health, Our Priority &bull; 24&times;7 Emergency Services &bull; VHRC Rewa: Committed to Medical Excellence &bull; Emergency Hotline: +91 9589899826</span>
                <span>👉 Your Health, Our Priority &bull; 24&times;7 Emergency Services &bull; VHRC Rewa: Committed to Medical Excellence &bull; Emergency Hotline: +91 9589899826</span>
            </div>
        </div>
    </div>

    <!-- Site Header -->
    <header class="site-header">
        
        <!-- 1. Top Contact & Social Bar -->
        <div class="top-bar">
            <div class="container">
                <div class="top-info">
                    <div class="info-item">
                        <!-- Lucide Clock Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span><?php echo htmlspecialchars($settings['header_hours'] ?? 'Mon - Sun 0900 - 2100'); ?></span>
                    </div>
                    <div class="info-item">
                        <!-- Lucide Phone Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $settings['header_phone'] ?? '+919589899826')); ?>"><?php echo htmlspecialchars($settings['header_phone'] ?? '+91 9589899826'); ?></a>
                    </div>
                    <div class="info-item">
                        <!-- Lucide Mail Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <a href="mailto:<?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?>"><?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?></a>
                    </div>
                </div>
                
                <div class="top-socials">
                    <!-- Facebook -->
                    <a href="<?php echo htmlspecialchars($settings['social_fb'] ?? '#'); ?>" class="social-link" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                    </a>
                    <!-- LinkedIn -->
                    <a href="<?php echo htmlspecialchars($settings['social_in'] ?? '#'); ?>" class="social-link" aria-label="LinkedIn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                    </a>
                    <!-- Pinterest -->
                    <a href="<?php echo htmlspecialchars($settings['social_pin'] ?? '#'); ?>" class="social-link" aria-label="Pinterest">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.966 1.406-5.966s-.359-.72-.359-1.781c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.621 0 11.988-5.367 11.988-11.988C24.005 5.368 18.638 0 12.017 0z"/></svg>
                    </a>
                    <!-- Twitter -->
                    <a href="<?php echo htmlspecialchars($settings['social_tw'] ?? '#'); ?>" class="social-link" aria-label="Twitter">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                    </a>
                    <!-- YouTube -->
                    <a href="<?php echo htmlspecialchars($settings['social_yt'] ?? '#'); ?>" class="social-link" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
                    </a>
                    <!-- Instagram -->
                    <a href="<?php echo htmlspecialchars($settings['social_ig'] ?? '#'); ?>" class="social-link" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Middle Brand & Badges Bar -->
        <div class="brand-bar">
            <div class="container">
                <div class="header-badges">
                    <!-- Badge 1: Trusted By -->
                    <div class="header-badge">
                        <div class="badge-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="badge-content">
                            <span class="badge-label"><?php echo htmlspecialchars($settings['accred1_title'] ?? 'Trusted By'); ?></span>
                            <span class="badge-value"><?php echo htmlspecialchars($settings['accred1_desc'] ?? '120,000+ People'); ?></span>
                        </div>
                    </div>
                    <!-- Badge 2: Best Hospital -->
                    <div class="header-badge">
                        <div class="badge-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><path d="M8.21 13.89 7 23l5-3 5 3-1.21-9.12"/></svg>
                        </div>
                        <div class="badge-content">
                            <span class="badge-label"><?php echo htmlspecialchars($settings['accred2_title'] ?? 'Best Hospital'); ?></span>
                            <span class="badge-value"><?php echo htmlspecialchars($settings['accred2_desc'] ?? 'Rewa (M.P.)'); ?></span>
                        </div>
                    </div>
                    <!-- Badge 3: Open 24 Hours -->
                    <div class="header-badge">
                        <div class="badge-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                        </div>
                        <div class="badge-content">
                            <span class="badge-label">Open 24 Hours</span>
                            <span class="badge-value">Services & Facilities</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Bottom Main Navigation Bar (Sticky) -->
        <nav class="main-nav-bar">
            <div class="container">
                <div class="nav-overlay"></div>
                
                <!-- Logo Container with Diagonal Background wrapper -->
                <div class="nav-logo-bg-wrap">
                    <a href="index.php" class="logo-container" aria-label="VHRC Home">
                        <img src="images/logo.png" alt="VHRC Logo" class="site-logo">
                    </a>
                </div>

                <div class="nav-menu-wrapper">
                    <ul class="nav-menu">
                        <li class="active"><a href="#" class="nav-link">Home</a></li>
                        <li><a href="#" class="nav-link">About Us</a></li>
                        <li class="nav-item-dropdown">
                            <a href="#" class="nav-link dropdown-trigger">
                                Department
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </a>
                            <div class="dropdown-menu">
                                <a href="#">Cardiology</a>
                                <a href="#">Neurology</a>
                                <a href="#">Orthopedics</a>
                                <a href="#">Pediatrics</a>
                                <a href="#">Emergency Medicine</a>
                            </div>
                        </li>
                        <li class="nav-item-dropdown">
                            <a href="#" class="nav-link dropdown-trigger">
                                Treatment
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </a>
                            <div class="dropdown-menu">
                                <a href="#">Surgical Care</a>
                                <a href="#">Physical Therapy</a>
                                <a href="#">Diagnostics & Lab</a>
                                <a href="#">Outpatient Care</a>
                            </div>
                        </li>
                        <li><a href="#" class="nav-link">Our Doctor</a></li>
                        <li><a href="#" class="nav-link">Gallery</a></li>
                        <li><a href="#" class="nav-link">Blog</a></li>
                        <li><a href="#" class="nav-link">Contact</a></li>
                    </ul>
                    
                    <a href="#contact" class="btn-cta">
                        <span>Contact Now</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Hamburger menu button (mobile only) -->
                <button class="mobile-toggle" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Main Content / Hero Slider Showcase -->
    <main>
        <section class="hero-slider-section">
            <div class="hero-slider">
                <?php if (empty($slides)): ?>
                    <!-- Slide 1 Fallback -->
                    <div class="hero-slide active" style="background-image: url('images/slide1.png');">
                        <div class="slide-overlay"></div>
                        <div class="container">
                            <div class="slide-content">
                                <span class="slide-tag">👉 Patient-Centred Healthcare</span>
                                <h1 class="slide-title">Healing with <br><span>Compassion & Care</span></h1>
                                <p class="slide-desc">Vindhya Hospital provides world-class clinical treatments under the gentle care of our certified specialists.</p>
                                <div class="slide-actions">
                                    <a href="#appointment" class="btn-cta">Book Appointment</a>
                                    <a href="#services" class="btn-outline">Our Services</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($slides as $index => $slide): ?>
                        <div class="hero-slide <?php echo ($index === 0) ? 'active' : ''; ?>" style="background-image: url('<?php echo htmlspecialchars($slide['image_path']); ?>');">
                            <div class="slide-overlay"></div>
                            <div class="container">
                                <div class="slide-content">
                                    <span class="slide-tag"><?php echo htmlspecialchars($slide['tag'] ?? ''); ?></span>
                                    <h1 class="slide-title"><?php echo $slide['title'] ?? ''; ?></h1>
                                    <p class="slide-desc"><?php echo htmlspecialchars($slide['description'] ?? ''); ?></p>
                                    <div class="slide-actions">
                                        <?php if (!empty($slide['cta1_text'])): ?>
                                            <a href="<?php echo htmlspecialchars($slide['cta1_link'] ?? '#'); ?>" class="btn-cta"><?php echo htmlspecialchars($slide['cta1_text']); ?></a>
                                        <?php endif; ?>
                                        <?php if (!empty($slide['cta2_text'])): ?>
                                            <a href="<?php echo htmlspecialchars($slide['cta2_link'] ?? '#'); ?>" class="btn-outline"><?php echo htmlspecialchars($slide['cta2_text']); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Slider Controls -->
            <button class="slider-arrow prev-arrow" aria-label="Previous Slide">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button class="slider-arrow next-arrow" aria-label="Next Slide">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
            </button>

            <!-- Slider Indicators -->
            <div class="slider-dots">
                <?php if (empty($slides)): ?>
                    <span class="dot active" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                <?php else: ?>
                    <?php foreach ($slides as $index => $slide): ?>
                        <span class="dot <?php echo ($index === 0) ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Trust, Emergency & Stats Section -->
        <section class="trust-info-section">
            <div class="container">
                <!-- 1. Emergency Service Card -->
                <div class="emergency-card-wrap">
                    <div class="emergency-card">
                        <div class="emergency-badge">
                            <span class="pulse-ring"></span>
                            <svg class="emergency-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M3.22 12H9.5l1.5-4.5 2 9 1.5-6 1.5 1.5h3.8"/></svg>
                        </div>
                        <div class="emergency-content">
                            <h2><?php echo htmlspecialchars($settings['emergency_title'] ?? '24/7 Emergency Medical Services'); ?></h2>
                            <p><?php echo htmlspecialchars($settings['emergency_desc'] ?? 'Vindhya Hospital, Rewa provides 24&times;7 rapid response emergency care with advanced diagnostic facilities and expert trauma specialists for immediate medical support.'); ?></p>
                        </div>
                        <a href="<?php echo htmlspecialchars($settings['emergency_btn_link'] ?? '#appointment'); ?>" class="btn-emergency-cta"><?php echo htmlspecialchars($settings['emergency_btn_text'] ?? 'Book An Appointment'); ?></a>
                    </div>
                </div>

                <!-- 2. Accreditation Bar -->
                <div class="accreditations-bar">
                    <div class="accreditation-item">
                        <div class="accred-seal-gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="accreditation-text">
                            <h3><?php echo htmlspecialchars($settings['accred1_title'] ?? 'National & International Standards'); ?></h3>
                            <p><?php echo htmlspecialchars($settings['accred1_desc'] ?? 'Accredited Organization: Vindhya Hospital, Rewa'); ?></p>
                        </div>
                    </div>
                    <div class="accreditation-divider"></div>
                    <div class="accreditation-item">
                        <div class="accred-seal-nabh">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="accreditation-text">
                            <h3><?php echo htmlspecialchars($settings['accred2_title'] ?? 'NABH & ISO Certified Quality'); ?></h3>
                            <p><?php echo htmlspecialchars($settings['accred2_desc'] ?? 'Offering best-in-class healthcare services and patient safety standards'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- 3. Hospital Stats Bar -->
                <div class="stats-counter-bar">
                    <!-- Stat 1 -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-stethoscope"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6h2a6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo htmlspecialchars($settings['stat1_number'] ?? '24+'); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($settings['stat1_label'] ?? 'Specialist Doctors'); ?></span>
                        </div>
                    </div>
                    <!-- Stat 2 -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo htmlspecialchars($settings['stat2_number'] ?? '29,000+'); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($settings['stat2_label'] ?? 'Patients Served'); ?></span>
                        </div>
                    </div>
                    <!-- Stat 3 -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><circle cx="6" cy="12" r="2"/></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo htmlspecialchars($settings['stat3_number'] ?? '100+'); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($settings['stat3_label'] ?? 'Advanced Care Beds'); ?></span>
                        </div>
                    </div>
                    <!-- Stat 4 -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><path d="M8.21 13.89 7 23l5-3 5 3-1.21-9.12"/></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo htmlspecialchars($settings['stat4_number'] ?? '15+'); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($settings['stat4_label'] ?? 'Years of Excellence'); ?></span>
                        </div>
                    </div>
                </div>
        </section>

        <!-- 4. Clinical Graphics Showcase Section (Alternating Theme Cycle: White) -->
        <?php if (!empty($posters)): ?>
        <section class="graphics-showcase-section section-bg-white">
            <div class="container">
                <div class="section-header">
                    <span class="section-badge">👉 Clinical Portfolio</span>
                    <h2 class="section-title">Specialities & Services</h2>
                    <p class="section-desc">View our advanced clinical department graphics, trauma guidelines, and specialized medical service flyers.</p>
                </div>
 
                <div class="graphics-grid">
                    <?php foreach ($posters as $poster): ?>
                        <div class="graphic-card">
                            <div class="graphic-image-wrap">
                                <img src="<?php echo htmlspecialchars($poster['image_path']); ?>" alt="Clinical Graphic" class="graphic-img">
                                <div class="graphic-hover-overlay">
                                    <div class="zoom-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                    </div>
                                    <span class="hover-text">Zoom Poster</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Our Experts Doctors Slider Section (Alternating Theme Cycle: Dark) -->
        <?php if (!empty($doctors)): ?>
        <section id="doctors" class="doctors-slider-section section-bg-dark">
            <div class="container">
                <div class="doctors-section-header">
                    <div class="header-left">
                        <span class="doctors-badge">Our Experts</span>
                        <h2 class="doctors-title">Expert Coaching & Facilities <br><span>Built for Healing</span></h2>
                    </div>
                    <div class="header-right">
                        <a href="#appointment" class="btn-view-experts">
                            <span>View All Experts</span>
                            <span class="arrow-circle">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Slider Track / Wrapper -->
                <div class="doctors-carousel-container">
                    <button class="carousel-nav-btn prev" aria-label="Scroll Left">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    
                    <div class="doctors-carousel-track">
                        <?php foreach ($doctors as $doctor): ?>
                            <!-- Doctor Card -->
                            <div class="doctor-slide-card">
                                <div class="doctor-image-container">
                                    <img src="<?php echo htmlspecialchars($doctor['image_path'] ?: 'images/doctor_default.png'); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>" class="doctor-img" onerror="this.src='images/doctor_default.png';">
                                                       <!-- Hover Floating Social Capsule overlay -->
                                    <div class="social-hover-capsule">
                                        <a href="<?php echo htmlspecialchars(!empty($doctor['social_fb']) ? $doctor['social_fb'] : '#'); ?>" target="_blank" class="social-icon" aria-label="Facebook">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                                        </a>
                                        <a href="<?php echo htmlspecialchars(!empty($doctor['social_tw']) ? $doctor['social_tw'] : '#'); ?>" target="_blank" class="social-icon" aria-label="Twitter">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>
                                        </a>
                                        <a href="<?php echo htmlspecialchars(!empty($doctor['social_ig']) ? $doctor['social_ig'] : '#'); ?>" target="_blank" class="social-icon" aria-label="Instagram">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                                        </a>
                                        <a href="<?php echo htmlspecialchars(!empty($doctor['social_in']) ? $doctor['social_in'] : '#'); ?>" target="_blank" class="social-icon" aria-label="LinkedIn">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Glassmorphic Info Card Overlay at the Bottom -->
                                <div class="doctor-info-glass">
                                    <span class="doctor-specialty-badge">
                                        <?php echo getSpecialtyIcon($doctor['specialty']); ?>
                                        <?php echo htmlspecialchars($doctor['specialty'] ?: 'Consultant'); ?>
                                    </span>
                                    <h3 class="doc-name"><?php echo htmlspecialchars($doctor['name']); ?></h3>
                                    <p class="doc-experience"><?php echo htmlspecialchars($doctor['experience'] ?? '0'); ?> Years Experience</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button class="carousel-nav-btn next" aria-label="Scroll Right">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>

                <!-- Custom Scroll Progress Bar -->
                <div class="carousel-progress-wrap">
                    <div class="carousel-progress-bar"></div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- 5. Appointment Booking Section (Alternating Theme Cycle: Soft Gray) -->
        <section id="appointment" class="appointment-booking-section section-bg-soft">
            <div class="container">
                <div class="booking-grid">
                    <!-- Left Column: Info Card -->
                    <div class="booking-info-card">
                        <span class="section-badge">👉 Connect With Us</span>
                        <h2 class="booking-title">Book an Appointment</h2>
                        <p class="booking-desc">Schedule your clinical consultation with VHRC's certified experts. For urgent emergencies, please call our hotline immediately.</p>
                        
                        <div class="booking-details-list">
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </div>
                                <div class="detail-text">
                                    <span>24/7 Emergency Hotline</span>
                                    <strong><?php echo htmlspecialchars($settings['booking_hotline'] ?? '+91 9589899826'); ?></strong>
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <div class="detail-text">
                                    <span>Clinical Hours</span>
                                    <strong><?php echo htmlspecialchars($settings['booking_hours'] ?? 'Mon - Sun: 09:00 AM - 09:00 PM'); ?></strong>
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <div class="detail-text">
                                    <span>Hospital Address</span>
                                    <strong><?php echo htmlspecialchars($settings['booking_address'] ?? 'Narendra Nagar, Amaiya Colony, Rewa (M.P.)'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Form Card -->
                    <div class="booking-form-card">
                        <form action="index.php#appointment" method="POST" class="booking-form">
                            <input type="hidden" name="action" value="book_appointment">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="required">*</span></label>
                                    <input type="text" id="name" name="name" placeholder="John Doe" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number <span class="required">*</span></label>
                                    <input type="tel" id="phone" name="phone" placeholder="+91 XXXXX XXXXX" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" placeholder="john@example.com">
                                </div>
                                <div class="form-group">
                                    <label for="date">Appointment Date <span class="required">*</span></label>
                                    <input type="date" id="date" name="date" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="department">Select Specialty</label>
                                <select id="department" name="department">
                                    <option value="General Medicine">General Medicine</option>
                                    <option value="Urology & Kidney Center">Urology & Kidney Center</option>
                                    <option value="Gastroenterology Unit">Gastroenterology Unit</option>
                                    <option value="Advanced Surgical Care">Advanced Surgical Care</option>
                                    <option value="Pulmonology Department">Pulmonology Department</option>
                                    <option value="Oncology & Cancer Care">Oncology & Cancer Care</option>
                                    <option value="Mother & Child Clinic">Mother & Child Clinic</option>
                                    <option value="ICU & Critical Care">ICU & Critical Care</option>
                                    <option value="Dialysis Department">Dialysis Department</option>
                                    <option value="Orthopedics Center">Orthopedics Center</option>
                                    <option value="Neurology Services">Neurology Services</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="message">Describe Symptoms / Message</label>
                                <textarea id="message" name="message" rows="4" placeholder="Briefly describe your symptoms or booking request..."></textarea>
                            </div>

                            <button type="submit" class="btn-cta submit-btn">
                                <span>Submit Request</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 7-7 7 7"/><path d="M12 5v14"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Lightbox Modal for Clinical Posters -->
    <div class="lightbox-modal" id="lightboxModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="lightbox-overlay"></div>
        <div class="lightbox-container">
            <button class="lightbox-close" aria-label="Close Poster">&times;</button>
            <div class="lightbox-content">
                <img src="" alt="High Resolution Poster" class="lightbox-image">
            </div>
        </div>
    </div>

    <!-- Custom JS Scripts -->
    <script src="js/main.js"></script>
</body>
</html>
