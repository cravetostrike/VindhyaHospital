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
    <title>Consultation &amp; Advanced Care - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Learn more about our comprehensive medical consultation and advanced care services at Vindhya Hospital &amp; Research Centre (VHRC) Rewa.">
    <base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'; ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Website Favicon -->
    <?php if (!empty($settings['website_favicon']) && file_exists(__DIR__ . '/../' . $settings['website_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['website_favicon']); ?>?v=<?php echo filemtime(__DIR__ . '/../' . $settings['website_favicon']); ?>">
    <?php else: ?>
        <link rel="icon" href="images/logo.png">
    <?php endif; ?>

    <style>
    /* Page Layout */
    .about-page-container {
        padding: 80px 0;
        background-color: var(--clr-bg-secondary);
    }

    .about-page-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 40px;
        align-items: start;
    }

    /* Sidebar Styling */
    .about-sidebar {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .sidebar-card {
        background-color: #FFFFFF;
        border-radius: var(--border-radius-md);
        border: 1px solid var(--clr-border);
        box-shadow: var(--shadow-sm);
        padding: 30px;
        overflow: hidden;
    }

    .sidebar-card h3 {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
        position: relative;
        padding-bottom: 12px;
    }

    .sidebar-card h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 35px;
        height: 3px;
        background-color: var(--clr-accent);
        border-radius: 2px;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sidebar-menu-item a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        color: var(--clr-text-main);
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all var(--transition-fast);
    }

    .sidebar-menu-item a svg {
        width: 16px;
        height: 16px;
        color: var(--clr-text-muted);
        transition: all var(--transition-fast);
    }

    .sidebar-menu-item a:hover {
        color: var(--clr-accent-hover);
        background-color: rgba(0, 210, 196, 0.05);
        border-color: rgba(0, 210, 196, 0.1);
        transform: translateX(4px);
    }

    .sidebar-menu-item a:hover svg {
        color: var(--clr-accent-hover);
    }

    .sidebar-menu-item.active a {
        color: #FFFFFF;
        background: var(--gradient-accent);
        border-color: var(--clr-accent);
        box-shadow: var(--shadow-glow);
    }

    .sidebar-menu-item.active a svg {
        color: #FFFFFF;
    }

    /* Sidebar Address Card */
    .sidebar-address-card {
        background: var(--gradient-dark);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #FFFFFF;
    }

    .sidebar-address-card h3 {
        color: #FFFFFF;
    }

    .sidebar-address-card h3::after {
        background-color: var(--clr-accent);
    }

    .address-info-p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 24px;
    }

    .sidebar-contact-details {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .sidebar-contact-line {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #FFFFFF;
        text-decoration: none;
        font-size: 0.95rem;
        transition: color var(--transition-fast);
    }

    .sidebar-contact-line:hover {
        color: var(--clr-accent);
    }

    .sidebar-contact-line .mini-icon-box {
        width: 36px;
        height: 36px;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--clr-accent);
    }

    /* Sidebar Hours Card */
    .sidebar-hours-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .sidebar-hours-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.92rem;
        padding-bottom: 12px;
        border-bottom: 1px dashed var(--clr-border);
    }

    .sidebar-hours-row:last-child {
        border-bottom: none;
    }

    .sidebar-hours-row .day {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--clr-text-main);
        font-weight: 600;
    }

    .sidebar-hours-row .day svg {
        width: 16px;
        height: 16px;
        color: var(--clr-accent);
    }

    .sidebar-hours-row .time {
        color: var(--clr-text-muted);
        font-weight: 600;
    }

    .sidebar-hours-row .time.teal-highlight {
        color: var(--clr-accent-hover);
    }

    /* Right Content Area Styling */
    .about-content {
        background-color: #FFFFFF;
        border-radius: var(--border-radius-md);
        border: 1px solid var(--clr-border);
        box-shadow: var(--shadow-sm);
        padding: 50px;
    }

    .content-breadcrumbs {
        font-size: 0.85rem;
        color: var(--clr-text-muted);
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .content-breadcrumbs a {
        color: var(--clr-text-muted);
        text-decoration: none;
        transition: color var(--transition-fast);
    }

    .content-breadcrumbs a:hover {
        color: var(--clr-accent-hover);
    }

    .content-title {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 12px;
    }

    .content-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: var(--gradient-accent);
        border-radius: 2px;
        position: absolute;
        bottom: 0;
        left: 0;
    }

    .care-intro {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--clr-text-main);
        margin-bottom: 25px;
    }

    .care-highlight-card {
        background: linear-gradient(135deg, rgba(30, 41, 93, 0.02) 0%, rgba(0, 210, 196, 0.02) 100%);
        border-left: 4px solid var(--clr-accent);
        padding: 25px 30px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 35px;
        box-shadow: inset 0 0 20px rgba(0, 210, 196, 0.01);
    }

    .care-highlight-card p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--clr-text-main);
        margin: 0;
    }

    .care-image-wrap {
        margin-bottom: 45px;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--clr-border);
    }

    .care-image-wrap img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform var(--transition-smooth);
    }

    .care-image-wrap:hover img {
        transform: scale(1.02);
    }

    /* Pillars Grid Styling */
    .features-section-title {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .features-lead {
        font-size: 1.05rem;
        color: var(--clr-text-muted);
        margin-bottom: 35px;
        font-weight: 500;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 35px;
    }

    .feature-card {
        background-color: #FFFFFF;
        border: 1px solid var(--clr-border);
        border-radius: var(--border-radius-md);
        padding: 30px;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--gradient-accent);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform var(--transition-fast);
    }

    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(0, 210, 196, 0.2);
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-icon-box {
        width: 48px;
        height: 48px;
        background-color: rgba(0, 210, 196, 0.08);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--clr-accent);
        margin-bottom: 20px;
        transition: all var(--transition-fast);
    }

    .feature-card:hover .feature-icon-box {
        background-color: var(--clr-accent);
        color: #FFFFFF;
        box-shadow: var(--shadow-glow);
    }

    .feature-icon-box svg {
        width: 22px;
        height: 22px;
    }

    .feature-card h4 {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .feature-card p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--clr-text-main);
        margin: 0;
    }

    .care-paragraph {
        font-size: 1.05rem;
        line-height: 1.75;
        color: var(--clr-text-main);
        margin-bottom: 20px;
    }

    /* Responsiveness */
    @media (max-width: 991px) {
        .about-page-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .about-page-container {
            padding: 50px 0;
        }
    }

    @media (max-width: 575px) {
        .about-content {
            padding: 30px 20px;
        }
        
        .content-title {
            font-size: 2rem;
        }
        
        .sidebar-card {
            padding: 24px 20px;
        }
    }
    </style>
</head>

<body>

    <?php
    $active_page = 'about';
    require_once __DIR__ . '/../includes/header.php';
    ?>

    <main>
        <div class="about-page-container">
            <div class="container">
                <div class="about-page-grid">
                    
                    <!-- Left Sidebar Column -->
                    <aside class="about-sidebar">
                        
                        <!-- 1. Services Menu Card -->
                        <div class="sidebar-card">
                            <h3>Services</h3>
                            <ul class="sidebar-menu">
                                <li class="sidebar-menu-item">
                                    <a href="about/mission-values.php">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        Our Mission &amp; Values
                                    </a>
                                </li>
                                <li class="sidebar-menu-item">
                                    <a href="about/policies-procedures.php">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        Policies &amp; Procedures
                                    </a>
                                </li>
                                <li class="sidebar-menu-item active">
                                    <a href="about/consultation-care.php">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        Consultation &amp; Advanced Care
                                    </a>
                                </li>
                                <li class="sidebar-menu-item">
                                    <a href="about/admission-prep.php">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        Preparing for Admission
                                    </a>
                                </li>
                                <li class="sidebar-menu-item">
                                    <a href="about/quality-safety.php">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        Quality Care &amp; Patient Safety
                                    </a>
                                </li>
                                <li class="sidebar-menu-item">
                                    <a href="about/diversity-specialty.php">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        Diversity is Our Specialty
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- 2. Department Address Card -->
                        <div class="sidebar-card sidebar-address-card">
                            <h3>Department Address</h3>
                            <p class="address-info-p">
                                Near Old Bus Stand, Bansh Ghat, Rewa (MP) 486001, Rewa, India, Madhya Pradesh
                            </p>
                            <div class="sidebar-contact-details">
                                <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $settings['header_phone'] ?? '+919589899826')); ?>" class="sidebar-contact-line">
                                    <div class="mini-icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    </div>
                                    <span><?php echo htmlspecialchars($settings['header_phone'] ?? '+91 9589899826'); ?></span>
                                </a>
                                <a href="mailto:<?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?>" class="sidebar-contact-line">
                                    <div class="mini-icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </div>
                                    <span><?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?></span>
                                </a>
                            </div>
                        </div>

                        <!-- 3. Department Hours Card -->
                        <div class="sidebar-card">
                            <h3>Department Hours</h3>
                            <div class="sidebar-hours-list">
                                <div class="sidebar-hours-row">
                                    <span class="day">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
                                        Monday - Friday
                                    </span>
                                    <span class="time teal-highlight">08:00 - 20:00</span>
                                </div>
                                <div class="sidebar-hours-row">
                                    <span class="day">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
                                        Saturday
                                    </span>
                                    <span class="time">09:00 - 14:00</span>
                                </div>
                                <div class="sidebar-hours-row">
                                    <span class="day">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
                                        Sunday
                                    </span>
                                    <span class="time">Close</span>
                                </div>
                            </div>
                        </div>

                    </aside>

                    <!-- Right Main Content Column -->
                    <article class="about-content">
                        
                        <!-- Breadcrumbs -->
                        <div class="content-breadcrumbs">
                            <a href="index.php">Home</a>
                            <span>&rsaquo;</span>
                            <a href="about/index.php">About Us</a>
                            <span>&rsaquo;</span>
                            <span style="color: var(--clr-brand); font-weight: 700;">Consultation &amp; Advanced Care</span>
                        </div>

                        <!-- Title -->
                        <h1 class="content-title">Consultation &amp; Advanced Care</h1>

                        <!-- Care Introduction -->
                        <p class="care-intro">
                            At <strong>Vindhya Hospital Rewa</strong>, we provide comprehensive consultation and advanced medical care designed to meet the unique health needs of every patient. Our expert doctors take the time to understand your medical history, symptoms, and concerns before recommending the most effective treatment plan. We believe that personalized consultation is the foundation of accurate diagnosis and successful recovery.
                        </p>

                        <!-- Care Highlight Card -->
                        <div class="care-highlight-card">
                            <p>
                                With state-of-the-art diagnostic facilities, modern equipment, and experienced specialists, we ensure that every patient receives the highest standard of care. Whether it’s a routine check-up or a complex medical condition, our team combines advanced technology with compassionate service to deliver the best outcomes.
                            </p>
                        </div>

                        <!-- Banner Image -->
                        <div class="care-image-wrap">
                            <img src="images/doctor_consultation.png" alt="Vindhya Hospital Doctor Consulting Patient">
                        </div>

                        <!-- Core Features Section Title -->
                        <h2 class="features-section-title">Key Elements of Advanced Care</h2>
                        <p class="features-lead">We structure our consultation and patient guidance through four core pillars:</p>

                        <!-- Features Grid -->
                        <div class="features-grid">
                            
                            <!-- Feature 1: Personalized Consultation -->
                            <div class="feature-card">
                                <div class="feature-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <line x1="19" y1="8" x2="19" y2="14"/>
                                        <line x1="22" y1="11" x2="16" y2="11"/>
                                    </svg>
                                </div>
                                <h4>Personalized Consultation</h4>
                                <p>Detailed case analysis and clinical review by our expert physicians, establishing a patient-centered care outline for your path to recovery.</p>
                            </div>

                            <!-- Feature 2: Advanced Diagnostics -->
                            <div class="feature-card">
                                <div class="feature-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                </div>
                                <h4>Advanced Diagnostics</h4>
                                <p>Equipped with advanced lab tech, imaging centers, and scanning systems for fast, highly accurate, and dependable diagnosis outcomes.</p>
                            </div>

                            <!-- Feature 3: Super Specialty -->
                            <div class="feature-card">
                                <div class="feature-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                                    </svg>
                                </div>
                                <h4>Super Specialty Care</h4>
                                <p>Renowned specialists across Urology, Obstetrics & Gynecology, Orthopedics, and Pulmonology collaborate for your comprehensive treatment.</p>
                            </div>

                            <!-- Feature 4: Post-Care Support -->
                            <div class="feature-card">
                                <div class="feature-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>
                                <h4>Wellness &amp; Post-Care</h4>
                                <p>Dedicated recovery programs, continuous post-treatment follow-ups, and dietary counseling ensuring long-term wellness and rehabilitation.</p>
                            </div>

                        </div>

                        <!-- Summary closing paragraph -->
                        <p class="care-paragraph" style="border-top: 1px solid var(--clr-border); padding-top: 25px; margin-top: 30px; font-weight: 500; color: var(--clr-brand);">
                            From preventive health consultations to specialized treatments and post-care support, we are committed to guiding you through every step of your healthcare journey. At Vindhya Hospital Rewa, your health, comfort, and trust are our top priorities, and we strive to make every consultation a step toward lasting wellness.
                        </p>

                    </article>

                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>
