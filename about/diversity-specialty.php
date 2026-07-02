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
    <title>Diversity is Our Specialty - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Learn how Vindhya Hospital &amp; Research Centre (VHRC) Rewa values inclusivity, serving diverse patients with tailored care and a multidisciplinary clinical team.">
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

    .diversity-intro {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--clr-text-main);
        margin-bottom: 25px;
    }

    .diversity-highlight-card {
        background: linear-gradient(135deg, rgba(30, 41, 93, 0.02) 0%, rgba(0, 210, 196, 0.02) 100%);
        border-left: 4px solid var(--clr-accent);
        padding: 25px 30px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 35px;
        box-shadow: inset 0 0 20px rgba(0, 210, 196, 0.01);
    }

    .diversity-highlight-card p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--clr-text-main);
        margin: 0;
    }

    .diversity-image-wrap {
        margin-bottom: 45px;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--clr-border);
    }

    .diversity-image-wrap img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform var(--transition-smooth);
    }

    .diversity-image-wrap:hover img {
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

    .diversity-paragraph {
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
                                <li class="sidebar-menu-item active">
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
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72(12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    </div>
                                    <span><?php echo htmlspecialchars($settings['header_phone'] ?? '+91 9589899826'); ?></span>
                                </a>
                                <a href="mailto:<?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?>" class="sidebar-contact-line">
                                    <div class="mini-icon-box">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </div>
                                    <span>vhrcrewa@gmail.com</span>
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
                            <span style="color: var(--clr-brand); font-weight: 700;">Diversity is Our Specialty</span>
                        </div>

                        <!-- Title -->
                        <h1 class="content-title">Diversity is Our Specialty</h1>

                        <!-- Diversity Introduction -->
                        <p class="diversity-intro">
                            At <strong>Vindhya Hospital Rewa</strong>, we take pride in serving patients from all walks of life with equal care, respect, and dedication. Our strength lies in embracing diversity — in our people, our patients, and our approach to healthcare. We understand that every individual is unique, with different medical needs, cultural backgrounds, and personal preferences, and we tailor our care to meet those differences with compassion and understanding.
                        </p>

                        <!-- Diversity Highlight Card -->
                        <div class="diversity-highlight-card">
                            <p>
                                Our team of doctors, nurses, and healthcare professionals come from diverse specialties and experiences, allowing us to offer a wide range of treatments under one roof. Whether it’s general medicine, surgery, maternity care, or advanced diagnostics, we work together to deliver comprehensive healthcare that reflects our commitment to inclusivity and excellence.
                            </p>
                        </div>

                        <!-- Banner Image -->
                        <div class="diversity-image-wrap">
                            <img src="images/diversity_team.png" alt="Vindhya Hospital Medical Team">
                        </div>

                        <!-- Diversity Paragraphs -->
                        <p class="diversity-paragraph">
                            At <strong>Vindhya Hospital Rewa</strong>, diversity is not just about variety — it’s about unity in care. We believe that respecting every patient’s individuality makes healing more effective and compassionate, helping us build a healthier, more connected community.
                        </p>

                        <!-- Pillars Section Title -->
                        <h2 class="pillars-section-title">Core Dimensions of Inclusivity</h2>
                        <p class="pillars-lead">How we translate diversity into premium everyday patient experiences:</p>

                        <!-- Pillars Grid -->
                        <div class="pillars-grid">
                            
                            <!-- Pillar 1: Patient Individuality -->
                            <div class="pillar-card">
                                <div class="pillar-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                </div>
                                <h4>Tailored Patient Care</h4>
                                <p>Customized care guidelines adapting to cultural preferences, language options, and dietary needs of patients from all communities.</p>
                            </div>

                            <!-- Pillar 2: Broad Specialties -->
                            <div class="pillar-card">
                                <div class="pillar-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <h4>Specialist Multi-specialty</h4>
                                <p>A diverse team of medical specialists covering Urology, Gynecology, Spine, and Trauma under a unified collaborative clinical umbrella.</p>
                            </div>

                            <!-- Pillar 3: Accessibility for All -->
                            <div class="pillar-card">
                                <div class="pillar-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="2" y1="12" x2="22" y2="12"/>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                    </svg>
                                </div>
                                <h4>Equal Opportunity Health</h4>
                                <p>Ensuring world-class healthcare is financially, geographically, and socially accessible to every single citizen without exception.</p>
                            </div>

                            <!-- Pillar 4: Community Spirit -->
                            <div class="pillar-card">
                                <div class="pillar-icon-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>
                                <h4>Unified in Wellness</h4>
                                <p>Local health camps, public education series, and medical outreach targeting remote areas to unify the Vindhya region in long-term wellness.</p>
                            </div>

                        </div>

                        <!-- Summary closing paragraph -->
                        <p class="diversity-paragraph" style="border-top: 1px solid var(--clr-border); padding-top: 25px; margin-top: 30px; font-weight: 500; color: var(--clr-brand);">
                            We believe that respecting every patient’s individuality makes healing more effective and compassionate, helping us build a healthier, more connected community.
                        </p>

                    </article>

                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>
