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

// Grouped Treatments and Sub-services Array
$grouped_services = [
    'laboratory-services' => [
        'id'          => 'laboratory-services',
        'name'        => 'Laboratory Services',
        'image'       => 'images/doctor_consultation.png',
        'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        'desc'        => 'Our Laboratory Services department provides a comprehensive range of clinical laboratory tests essential for patient health assessment, diagnosis, and treatment monitoring.',
        'subs'        => [
            [
                'id'    => 'clinical-biochemistry',
                'name'  => 'Clinical Biochemistry',
                'image' => 'images/doctor_consultation.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                'desc'  => 'Quantitative analysis of chemical and biochemical constituents in bodily fluids to diagnose and monitor metabolic disorders, organ functions, and system wellness.'
            ],
            [
                'id'    => 'clinical-microbiology-serology',
                'name'  => 'Clinical Micro-biology and Serology',
                'image' => 'images/doctor_consultation.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m10 15 5-5M15 15l-5-5"/></svg>',
                'desc'  => 'Advanced identification of infectious pathogens (viruses, bacteria, fungi) and serological testing for antibody levels to assist in targeted antimicrobial treatments.'
            ],
            [
                'id'    => 'clinical-pathology',
                'name'  => 'Clinical Pathology',
                'image' => 'images/doctor_consultation.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2v6.5L4.2 17a2 2 0 0 0 1.8 3h12a2 2 0 0 0 1.8-3L15 8.5V2M7 2h10M7.5 13h9"/></svg>',
                'desc'  => 'Complete physical, chemical, and microscopic examination of biological samples including urine, stool, and semen for diagnostic analysis.'
            ],
            [
                'id'    => 'haematology',
                'name'  => 'Haematology',
                'image' => 'images/doctor_consultation.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 9.5v5M9.5 12h5"/></svg>',
                'desc'  => 'Comprehensive evaluation of blood components (RBCs, WBCs, platelets), blood-forming tissues, coagulation profiles, and hematological disorders.'
            ],
            [
                'id'    => 'blood-centre',
                'name'  => 'Blood Centre',
                'image' => 'images/doctor_consultation.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.7S6 9.3 6 14.2a6 6 0 0 0 12 0C18 9.3 12 2.7 12 2.7z"/></svg>',
                'desc'  => 'Fully licensed 24/7 blood transfusion service providing whole blood and isolated blood components with rigorous infectious screening and matching.'
            ]
        ]
    ],
    'diagnostic-services' => [
        'id'          => 'diagnostic-services',
        'name'        => 'Diagnostic Services',
        'image'       => 'images/patient_safety.png',
        'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>',
        'desc'        => 'The Diagnostic Services department offers state-of-the-art diagnostic imaging and physiological measurements using high-precision non-invasive testing equipment.',
        'subs'        => [
            [
                'id'    => 'ultrasound',
                'name'  => 'Ultrasound',
                'image' => 'images/patient_safety.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>',
                'desc'  => 'High-resolution abdominal, pelvic, obstetrics/gynecology sonographies, and color Doppler scans for blood flow evaluation.'
            ],
            [
                'id'    => 'ct-scan',
                'name'  => 'C-T scan',
                'image' => 'images/patient_safety.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="12" y1="3" x2="12" y2="21"/></svg>',
                'desc'  => 'Advanced multi-slice Computed Tomography scanning for cross-sectional views of bone, tissue, and vasculature.'
            ],
            [
                'id'    => 'uro-flow',
                'name'  => 'Uro Flow',
                'image' => 'images/patient_safety.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5"/></svg>',
                'desc'  => 'Diagnostic measurements of urinary flow rate and bladder volume output to evaluate bladder and urethral function.'
            ],
            [
                'id'    => 'echo',
                'name'  => 'ECHO',
                'image' => 'images/patient_safety.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
                'desc'  => 'Echocardiography cardiac sonograms mapping heart valve movement, chamber size, and myocardial contraction safety.'
            ],
            [
                'id'    => 'x-ray',
                'name'  => 'X-Ray',
                'image' => 'images/patient_safety.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>',
                'desc'  => 'High-definition digital skeletal and chest radiography to assist in immediate trauma evaluation and orthopedic diagnosis.'
            ],
            [
                'id'    => 'ecg',
                'name'  => 'ECG',
                'image' => 'images/patient_safety.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l2 5 4-10 2 5h6"/></svg>',
                'desc'  => 'Electrocardiogram testing mapping cardiac electrical paths to check for heart wall thickness, rhythm blocks, and ischemia.'
            ],
            [
                'id'    => 'tmt',
                'name'  => 'T.M.T',
                'image' => 'images/patient_safety.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/></svg>',
                'desc'  => 'Treadmill Test cardiovascular stress monitoring under controlled treadmill exercise conditions.'
            ]
        ]
    ],
    'clinical-support-services' => [
        'id'          => 'clinical-support-services',
        'name'        => 'Clinical Support Services',
        'image'       => 'images/hospital_admission.png',
        'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
        'desc'        => 'Clinical Support Services at Vindhya Hospital provide integrated care to enhance recovery, physical rehabilitation, nutrition counseling, and emergency support.',
        'subs'        => [
            [
                'id'    => 'physiotherapy',
                'name'  => 'Physiotherapy',
                'image' => 'images/hospital_admission.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                'desc'  => 'Physical therapy, rehabilitation, electrotherapy, muscle strengthening, and joint mobilization programs for recovering patients.'
            ],
            [
                'id'    => 'dietetics',
                'name'  => 'Dietetics',
                'image' => 'images/hospital_admission.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/></svg>',
                'desc'  => 'Clinical nutritional counseling, customized therapeutic meal charts, and expert dietary planning for in-patients and outpatient health.'
            ],
            [
                'id'    => 'ambulance',
                'name'  => 'Ambulance',
                'image' => 'images/hospital_admission.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
                'desc'  => '24/7 emergency medical transportation services equipped with oxygen cylinders, cardiac monitors, and emergency medical kits.'
            ],
            [
                'id'    => 'oxygen-plant',
                'name'  => 'Oxygen Plant',
                'image' => 'images/hospital_admission.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7"/></svg>',
                'desc'  => 'In-house high-capacity Pressure Swing Adsorption (PSA) oxygen generation plant feeding medical oxygen directly to central pipelines.'
            ]
        ]
    ],
    'radiology-services' => [
        'id'          => 'radiology-services',
        'name'        => 'Radiology Services',
        'image'       => 'images/policies_procedures.png',
        'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>',
        'desc'        => 'Radiology Services provide advanced skeletal, chest, and mobile digital imaging to visualize internal body structures and support surgical teams.',
        'subs'        => [
            [
                'id'    => 'xray-fix-mobile-carm',
                'name'  => 'X-RAY Fix Mobile C-Arm',
                'image' => 'images/policies_procedures.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>',
                'desc'  => 'High-frequency mobile C-Arm fluoroscopic imaging systems for real-time surgical guidance and bedside patient diagnostics.'
            ]
        ]
    ],
    'health-services' => [
        'id'          => 'health-services',
        'name'        => 'Health Services',
        'image'       => 'images/diversity_team.png',
        'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
        'desc'        => 'We coordinate multiple corporate panels, private health insurance TPAs, and government wellness programs to ensure cashless and subsidized treatments.',
        'subs'        => [
            [
                'id'    => 'mediclaim',
                'name'  => 'Mediclaim',
                'image' => 'images/diversity_team.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
                'desc'  => 'Corporate panels, health insurance TPA coordination, and cashless treatment services managed under private mediclaim cards.'
            ],
            [
                'id'    => 'ayushman',
                'name'  => 'Ayushman',
                'image' => 'images/diversity_team.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><path d="M8.21 13.89 7 23l5-3"/></svg>',
                'desc'  => 'Subsidized and free hospitalization services coordinated under the Ayushman Bharat PM-JAY national health scheme.'
            ],
            [
                'id'    => 'echs',
                'name'  => 'ECHS',
                'image' => 'images/diversity_team.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3"/></svg>',
                'desc'  => 'Cashless and premium medical facilities support for ex-servicemen, veterans, and military dependents under ECHS panel.'
            ],
            [
                'id'    => 'sghs',
                'name'  => 'SGHS',
                'image' => 'images/diversity_team.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>',
                'desc'  => 'Government employees and retired pensioners health schemes packages coordination and cashless therapy services.'
            ]
        ]
    ],
    'cssd' => [
        'id'          => 'cssd',
        'name'        => 'CSSD',
        'image'       => 'images/about_facade.png',
        'icon'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'desc'        => 'The Central Sterile Services Department (CSSD) oversees rigorous sterilization of all instruments, linen, and consumables for operating theaters and ICUs.',
        'subs'        => [
            [
                'id'    => 'fully-automatic-autoclave-machine',
                'name'  => 'Fully Automatic Autoclave Machine with All Indicator',
                'image' => 'images/about_facade.png',
                'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                'desc'  => 'Fully automatic sterilizing autoclave steam systems utilizing chemical, biological, and physical parameters indicators to guarantee sanitization.'
            ]
        ]
    ]
];?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'; ?>">
    <title>Clinical Treatments &amp; Services - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Explore our specialized clinical treatment services, advanced laboratory, radiology, health insurance schemes, and sterilisation CSSD units at Vindhya Hospital.">
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
    
    /* Grouped Treatments CSS */
    .treatment-group {
        margin-bottom: 70px;
        padding-bottom: 50px;
        border-bottom: 2px dashed var(--clr-border);
    }
    .treatment-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .group-main-service-container {
        display: flex;
        justify-content: center;
        margin-bottom: 25px;
    }
    .group-subservices-divider {
        text-align: center;
        position: relative;
        margin: 60px 0 40px;
    }
    /* Sleek fading gradient lines on left and right */
    .group-subservices-divider::before {
        content: '';
        position: absolute;
        left: 5%;
        top: 50%;
        width: 90%;
        height: 2px;
        background: linear-gradient(to right, rgba(0, 143, 132, 0) 0%, rgba(0, 143, 132, 0.4) 50%, rgba(0, 143, 132, 0) 100%);
        z-index: 1;
    }
    .subservices-divider-title {
        display: inline-block;
        position: relative;
        z-index: 2;
        background: #ffffff;
        padding: 10px 30px;
        border-radius: 50px;
        border: 2px solid rgba(0, 143, 132, 0.15);
        box-shadow: 0 10px 30px rgba(0, 143, 132, 0.06);
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--clr-brand);
        transition: all var(--transition-fast);
    }
    .subservices-divider-title span {
        background: linear-gradient(135deg, var(--clr-brand) 30%, var(--clr-accent) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }
    /* Little glowing green dots at the sides */
    .subservices-divider-title span::before,
    .subservices-divider-title span::after {
        content: '';
        display: inline-block;
    }</style>
</head>

<body>

    <?php
    $active_page = 'treatments';
    require_once __DIR__ . '/../includes/header.php';
    ?>

    <main>
        
        <!-- Department Banner Header -->
        <!-- Treatment Banner Header -->
        <section class="dept-hero">
            <div class="container">
                <div class="dept-hero-content">
                    <span class="dept-hero-badge">Vindhya Medical Services</span>
                    <h1 class="dept-hero-title">Our Clinical <span>Treatments</span></h1>
                    <p class="dept-hero-desc">
                        Explore our comprehensive range of clinical departments, medical diagnostics, laboratory facilities, and support systems designed to deliver safe, accurate, and advanced care at Vindhya Hospital.
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
                        <button class="filter-btn active" data-filter="all">All Services</button>
                        <button class="filter-btn" data-filter="laboratory-services">Laboratory</button>
                        <button class="filter-btn" data-filter="diagnostic-services">Diagnostics</button>
                        <button class="filter-btn" data-filter="clinical-support-services">Clinical Support</button>
                        <button class="filter-btn" data-filter="radiology-services">Radiology</button>
                        <button class="filter-btn" data-filter="health-services">Health Services</button>
                        <button class="filter-btn" data-filter="cssd">CSSD</button>
                    </div>

                    <!-- Search Bar -->
                    <div class="search-box-wrap">
                        <input type="text" id="deptSearch" class="search-input" placeholder="Search treatments, diagnostics, or sub-services...">
                        <svg class="search-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

                        <!-- Main Services Section (At Top) -->
        <section class="depts-grid-section core-services-section" style="padding-bottom: 40px;">
            <div class="container">
                <h2 class="section-title-premium" id="mainServicesTitle" style="text-align: center; margin-bottom: 35px; color: var(--clr-brand); font-family: var(--font-heading); font-weight: 700; font-size: 1.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Clinical Services</h2>
                
                <div class="depts-grid-container" id="mainServicesGrid">
                    <?php foreach ($grouped_services as $group): 
                        // Assign header gradient colors based on group ID
                        $theme_map = [
                            'laboratory-services' => 'theme-pink',
                            'diagnostic-services' => 'theme-orange',
                            'clinical-support-services' => 'theme-green',
                            'radiology-services' => 'theme-blue',
                            'health-services' => 'theme-indigo',
                            'cssd' => 'theme-red'
                        ];
                        $theme_class = $theme_map[$group['id']] ?? 'theme-teal';

                        // Define dynamic bottom badges with SVG icons
                        $badge_map = [
                            'laboratory-services' => ['icon' => '<svg viewBox="0 0 24 24"><path d="M4.5 16.5c-1.5 1.25-2.5 3-2.5 5h20c0-2-1-3.75-2.5-5M12 2v10M9 8h6"/></svg>', 'label' => '24/7 Labs'],
                            'diagnostic-services' => ['icon' => '<svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>', 'label' => 'High Precision'],
                            'clinical-support-services' => ['icon' => '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', 'label' => 'Integrated Care'],
                            'radiology-services' => ['icon' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m8 12 4 4 4-4"/></svg>', 'label' => 'Low Radiation'],
                            'health-services' => ['icon' => '<svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>', 'label' => 'TPA Cashless'],
                            'cssd' => ['icon' => '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>', 'label' => '100% Sterile']
                        ];
                        $badge_info = $badge_map[$group['id']] ?? ['icon' => '<svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', 'label' => 'Clinical Care'];
                    ?>
                        <div class="treatment-card-wrapper <?php echo $theme_class; ?>" id="main-<?php echo $group['id']; ?>" data-category="<?php echo $group['id']; ?>" data-name="<?php echo strtolower($group['name']); ?>">
                            <div class="treatment-card-header" style="background-image: linear-gradient(135deg, <?php 
                                $color_map = [
                                    'theme-pink' => 'rgba(217, 70, 239, 0.85) 0%, rgba(236, 72, 153, 0.85) 100%',
                                    'theme-orange' => 'rgba(249, 115, 22, 0.85) 0%, rgba(234, 88, 12, 0.85) 100%',
                                    'theme-green' => 'rgba(16, 185, 129, 0.85) 0%, rgba(5, 150, 105, 0.85) 100%',
                                    'theme-blue' => 'rgba(59, 130, 246, 0.85) 0%, rgba(37, 99, 235, 0.85) 100%',
                                    'theme-indigo' => 'rgba(99, 102, 241, 0.85) 0%, rgba(79, 70, 229, 0.85) 100%',
                                    'theme-red' => 'rgba(244, 63, 94, 0.85) 0%, rgba(225, 29, 72, 0.85) 100%',
                                    'theme-teal' => 'rgba(13, 148, 136, 0.85) 0%, rgba(15, 118, 110, 0.85) 100%'
                                ];
                                echo $color_map[$theme_class] ?? $color_map['theme-teal'];
                            ?>), url('<?php echo htmlspecialchars($group['image']); ?>'); background-size: cover; background-position: center;">
                                <!-- Outline icon box top left -->
                                <div class="treatment-card-icon-box">
                                    <?php echo $group['icon']; ?>
                                </div>
                                <!-- Watermark icon top right -->
                                <div class="treatment-card-watermark">
                                    <?php echo $group['icon']; ?>
                                </div>
                                <h3 class="treatment-card-title"><?php echo htmlspecialchars($group['name']); ?></h3>
                                <span class="treatment-card-subtitle">Vindhya Care Services</span>
                            </div>
                            <div class="treatment-card-body">
                                <p class="treatment-card-desc"><?php echo htmlspecialchars($group['desc']); ?></p>
                                
                                <?php if (!empty($group['subs'])): ?>
                                    <div class="treatment-keys-section">
                                        <span class="treatment-keys-label">Key Services</span>
                                        <div class="treatment-keys-list">
                                            <?php 
                                            $cnt = 0;
                                            foreach ($group['subs'] as $sub) {
                                                if ($cnt >= 3) break;
                                                echo '<span class="treatment-key-pill">' . htmlspecialchars($sub['name']) . '</span>';
                                                $cnt++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="treatment-card-footer">
                                    <div class="treatment-badge-left">
                                        <span class="treatment-badge-left-icon"><?php echo $badge_info['icon']; ?></span>
                                        <span><?php echo $badge_info['label']; ?></span>
                                    </div>
                                    <a href="treatments/<?php echo $group['id']; ?>.php" class="treatment-learn-link">
                                        <span>Learn More</span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Sub Services Section (At Bottom, Grouped & Separated by Headings) -->
        <section class="depts-grid-section sub-services-section" style="background-color: #f8fafc; border-top: 1px solid var(--clr-border); padding: 60px 0;">
            <div class="container">
                <div id="subServicesWrapper">
                    
                    <?php foreach ($grouped_services as $group): ?>
                        <?php if (!empty($group['subs'])): ?>
                            <div class="sub-services-group" data-category="<?php echo $group['id']; ?>" style="margin-bottom: 50px;">
                                
                                <!-- Divided by Main Service Name heading -->
                                <div class="group-subservices-divider">
                                    <h3 class="subservices-divider-title"><span><?php echo htmlspecialchars($group['name']); ?></span></h3>
                                </div>

                                <!-- Sub-services Grid -->
                                <div class="depts-grid-container subservices-grid">
                                    <?php foreach ($group['subs'] as $sub): 
                                        // Inherit theme from parent major service
                                        $theme_class = $theme_map[$group['id']] ?? 'theme-teal';

                                        // Load sub-service data file to fetch features/why_choose
                                        $sub_pills = [];
                                        $sub_file = __DIR__ . '/data/' . $sub['id'] . '.php';
                                        if (file_exists($sub_file)) {
                                            $sub_data = require $sub_file;
                                            if (!empty($sub_data['features'])) {
                                                foreach ($sub_data['features'] as $feat) {
                                                    $clean_feat = preg_replace('/\s*\(.*\)/s', '', $feat);
                                                    $clean_feat = preg_replace('/\s*[\.\-].*/s', '', $clean_feat);
                                                    $clean_feat = trim($clean_feat);
                                                    if (strlen($clean_feat) > 28) {
                                                        $clean_feat = substr($clean_feat, 0, 25) . '...';
                                                    }
                                                    $sub_pills[] = $clean_feat;
                                                }
                                            }
                                        }
                                        $sub_pills = array_slice($sub_pills, 0, 3);
                                    ?>
                                        <div class="treatment-card-wrapper <?php echo $theme_class; ?>" id="<?php echo $sub['id']; ?>" data-category="<?php echo $group['id']; ?>" data-name="<?php echo strtolower($sub['name']); ?>">
                                            <div class="treatment-card-header" style="background-image: linear-gradient(135deg, <?php 
                                                $color_map = [
                                                    'theme-pink' => 'rgba(217, 70, 239, 0.85) 0%, rgba(236, 72, 153, 0.85) 100%',
                                                    'theme-orange' => 'rgba(249, 115, 22, 0.85) 0%, rgba(234, 88, 12, 0.85) 100%',
                                                    'theme-green' => 'rgba(16, 185, 129, 0.85) 0%, rgba(5, 150, 105, 0.85) 100%',
                                                    'theme-blue' => 'rgba(59, 130, 246, 0.85) 0%, rgba(37, 99, 235, 0.85) 100%',
                                                    'theme-indigo' => 'rgba(99, 102, 241, 0.85) 0%, rgba(79, 70, 229, 0.85) 100%',
                                                    'theme-red' => 'rgba(244, 63, 94, 0.85) 0%, rgba(225, 29, 72, 0.85) 100%',
                                                    'theme-teal' => 'rgba(13, 148, 136, 0.85) 0%, rgba(15, 118, 110, 0.85) 100%'
                                                ];
                                                echo $color_map[$theme_class] ?? $color_map['theme-teal'];
                                            ?>), url('<?php echo htmlspecialchars($sub['image']); ?>'); background-size: cover; background-position: center;">
                                                <!-- Outline icon box top left -->
                                                <div class="treatment-card-icon-box">
                                                    <?php echo $sub['icon']; ?>
                                                </div>
                                                <!-- Watermark icon top right -->
                                                <div class="treatment-card-watermark">
                                                    <?php echo $sub['icon']; ?>
                                                </div>
                                                <h3 class="treatment-card-title" style="font-size: 1.15rem;"><?php echo htmlspecialchars($sub['name']); ?></h3>
                                                <span class="treatment-card-subtitle"><?php echo htmlspecialchars($group['name']); ?></span>
                                            </div>
                                            <div class="treatment-card-body">
                                                <p class="treatment-card-desc"><?php echo htmlspecialchars($sub['desc']); ?></p>
                                                
                                                <?php if (!empty($sub_pills)): ?>
                                                    <div class="treatment-keys-section">
                                                        <span class="treatment-keys-label">Key Features</span>
                                                        <div class="treatment-keys-list">
                                                            <?php foreach ($sub_pills as $pill): ?>
                                                                <span class="treatment-key-pill"><?php echo htmlspecialchars($pill); ?></span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="treatment-card-footer">
                                                    <div class="treatment-badge-left">
                                                        <span class="treatment-badge-left-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
                                                        <span>Sub Service</span>
                                                    </div>
                                                    <a href="treatments/<?php echo $sub['id']; ?>.php" class="treatment-learn-link">
                                                        <span>Learn More</span>
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- No Results Placeholder -->
                    <div class="no-results-card" id="noResults" style="display: none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        <h3>No Treatments Found</h3>
                        <p>We couldn't find any treatment or service matching your keyword. Please try another search term.</p>
                    </div>

                </div>
            </div>
        </section></main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const searchInput = document.getElementById('deptSearch');
        const noResults = document.getElementById('noResults');

        let activeCategory = 'all';
        let searchQuery = '';

        // Handle category tab switching
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeCategory = this.getAttribute('data-filter');
                filterTreatments();
            });
        });

        // Handle real-time search typing
        searchInput.addEventListener('input', function() {
            searchQuery = this.value.toLowerCase().trim();
            filterTreatments();
        });

        // Combined filter function
        function filterTreatments() {
            let visibleMainCards = 0;
            let visibleSubGroups = 0;

            // 1. Filter Main Cards
            const mainCards = document.querySelectorAll('.core-services-section .main-card');
            mainCards.forEach(card => {
                const cardCat = card.getAttribute('data-category') || '';
                const cardName = card.getAttribute('data-name') || '';

                const matchesCat = (activeCategory === 'all' || cardCat === activeCategory);
                const matchesSearch = (searchQuery === '' || cardName.includes(searchQuery));

                if (matchesCat && matchesSearch) {
                    card.style.display = 'flex';
                    visibleMainCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Toggle Main Section title
            const mainServicesTitle = document.getElementById('mainServicesTitle');
            const coreServicesSection = document.querySelector('.core-services-section');
            if (visibleMainCards === 0) {
                mainServicesTitle.style.display = 'none';
                if (activeCategory !== 'all') {
                    coreServicesSection.style.display = 'none';
                }
            } else {
                mainServicesTitle.style.display = 'block';
                coreServicesSection.style.display = 'block';
            }

            // 2. Filter Sub-services Groups
            const subGroups = document.querySelectorAll('.sub-services-group');
            subGroups.forEach(group => {
                const groupCat = group.getAttribute('data-category') || '';
                const matchesCat = (activeCategory === 'all' || groupCat === activeCategory);

                if (!matchesCat) {
                    group.style.display = 'none';
                    return;
                }

                // Filter cards inside the group
                let groupHasMatch = false;
                const subCards = group.querySelectorAll('.sub-card');

                subCards.forEach(card => {
                    const cardName = card.getAttribute('data-name') || '';
                    const matchesSearch = (searchQuery === '' || cardName.includes(searchQuery));

                    if (matchesSearch) {
                        card.style.display = 'flex';
                        groupHasMatch = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (groupHasMatch) {
                    group.style.display = 'block';
                    visibleSubGroups++;
                } else {
                    group.style.display = 'none';
                }
            });

            // Toggle sub-services section background visibility
            const subServicesSection = document.querySelector('.sub-services-section');
            if (visibleSubGroups === 0) {
                subServicesSection.style.display = 'none';
            } else {
                subServicesSection.style.display = 'block';
            }

            // 3. Toggle no results message
            if (visibleMainCards === 0 && visibleSubGroups === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
    });
    </script>
</body>

</html>
