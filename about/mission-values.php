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
    <title>Our Mission &amp; Values - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Learn more about the mission, vision, and core values of Vindhya Hospital &amp; Research Centre (VHRC). Serving Rewa with advanced super speciality clinical care.">
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

    .mission-intro {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--clr-text-main);
        margin-bottom: 25px;
    }

    .mission-highlight-card {
        background: linear-gradient(135deg, rgba(30, 41, 93, 0.02) 0%, rgba(0, 210, 196, 0.02) 100%);
        border-left: 4px solid var(--clr-accent);
        padding: 25px 30px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 35px;
        box-shadow: inset 0 0 20px rgba(0, 210, 196, 0.01);
    }

    .mission-highlight-card p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--clr-text-main);
        margin: 0;
    }

    .facade-image-wrap {
        margin-bottom: 45px;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--clr-border);
    }

    .facade-image-wrap img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform var(--transition-smooth);
    }

    .facade-image-wrap:hover img {
        transform: scale(1.02);
    }

    /* Values Grid Styling */
    .values-section-title {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .values-lead {
        font-size: 1.05rem;
        color: var(--clr-text-muted);
        margin-bottom: 35px;
        font-weight: 500;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
    }

    .value-card {
        background-color: #FFFFFF;
        border: 1px solid var(--clr-border);
        border-radius: var(--border-radius-md);
        padding: 30px;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }

    .value-card::before {
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

    .value-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(0, 210, 196, 0.2);
    }

    .value-card:hover::before {
        transform: scaleX(1);
    }

    .value-icon-box {
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

    .value-card:hover .value-icon-box {
        background-color: var(--clr-accent);
        color: #FFFFFF;
        box-shadow: var(--shadow-glow);
    }

    .value-icon-box svg {
        width: 22px;
        height: 22px;
    }

    .value-card h4 {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .value-card p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--clr-text-main);
        margin: 0;
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
                                <li class="sidebar-menu-item active">
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
                                <li class="sidebar-menu-item">
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
                            <span style="color: var(--clr-brand); font-weight: 700;">Our Mission &amp; Values</span>
                        </div>

                        <!-- Title -->
                        <h1 class="content-title">Our Mission &amp; Values</h1>

                        <!-- Mission Introduction -->
                        <p class="mission-intro">
                            At <strong>Vindhya Hospital Rewa</strong>, our mission is to redefine healthcare through compassion, innovation, and excellence. We are dedicated to providing high-quality, affordable, and patient-focused medical care that meets international standards. Every patient who walks through our doors is treated with dignity, empathy, and personalized attention — because we believe health is not just about curing disease, but restoring hope and improving lives.
                        </p>

                        <!-- Mission Highlight Card -->
                        <div class="mission-highlight-card">
                            <p>
                                Our mission extends beyond treatment; it is about transforming healthcare delivery in the Vindhya region through advanced medical technology, continuous research, and a strong commitment to ethical medical practice. We aim to make world-class healthcare accessible to every individual, ensuring that no one is deprived of quality treatment due to geographical or financial barriers.
                            </p>
                        </div>

                        <!-- Facade Image Banner -->
                        <div class="facade-image-wrap">
                            <img src="images/about_facade.png" alt="Vindhya Hospital Facade Building">
                        </div>

                        <!-- Core Values Section -->
                        <h2 class="values-section-title">Our Core Values</h2>
                        <p class="values-lead">Our core values form the foundation of everything we do:</p>

                        <!-- Values Grid -->
                        <div class="values-grid">
                            
                            <!-- Value 1: Compassion -->
                            <div class="value-card">
                                <div class="value-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                                    </svg>
                                </div>
                                <h4>Compassion</h4>
                                <p>We care deeply for our patients and their families, offering comfort, understanding, and kindness in every interaction.</p>
                            </div>

                            <!-- Value 2: Excellence -->
                            <div class="value-card">
                                <div class="value-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                </div>
                                <h4>Excellence</h4>
                                <p>We strive for perfection in every procedure, diagnosis, and decision, constantly upgrading our knowledge and technology to stay at the forefront of medical advancement.</p>
                            </div>

                            <!-- Value 3: Integrity -->
                            <div class="value-card">
                                <div class="value-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                </div>
                                <h4>Integrity</h4>
                                <p>We uphold the highest standards of honesty, ethics, and transparency in all aspects of our practice.</p>
                            </div>

                            <!-- Value 4: Teamwork -->
                            <div class="value-card">
                                <div class="value-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <h4>Teamwork</h4>
                                <p>Our multidisciplinary team works collaboratively, combining expertise and empathy to achieve the best patient outcomes.</p>
                            </div>

                            <!-- Value 5: Innovation -->
                            <div class="value-card">
                                <div class="value-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5 5 0 0 0 8 8c0 1 .3 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/>
                                        <line x1="9" y1="18" x2="15" y2="18"/>
                                        <line x1="10" y1="22" x2="14" y2="22"/>
                                    </svg>
                                </div>
                                <h4>Innovation</h4>
                                <p>We continuously seek new and improved ways to deliver healthcare, adopting modern technologies and evidence-based practices.</p>
                            </div>

                            <!-- Value 6: Commitment -->
                            <div class="value-card">
                                <div class="value-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                </div>
                                <h4>Commitment</h4>
                                <p>We are dedicated to serving our community, focusing on preventive care, education, and long-term wellness.</p>
                            </div>

                        </div>

                    </article>

                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>
