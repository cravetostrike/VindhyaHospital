<?php
/**
 * VHRC Database Connection & Schema Initialization
 * Uses PDO MySQL — manageable via phpMyAdmin.
 */

// ─── MySQL Credentials ────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'vindhya_hospital');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
// ─────────────────────────────────────────────────────────────────────────────

// Ensure upload directories exist
$uploads_gallery_dir = dirname(__DIR__) . '/uploads/gallery';
if (!is_dir($uploads_gallery_dir)) {
    mkdir($uploads_gallery_dir, 0777, true);
}

$uploads_favicons_dir = dirname(__DIR__) . '/uploads/favicons';
if (!is_dir($uploads_favicons_dir)) {
    mkdir($uploads_favicons_dir, 0777, true);
}

try {
    // Step 1: Connect without selecting a database so we can create it if needed
    $init_pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $init_pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $init_pdo = null; // close temp connection

    // Step 2: Connect to the target database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    // ── 1. Admins Table ───────────────────────────────────────────────────────
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `username`   VARCHAR(100) NOT NULL UNIQUE,
        `password`   VARCHAR(255) NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── 2. Appointments Table ─────────────────────────────────────────────────
    $pdo->exec("CREATE TABLE IF NOT EXISTS `appointments` (
        `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `patient_name`     VARCHAR(200) NOT NULL,
        `patient_phone`    VARCHAR(30)  NOT NULL,
        `patient_email`    VARCHAR(200) DEFAULT NULL,
        `appointment_date` VARCHAR(50)  NOT NULL,
        `department`       VARCHAR(200) DEFAULT NULL,
        `doctor`           VARCHAR(200) DEFAULT NULL,
        `message`          TEXT         DEFAULT NULL,
        `status`           VARCHAR(50)  NOT NULL DEFAULT 'Pending',
        `created_at`       DATETIME     DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── 3. Doctors Table ──────────────────────────────────────────────────────
    $pdo->exec("CREATE TABLE IF NOT EXISTS `doctors` (
        `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name`           VARCHAR(200) NOT NULL,
        `specialty`      VARCHAR(200) NOT NULL,
        `qualifications` TEXT         NOT NULL,
        `experience`     VARCHAR(100) DEFAULT NULL,
        `image_path`     VARCHAR(500) DEFAULT NULL,
        `social_fb`      VARCHAR(500) NOT NULL DEFAULT '',
        `social_tw`      VARCHAR(500) NOT NULL DEFAULT '',
        `social_ig`      VARCHAR(500) NOT NULL DEFAULT '',
        `social_in`      VARCHAR(500) NOT NULL DEFAULT '',
        `created_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── 4. Gallery Posters Table ──────────────────────────────────────────────
    $pdo->exec("CREATE TABLE IF NOT EXISTS `gallery_posters` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `image_path` VARCHAR(500) NOT NULL,
        `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── 5. Hero Slides Table ──────────────────────────────────────────────────
    $pdo->exec("CREATE TABLE IF NOT EXISTS `hero_slides` (
        `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `slide_order` INT UNIQUE   NOT NULL,
        `tag`         VARCHAR(300) DEFAULT NULL,
        `title`       TEXT         DEFAULT NULL,
        `description` TEXT         DEFAULT NULL,
        `image_path`  VARCHAR(500) DEFAULT NULL,
        `cta1_text`   VARCHAR(200) DEFAULT NULL,
        `cta1_link`   VARCHAR(500) DEFAULT NULL,
        `cta2_text`   VARCHAR(200) DEFAULT NULL,
        `cta2_link`   VARCHAR(500) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── 6. Homepage Settings Table ────────────────────────────────────────────
    $pdo->exec("CREATE TABLE IF NOT EXISTS `homepage_settings` (
        `key`   VARCHAR(100) NOT NULL,
        `value` TEXT         NOT NULL,
        PRIMARY KEY (`key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── Migration: Add social columns to doctors if missing ───────────────────
    $social_cols = ['social_fb', 'social_tw', 'social_ig', 'social_in'];
    foreach ($social_cols as $col) {
        $check = $pdo->prepare("
            SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'doctors' AND COLUMN_NAME = ?
        ");
        $check->execute([DB_NAME, $col]);
        if ($check->fetchColumn() == 0) {
            $pdo->exec("ALTER TABLE `doctors` ADD COLUMN `{$col}` VARCHAR(500) NOT NULL DEFAULT ''");
        }
    }

    // ── Seed: Default Admin (admin / admin123) ────────────────────────────────
    $stmt = $pdo->query("SELECT COUNT(*) FROM `admins`");
    if ($stmt->fetchColumn() == 0) {
        $insert = $pdo->prepare("INSERT INTO `admins` (`username`, `password`) VALUES (?, ?)");
        $insert->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
    }

    // ── Seed: Default Hero Slides ─────────────────────────────────────────────
    $stmt = $pdo->query("SELECT COUNT(*) FROM `hero_slides`");
    if ($stmt->fetchColumn() == 0) {
        $default_slides = [
            [
                'slide_order' => 1,
                'tag'         => '👉 Patient-Centred Healthcare',
                'title'       => 'Healing with <br><span>Compassion & Care</span>',
                'description' => 'Vindhya Hospital provides world-class clinical treatments under the gentle care of our certified specialists.',
                'image_path'  => 'images/slide1.png',
                'cta1_text'   => 'Book Appointment',
                'cta1_link'   => '#appointment',
                'cta2_text'   => 'Our Services',
                'cta2_link'   => '#services',
            ],
            [
                'slide_order' => 2,
                'tag'         => '👉 Medical Accreditations',
                'title'       => 'World-Class <br><span>Doctors & Experts</span>',
                'description' => 'Consult with leading physicians and experienced surgeons dedicated to your family\'s safety and recovery.',
                'image_path'  => 'images/slide2.png',
                'cta1_text'   => 'Meet Our Team',
                'cta1_link'   => '#doctors',
                'cta2_text'   => 'Contact Us',
                'cta2_link'   => '#contact',
            ],
            [
                'slide_order' => 3,
                'tag'         => '👉 Emergency Care 24/7',
                'title'       => 'Modern Facilities & <br><span>Infrastructure</span>',
                'description' => 'Equipped with state-of-the-art diagnostic labs, high-end ICU chambers, and a 24x7 trauma response team.',
                'image_path'  => 'images/slide3.png',
                'cta1_text'   => 'Emergency Hotline',
                'cta1_link'   => 'tel:+919589899826',
                'cta2_text'   => 'Take a Tour',
                'cta2_link'   => '#gallery',
            ],
        ];

        $insert_slide = $pdo->prepare("
            INSERT INTO `hero_slides`
                (`slide_order`,`tag`,`title`,`description`,`image_path`,`cta1_text`,`cta1_link`,`cta2_text`,`cta2_link`)
            VALUES
                (:slide_order,:tag,:title,:description,:image_path,:cta1_text,:cta1_link,:cta2_text,:cta2_link)
        ");
        foreach ($default_slides as $slide) {
            $insert_slide->execute($slide);
        }
    }

    // ── Seed: Default Homepage Settings ──────────────────────────────────────
    $stmt = $pdo->query("SELECT COUNT(*) FROM `homepage_settings`");
    if ($stmt->fetchColumn() == 0) {
        $default_settings = [
            'emergency_title'    => '24/7 Emergency Medical Services',
            'emergency_desc'     => 'Vindhya Hospital, Rewa provides 24×7 rapid response emergency care with advanced diagnostic facilities and expert trauma specialists for immediate medical support.',
            'emergency_btn_text' => 'Book An Appointment',
            'emergency_btn_link' => '#appointment',

            'accred1_title' => 'National & International Standards',
            'accred1_desc'  => 'Accredited Organization: Vindhya Hospital, Rewa',
            'accred2_title' => 'NABH & ISO Certified Quality',
            'accred2_desc'  => 'Offering best-in-class healthcare services and patient safety standards',

            'stat1_number' => '24+',
            'stat1_label'  => 'Specialist Doctors',
            'stat2_number' => '29,000+',
            'stat2_label'  => 'Patients Served',
            'stat3_number' => '100+',
            'stat3_label'  => 'Advanced Care Beds',
            'stat4_number' => '15+',
            'stat4_label'  => 'Years of Excellence',

            'booking_hotline' => '+91 9589899826',
            'booking_hours'   => 'Mon - Sun: 09:00 AM - 09:00 PM',
            'booking_address' => 'Narendra Nagar, Amaiya Colony, Rewa (M.P.)',

            'header_hours' => 'Mon - Sun 0900 - 2100',
            'header_phone' => '+91 9589899826',
            'header_email' => 'vhrcrewa@gmail.com',

            'social_fb'  => '#',
            'social_in'  => '#',
            'social_pin' => '#',
            'social_tw'  => '#',
            'social_yt'  => '#',
            'social_ig'  => '#',

            'notification_email' => 'vhrcrewa@gmail.com',
            'website_favicon'    => '',
            'admin_favicon'      => '',
        ];

        $insert_setting = $pdo->prepare("INSERT INTO `homepage_settings` (`key`, `value`) VALUES (?, ?)");
        foreach ($default_settings as $key => $value) {
            $insert_setting->execute([$key, $value]);
        }
    } else {
        // Seed any new keys that may not exist yet (backward-compat)
        $new_defaults = [
            'notification_email' => 'vhrcrewa@gmail.com',
            'website_favicon'    => '',
            'admin_favicon'      => '',
        ];
        $check_key    = $pdo->prepare("SELECT COUNT(*) FROM `homepage_settings` WHERE `key` = ?");
        $insert_key   = $pdo->prepare("INSERT INTO `homepage_settings` (`key`, `value`) VALUES (?, ?)");
        foreach ($new_defaults as $k => $v) {
            $check_key->execute([$k]);
            if ($check_key->fetchColumn() == 0) {
                $insert_key->execute([$k, $v]);
            }
        }
    }

} catch (PDOException $e) {
    die("Database initialization error: " . $e->getMessage());
}
