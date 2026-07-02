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
    <title>Policies &amp; Procedures - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Read about the operational policies and medical procedures at Vindhya Hospital &amp; Research Centre (VHRC). Committed to patient safety, ethics, and transparency.">
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

    .policy-intro {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--clr-text-main);
        margin-bottom: 25px;
    }

    .policy-highlight-card {
        background: linear-gradient(135deg, rgba(30, 41, 93, 0.02) 0%, rgba(0, 210, 196, 0.02) 100%);
        border-left: 4px solid var(--clr-accent);
        padding: 25px 30px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 35px;
        box-shadow: inset 0 0 20px rgba(0, 210, 196, 0.01);
    }

    .policy-highlight-card p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--clr-text-main);
        margin: 0;
    }

    .policy-image-wrap {
        margin-bottom: 45px;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--clr-border);
    }

    .policy-image-wrap img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform var(--transition-smooth);
    }

    .policy-image-wrap:hover img {
        transform: scale(1.02);
    }

    /* Pillars Grid Styling */
    .pillars-section-title {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .pillars-lead {
        font-size: 1.05rem;
        color: var(--clr-text-muted);
        margin-bottom: 35px;
        font-weight: 500;
    }

    .pillars-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 35px;
    }

    .pillar-card {
        background-color: #FFFFFF;
        border: 1px solid var(--clr-border);
        border-radius: var(--border-radius-md);
        padding: 30px;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }

    .pillar-card::before {
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

    .pillar-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(0, 210, 196, 0.2);
    }

    .pillar-card:hover::before {
        transform: scaleX(1);
    }

    .pillar-icon-box {
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

    .pillar-card:hover .pillar-icon-box {
        background-color: var(--clr-accent);
        color: #FFFFFF;
        box-shadow: var(--shadow-glow);
    }

    .pillar-icon-box svg {
        width: 22px;
        height: 22px;
    }

    .pillar-card h4 {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .pillar-card p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--clr-text-main);
        margin: 0;
    }

    .policy-paragraph {
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
                                <li class="sidebar-menu-item active">
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
                            <span style="color: var(--clr-brand); font-weight: 700;">Policies &amp; Procedures</span>
                        </div>

                        <!-- Title -->
                        <h1 class="content-title">Policies &amp; Procedures</h1>

                        <!-- Policy Introduction -->
                        <p class="policy-intro">
                            At <strong>Vindhya Hospital Rewa</strong>, our <strong>Policies and Procedures</strong> are designed to ensure transparency, safety, and the highest standards of medical care for every patient. We follow a structured system that aligns with national and international healthcare guidelines to maintain consistency and quality in every aspect of our operations.
                        </p>

                        <!-- Policy Highlight Card -->
                        <div class="policy-highlight-card">
                            <p>
                                Our policies cover a wide range of areas — from patient admission and discharge protocols to infection control, medication safety, ethical medical practices, and data privacy. Each policy is regularly reviewed and updated to meet the evolving needs of healthcare and patient expectations.
                            </p>
                        </div>

                        <!-- Banner Image -->
                        <div class="policy-image-wrap">
                            <img src="images/policies_procedures.png" alt="Vindhya Hospital Policies &amp; Procedures Binders">
                        </div>

                        <!-- Core Pillars Description -->
                        <p class="policy-paragraph">
                            We emphasize <strong>patient rights and confidentiality</strong>, ensuring that every individual receives care with dignity and respect. Our <strong>clinical procedures</strong> are based on evidence-based practices, ensuring accuracy, efficiency, and minimal risk during treatment. The <strong>emergency response protocols</strong> at Vindhya Hospital Rewa are well-defined, ensuring immediate and effective action in critical situations.
                        </p>
                        <p class="policy-paragraph">
                            In addition, our staff undergoes continuous training to stay informed about the latest advancements and safety standards. Every team member — from doctors and nurses to administrative personnel — is responsible for adhering to these policies to maintain a safe, ethical, and patient-friendly environment.
                        </p>

                        <!-- Pillars Section Title -->
                        <h2 class="pillars-section-title">Key Policy Pillars</h2>
                        <p class="pillars-lead">Our clinical guidelines are supported by four central pillars:</p>

                        <!-- Pillars Grid -->
                        <div class="pillars-grid">
                            
                            <!-- Pillar 1: Patient Rights -->
                            <div class="pillar-card">
                                <div class="pillar-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </div>
                                <h4>Confidentiality &amp; Rights</h4>
                                <p>Strict data privacy compliance and absolute security of patient records. Every patient is treated with dignity, and medical information is never shared without explicit consent.</p>
                            </div>

                            <!-- Pillar 2: Clinical Quality -->
                            <div class="pillar-card">
                                <div class="pillar-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                </div>
                                <h4>Clinical Operations</h4>
                                <p>Standard operating procedures (SOPs) for surgeries, outpatient treatments, and critical care units are regularly audited to reduce treatment risks and ensure accuracy.</p>
                            </div>

                            <!-- Pillar 3: Emergency Protocols -->
                            <div class="pillar-card">
                                <div class="pillar-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8.21 13.89 7 23l5-3 5 3-1.21-9.12"/>
                                        <circle cx="12" cy="8" r="7"/>
                                    </svg>
                                </div>
                                <h4>Emergency Care</h4>
                                <p>Fast-response codes and designated critical action pathways for trauma, cardiac arrests, and acute surgical events are drilled quarterly for all staff.</p>
                            </div>

                            <!-- Pillar 4: Staff Training -->
                            <div class="pillar-card">
                                <div class="pillar-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    </svg>
                                </div>
                                <h4>Staff Compliance</h4>
                                <p>Every nurse, doctor, and administrator is trained continuously in patient safety norms, infection control, and customer-first hospitality practices.</p>
                            </div>

                        </div>

                        <!-- Summary closing paragraph -->
                        <p class="policy-paragraph" style="border-top: 1px solid var(--clr-border); padding-top: 25px; margin-top: 30px; font-weight: 500; color: var(--clr-brand);">
                            Through our strict policies and well-defined procedures, Vindhya Hospital Rewa ensures that every patient receives consistent, high-quality, and compassionate care — building trust, safety, and reliability at every step of the healthcare journey.
                        </p>

                    </article>

                </div>
            </div>
            <?php require_once __DIR__ . '/../includes/footer.php'; ?>
        </div>
    </main>
</body>

</html>
