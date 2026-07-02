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
function getSpecialtyIcon($specialty)
{
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

function getDoctorSlug($name)
{
    // Remove Dr. / Dr / DR / DR. prefix
    $name = preg_replace('/^dr\.\s*|^dr\s+/i', '', trim($name));
    // Convert to lowercase
    $name = strtolower($name);
    // Replace non-alphanumeric characters with hyphens
    $name = preg_replace('/[^a-z0-9]+/i', '-', $name);
    // Trim hyphens
    return trim($name, '-');
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
    <?php endif; ?>

</head>

<body>

    <!-- PHP Submission Alert Toasts -->
    <?php if ($booking_success): ?>
        <div class="booking-toast-wrap" id="bookingToast">
            <div class="booking-toast">
                <div class="toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6 9 17l-5-5" />
                    </svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
                <div class="toast-content">
                    <h4>Booking Failed</h4>
                    <p><?php echo htmlspecialchars($booking_error); ?></p>
                </div>
                <button class="toast-close" onclick="document.getElementById('bookingToast').remove();">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $active_page = 'home';
    require_once __DIR__ . '/includes/header.php';
    ?>

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
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </button>
            <button class="slider-arrow next-arrow" aria-label="Next Slide">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="m9 18 6-6-6-6" />
                </svg>
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
                            <svg class="emergency-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                <path d="M3.22 12H9.5l1.5-4.5 2 9 1.5-6 1.5 1.5h3.8" />
                            </svg>
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>
                        <div class="accreditation-text">
                            <h3><?php echo htmlspecialchars($settings['accred1_title'] ?? 'National & International Standards'); ?></h3>
                            <p><?php echo htmlspecialchars($settings['accred1_desc'] ?? 'Accredited Organization: Vindhya Hospital, Rewa'); ?></p>
                        </div>
                    </div>
                    <div class="accreditation-divider"></div>
                    <div class="accreditation-item">
                        <div class="accred-seal-nabh">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-stethoscope">
                                <path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3" />
                                <path d="M8 15v1a6 6 0 0 0 6 6h2a6 6 0 0 0 6-6v-4" />
                                <circle cx="20" cy="10" r="2" />
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo htmlspecialchars($settings['stat1_number'] ?? '24+'); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($settings['stat1_label'] ?? 'Specialist Doctors'); ?></span>
                        </div>
                    </div>
                    <!-- Stat 2 -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo htmlspecialchars($settings['stat2_number'] ?? '29,000+'); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($settings['stat2_label'] ?? 'Patients Served'); ?></span>
                        </div>
                    </div>
                    <!-- Stat 3 -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 4v16" />
                                <path d="M2 8h18a2 2 0 0 1 2 2v10" />
                                <path d="M2 17h20" />
                                <circle cx="6" cy="12" r="2" />
                            </svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo htmlspecialchars($settings['stat3_number'] ?? '100+'); ?></span>
                            <span class="stat-label"><?php echo htmlspecialchars($settings['stat3_label'] ?? 'Advanced Care Beds'); ?></span>
                        </div>
                    </div>
                    <!-- Stat 4 -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="7" />
                                <path d="M8.21 13.89 7 23l5-3 5 3-1.21-9.12" />
                            </svg>
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
                    <div class="doctor-section-header">
                        <div class="doctor-badge-wrapper">
                            <span class="doctor-badge-pill portfolio-badge-pill">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-icon"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                CLINICAL PORTFOLIO
                            </span>
                        </div>
                        <h2 class="doctor-section-title">Specialities & Services <br><span>Clinical Graphics Showcase</span></h2>
                        <p class="doctor-section-desc">View our advanced clinical department graphics, trauma guidelines, and specialized medical service flyers.</p>
                    </div>

                    <div class="graphics-grid">
                        <?php foreach ($posters as $poster): ?>
                            <div class="graphic-card">
                                <div class="graphic-image-wrap">
                                    <img src="<?php echo htmlspecialchars($poster['image_path']); ?>" alt="Clinical Graphic" class="graphic-img">
                                    <div class="graphic-hover-overlay">
                                        <div class="zoom-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <circle cx="11" cy="11" r="8" />
                                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                                <line x1="11" y1="8" x2="11" y2="14" />
                                                <line x1="8" y1="11" x2="14" y2="11" />
                                            </svg>
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

        <!-- Our Experts Doctors Section -->
        <?php if (!empty($doctors)): ?>
            <section id="doctors" class="doctors-slider-section">
                <div class="container">
                    <div class="doctor-section-header">
                        <div class="doctor-badge-wrapper">
                            <span class="doctor-badge-pill">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-icon"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                TOP MEDICAL EXPERTS
                            </span>
                        </div>
                        <h2 class="doctor-section-title">Meet Expert Doctors <br><span>Expert Behind Your Health</span></h2>
                        <p class="doctor-section-desc">World-class healthcare professionals dedicated to providing exceptional care with compassion and expertise</p>
                    </div>

                    <!-- Slider Track / Wrapper -->
                    <div class="doctors-carousel-container">
                        <button class="carousel-nav-btn prev" aria-label="Scroll Left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                        </button>

                        <div class="doctors-carousel-track">
                            <?php foreach ($doctors as $doctor): ?>
                                <a href="doctor/<?php echo getDoctorSlug($doctor['name']); ?>.php" class="home-doctor-card doctor-slide-card" style="text-decoration: none; color: inherit; display: flex;">
                                    <div class="home-doctor-img-wrap">
                                        <img src="<?php echo htmlspecialchars($doctor['image_path'] ?: 'images/doctor_default.png'); ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>" class="home-doctor-img" onerror="this.src='images/doctor_default.png';">
                                        
                                        <!-- Overlays -->
                                        <div class="home-doctor-overlay-specialty">
                                            <span class="home-doctor-badge-specialty">
                                                <?php echo getSpecialtyIcon($doctor['specialty']); ?>
                                                <?php echo htmlspecialchars($doctor['specialty'] ?: 'Specialist'); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="home-doctor-overlay-rating">
                                            <span class="home-doctor-badge-rating">
                                                <svg viewBox="0 0 24 24" fill="currentColor" class="star-icon"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                                <?php
                                                $rating = ($doctor['id'] % 2 === 0) ? '4.8' : '4.9';
                                                echo $rating;
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="home-doctor-info">
                                        <h3 class="home-doctor-name"><?php echo htmlspecialchars($doctor['name']); ?></h3>
                                        <span class="home-doctor-title">
                                            <?php
                                            if (strtolower($doctor['specialty']) === 'urology') {
                                                echo 'Senior Urologist';
                                            } else if (strtolower($doctor['specialty']) === 'gynecology') {
                                                echo 'Gynecologist';
                                            } else if (strpos(strtolower($doctor['specialty']), 'medicine') !== false) {
                                                echo 'Medicine Specialist';
                                            } else {
                                                echo htmlspecialchars($doctor['specialty']) . ' Specialist';
                                            }
                                            ?>
                                        </span>
                                        
                                        <div class="home-doctor-meta">
                                            <div class="meta-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="meta-icon"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                                <span><?php echo htmlspecialchars($doctor['experience'] ?: '5'); ?>+ Years</span>
                                            </div>
                                            <div class="meta-divider">|</div>
                                            <div class="meta-item">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="meta-icon"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                <span>
                                                    <?php
                                                    $patients = intval($doctor['experience'] ?: 5) * 400 . '+ Patients';
                                                    echo $patients;
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <button class="carousel-nav-btn next" aria-label="Scroll Right">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </button>
                    </div>

                    <!-- Custom Scroll Progress Bar -->
                    <div class="carousel-progress-wrap">
                        <div class="carousel-progress-bar"></div>
                    </div>

                    <div class="home-doctors-footer">
                        <div class="home-doctors-trust-info">
                            <span class="trust-dot">●</span> 24/7 Available
                            <span class="trust-dot">●</span> Book Instantly
                            <span class="trust-dot">●</span> Trusted by 50,000+ Patients
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php
        $departments = [
            [
                'name'        => 'Urology',
                'sub_title'   => 'Kidney & Urinary System Care',
                'link'        => 'departments/urology.php',
                'theme_class' => 'dept-urology',
                'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-4.3-7-11-7-11S5 10.7 5 15a7 7 0 0 0 7 7z"/></svg>'
            ],
            [
                'name'        => 'Critical Care ICU',
                'sub_title'   => 'Advanced Life Support & Monitoring',
                'link'        => 'departments/icu-dialysis.php',
                'theme_class' => 'dept-icu',
                'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>'
            ],
            [
                'name'        => 'General Medicine',
                'sub_title'   => 'Comprehensive Primary Care',
                'link'        => 'departments/medicine.php',
                'theme_class' => 'dept-medicine',
                'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6h2a6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg>'
            ],
            [
                'name'        => 'Gynaecology',
                'sub_title'   => 'Women\'s Health & Wellness',
                'link'        => 'departments/gynecology.php',
                'theme_class' => 'dept-gynecology',
                'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6"/><path d="M12 15v7M9 19h6"/></svg>'
            ],
            [
                'name'        => 'Orthopedics and Trauma',
                'sub_title'   => 'Bone, Joint & Spine Care',
                'link'        => 'departments/orthopedics.php',
                'theme_class' => 'dept-ortho',
                'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 7.5a2.12 2.12 0 0 0-3 0L9.4 11.6a2.12 2.12 0 0 0 0 3L6.5 17.5a2.12 2.12 0 1 0 3 3l2.9-2.9a2.12 2.12 0 0 0 3 0l4.1-4.1a2.12 2.12 0 1 0-3-3z"/></svg>'
            ],
            [
                'name'        => 'Gastroenterology',
                'sub_title'   => 'Digestive System Treatment',
                'link'        => 'departments/gastroenterology.php',
                'theme_class' => 'dept-gastro',
                'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.52 0 10-4.48 10-10a8 8 0 0 0-8-8c-1.33 0-2.67.67-4 2-1.33-1.33-2.67-2-4-2a8 8 0 0 0-8 8c0 5.52 4.48 10 10 10z"/></svg>'
            ]
        ];
        ?>
        <section id="departments" class="departments-section section-bg-white">
            <div class="container">
                <div class="section-header departments-header">
                    <span class="section-badge">Our Departments</span>
                    <h2 class="section-title">Centers of <span>Excellence</span> &amp; Specialized Departments</h2>
                    <p class="section-desc">Comprehensive, technology-driven care across our specialised departments — staffed by experienced professionals and built around your safety, comfort and recovery.</p>
                </div>

                <div class="departments-grid">
                    <?php foreach ($departments as $dept): ?>
                        <article class="department-card <?php echo $dept['theme_class']; ?>">
                            <div class="department-card-top-row">
                                <div class="department-icon-wrap">
                                    <?php echo $dept['icon']; ?>
                                </div>
                                <span class="dept-badge">24/7 Care</span>
                            </div>
                            <div class="department-card-content">
                                <h3 class="dept-title"><?php echo htmlspecialchars($dept['name']); ?></h3>
                                <p class="dept-subtitle"><?php echo htmlspecialchars($dept['sub_title']); ?></p>
                            </div>
                            <div class="department-card-bottom-row">
                                <a href="<?php echo htmlspecialchars($dept['link']); ?>" class="dept-explore-btn">
                                    <span>Explore Services</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14" />
                                        <path d="m12 5 7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="departments-footer-cta">
                    <a href="departments/index.php" class="btn-view-all-departments">
                        <span>View All Departments</span>
                        <span class="arrow-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="m12 5 7 7-7 7" />
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 5. Appointment Booking / Contact Section -->
        <section id="appointment" class="appointment-booking-section">
            <div class="container">
                <div class="doctor-section-header">
                    <div class="doctor-badge-wrapper">
                        <span class="doctor-badge-pill contact-badge-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-icon"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            GET IN TOUCH
                        </span>
                    </div>
                    <h2 class="doctor-section-title">Contact Our <br><span>Healthcare Team</span></h2>
                    <p class="doctor-section-desc">We're here to help 24/7. Reach out for appointments, emergencies, or any health inquiries.</p>
                </div>

                <div class="contact-section-grid">
                    <!-- Left Column: Info Cards -->
                    <div class="contact-info-column">
                        <!-- Phone Card -->
                        <div class="contact-info-card">
                            <div class="contact-card-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <div class="contact-card-text">
                                <span class="contact-card-label">Phone</span>
                                <strong class="contact-card-value"><?php echo htmlspecialchars($settings['header_phone'] ?? '+91 9589899826'); ?></strong>
                                <span class="contact-card-subtext">24/7 Available for Emergencies</span>
                            </div>
                        </div>

                        <!-- Email Card -->
                        <div class="contact-info-card">
                            <div class="contact-card-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <div class="contact-card-text">
                                <span class="contact-card-label">Email</span>
                                <strong class="contact-card-value"><?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?></strong>
                                <span class="contact-card-subtext">We'll respond within 24 hours</span>
                            </div>
                        </div>

                        <!-- Address Card -->
                        <div class="contact-info-card">
                            <div class="contact-card-icon-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div class="contact-card-text">
                                <span class="contact-card-label">Address</span>
                                <strong class="contact-card-value"><?php echo htmlspecialchars($settings['booking_address'] ?? 'Narendra Nagar, Amaiya Colony, Rewa (M.P.)'); ?></strong>
                                <span class="contact-card-subtext">Vindhya Hospital & Research Centre</span>
                            </div>
                        </div>

                        <!-- Working Hours Card -->
                        <div class="working-hours-card">
                            <div class="working-hours-header">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="clock-icon"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>Working Hours</span>
                            </div>
                            <div class="working-hours-rows">
                                <div class="working-hours-row">
                                    <span class="day-label">Monday - Saturday</span>
                                    <span class="hours-val">9:00 AM - 3:00 PM <br>6:00 PM - 9:00 PM</span>
                                </div>
                                <div class="working-hours-divider"></div>
                                <div class="working-hours-row">
                                    <span class="day-label">Sunday</span>
                                    <span class="hours-val">9:00 AM - 1:00 PM</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Form Card -->
                    <div class="contact-form-column">
                        <form action="index.php#appointment" method="POST" class="home-contact-form">
                            <input type="hidden" name="action" value="book_appointment">

                            <div class="form-group">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" id="name" name="name" class="form-input" placeholder="siva singh" required>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com">
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-input" placeholder="+91 9589899826" required>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label for="department" class="form-label">Select Specialty</label>
                                    <select id="department" name="department" class="form-select">
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
                                    <label for="date" class="form-label">Appointment Date</label>
                                    <input type="date" id="date" name="date" class="form-input" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message" class="form-label">Your Message</label>
                                <textarea id="message" name="message" class="form-textarea" placeholder="Tell us how we can help you..." rows="4"></textarea>
                            </div>

                            <button type="submit" class="btn-send-message">
                                <span>Send Message</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="send-icon">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Emergency Banner -->
                <div class="emergency-contact-banner">
                    <div class="emergency-left">
                        <div class="emergency-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="22" height="13" rx="2" ry="2"/><path d="M12 8v4"/><path d="M8 10h8"/><path d="M17 16h3a2 2 0 0 1 2 2v3H2v-3a2 2 0 0 1 2-2h3"/><circle cx="7" cy="21" r="2"/><circle cx="17" cy="21" r="2"/></svg>
                        </div>
                        <div class="emergency-text">
                            <h3>Medical Emergency?</h3>
                            <span>24/7 Emergency Services Available</span>
                        </div>
                    </div>
                    <div class="emergency-right">
                        <a href="tel:108" class="emergency-btn-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            Call 108
                        </a>
                        <a href="tel:<?php echo htmlspecialchars($settings['header_phone'] ?? '+919589899826'); ?>" class="emergency-btn-outline">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            Hospital Line
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="fancy-tabs-section">
            <!-- Tab Navigation Header -->
            <div class="tabs-container">
                <button class="tab-btn active" onclick="switchTab(event, 'why-choose-us')">
                    <span class="tab-indicator"></span>
                    <h3>Why Choose Us</h3>
                    <p>Listen from real patients</p>
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'our-innovation')">
                    <span class="tab-indicator"></span>
                    <h3>Our Innovation</h3>
                    <p>Our dedicated research team</p>
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'facilities')">
                    <span class="tab-indicator"></span>
                    <h3>Facilities</h3>
                    <p>Facilities - Vindhya Hospital Rewa</p>
                </button>
            </div>

            <!-- Content Wrapper -->
            <div class="tab-content-wrapper">

                <!-- Tab 1: Why Choose Us -->
                <div id="why-choose-us" class="tab-panel active">
                    <div class="tab-grid">
                        <div class="tab-image-side">
                            <img src="images/Homepage-images/1.jpg" alt="Vindhya Hospital Reception">
                            <div class="floating-badge">24/7 Care</div>
                        </div>
                        <div class="tab-text-side">
                            <h2>Why Choose <span class="accent-text">Vindhya Us</span></h2>
                            <p class="lead-text">Trusted care, advanced treatment, and compassion under one roof.</p>
                            <p class="body-text">At Vindhya Hospital, Rewa, we are committed to delivering high-quality healthcare with modern technology and experienced specialists. Our 24×7 emergency services, patient-focused approach, and advanced diagnostic facilities ensure timely care and better recovery.</p>
                            <a href="#" class="fancy-btn">Contact Us <i class="arrow-icon">&rarr;</i></a>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Our Innovation (Hidden by default) -->
                <div id="our-innovation" class="tab-panel">
                    <div class="tab-grid">
                        <div class="tab-image-side">
                            <img src="images/Homepage-images/2.jpg" alt="Medical Innovation">
                        </div>
                        <div class="tab-text-side">
                            <h2>Our Healthcare <span class="accent-text">Innovation</span></h2>
                            <p class="lead-text">Advancing healthcare through technology, expertise, and compassion.</p>
                            <p class="body-text">We combine modern medical technology, evidence-based practices, and skilled professionals to deliver precise diagnosis and advanced treatment. From digital health records to minimally invasive surgeries, our hospital continuously upgrades to meet global standards.</p>
                            <a href="#" class="fancy-btn">Contact Us <i class="arrow-icon">&rarr;</i></a>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Facilities (Hidden by default) -->
                <div id="facilities" class="tab-panel">
                    <div class="tab-grid">
                        <div class="tab-image-side">
                            <img src="images/Homepage-images/3.jpg" alt="Operation Theater">
                        </div>
                        <div class="tab-text-side">
                            <h2>Advanced <span class="accent-text">Facilities</span></h2>
                            <p class="lead-text">Comprehensive medical services with modern infrastructure.</p>
                            <p class="body-text">We provide state-of-the-art facilities designed to ensure comfort, safety, and advanced medical support. Our hospital features fully equipped emergency units, modern operation theaters, intensive care units (ICU), advanced diagnostic laboratories, and digital X-ray capabilities.</p>
                            <a href="#" class="fancy-btn">Contact Us <i class="arrow-icon">&rarr;</i></a>
                        </div>
                    </div>
                </div>

            </div>
        </section>


        <section class="testimonial-section">
            <div class="section-header">
                <span class="pill-badge">REVIEWS</span>
                <h2>Patient <span class="accent-text">Testimonials</span></h2>
                <div class="header-line"></div>
            </div>

            <div class="testimonial-container">
                <div class="rating-summary-box">
                    <div class="logo-wrapper">
                        <img src="images/logo.png" alt="VHRC Logo" class="hospital-logo">
                    </div>
                    <h3>Vindhya Hospital &<br>Research Centre</h3>
                    <div class="rating-stars-row">
                        <span class="rating-number">4.4</span>
                        <div class="stars">★★★★★</div>
                    </div>
                    <p class="review-count">1,421 Google reviews</p>
                    <a href="https://google.com" target="_blank" class="write-review-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                        </svg>
                        Write a review
                    </a>
                </div>

                <div class="slider-wrapper">
                    <button class="nav-arrow prev-btn" aria-label="Previous review">&#10094;</button>
                    <button class="nav-arrow next-btn" aria-label="Next review">&#10095;</button>

                    <div class="reviews-track">

                        <div class="review-card" data-full-text="All good experience with the staff and physicians. Very clean and supportive environment. Highly recommended for any advanced treatments and super speciality care in Rewa.">
                            <div class="card-header">
                                <div class="user-avatar avatar-n">N</div>
                                <div class="user-meta">
                                    <h4>Narendra Singh</h4>
                                    <span class="time-ago">4 months ago</span>
                                </div>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" class="google-icon">
                            </div>
                            <div class="card-rating">
                                <div class="card-stars">★★★★★</div>
                                <span class="verified-badge" title="Verified Review">✓</span>
                            </div>
                            <p class="review-text">All good experience with the staff and physicians. Very clean and supportive environment.</p>
                            <button class="read-more-link">Read more</button>
                        </div>

                        <div class="review-card" data-full-text="Dr. Dhirendra Gautam ne sahi treatment or sahi bimari ki jankari di.. yaha bahot acha raha or dr. aradhana mam ne sahi advice aur care di. Staff bhi bahut accha aur helpful hai. Main Vindhya Hospital se bahut santusht hoon aur sabhi ko yaha aane ki salah deta hoon.">
                            <div class="card-header">
                                <div class="user-avatar avatar-f">F</div>
                                <div class="user-meta">
                                    <h4>Faizan</h4>
                                    <span class="time-ago">4 months ago</span>
                                </div>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" class="google-icon">
                            </div>
                            <div class="card-rating">
                                <div class="card-stars">★★★★★</div>
                                <span class="verified-badge" title="Verified Review">✓</span>
                            </div>
                            <p class="review-text">Dr. Dhirendra Gautam ne sahi treatment or sahi bimari ki jankari di.. yaha bahot acha raha or dr. aradhana mam ne sahi...</p>
                            <button class="read-more-link">Read more</button>
                        </div>

                        <div class="review-card" data-full-text="My parents sunila devi in under Dr. Vishal mishra sir hme hamri bimari ke bare me achhe sae bateye hain aur sir ne operation bahut ache se kiya. Hospital ki suvidhaen aur safai bahut acchi hai. Paramedical staff ne bhi bahut accha sehyog diya. Dhanyawad.">
                            <div class="card-header">
                                <div class="user-avatar avatar-p">P</div>
                                <div class="user-meta">
                                    <h4>Pawan Kumar</h4>
                                    <span class="time-ago">4 months ago</span>
                                </div>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" class="google-icon">
                            </div>
                            <div class="card-rating">
                                <div class="card-stars">★★★★★</div>
                                <span class="verified-badge" title="Verified Review">✓</span>
                            </div>
                            <p class="review-text">My parents sunila devi in under Dr. Vishal mishra sir hme hamri bimari ke bare me achhe sae bateye hain aur sir ne operatio...</p>
                            <button class="read-more-link">Read more</button>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <section class="trust-and-message-section">

            <div class="certificates-container">
                <div class="certs-header">
                    <h2>Certificates & <span class="accent-text">Standards</span></h2>
                    <div class="small-accent-line"></div>
                </div>
                <div class="certs-grid">
                    <div class="cert-item">
                        <img src="images/Homepage-images/nabh_logo-1.jpg" alt="NABH Accredited">
                    </div>
                    <div class="cert-item">
                        <img src="images/Homepage-images/logo-2.png" alt="VHRC Certification">
                    </div>
                    <div class="cert-item">
                        <img src="images/Homepage-images/Untitled-design-13.png" alt="24/7 Service Everyday">
                    </div>
                </div>
            </div>

            <div class="chairman-section">
                <div class="chairman-grid">

                    <div class="chairman-image-wrapper">
                        <img src="images/Homepage-images/CHAIR-MAN-IMAGE.jpg" alt="Mr. Narendra Singh - Chairman" class="chairman-img">
                        <div class="image-decorative-backdrop"></div>
                    </div>

                    <div class="chairman-message-card">
                        <div class="quote-mark">&#8220;</div>

                        <span class="hospital-sub-tag">Vindhya Hospital & Research Centre</span>

                        <div class="message-body">
                            <p>A deep sense of commitment, clinical excellence, innovation, teamwork, and care in Vindhya Hospital & Research Center, Rewa to new horizons of success.</p>
                            <p>We provide distinctive comprehensive and compassionate patient care for patient utilizing multidisciplinary approach. To our aim to providing patient with early diagnosis and the most advance techniques available to achieve outstanding result.</p>
                            <p>We entail the finest medical skills and best medical professional to dispense quality treatment and care. We are the only hospital in Rewa Region who is providing world class health care facility in rural areas for poor patient.</p>
                        </div>

                        <div class="chairman-signature">
                            <p class="wishes-text">Best Wishes,</p>
                            <h3>Mr. Narendra Singh</h3>
                            <span class="title-badge">Chairman</span>
                        </div>
                    </div>

                </div>
            </div>

        </section>
       

        <section class="news-and-links-section">
  <div class="nl-container">
    
    <div class="news-column">
      <div class="column-header">
        <h2>Recent <span class="accent-text">News</span></h2>
        <a href="#" class="view-all-link">Read All News</a>
      </div>
      
      <div class="news-grid">
        <article class="news-card">
          <div class="news-meta">
            <span class="news-date">June 16, 2026</span>
            <div class="news-badge">Health</div>
          </div>
          <h3 class="news-title">Health Checkup in Rewa — Comprehensive Preventive Healthcare at Vindhya Hospital & Research Center</h3>
          <a href="#" class="news-read-more">Read article &rarr;</a>
        </article>
        
        <article class="news-card">
          <div class="news-meta">
            <span class="news-date">May 19, 2026</span>
            <div class="news-badge">Specialty</div>
          </div>
          <h3 class="news-title">IVF Treatment in Rewa | Vindhya Hospital & Research Center</h3>
          <a href="#" class="news-read-more">Read article &rarr;</a>
        </article>
        
        <article class="news-card">
          <div class="news-meta">
            <span class="news-date">June 9, 2026</span>
            <div class="news-badge">Surgery</div>
          </div>
          <h3 class="news-title">Best Kidney Stone Treatment in Rewa | Advanced Laser Surgery at Vindhya Hospital & Research Center</h3>
          <a href="#" class="news-read-more">Read article &rarr;</a>
        </article>
        
        <article class="news-card">
          <div class="news-meta">
            <span class="news-date">April 28, 2026</span>
            <div class="news-badge">Emergency</div>
          </div>
          <h3 class="news-title">Emergency & Critical Care Services in Rewa | Vindhya Hospital & Research Center</h3>
          <a href="#" class="news-read-more">Read article &rarr;</a>
        </article>
      </div>
    </div>

    <div class="links-column">
      <div class="column-header">
        <h2>Quick <span class="accent-text">Links</span></h2>
      </div>
      
      <div class="links-list">
        <a href="#" class="quick-link-item">
          <div class="link-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
          </div>
          <div class="link-details">
            <h4>Make An Appointment</h4>
            <span class="link-subtext">Schedule a visit online</span>
          </div>
          <span class="arrow-indicator">&rsaquo;</span>
        </a>
        
        <a href="mailto:info@vindhyahospital.com" class="quick-link-item">
          <div class="link-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
          </div>
          <div class="link-details">
            <h4>Email Us</h4>
            <span class="link-subtext">Get in touch via message</span>
          </div>
          <span class="arrow-indicator">&rsaquo;</span>
        </a>
        
        <a href="tel:+1234567890" class="quick-link-item">
          <div class="link-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
          </div>
          <div class="link-details">
            <h4>Phone Support</h4>
            <span class="link-subtext">24/7 Helpline available</span>
          </div>
          <span class="arrow-indicator">&rsaquo;</span>
        </a>
        
        <a href="#" class="quick-link-item whatsapp-variant">
          <div class="link-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
          </div>
          <div class="link-details">
            <h4>WhatsApp Support</h4>
            <span class="link-subtext">Instant chat assistant</span>
          </div>
          <span class="arrow-indicator">&rsaquo;</span>
        </a>
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

    <footer class="site-footer">
  <div class="footer-container">
    
    <!-- Column 1: Brand & Contact Info -->
    <div class="footer-column brand-info">
      <img src="images/logo.png" alt="VHRC Logo" class="footer-logo">
      <p class="address-text">
        Near Old Bus Stand, Bansh Ghat,<br>
        Rewa (MP) 486001, Rewa, India,<br>
        Madhya Pradesh
      </p>
      <div class="contact-details">
        <a href="tel:07662406000" class="contact-line">
          <div class="mini-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <span>076624 06000</span>
        </a>
        <a href="tel:+919589899826" class="contact-line">
          <div class="mini-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <span>+91 9589899826</span>
        </a>
        <a href="mailto:vhrcrewa@gmail.com" class="contact-line">
          <div class="mini-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          </div>
          <span>vhrcrewa@gmail.com</span>
        </a>
      </div>
    </div>

    <!-- Column 2: Main Menu Links -->
    <div class="footer-column links-nav">
      <h3>Main Menu</h3>
      <div class="footer-title-line"></div>
      <ul>
        <li><a href="#">Homepage</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="departments/">Department</a></li>
        <li><a href="#">Our Doctor</a></li>
        <li><a href="#">Gallery</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>

    <!-- Column 3: About Us Links -->
    <div class="footer-column links-nav">
      <h3>About Us</h3>
      <div class="footer-title-line"></div>
      <ul>
        <li><a href="about/mission-values.php">Our Mission &amp; Values</a></li>
        <li><a href="about/policies-procedures.php">Policies &amp; Procedures</a></li>
        <li><a href="about/consultation-care.php">Consultation &amp; Advanced Care</a></li>
        <li><a href="about/admission-prep.php">Preparing for Admission</a></li>
        <li><a href="about/quality-safety.php">Quality Care &amp; Patient Safety</a></li>
        <li><a href="about/diversity-specialty.php">Diversity is Our Specialty</a></li>
      </ul>
    </div>

    <!-- Column 4: Hospital Hours & Emergency Box -->
    <div class="footer-column hospital-hours">
      <h3>Hospital Hours</h3>
      <div class="footer-title-line"></div>
      <div class="hours-list">
        <div class="hours-row">
          <span class="day">
            <svg class="clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
            Monday - Friday
          </span>
          <span class="time teal-highlight">08:00 - 20:00</span>
        </div>
        <div class="hours-row">
          <span class="day">
            <svg class="clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
            Saturday
          </span>
          <span class="time">09:00 - 18:00</span>
        </div>
        <div class="hours-row">
          <span class="day">
            <svg class="clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
            Sunday
          </span>
          <span class="time">09:00 - 18:00</span>
        </div>
      </div>
      
      <!-- Premium Glassmorphism Emergency Badge -->
      <div class="emergency-footer-card">
        <span class="live-pulse-dot"></span>
        <span class="card-title">Emergency : 24 Hours</span>
      </div>
    </div>

  </div>

  <!-- BOTTOM FOOTER SUB-BAR WITH CREDIT -->
  <div class="footer-bottom-bar">
    <div class="bottom-bar-container">
      <p>Copyright &copy; 2026 <strong>Vindhya Hospital Rewa</strong> | Designed by <a href="#" class="agency-credit">Rainbow Shine Infotech</a></p>
    </div>
  </div>
</footer>
    <!-- Review Modal Popup -->
    <div class="review-modal-overlay" id="reviewModal">
        <div class="review-modal-card">
            <button class="review-modal-close" aria-label="Close modal">&times;</button>
            <div class="review-modal-header">
                <div class="user-avatar" id="modalAvatar"></div>
                <div class="user-meta">
                    <h4 id="modalAuthor"></h4>
                    <span class="time-ago" id="modalTime"></span>
                </div>
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" class="google-icon">
            </div>
            <div class="card-rating">
                <div class="card-stars">★★★★★</div>
                <span class="verified-badge">✓</span>
            </div>
            <div class="review-modal-content">
                <p id="modalText"></p>
            </div>
        </div>
    </div>

    <!-- Custom JS Scripts -->
    <script src="js/main.js"></script>
</body>

</html>