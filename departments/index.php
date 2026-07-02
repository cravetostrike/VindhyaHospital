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

// 18 Departments Array
$depts = [
    [
        'id'    => 'urology',
        'name'  => 'Urology & Urosurgery',
        'cat'   => 'surgical',
        'image' => 'images/graphics-for-homepage/poster-12.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.7S6 9.3 6 14.2a6 6 0 0 0 12 0C18 9.3 12 2.7 12 2.7z"/><path d="M12 9.5v5"/></svg>',
        'desc'  => 'Complete solution for Kidney, Stone, Prostate and Genito-urinary diseases with advanced Mini-TURP (22 Fr. Sheath) technique that reduces stricture formation.'
    ],
    [
        'id'    => 'gynecology',
        'name'  => 'Obstetrics, Gynecology & Infertility',
        'cat'   => 'medical',
        'image' => 'images/graphics-for-homepage/poster-13.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="10" r="4"/><path d="M12 14v7M9 18h6"/></svg>',
        'desc'  => 'Complete solution for Obstetrics, Gynecology & Infertility- High Risk Pregnancy Care, AC Labour Suit Equipped With Neonatal support.'
    ],
    [
        'id'    => 'ivf',
        'name'  => 'IVF (In Vitro Fertilization)',
        'cat'   => 'medical',
        'image' => 'images/graphics-for-homepage/poster-13.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>',
        'desc'  => 'State-of-the-art fertility center offering personalized In Vitro Fertilization (IVF), ICSI, and reproductive healthcare to help build families.'
    ],
    [
        'id'    => 'laparoscopy',
        'name'  => 'General & Laparoscopic Surgery',
        'cat'   => 'surgical',
        'image' => 'images/graphics-for-homepage/poster-7.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l4-4a1 1 0 0 0 0-1.4l-1.6-1.6a1 1 0 0 0-1.4 0l-4 4zM14.7 6.3 3 18v3h3l11.7-11.7"/></svg>',
        'desc'  => 'General Surgery: The Department of Surgery at Vindhya Hospital is a well-established unit with facilities to carry out a full spectrum of surgical procedures.'
    ],
    [
        'id'    => 'orthopedics',
        'name'  => 'Orthopedics & Adv. Trauma Centre',
        'cat'   => 'surgical',
        'image' => 'images/graphics-for-homepage/poster-8.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5V3m0 18v-2m-7-7H3m18 0h-2m-1-6-1.5 1.5M6.5 17.5 5 19m12.5 0-1.5-1.5M6.5 6.5 5 5"/></svg>',
        'desc'  => 'Orthopedics & Advanced Trauma centre: 24 &times; 7 emergency services available for all kind of trauma cases. Advanced Trauma care centre.'
    ],
    [
        'id'    => 'medicine',
        'name'  => 'General Medicine',
        'cat'   => 'medical',
        'image' => 'images/graphics-for-homepage/poster-9.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6h2a6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/></svg>',
        'desc'  => 'Treatment related to Respiratory disease including Asthma, COPD, Bronchitis, Acute Respiratory illness like pneumonia. Diabetes, Hypothyroidism.'
    ],
    [
        'id'    => 'gastroenterology',
        'name'  => 'Gastroenterology',
        'cat'   => 'medical',
        'image' => 'images/graphics-for-homepage/poster-2.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        'desc'  => 'Advanced care for digestive system and liver disorders, including diagnostic and therapeutic endoscopy, colonoscopy, and acidity treatments.'
    ],
    [
        'id'    => 'plastic-surgery',
        'name'  => 'Plastic Surgery & Burn Unit',
        'cat'   => 'surgical',
        'image' => 'images/graphics-for-homepage/poster-10.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        'desc'  => 'Our department provides a Holistic, Scientific &amp; Technologically Advanced Approach for all dermatological and cosmetic reconstructive needs.'
    ],
    [
        'id'    => 'pediatrics',
        'name'  => 'Peadiatrics and Neonatology',
        'cat'   => 'medical',
        'image' => 'images/graphics-for-homepage/poster-3.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-4.35-6-9a6 6 0 0 1 12 0c0 4.65-6 9-6 9z"/><circle cx="12" cy="11" r="2"/></svg>',
        'desc'  => 'Vindhya Hospital Children\'s Health NICUs provide specialized care for the tiniest patients. NICUs also have intermediate or continuing care.'
    ],
    [
        'id'    => 'icu-dialysis',
        'name'  => 'ICU & Dialysis Department',
        'cat'   => 'critical',
        'image' => 'images/graphics-for-homepage/poster-2.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h4l2 5 4-10 2 5h6"/></svg>',
        'desc'  => 'ICU & Dialysis: 24 hrs Critical Care Specialist available. 24&times;7 Dialysis facility. Central Monitoring system. Central Oxygen &amp; Central Suction.'
    ],
    [
        'id'    => 'spine-surgery',
        'name'  => 'Spine Surgery',
        'cat'   => 'surgical',
        'image' => 'images/graphics-for-homepage/poster-4.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1zM4 22V15"/></svg>',
        'desc'  => 'Spine Surgery is a complex and critical field associated with the diagnosis, treatment, and rehabilitation of patients with spinal disorders.'
    ],
    [
        'id'    => 'neurosurgery',
        'name'  => 'Neurosurgery',
        'cat'   => 'surgical',
        'image' => 'images/graphics-for-homepage/poster-1.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44L4 12h4.5M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44L20 12h-4.5"/></svg>',
        'desc'  => 'Neurosurgery is a complex and critical field that is associated with the diagnosis, treatment, and rehabilitation of patients with neurological issues.'
    ],
    [
        'id'    => 'anaesthesiology',
        'name'  => 'Anaesthesiology',
        'cat'   => 'critical',
        'image' => 'images/graphics-for-homepage/poster-6.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m10 15 5-5M15 15l-5-5"/></svg>',
        'desc'  => 'The Department of Anesthesiology at Vindhya Hospital is a well-established unit with facilities to carry out a full spectrum of surgical support.'
    ],
    [
        'id'    => 'oncology',
        'name'  => 'Oncology Department',
        'cat'   => 'medical',
        'image' => 'images/graphics-for-homepage/poster-5.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4.5 16.5c-1.5 1.25-2.5 3-2.5 4.5 0 1 1 2 2 2 1.5 0 3.25-1 4.5-2.5M19.5 7.5C21 6.25 22 4.5 22 3c0-1-1-2-2-2-1.5 0-3.25 1-4.5 2.5m-3 3c-.5-.5-1-.5-1.5 0l-5 5c-.5.5-.5 1 0 1.5l3 3c.5.5 1 .5 1.5 0l5-5c.5-.5.5-1 0-1.5l-3-3z"/></svg>',
        'desc'  => 'An oncologist is a doctor who treats cancer and provides medical care for a person diagnosed with cancer. Compassionate oncology care.'
    ],
    [
        'id'    => 'pulmonology',
        'name'  => 'Pulmonology',
        'cat'   => 'medical',
        'image' => 'images/graphics-for-homepage/poster-13.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        'desc'  => 'Comprehensive treatments for chronic respiratory illnesses, asthma, lung damage, sleep apnea, and tuberculosis with state-of-the-art PFT testing.'
    ],
    [
        'id'    => 'psychiatry',
        'name'  => 'Psychiatry & Mental Health',
        'cat'   => 'medical',
        'image' => 'images/graphics-for-homepage/poster-9.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 14a4 4 0 1 1 4-4 4 4 0 0 1-4 4z"/></svg>',
        'desc'  => 'Caring psychiatric assessments, clinical treatments for stress, anxiety, and depression. Restoring your peace of mind with complete confidentiality.'
    ],
    [
        'id'    => 'dental',
        'name'  => 'Dental, Oral &amp; Maxillofacial Surgery',
        'cat'   => 'surgical',
        'image' => 'images/graphics-for-homepage/poster-10.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a5 5 0 0 0-5 5c0 4 3 6 5 11 2-5 5-7 5-11a5 5 0 0 0-5-5z"/></svg>',
        'desc'  => 'Premium oral wellness, advanced jaw trauma reconstruction, painless root canal surgeries, and custom orthodontic dental alignments.'
    ],
    [
        'id'    => 'ent',
        'name'  => 'ENT Department',
        'cat'   => 'surgical',
        'image' => 'images/graphics-for-homepage/poster-7.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18h18M12 6v12M12 6a3 3 0 1 1 6 0 3 3 0 0 1-6 0zM12 6a3 3 0 1 0-6 0 3 3 0 0 0 6 0z"/></svg>',
        'desc'  => 'Modern diagnosis and treatment for sinus infections, tonsillitis, hearing disorders, voice box issues, and micro-ear surgical procedures.'
    ],
    [
        'id'    => 'pathology',
        'name'  => 'Advanced Pathology Lab',
        'cat'   => 'diagnostic',
        'image' => 'images/graphics-for-homepage/poster-1.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2v6.5L4.2 17a2 2 0 0 0 1.8 3h12a2 2 0 0 0 1.8-3L15 8.5V2M7 2h10M7.5 13h9"/></svg>',
        'desc'  => 'Diagnostic backbone of VHRC, featuring fully automated analysers, barcoded samples, and verified pathologist reports delivered promptly.'
    ],
    [
        'id'    => 'bloodbank',
        'name'  => 'Life Saving Blood Bank',
        'cat'   => 'diagnostic',
        'image' => 'images/graphics-for-homepage/poster-6.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.7S6 9.3 6 14.2a6 6 0 0 0 12 0C18 9.3 12 2.7 12 2.7z"/><path d="M12 9.5v5M9.5 12h5"/></svg>',
        'desc'  => '24&times;7 life support blood bank, processing whole blood and separate blood components with strict infectious screening guidelines.'
    ],
    [
        'id'    => 'health-checkup',
        'name'  => 'Health Checkup',
        'cat'   => 'diagnostic',
        'image' => 'images/graphics-for-homepage/poster-9.jpeg',
        'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        'desc'  => 'Comprehensive and preventive health packages designed for early detection and lifestyle management tailored for all age groups.'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'; ?>">
    <title>Clinical Departments - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Explore our specialized clinical departments, pediatric intensive care, advanced diagnostics, and trauma surgical centres at Vindhya Hospital Rewa.">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Website Favicon -->
    <?php if (!empty($settings['website_favicon']) && file_exists(__DIR__ . '/../' . $settings['website_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['website_favicon']); ?>?v=<?php echo filemtime(__DIR__ . '/../' . $settings['website_favicon']); ?>">
    <?php else: ?>
        <link rel="icon" href="images/logo.png">
    <?php endif; ?>

    <style>
    /* Departments Page Banner Styles */
    .dept-hero {
        background: var(--gradient-dark);
        padding: 90px 0 70px;
        position: relative;
        overflow: hidden;
        border-bottom: 4px solid var(--clr-accent);
        text-align: center;
    }

    .dept-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 80% 20%, rgba(0, 210, 196, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    .dept-hero-content {
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .dept-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(0, 210, 196, 0.1);
        border: 1px solid rgba(0, 210, 196, 0.2);
        color: var(--clr-accent);
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 8px 18px;
        border-radius: 30px;
        margin-bottom: 25px;
    }

    .dept-hero-title {
        font-family: var(--font-heading);
        font-size: 3rem;
        font-weight: 800;
        color: #FFFFFF;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .dept-hero-title span {
        color: var(--clr-accent);
        background: linear-gradient(135deg, #FFFFFF 30%, var(--clr-accent) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .dept-hero-desc {
        color: rgba(255, 255, 255, 0.85);
        font-size: 1.1rem;
        line-height: 1.7;
    }

    /* Filters and Search Bar Section */
    .controls-section {
        background-color: #FFFFFF;
        border-bottom: 1px solid var(--clr-border);
        position: sticky;
        top: var(--header-height-nav);
        z-index: 100;
        padding: 20px 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .controls-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 10px 20px;
        border-radius: 30px;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--clr-text-main);
        background-color: var(--clr-bg-secondary);
        border: 1px solid var(--clr-border);
        transition: all var(--transition-fast);
    }

    .filter-btn:hover {
        background-color: rgba(0, 210, 196, 0.05);
        border-color: rgba(0, 210, 196, 0.2);
        color: var(--clr-accent-hover);
    }

    .filter-btn.active {
        background: var(--gradient-accent);
        color: #FFFFFF;
        border-color: var(--clr-accent);
        box-shadow: var(--shadow-glow);
    }

    /* Search Bar */
    .search-box-wrap {
        position: relative;
        width: 320px;
        max-width: 100%;
    }

    .search-input {
        width: 100%;
        padding: 12px 18px 12px 45px;
        border-radius: 30px;
        border: 1px solid var(--clr-border);
        background-color: var(--clr-bg-secondary);
        font-family: var(--font-body);
        font-size: 0.9rem;
        color: var(--clr-text-main);
        font-weight: 500;
        transition: all var(--transition-fast);
    }

    .search-input:focus {
        outline: none;
        background-color: #FFFFFF;
        border-color: var(--clr-accent);
        box-shadow: 0 0 10px rgba(0, 210, 196, 0.1);
    }

    .search-icon-svg {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: var(--clr-text-muted);
        pointer-events: none;
        transition: color var(--transition-fast);
    }

    .search-input:focus + .search-icon-svg {
        color: var(--clr-accent);
    }

    /* Grid Section Styles */
    .depts-grid-section {
        padding: 80px 0;
        background-color: var(--clr-bg-secondary);
        min-height: 500px;
    }

    .depts-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 30px;
    }

    /* ── Card: flex column, two clear halves ── */
    .dept-card-wrapper {
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
        animation: fadeInUp 0.5s ease forwards;
        box-shadow: 0 4px 24px rgba(0,0,0,0.14);
        border: 1px solid rgba(0,210,196,0.12);
        cursor: pointer;
    }

    .dept-card-wrapper:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 56px rgba(0,0,0,0.28), 0 0 0 1.5px rgba(0,210,196,0.4);
    }

    /* ── TOP HALF: image background + glass overlay ── */
    .dept-card-top {
        position: relative;
        height: 200px;
        flex-shrink: 0;
        overflow: hidden;
        display: flex;
        align-items: center;       /* vertically center the icon+name */
        justify-content: center;
    }

    /* Image sits behind everything */
    .dept-card-top-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 0;
    }

    .dept-card-wrapper:hover .dept-card-top-img {
        transform: scale(1.06);
    }

    /* Translucent overlay — image stays fully visible */
    .dept-card-glass {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: rgba(15, 60, 160, 0.35);
        transition: background 0.35s ease;
    }

    .dept-card-wrapper:hover .dept-card-glass {
        background: rgba(15, 60, 160, 0.48);
    }

    /* Category badge — top right */
    .dept-card-badge {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 3;
        background: rgba(0, 210, 196, 0.15);
        border: 1px solid rgba(0, 210, 196, 0.5);
        color: #00d2c4;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.3px;
        padding: 4px 11px;
        border-radius: 20px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    /* Icon + Name row — centered in the top half, over the glass */
    .dept-card-header-row {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 0 24px;
        width: 100%;
    }

    .dept-card-icon {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #00d2c4, #0090c8);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: 0 6px 20px rgba(0, 210, 196, 0.4);
        border: 1.5px solid rgba(255,255,255,0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dept-card-wrapper:hover .dept-card-icon {
        transform: scale(1.08);
        box-shadow: 0 8px 28px rgba(0, 210, 196, 0.6);
    }

    .dept-card-icon svg {
        width: 26px;
        height: 26px;
    }

    .dept-card-name {
        font-family: var(--font-heading);
        font-size: 1.12rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.3;
        margin: 0;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        transition: color 0.3s ease;
    }

    .dept-card-wrapper:hover .dept-card-name {
        color: #4af5eb;
    }

    /* ── BOTTOM HALF: solid bg, no glass ── */
    .dept-card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 18px 22px 20px;
        background: #07111e;    /* solid dark background, no glass */
        border-top: 2px solid rgba(0, 210, 196, 0.2);
        transition: border-color 0.3s ease, background 0.3s ease;
    }

    .dept-card-wrapper:hover .dept-card-body {
        border-top-color: rgba(0, 210, 196, 0.5);
        background: #091827;
    }

    /* Divider line */
    .dept-card-divider {
        height: 1px;
        background: linear-gradient(to right, rgba(0,210,196,0.3), rgba(0,210,196,0.05), transparent);
        border: none;
        margin: 2px 0;
    }

    .dept-card-text {
        font-size: 0.87rem;
        line-height: 1.65;
        color: rgba(170, 195, 235, 0.82);
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.3s ease;
    }

    .dept-card-wrapper:hover .dept-card-text {
        color: rgba(200, 220, 255, 0.95);
    }

    /* Actions row */
    .dept-card-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 4px;
    }

    /* View More button */
    .dept-view-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        border: 1px solid rgba(0, 210, 196, 0.35);
        color: #00d2c4;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 8px 15px;
        border-radius: 30px;
        text-decoration: none;
        transition: all 0.3s ease;
        letter-spacing: 0.2px;
    }

    .dept-view-more-btn svg {
        width: 13px;
        height: 13px;
        transition: transform 0.3s ease;
    }

    .dept-view-more-btn:hover {
        background: rgba(0, 210, 196, 0.12);
        border-color: rgba(0, 210, 196, 0.7);
        color: #4af5eb;
    }

    .dept-view-more-btn:hover svg {
        transform: translateX(3px);
    }

    /* Book Now button */
    .dept-card-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #00d2c4, #0090c8);
        color: #fff;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 8px 15px;
        border-radius: 30px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 3px 12px rgba(0, 210, 196, 0.28);
    }

    .dept-card-btn svg {
        width: 13px;
        height: 13px;
        transition: transform 0.3s ease;
    }

    .dept-card-btn:hover {
        box-shadow: 0 6px 20px rgba(0, 210, 196, 0.5);
        transform: translateY(-1px);
    }

    .dept-card-btn:hover svg {
        transform: translateX(2px);
    }

    .no-results-card {
        grid-column: 1 / -1;
        background-color: #FFFFFF;
        border: 1px dashed var(--clr-border);
        border-radius: var(--border-radius-md);
        padding: 60px 40px;
        text-align: center;
        display: none;
    }

    .no-results-card svg {
        width: 60px;
        height: 60px;
        color: var(--clr-text-muted);
        margin-bottom: 20px;
    }

    .no-results-card h3 {
        font-family: var(--font-heading);
        color: var(--clr-brand);
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .no-results-card p {
        color: var(--clr-text-muted);
    }

    /* Keyframes */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsiveness */
    @media (max-width: 991px) {
        .controls-wrapper {
            flex-direction: column;
            align-items: stretch;
            gap: 20px;
        }

        .search-box-wrap {
            width: 100%;
        }

        .depts-grid-container {
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        }
    }

    @media (max-width: 575px) {
        .dept-hero {
            padding: 60px 0 50px;
        }

        .dept-hero-title {
            font-size: 2.2rem;
        }

        .depts-grid-section {
            padding: 40px 0;
        }
    }
    </style>
</head>

<body>

    <?php
    $active_page = 'departments';
    require_once __DIR__ . '/../includes/header.php';
    ?>

    <main>
        
        <!-- Department Banner Header -->
        <section class="dept-hero">
            <div class="container">
                <div class="dept-hero-content">
                    <span class="dept-hero-badge">Vindhya Care Excellence</span>
                    <h1 class="dept-hero-title">Our Specialized <span>Departments</span></h1>
                    <p class="dept-hero-desc">
                        At Vindhya Hospital Rewa, we offer a wide range of medical services designed to provide complete care for every patient. From advanced diagnostics to specialized treatments and compassionate recovery support, our expert team ensures the highest standards of healthcare.
                    </p>
                </div>
            </div>
        </section>

        <!-- Controls Filter Sticky Sub-bar -->
        <section class="controls-section">
            <div class="container">
                <div class="controls-wrapper">
                    <!-- Filter Tabs -->
                    <div class="filter-tabs" id="filterTabs">
                        <button class="filter-btn active" data-filter="all">All Specialties</button>
                        <button class="filter-btn" data-filter="surgical">Surgical Specialties</button>
                        <button class="filter-btn" data-filter="medical">Medical Specialties</button>
                        <button class="filter-btn" data-filter="critical">Critical &amp; Life Support</button>
                        <button class="filter-btn" data-filter="diagnostic">Diagnostics &amp; Blood Bank</button>
                    </div>

                    <!-- Search Bar -->
                    <div class="search-box-wrap">
                        <input type="text" id="deptSearch" class="search-input" placeholder="Search departments...">
                        <svg class="search-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Department Cards Grid Section -->
        <section class="depts-grid-section">
            <div class="container">
                <div class="depts-grid-container" id="deptsGrid">
                    
                    <?php foreach ($depts as $dept): ?>
                        <div class="dept-card-wrapper" id="<?php echo $dept['id']; ?>" data-category="<?php echo $dept['cat']; ?>" data-name="<?php echo strtolower($dept['name']); ?>">

                            <!-- TOP HALF: image + glass overlay + icon left + name right -->
                            <div class="dept-card-top">
                                <!-- Background image -->
                                <img src="<?php echo htmlspecialchars($dept['image']); ?>" alt="<?php echo htmlspecialchars($dept['name']); ?>" class="dept-card-top-img">
                                <!-- Glass overlay over image -->
                                <div class="dept-card-glass"></div>
                                <!-- Category badge top-right -->
                                <span class="dept-card-badge"><?php
                                    $cat_labels = ['surgical'=>'Surgical','medical'=>'Medical','critical'=>'Critical Care','diagnostic'=>'Diagnostics'];
                                    echo $cat_labels[$dept['cat']] ?? ucfirst($dept['cat']);
                                ?></span>
                                <!-- Icon (left) + Name (right), centered in top half -->
                                <div class="dept-card-header-row">
                                    <div class="dept-card-icon"><?php echo $dept['icon']; ?></div>
                                    <h3 class="dept-card-name"><?php echo htmlspecialchars($dept['name']); ?></h3>
                                </div>
                            </div>

                            <!-- BOTTOM HALF: solid bg, text + buttons, no glass -->
                            <div class="dept-card-body">
                                <hr class="dept-card-divider">
                                <p class="dept-card-text"><?php echo htmlspecialchars($dept['desc']); ?></p>
                                <div class="dept-card-actions">
                                    <a href="departments/<?php echo $dept['id']; ?>.php" class="dept-view-more-btn">
                                        <span>View More</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                    </a>
                                    <a href="index.php#appointment" class="dept-card-btn">
                                        <span>Book Now</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                                    </a>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>

                    <!-- No Results Placeholder -->
                    <div class="no-results-card" id="noResults">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        <h3>No Departments Found</h3>
                        <p>We couldn't find any department matching your keyword. Please try another search term.</p>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <!-- Interactive Live Filtering JS -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const searchInput = document.getElementById('deptSearch');
        const cards = document.querySelectorAll('.dept-card-wrapper');
        const noResults = document.getElementById('noResults');

        let activeCategory = 'all';
        let searchQuery = '';

        // Handle category tab switching
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeCategory = this.getAttribute('data-filter');
                filterDepartments();
            });
        });

        // Handle real-time search typing
        searchInput.addEventListener('input', function() {
            searchQuery = this.value.toLowerCase().trim();
            filterDepartments();
        });

        // Combined filter function
        function filterDepartments() {
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                const cardName = card.getAttribute('data-name');

                const matchesCat = (activeCategory === 'all' || cardCat === activeCategory);
                const matchesSearch = (searchQuery === '' || cardName.includes(searchQuery));

                if (matchesCat && matchesSearch) {
                    card.style.display = 'flex';
                    // Trigger reflow for fade-in animation
                    card.style.animation = 'none';
                    card.offsetHeight; // trigger reflow
                    card.style.animation = 'fadeInUp 0.4s ease forwards';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Toggle no results message
            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
    });
    </script>
</body>

</html>
