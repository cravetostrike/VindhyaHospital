<?php
/**
 * VHRC Database Connection & Schema Initialization
 * Uses PDO SQLite for zero-configuration local stack setup.
 */

// Define database directory and file paths
$db_dir = dirname(__DIR__) . '/database';
if (!is_dir($db_dir)) {
    mkdir($db_dir, 0777, true);
}

$uploads_gallery_dir = dirname(__DIR__) . '/uploads/gallery';
if (!is_dir($uploads_gallery_dir)) {
    mkdir($uploads_gallery_dir, 0777, true);
}

$uploads_favicons_dir = dirname(__DIR__) . '/uploads/favicons';
if (!is_dir($uploads_favicons_dir)) {
    mkdir($uploads_favicons_dir, 0777, true);
}


$db_file = $db_dir . '/vhrc.db';

try {
    // Establish connection to SQLite
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Enable WAL mode for better concurrency in SQLite
    $pdo->exec("PRAGMA journal_mode=WAL;");

    // Migration check: check if gallery_posters has 'slot' column. If so, drop and recreate it.
    try {
        $check_stmt = $pdo->query("PRAGMA table_info(gallery_posters)");
        $columns = $check_stmt->fetchAll(PDO::FETCH_ASSOC);
        $has_slot = false;
        foreach ($columns as $column) {
            if ($column['name'] === 'slot') {
                $has_slot = true;
                break;
            }
        }
        if ($has_slot) {
            $pdo->exec("DROP TABLE gallery_posters");
        }
    } catch (PDOException $e) {
        // Table might not exist yet, ignore
    }

    // 1. Create Admins Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Create Appointments Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_name TEXT NOT NULL,
        patient_phone TEXT NOT NULL,
        patient_email TEXT,
        appointment_date TEXT NOT NULL,
        department TEXT,
        doctor TEXT,
        message TEXT,
        status TEXT DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Create Doctors Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS doctors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        specialty TEXT NOT NULL,
        qualifications TEXT NOT NULL,
        experience TEXT,
        image_path TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Create Gallery Posters Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS gallery_posters (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        image_path TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 4b. Create Hero Slides Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_slides (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slide_order INTEGER UNIQUE NOT NULL,
        tag TEXT,
        title TEXT,
        description TEXT,
        image_path TEXT,
        cta1_text TEXT,
        cta1_link TEXT,
        cta2_text TEXT,
        cta2_link TEXT
    )");

    // 4c. Create Homepage Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS homepage_settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL
    )");

    // 5. Create Default Admin Credentials (username: admin / password: admin123)
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        $default_user = 'admin';
        $default_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $insert->execute([$default_user, $default_pass]);
    }

    // 6. Seed Default Gallery (Removed - Dynamic CRUD)

    // 6b. Seed Default Hero Slides
    $stmt = $pdo->query("SELECT COUNT(*) FROM hero_slides");
    if ($stmt->fetchColumn() == 0) {
        $default_slides = [
            [
                'slide_order' => 1,
                'tag' => "👉 Patient-Centred Healthcare",
                'title' => "Healing with <br><span>Compassion & Care</span>",
                'description' => "Vindhya Hospital provides world-class clinical treatments under the gentle care of our certified specialists.",
                'image_path' => "images/slide1.png",
                'cta1_text' => "Book Appointment",
                'cta1_link' => "#appointment",
                'cta2_text' => "Our Services",
                'cta2_link' => "#services"
            ],
            [
                'slide_order' => 2,
                'tag' => "👉 Medical Accreditations",
                'title' => "World-Class <br><span>Doctors & Experts</span>",
                'description' => "Consult with leading physicians and experienced surgeons dedicated to your family's safety and recovery.",
                'image_path' => "images/slide2.png",
                'cta1_text' => "Meet Our Team",
                'cta1_link' => "#doctors",
                'cta2_text' => "Contact Us",
                'cta2_link' => "#contact"
            ],
            [
                'slide_order' => 3,
                'tag' => "👉 Emergency Care 24/7",
                'title' => "Modern Facilities & <br><span>Infrastructure</span>",
                'description' => "Equipped with state-of-the-art diagnostic labs, high-end ICU chambers, and a 24x7 trauma response team.",
                'image_path' => "images/slide3.png",
                'cta1_text' => "Emergency Hotline",
                'cta1_link' => "tel:+919589899826",
                'cta2_text' => "Take a Tour",
                'cta2_link' => "#gallery"
            ]
        ];
        
        $insert_slide = $pdo->prepare("INSERT INTO hero_slides (slide_order, tag, title, description, image_path, cta1_text, cta1_link, cta2_text, cta2_link) VALUES (:slide_order, :tag, :title, :description, :image_path, :cta1_text, :cta1_link, :cta2_text, :cta2_link)");
        foreach ($default_slides as $slide) {
            $insert_slide->execute($slide);
        }
    }

    // 6c. Seed Default Homepage Settings
    $stmt = $pdo->query("SELECT COUNT(*) FROM homepage_settings");
    if ($stmt->fetchColumn() == 0) {
        $default_settings = [
            'emergency_title' => "24/7 Emergency Medical Services",
            'emergency_desc' => "Vindhya Hospital, Rewa provides 24&times;7 rapid response emergency care with advanced diagnostic facilities and expert trauma specialists for immediate medical support.",
            'emergency_btn_text' => "Book An Appointment",
            'emergency_btn_link' => "#appointment",
            
            'accred1_title' => "National & International Standards",
            'accred1_desc' => "Accredited Organization: Vindhya Hospital, Rewa",
            'accred2_title' => "NABH & ISO Certified Quality",
            'accred2_desc' => "Offering best-in-class healthcare services and patient safety standards",
            
            'stat1_number' => "24+",
            'stat1_label' => "Specialist Doctors",
            'stat2_number' => "29,000+",
            'stat2_label' => "Patients Served",
            'stat3_number' => "100+",
            'stat3_label' => "Advanced Care Beds",
            'stat4_number' => "15+",
            'stat4_label' => "Years of Excellence",
            
            'booking_hotline' => "+91 9589899826",
            'booking_hours' => "Mon - Sun: 09:00 AM - 09:00 PM",
            'booking_address' => "Narendra Nagar, Amaiya Colony, Rewa (M.P.)",
            
            'header_hours' => "Mon - Sun 0900 - 2100",
            'header_phone' => "+91 9589899826",
            'header_email' => "vhrcrewa@gmail.com",
            
            'social_fb' => "#",
            'social_in' => "#",
            'social_pin' => "#",
            'social_tw' => "#",
            'social_yt' => "#",
            'social_ig' => "#"
        ];
        
        $insert_setting = $pdo->prepare("INSERT INTO homepage_settings (key, value) VALUES (?, ?)");
        foreach ($default_settings as $key => $value) {
            $insert_setting->execute([$key, $value]);
        }
    }

    // Seed new keys if they don't exist yet (for backward compatibility)
    $new_defaults = [
        'notification_email' => "vhrcrewa@gmail.com",
        'website_favicon' => "",
        'admin_favicon' => ""
    ];
    $check_setting = $pdo->prepare("SELECT COUNT(*) FROM homepage_settings WHERE key = ?");
    $insert_setting = $pdo->prepare("INSERT INTO homepage_settings (key, value) VALUES (?, ?)");
    foreach ($new_defaults as $key => $val) {
        $check_setting->execute([$key]);
        if ($check_setting->fetchColumn() == 0) {
            $insert_setting->execute([$key, $val]);
        }
    }

    // Migration: Check if doctors table has social_fb column. If not, add the social columns.
    try {
        $check_stmt = $pdo->query("PRAGMA table_info(doctors)");
        $columns = $check_stmt->fetchAll(PDO::FETCH_ASSOC);
        $has_social = false;
        foreach ($columns as $column) {
            if ($column['name'] === 'social_fb') {
                $has_social = true;
                break;
            }
        }
        if (!$has_social) {
            $pdo->exec("ALTER TABLE doctors ADD COLUMN social_fb TEXT DEFAULT ''");
            $pdo->exec("ALTER TABLE doctors ADD COLUMN social_tw TEXT DEFAULT ''");
            $pdo->exec("ALTER TABLE doctors ADD COLUMN social_ig TEXT DEFAULT ''");
            $pdo->exec("ALTER TABLE doctors ADD COLUMN social_in TEXT DEFAULT ''");
        }
    } catch (PDOException $e) {
        // Ignore or log
    }

} catch (PDOException $e) {
    die("Database initialization error: " . $e->getMessage());
}
