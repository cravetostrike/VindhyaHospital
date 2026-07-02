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

// All Treatments & Sub-services basic list
$treatments_list = [
    'laboratory-services' => 'Laboratory Services',
    'diagnostic-services' => 'Diagnostic Services',
    'clinical-support-services' => 'Clinical Support Services',
    'radiology-services' => 'Radiology Services',
    'health-services' => 'Health Services',
    'cssd' => 'CSSD',
    
    // Sub-services
    'clinical-biochemistry' => 'Clinical Biochemistry',
    'clinical-microbiology-serology' => 'Clinical Micro-biology & Serology',
    'clinical-pathology' => 'Clinical Pathology',
    'haematology' => 'Haematology',
    'blood-centre' => 'Blood Centre',
    'ultrasound' => 'Ultrasound',
    'ct-scan' => 'C-T scan',
    'uro-flow' => 'Uro Flow',
    'echo' => 'ECHO',
    'x-ray' => 'X-Ray',
    'ecg' => 'ECG',
    'tmt' => 'T.M.T',
    'physiotherapy' => 'Physiotherapy',
    'dietetics' => 'Dietetics',
    'ambulance' => 'Ambulance',
    'oxygen-plant' => 'Oxygen Plant',
    'xray-fix-mobile-carm' => 'X-RAY Fix Mobile C-Arm',
    'mediclaim' => 'Mediclaim',
    'ayushman' => 'Ayushman',
    'echs' => 'ECHS',
    'sghs' => 'SGHS',
    'fully-automatic-autoclave-machine' => 'Fully Automatic Autoclave'
];

// Active Treatment ID from URL or rewritten path info
if (!isset($active_id) || $active_id === '') {
    $active_id = '';
    if (isset($_GET['id']) && $_GET['id'] !== '') {
        $active_id = trim($_GET['id']);
    } else {
        // Fallback 1: Check PATH_INFO (common when MultiViews is active)
        if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
            $active_id = trim($_SERVER['PATH_INFO'], '/');
        } else {
            // Fallback 2: Parse from REQUEST_URI
            $uri_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
            $last_part = end($uri_parts);
            $last_part = explode('?', $last_part)[0];
            $active_id = $last_part;
        }
    }
}

// Check if valid treatment, if not default to laboratory-services
if (!array_key_exists($active_id, $treatments_list)) {
    $active_id = 'laboratory-services';
}

// Load dynamic treatment data from separate files if not already set
if (!isset($active_dept) || $active_dept === null) {
    $data_file = __DIR__ . '/data/'. $active_id . '.php';
    if (file_exists($data_file)) {
        $active_dept = require $data_file;
    } else {
        // Fallback to laboratory-services if the file doesn't exist
        $active_dept = require __DIR__ . '/data/laboratory-services.php';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'; ?>">
    <title><?php echo htmlspecialchars($active_dept['name']); ?> - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Learn more about the <?php echo htmlspecialchars($active_dept['name']); ?> department at Vindhya Hospital Rewa. Explore sub-services, specialists, and book appointments online.">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Website Favicon -->
    <?php if (!empty($settings['website_favicon']) && file_exists(__DIR__ . '/../' . $settings['website_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['website_favicon']); ?>?v=<?php echo filemtime(__DIR__ . '/../' . $settings['website_favicon']); ?>">
    <?php else: ?>
        <link rel="icon" href="images/logo.png">
    <?php endif; ?>

    <style>
        /* Department Detail Grid */
        .dept-detail-container {
            padding: 80px 0;
            background-color: var(--clr-bg-secondary);
        }

        .dept-detail-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 50px;
            align-items: start;
        }

        @media (max-width: 991px) {
            .dept-detail-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        /* Banner styling matching urology background */
        .dept-detail-banner {
            position: relative;
            padding: 120px 0;
            background-image: url('images/hero_banner.png'); /* fallback operating room */
            background-size: cover;
            background-position: center;
            color: #ffffff;
            display: flex;
            align-items: center;
        }

        .dept-detail-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(30, 41, 93, 0.95) 0%, rgba(0, 210, 196, 0.75) 100%);
            z-index: 1;
        }

        .dept-detail-banner .container {
            position: relative;
            z-index: 2;
        }

        .dept-detail-banner-title {
            font-family: var(--font-heading);
            font-size: 3.2rem;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .dept-detail-banner-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 500;
            color: var(--clr-accent);
        }

        /* Main Content Styling */
        .dept-main-image {
            width: 100%;
            max-height: 480px;
            object-fit: cover;
            border-radius: var(--border-radius-md);
            margin-bottom: 35px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--clr-border);
        }

        .dept-main-title {
            font-family: var(--font-heading);
            color: var(--clr-brand);
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 15px;
        }

        .dept-main-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background-color: var(--clr-accent);
            border-radius: 2px;
        }

        .dept-paragraph {
            font-size: 1.05rem;
            line-height: 1.75;
            color: var(--clr-text-main);
            margin-bottom: 20px;
        }

        /* Accordion Styling */
        .accordion-container {
            margin: 40px 0;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .accordion-item {
            border: 1px solid var(--clr-border);
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            background-color: #ffffff;
            transition: all var(--transition-fast);
            box-shadow: var(--shadow-sm);
        }

        .accordion-header {
            width: 100%;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            color: var(--clr-text-main);
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.1rem;
            text-align: left;
            transition: all var(--transition-fast);
            border: none;
            cursor: pointer;
        }

        .accordion-header:hover {
            color: var(--clr-accent-hover);
            background-color: var(--clr-bg-secondary);
        }

        .accordion-icon {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--clr-accent);
            transition: transform var(--transition-fast);
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height var(--transition-smooth);
            padding: 0 24px;
            background-color: #ffffff;
        }

        .accordion-content-inner {
            padding: 20px 0;
            color: var(--clr-text-muted);
            font-size: 1rem;
            line-height: 1.65;
            border-top: 1px solid var(--clr-border);
        }

        /* Active accordion state */
        .accordion-item.active {
            border-color: var(--clr-accent);
            box-shadow: 0 4px 12px rgba(0, 210, 196, 0.08);
        }

        .accordion-item.active .accordion-header {
            background-color: var(--clr-accent);
            color: #ffffff;
        }

        .accordion-item.active .accordion-header .accordion-icon {
            color: #ffffff;
        }

        /* Why Choose Section */
        .why-choose-section {
            margin: 45px 0;
        }

        .why-choose-title {
            font-family: var(--font-heading);
            color: var(--clr-brand);
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .why-choose-list {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .why-choose-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .why-choose-bullet {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: rgba(0, 210, 196, 0.1);
            color: var(--clr-accent);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .why-choose-bullet svg {
            width: 14px;
            height: 14px;
        }

        .why-choose-text {
            font-size: 1.05rem;
            color: var(--clr-text-main);
            line-height: 1.5;
        }

        /* Sidebar Styling */
        .sidebar-card {
            background-color: #FFFFFF;
            border-radius: var(--border-radius-md);
            border: 1px solid var(--clr-border);
            box-shadow: var(--shadow-sm);
            padding: 30px;
            margin-bottom: 30px;
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

        /* Services Sidebar Menu */
        .sidebar-services-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-services-item {
            border-bottom: 1px solid var(--clr-border);
        }

        .sidebar-services-item:last-child {
            border-bottom: none;
        }

        .sidebar-services-item a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 8px;
            color: var(--clr-text-main);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all var(--transition-fast);
        }

        .sidebar-services-item a svg {
            width: 14px;
            height: 14px;
            color: var(--clr-text-muted);
            transition: transform var(--transition-fast);
        }

        .sidebar-services-item a:hover {
            color: var(--clr-accent-hover);
            padding-left: 15px;
        }

        .sidebar-services-item a:hover svg {
            color: var(--clr-accent-hover);
            transform: translateX(4px);
        }

        .sidebar-services-item.active a {
            color: var(--clr-accent-hover);
            font-weight: 700;
        }

        .sidebar-services-item.active a svg {
            color: var(--clr-accent-hover);
        }

        /* Teal Box for Department Address */
        .sidebar-teal-card {
            background-color: #008f84; /* matching screenshot dark teal */
            border: none;
            color: #ffffff;
        }

        .sidebar-teal-card h3 {
            color: #ffffff;
        }

        .sidebar-teal-card h3::after {
            background-color: #ffffff;
        }

        .sidebar-teal-card .address-info-p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
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
            color: #ffffff;
            font-weight: 500;
        }

        .sidebar-teal-card .mini-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            flex-shrink: 0;
        }

        .sidebar-teal-card .mini-icon-box svg {
            width: 16px;
            height: 16px;
        }

        /* Hours Card styling */
        .sidebar-hours-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 15px;
        }

        .sidebar-hours-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.95rem;
        }

        .sidebar-hours-day {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: var(--clr-text-main);
        }

        .sidebar-hours-day svg {
            width: 16px;
            height: 16px;
            color: var(--clr-accent);
        }

        .sidebar-hours-time {
            font-weight: 500;
            color: var(--clr-text-muted);
        }

        /* Appointment Form */
        .dept-appointment-box {
            background-color: var(--clr-bg-secondary);
            border: 1px solid var(--clr-border);
            border-radius: var(--border-radius-md);
            padding: 40px;
            margin-top: 50px;
        }

        .dept-appointment-box h3 {
            font-family: var(--font-heading);
            color: var(--clr-brand);
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .dept-appointment-box .subtitle-alert {
            font-size: 1rem;
            color: var(--clr-text-muted);
            margin-bottom: 30px;
        }

        .dept-appointment-box .subtitle-alert strong {
            color: var(--clr-accent-hover);
        }

        .appt-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 576px) {
            .appt-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .appt-form-group {
            display: flex;
            flex-direction: column;
        }

        .appt-form-control {
            padding: 15px 20px;
            border: 1px solid var(--clr-border);
            border-radius: var(--border-radius-sm);
            font-family: var(--font-body);
            font-size: 0.95rem;
            background-color: #ffffff;
            transition: all var(--transition-fast);
            color: var(--clr-text-main);
        }

        .appt-form-control:focus {
            outline: none;
            border-color: var(--clr-accent);
            box-shadow: 0 0 0 3px rgba(0, 210, 196, 0.12);
        }

        .appt-form-group.full-width {
            grid-column: 1 / -1;
        }

        .appt-submit-btn {
            width: 100%;
            padding: 16px;
            background: var(--gradient-accent);
            color: #ffffff;
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.05rem;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-fast);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .appt-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
        }

        /* Our Doctors Section */
        .dept-doctors-section {
            padding: 80px 0;
            background-color: #f8fafc;
            border-top: 1px solid var(--clr-border);
        }

        .dept-doctors-title {
            text-align: center;
            font-family: var(--font-heading);
            color: var(--clr-brand);
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 15px;
        }

        .dept-doctors-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 4px;
            background-color: var(--clr-accent);
            border-radius: 2px;
        }

        .dept-doctors-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            max-width: 1000px;
            margin: 0 auto;
        }

        .dept-doctor-card {
            flex: 0 1 300px;
            background-color: #ffffff;
            border-radius: var(--border-radius-md);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid var(--clr-border);
            text-align: center;
            transition: transform var(--transition-medium);
            margin: 10px 0;
        }

        .dept-doctor-card:hover {
            transform: translateY(-5px);
        }

        .dept-doctor-image-wrap {
            width: 100%;
            height: 280px;
            overflow: hidden;
            background-color: #f1f5f9;
        }

        .dept-doctor-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
        }

        .dept-doctor-info {
            padding: 25px 20px;
        }

        .dept-doctor-name {
            font-family: var(--font-heading);
            color: #008f84; /* matching screenshot green */
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .dept-doctor-qual {
            font-size: 0.9rem;
            color: var(--clr-text-muted);
            font-weight: 500;
            margin-bottom: 5px;
        }

        .dept-doctor-specialty {
            font-size: 0.95rem;
            color: var(--clr-text-main);
            font-weight: 600;
        }

        /* Treatment Subservices List styles */
        .treatment-subservices-title {
            font-family: var(--font-heading);
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--clr-brand);
            margin: 15px 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .treatment-subservices-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            list-style: none;
            padding: 0;
        }

        .subservice-link {
            display: inline-flex;
            align-items: center;
            background-color: #f1f5f9;
            border: 1px solid var(--clr-border);
            color: var(--clr-text-main);
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 30px;
            text-decoration: none;
            transition: all var(--transition-fast);
        }

        .subservice-link:hover {
            background: var(--gradient-accent);
            color: #ffffff;
            border-color: var(--clr-accent);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 210, 196, 0.15);
        }
    </style>
</head>

<body>

    <?php
    $active_page = 'treatments';
    require_once __DIR__ . '/../includes/header.php';
    ?>

    <main>
        <!-- Department Banner Header -->
        <section class="dept-detail-banner">
            <div class="container">
                <h1 class="dept-detail-banner-title"><?php echo htmlspecialchars($active_dept['name']); ?></h1>
                <p class="dept-detail-banner-subtitle"><?php echo htmlspecialchars($active_dept['subtitle'] ?? 'Detail about our service'); ?></p>
            </div>
        </section>

        <!-- Two Column Content Grid -->
        <section class="dept-detail-container">
            <div class="container">
                <div class="dept-detail-grid">

                    <!-- Left: Main Details & Form -->
                    <div class="dept-main-content">
                        <img src="<?php echo htmlspecialchars($active_dept['image']); ?>" alt="<?php echo htmlspecialchars($active_dept['name']); ?>" class="dept-main-image" onerror="this.src='images/doctor_consultation.png';">
                        
                        <!-- Parent breadcrumb if sub-service -->
                        <?php if (!empty($active_dept['parent_service'])): ?>
                            <div style="margin-top: 15px; font-size: 0.9rem; font-weight: 600; color: var(--clr-text-muted);">
                                <a href="treatments/" style="color: var(--clr-brand); text-decoration: none;">Treatments</a> &gt; 
                                <a href="treatments/<?php echo $active_dept['parent_slug']; ?>.php" style="color: var(--clr-brand); text-decoration: none;"><?php echo htmlspecialchars($active_dept['parent_service']); ?></a> &gt; 
                                <span style="color: var(--clr-text-main);"><?php echo htmlspecialchars($active_dept['name']); ?></span>
                            </div>
                        <?php endif; ?>

                        <h2 class="dept-main-title"><?php echo htmlspecialchars($active_dept['name']); ?></h2>
                        <p class="dept-paragraph"><?php echo htmlspecialchars($active_dept['intro'] ?? ''); ?></p>

                        <!-- Sub-services lists (if major service) -->
                        <?php if (!empty($active_dept['sub_services'])): ?>
                            <div class="plain-section" style="margin-bottom: 35px;">
                                <h3 class="dept-sub-title" style="font-family: var(--font-heading); color: var(--clr-brand); font-size: 1.6rem; font-weight: 700; margin-bottom: 12px; margin-top: 25px;">Sub Services Included:</h3>
                                <ul class="treatment-subservices-list">
                                    <?php foreach ($active_dept['sub_services'] as $sub_slug => $sub_name): ?>
                                        <li>
                                            <a href="treatments/<?php echo $sub_slug; ?>.php" class="subservice-link">
                                                <?php echo htmlspecialchars($sub_name); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Features and capabilities -->
                        <?php if (!empty($active_dept['features'])): ?>
                            <div class="plain-section" style="margin-bottom: 35px;">
                                <h3 class="dept-sub-title" style="font-family: var(--font-heading); color: var(--clr-brand); font-size: 1.6rem; font-weight: 700; margin-bottom: 12px; margin-top: 25px;">Key Features &amp; Capabilities</h3>
                                <ul class="dept-bullet-list" style="margin-bottom: 20px; padding-left: 20px; list-style-type: disc;">
                                    <?php foreach ($active_dept['features'] as $f): ?>
                                        <li style="font-size: 0.95rem; line-height: 1.6; color: var(--clr-text-main); margin-bottom: 8px;"><?php echo htmlspecialchars($f); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Why Choose Section -->
                        <?php if (!empty($active_dept['why_choose'])): ?>
                            <div class="why-choose-section">
                                <h3 class="why-choose-title">Why Choose Vindhya Hospital for <?php echo htmlspecialchars($active_dept['name']); ?>?</h3>
                                <div class="why-choose-list">
                                    <?php foreach ($active_dept['why_choose'] as $item): ?>
                                        <div class="why-choose-item">
                                            <div class="why-choose-bullet">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                            </div>
                                            <span class="why-choose-text"><?php echo htmlspecialchars($item); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Make an appointment Form -->
                        <div class="dept-appointment-box" id="appointment">
                            <h3>Make an appointment</h3>
                            <p class="subtitle-alert">We will send you a confirmation within 24 hours. <strong>Emergency?</strong> Call +91 9589899826</p>
                            
                            <form action="index.php" method="POST">
                                <div class="appt-form-grid">
                                    <div class="appt-form-group">
                                        <input type="text" name="patient_name" class="appt-form-control" placeholder="Full Name*" required>
                                    </div>
                                    <div class="appt-form-group">
                                        <input type="tel" name="patient_phone" class="appt-form-control" placeholder="Phone*" required>
                                    </div>
                                    <div class="appt-form-group">
                                        <input type="email" name="patient_email" class="appt-form-control" placeholder="Email*">
                                    </div>
                                    <div class="appt-form-group">
                                        <select name="department" class="appt-form-control">
                                            <option value="<?php echo htmlspecialchars($active_dept['name']); ?>"><?php echo htmlspecialchars($active_dept['name']); ?></option>
                                            <?php foreach ($treatments_list as $t_slug => $t_name): ?>
                                                <?php if ($t_name !== $active_dept['name']): ?>
                                                    <option value="<?php echo htmlspecialchars($t_name); ?>"><?php echo htmlspecialchars($t_name); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="appt-form-group">
                                        <input type="date" name="appointment_date" class="appt-form-control" required>
                                    </div>
                                    <div class="appt-form-group">
                                        <input type="time" name="appointment_time" class="appt-form-control" required>
                                    </div>
                                    <div class="appt-form-group full-width">
                                        <textarea name="message" class="appt-form-control" rows="4" placeholder="Message*"></textarea>
                                    </div>
                                </div>
                                <button type="submit" name="submit_appointment" class="appt-submit-btn">Submit Now</button>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Sidebar -->
                    <aside class="dept-sidebar">
                        
                        <!-- 1. Services List -->
                        <div class="sidebar-card">
                            <h3>Clinical Services</h3>
                            <?php
                            $structured_menu = [
                                'laboratory-services' => [
                                    'name' => 'Laboratory Services',
                                    'subs' => [
                                        'clinical-biochemistry' => 'Clinical Biochemistry',
                                        'clinical-microbiology-serology' => 'Clinical Micro-biology & Serology',
                                        'clinical-pathology' => 'Clinical Pathology',
                                        'haematology' => 'Haematology',
                                        'blood-centre' => 'Blood Centre'
                                    ]
                                ],
                                'diagnostic-services' => [
                                    'name' => 'Diagnostic Services',
                                    'subs' => [
                                        'ultrasound' => 'Ultrasound',
                                        'ct-scan' => 'C-T scan',
                                        'uro-flow' => 'Uro Flow',
                                        'echo' => 'ECHO',
                                        'x-ray' => 'X-Ray',
                                        'ecg' => 'ECG',
                                        'tmt' => 'T.M.T'
                                    ]
                                ],
                                'clinical-support-services' => [
                                    'name' => 'Clinical Support Services',
                                    'subs' => [
                                        'physiotherapy' => 'Physiotherapy',
                                        'dietetics' => 'Dietetics',
                                        'ambulance' => 'Ambulance',
                                        'oxygen-plant' => 'Oxygen Plant'
                                    ]
                                ],
                                'radiology-services' => [
                                    'name' => 'Radiology Services',
                                    'subs' => [
                                        'xray-fix-mobile-carm' => 'X-RAY Fix Mobile C-Arm'
                                    ]
                                ],
                                'health-services' => [
                                    'name' => 'Health Services',
                                    'subs' => [
                                        'mediclaim' => 'Mediclaim',
                                        'ayushman' => 'Ayushman',
                                        'echs' => 'ECHS',
                                        'sghs' => 'SGHS'
                                    ]
                                ],
                                'cssd' => [
                                    'name' => 'CSSD',
                                    'subs' => [
                                        'fully-automatic-autoclave-machine' => 'Fully Automatic Autoclave'
                                    ]
                                ]
                            ];
                            ?>
                            <ul class="sidebar-services-list">
                                <?php foreach ($structured_menu as $m_slug => $m_data): ?>
                                    <?php 
                                    $is_active_major = ($m_slug === $active_id);
                                    $has_active_sub = isset($m_data['subs'][$active_id]);
                                    $is_expanded = ($is_active_major || $has_active_sub);
                                    ?>
                                    <li class="sidebar-services-item <?php echo $is_active_major ? 'active' : ''; ?>" style="display: flex; flex-direction: column; align-items: stretch; padding: 0;">
                                        <a href="treatments/<?php echo $m_slug; ?>.php" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; font-weight: 700; border-bottom: 1px solid var(--clr-border);">
                                            <span><?php echo htmlspecialchars($m_data['name']); ?></span>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 14px; height: 14px; transform: <?php echo $is_expanded ? 'rotate(90deg)' : 'none'; ?>; transition: transform var(--transition-fast);">
                                                <path d="M5 12h14M12 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                        <?php if ($is_expanded && !empty($m_data['subs'])): ?>
                                            <ul style="list-style: none; padding: 10px 0 10px 20px; background-color: #f8fafc; border-bottom: 1px solid var(--clr-border);">
                                                <?php foreach ($m_data['subs'] as $sub_slug => $sub_name): ?>
                                                    <li style="margin: 8px 0;">
                                                        <a href="treatments/<?php echo $sub_slug; ?>.php" style="display: flex; align-items: center; gap: 8px; color: <?php echo ($sub_slug === $active_id) ? 'var(--clr-accent-hover)' : 'var(--clr-text-main)'; ?>; font-weight: <?php echo ($sub_slug === $active_id) ? '700' : '500'; ?>; font-size: 0.9rem; text-decoration: none;">
                                                            <span style="width: 6px; height: 6px; border-radius: 50%; background-color: <?php echo ($sub_slug === $active_id) ? 'var(--clr-accent)' : '#cbd5e1'; ?>;"></span>
                                                            <span><?php echo htmlspecialchars($sub_name); ?></span>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- 2. Address Card (Teal) -->
                        <div class="sidebar-card sidebar-teal-card">
                            <h3>Department Address</h3>
                            <p class="address-info-p">
                                <?php echo htmlspecialchars($settings['booking_address'] ?? 'Narendra Nagar, Amaiya Colony, Rewa (M.P.)'); ?>
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
                                    <span><?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?></span>
                                </a>
                            </div>
                        </div>

                        <!-- 3. Hours Card -->
                        <div class="sidebar-card">
                            <h3>Department Hours</h3>
                            <div class="sidebar-hours-list">
                                <div class="sidebar-hours-row">
                                    <span class="sidebar-hours-day">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        Monday - Friday
                                    </span>
                                    <span class="sidebar-hours-time">08:00 - 20:00</span>
                                </div>
                                <div class="sidebar-hours-row">
                                    <span class="sidebar-hours-day">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        Saturday
                                    </span>
                                    <span class="sidebar-hours-time">09:00 - 14:00</span>
                                </div>
                                <div class="sidebar-hours-row">
                                    <span class="sidebar-hours-day">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        Sunday
                                    </span>
                                    <span class="sidebar-hours-time">Close</span>
                                </div>
                            </div>
                        </div>

                    </aside>

                </div>
            </div>
        <!-- Our Doctors Section (if defined for this department) -->
        <?php if (!empty($active_dept['doctors'])): ?>
            <section class="dept-doctors-section">
                <div class="container">
                    <h2 class="dept-doctors-title">Our Doctors</h2>
                    <div class="dept-doctors-grid">
                        <?php foreach ($active_dept['doctors'] as $doc): ?>
                            <div class="dept-doctor-card">
                                <div class="dept-doctor-image-wrap">
                                    <img src="<?php echo htmlspecialchars($doc['image']); ?>" alt="<?php echo htmlspecialchars($doc['name']); ?>" class="dept-doctor-img" onerror="this.src='images/doctor_default.png';">
                                </div>
                                <div class="dept-doctor-info">
                                    <h3 class="dept-doctor-name"><?php echo htmlspecialchars($doc['name']); ?></h3>
                                    <p class="dept-doctor-qual"><?php echo htmlspecialchars($doc['qualifications']); ?></p>
                                    <p class="dept-doctor-specialty"><?php echo htmlspecialchars($doc['specialty']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <!-- Accordion Interactions & Mobile Navigation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const item = this.parentElement;
                    const content = this.nextElementSibling;
                    const icon = this.querySelector('.accordion-icon');
                    
                    const isActive = item.classList.contains('active');
                    
                    // Close all other open items
                    document.querySelectorAll('.accordion-item').forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                            otherItem.querySelector('.accordion-content').style.maxHeight = null;
                            otherItem.querySelector('.accordion-icon').innerHTML = '&plus;';
                        }
                    });
                    
                    if (isActive) {
                        item.classList.remove('active');
                        content.style.maxHeight = null;
                        icon.innerHTML = '&plus;';
                    } else {
                        item.classList.add('active');
                        content.style.maxHeight = content.scrollHeight + "px";
                        icon.innerHTML = '&minus;';
                    }
                });
            });
        });
    </script>
</body>

</html>
