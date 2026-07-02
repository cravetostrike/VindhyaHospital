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

// Fetch all registered doctors
try {
    $doctors_stmt = $pdo->query("SELECT * FROM doctors ORDER BY id ASC");
    $doctors = $doctors_stmt->fetchAll();
} catch (PDOException $e) {
    $doctors = [];
}

// Get unique specialties for the Filter By dropdown
$specialties = [];
foreach ($doctors as $doc) {
    if (!empty($doc['specialty'])) {
        $specialties[] = trim($doc['specialty']);
    }
}
$specialties = array_unique($specialties);
sort($specialties);

// Helper function to return specialty icons
function getSpecialtyIcon($specialty)
{
    $spec = strtolower($specialty);
    if (strpos($spec, 'cardio') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>';
    } else if (strpos($spec, 'neuro') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>';
    } else if (strpos($spec, 'ortho') !== false || strpos($spec, 'अस्थि') !== false || strpos($spec, 'स्पाइन') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><rect x="8" y="2" width="8" height="20" rx="2"/><path d="M20 14h-4m-8 0H4"/></svg>';
    } else if (strpos($spec, 'pediatric') !== false || strpos($spec, 'child') !== false || strpos($spec, 'शिशु') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v2m0 0v2m0-2h-2m2 0h2"/></svg>';
    } else if (strpos($spec, 'surg') !== false || strpos($spec, 'operation') !== false || strpos($spec, 'सर्जन') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><path d="M12 2v20M2 12h20"/></svg>';
    } else if (strpos($spec, 'स्त्री') !== false || strpos($spec, 'prashuti') !== false || strpos($spec, 'gyne') !== false || strpos($spec, 'obst') !== false) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="badge-svg" style="width:13px; height:13px; margin-right:5px; display:inline-block; vertical-align:middle;"><circle cx="12" cy="10" r="4"/><path d="M12 14v7M9 18h6"/></svg>';
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'; ?>">
    <title>Our Doctors - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="Meet our expert medical consultants and specialist doctors dedicated to your care and recovery at Vindhya Hospital, Rewa.">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Website Favicon -->
    <?php if (!empty($settings['website_favicon']) && file_exists(__DIR__ . '/../' . $settings['website_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['website_favicon']); ?>?v=<?php echo filemtime(__DIR__ . '/../' . $settings['website_favicon']); ?>">
    <?php endif; ?>

    <style>
    /* Doctor Hero Section */
    .doctor-hero {
        background: var(--gradient-dark);
        padding: 90px 0 70px;
        position: relative;
        overflow: hidden;
        border-bottom: 4px solid var(--clr-accent);
        text-align: center;
    }

    .doctor-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 80% 20%, rgba(0, 210, 196, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    .doctor-hero-content {
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .doctor-hero-badge {
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

    .doctor-hero-title {
        font-family: var(--font-heading);
        font-size: 3rem;
        font-weight: 800;
        color: var(--clr-bg-primary);
        line-height: 1.15;
        margin-bottom: 15px;
    }

    .doctor-hero-title span {
        background: var(--gradient-accent);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .doctor-hero-subtitle {
        color: var(--clr-text-muted);
        font-size: 1.1rem;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Controls sub-bar (sticky filter & search) */
    .controls-section {
        background-color: var(--clr-bg-primary);
        padding: 20px 0;
        border-bottom: 1px solid var(--clr-border);
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: var(--shadow-sm);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .controls-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
    }

    .filter-dropdown-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .filter-label {
        font-family: var(--font-heading);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--clr-brand);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .select-container {
        position: relative;
        display: inline-block;
    }

    .filter-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: transparent;
        border: none;
        border-bottom: 2.5px solid var(--clr-brand);
        padding: 6px 36px 6px 12px;
        font-family: var(--font-body);
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--clr-brand);
        cursor: pointer;
        outline: none;
        transition: border-color var(--transition-fast);
    }

    .filter-select:focus {
        border-color: var(--clr-accent);
    }

    .select-arrow {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--clr-brand);
        display: flex;
        align-items: center;
    }

    .select-arrow svg {
        width: 16px;
        height: 16px;
    }

    .search-box-wrap {
        position: relative;
        width: 340px;
    }

    .search-input {
        width: 100%;
        padding: 11px 44px 11px 20px;
        border: 1px solid var(--clr-border);
        border-radius: var(--border-radius-lg);
        background-color: var(--clr-bg-secondary);
        font-family: var(--font-body);
        font-size: 0.9rem;
        color: var(--clr-text-main);
        outline: none;
        transition: border-color var(--transition-fast), background-color var(--transition-fast), box-shadow var(--transition-fast);
    }

    .search-input:focus {
        border-color: var(--clr-accent);
        background-color: var(--clr-bg-primary);
        box-shadow: 0 0 10px rgba(0, 210, 196, 0.1);
    }

    .search-icon-svg {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: var(--clr-text-muted);
        pointer-events: none;
    }

    /* Doctors Grid Section */
    .doctors-grid-section {
        padding: 70px 0;
        background-color: var(--clr-bg-secondary);
        min-height: 500px;
    }

    .doctors-grid-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 35px;
        max-width: 800px;
        margin: 0 auto;
    }

    @media (min-width: 1200px) {
        .doctors-grid-container {
            grid-template-columns: 1fr 1fr;
            max-width: 1400px;
        }
    }

    /* Doctor Card Layout */
    .doctor-card-wrapper {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--clr-border);
        padding: 30px;
        transition: transform var(--transition-smooth), box-shadow var(--transition-smooth), border-color var(--transition-smooth);
        display: flex;
        flex-direction: column;
    }

    .doctor-card-wrapper:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: var(--clr-accent);
    }

    .doctor-card-grid {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 30px;
    }

    @media (max-width: 768px) {
        .doctor-card-grid {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .doctor-card-left {
            align-items: center;
        }
        .doctor-card-meta-row {
            justify-content: center;
        }
        .doctor-card-actions {
            align-self: center;
            width: 100%;
            flex-direction: column;
            gap: 10px;
        }
        .btn-card-cta,
        .btn-card-secondary {
            width: 100%;
        }
    }

    .doctor-card-left {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .doctor-card-img-wrap {
        width: 200px;
        height: 200px;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        border: 1px solid var(--clr-border);
        box-shadow: var(--shadow-sm);
        background-color: #f8fafc;
    }

    .doctor-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
        transition: transform var(--transition-smooth);
    }

    .doctor-card-wrapper:hover .doctor-card-img {
        transform: scale(1.03);
    }

    .doctor-card-socials {
        display: flex;
        gap: 14px;
    }

    .doctor-card-socials a {
        color: var(--clr-brand);
        transition: color var(--transition-fast), transform var(--transition-fast);
    }

    .doctor-card-socials a:hover {
        color: var(--clr-accent);
        transform: translateY(-2px);
    }

    .doctor-card-socials svg {
        width: 20px;
        height: 20px;
    }

    .doctor-card-right {
        display: flex;
        flex-direction: column;
    }

    .doctor-card-specialty {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--clr-accent);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
    }

    .doctor-card-specialty svg {
        width: 14px;
        height: 14px;
        color: var(--clr-accent);
    }

    .doctor-card-name {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--clr-brand);
        margin-bottom: 4px;
    }

    .doctor-card-qual {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--clr-text-muted);
        margin-bottom: 12px;
    }

    .card-divider {
        border: none;
        border-top: 1px solid var(--clr-border);
        margin-bottom: 12px;
    }

    .doctor-card-meta-row {
        display: flex;
        gap: 30px;
        margin-bottom: 15px;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: var(--clr-text-muted);
        letter-spacing: 1px;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .meta-item strong {
        font-size: 1.05rem;
        color: var(--clr-brand);
        font-weight: 700;
    }

    .doctor-card-bio {
        font-size: 0.88rem;
        line-height: 1.6;
        color: var(--clr-text-main);
        margin-bottom: 20px;
    }

    .doctor-card-actions {
        display: flex;
        gap: 15px;
        margin-top: auto;
        align-self: flex-start;
    }

    .btn-card-cta,
    .btn-card-secondary {
        display: inline-block;
        padding: 10px 24px;
        font-size: 0.9rem;
        font-weight: 700;
        font-family: var(--font-heading);
        border-radius: 50px;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
    }

    .btn-card-cta {
        background: var(--gradient-accent);
        border: none;
        color: var(--clr-text-light) !important;
        box-shadow: 0 4px 10px rgba(0, 210, 196, 0.2);
    }

    .btn-card-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 210, 196, 0.35);
        color: #ffffff;
    }

    .btn-card-secondary {
        background: transparent;
        border: 2px solid var(--clr-brand);
        color: var(--clr-brand) !important;
    }

    .btn-card-secondary:hover {
        background: var(--clr-brand);
        color: var(--clr-text-light) !important;
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    /* No Results styling */
    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 20px;
        background-color: var(--clr-bg-primary);
        border: 1px dashed var(--clr-border);
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-sm);
        max-width: 500px;
        margin: 0 auto;
    }

    .no-results-icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .no-results h3 {
        font-family: var(--font-heading);
        font-size: 1.4rem;
        color: var(--clr-brand);
        margin-bottom: 8px;
    }

    .no-results p {
        color: var(--clr-text-muted);
        font-size: 0.95rem;
    }

    /* Glassmorphic Doctor Modal */
    .doctor-modal-overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .doctor-modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .doctor-modal-card {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-lg);
        width: 100%;
        max-width: 680px;
        position: relative;
        overflow: hidden;
        transform: translateY(30px);
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 35px;
    }

    .doctor-modal-overlay.active .doctor-modal-card {
        transform: translateY(0);
    }

    .doctor-modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 2rem;
        line-height: 1;
        color: var(--clr-text-muted);
        cursor: pointer;
        transition: color var(--transition-fast), transform var(--transition-fast);
        z-index: 10;
    }

    .doctor-modal-close:hover {
        color: var(--clr-brand);
        transform: scale(1.1);
    }

    .doctor-modal-grid {
        display: grid;
        grid-template-columns: 220px 1fr;
        gap: 30px;
    }

    .doctor-modal-left {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .doctor-modal-img-wrap {
        width: 220px;
        height: 220px;
        border-radius: var(--border-radius-md);
        overflow: hidden;
        border: 1px solid var(--clr-border);
        box-shadow: var(--shadow-sm);
        background-color: #f8fafc;
    }

    .doctor-modal-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
    }

    .doctor-modal-socials {
        display: flex;
        gap: 14px;
    }

    .doctor-modal-socials a {
        color: var(--clr-brand);
        transition: color var(--transition-fast), transform var(--transition-fast);
    }

    .doctor-modal-socials a:hover {
        color: var(--clr-accent);
        transform: translateY(-2px);
    }

    .doctor-modal-socials svg {
        width: 20px;
        height: 20px;
    }

    .doctor-modal-right {
        display: flex;
        flex-direction: column;
    }

    .doctor-modal-name {
        font-family: var(--font-heading);
        font-size: 1.7rem;
        font-weight: 800;
        color: var(--clr-brand);
        margin-bottom: 4px;
    }

    .doctor-modal-qual {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--clr-text-muted);
        margin-bottom: 15px;
    }

    .modal-divider {
        border: none;
        border-top: 1px solid var(--clr-border);
        margin-bottom: 15px;
    }

    .doctor-modal-meta-row {
        display: flex;
        gap: 30px;
        margin-bottom: 20px;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: var(--clr-text-muted);
        letter-spacing: 1px;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .meta-item strong {
        font-size: 1.05rem;
        color: var(--clr-brand);
        font-weight: 700;
    }

    .doctor-modal-bio {
        font-size: 0.9rem;
        line-height: 1.6;
        color: var(--clr-text-main);
        margin-bottom: 25px;
    }

    .btn-modal-cta {
        background: var(--gradient-accent);
        border: none;
        color: var(--clr-text-light);
        padding: 12px 28px;
        font-size: 0.95rem;
        font-weight: 700;
        font-family: var(--font-heading);
        border-radius: var(--border-radius-sm);
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        align-self: flex-start;
    }

    .btn-modal-cta:hover {
        transform: scale(1.02);
        box-shadow: var(--shadow-glow);
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

    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease forwards;
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .controls-wrapper {
            flex-direction: column;
            align-items: stretch;
            gap: 20px;
        }

        .search-box-wrap {
            width: 100%;
        }
    }

    @media (max-width: 575px) {
        .doctor-hero {
            padding: 60px 0 50px;
        }

        .doctor-hero-title {
            font-size: 2.2rem;
        }

        .doctor-modal-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .doctor-modal-left {
            align-items: center;
        }

        .btn-modal-cta {
            align-self: stretch;
        }
    }
    </style>
</head>

<body>

    <?php 
    $active_page = 'doctor'; 
    require_once __DIR__ . '/../includes/header.php'; 
    ?>

    <main>
        <!-- Doctor Hero Banner -->
        <section class="doctor-hero">
            <div class="container">
                <div class="doctor-hero-content animate-fade-in-up">
                    <span class="doctor-hero-badge">Our Specialists</span>
                    <h1 class="doctor-hero-title">Meet Our Expert <br><span>Medical Consultants</span></h1>
                    <p class="doctor-hero-subtitle">Providing affordable, holistic, individualized, and high-quality healthcare under the guidance of leading clinical professionals.</p>
                </div>
            </div>
        </section>

        <!-- Sticky Controls sub-bar -->
        <section class="controls-section">
            <div class="container">
                <div class="controls-wrapper">
                    <!-- Filter Select -->
                    <div class="filter-dropdown-wrap">
                        <label for="specialtyFilter" class="filter-label">Filter By</label>
                        <div class="select-container">
                            <select id="specialtyFilter" class="filter-select">
                                <option value="all">All Specialties</option>
                                <?php foreach ($specialties as $spec): ?>
                                    <option value="<?php echo htmlspecialchars($spec); ?>"><?php echo htmlspecialchars($spec); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="select-arrow">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="search-box-wrap">
                        <input type="text" id="doctorSearch" class="search-input" placeholder="Search doctor by name...">
                        <svg class="search-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Doctors Grid Section -->
        <section class="doctors-grid-section">
            <div class="container">
                <div class="doctors-grid-container" id="doctorsGrid">
                    <?php if (empty($doctors)): ?>
                        <div class="no-results" id="noResults">
                            <div class="no-results-icon">🔍</div>
                            <h3>No Doctors Registered</h3>
                            <p>We couldn't find any doctor profiles registered in the system right now.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($doctors as $doc): ?>
                            <!-- Doctor Card -->
                            <div class="doctor-card-wrapper" 
                                 data-specialty="<?php echo htmlspecialchars($doc['specialty'] ?? ''); ?>" 
                                 data-name="<?php echo strtolower(htmlspecialchars($doc['name'] ?? '')); ?>">
                                <div class="doctor-card-grid">
                                    <!-- Left Column -->
                                    <div class="doctor-card-left">
                                        <div class="doctor-card-img-wrap">
                                            <img src="<?php echo htmlspecialchars($doc['image_path'] ?: 'images/doctor_default.png'); ?>" 
                                                 alt="<?php echo htmlspecialchars($doc['name']); ?>" 
                                                 class="doctor-card-img" 
                                                 onerror="this.src='images/doctor_default.png';">
                                        </div>
                                        <div class="doctor-card-socials">
                                            <?php if (!empty($doc['social_fb']) && $doc['social_fb'] !== '#'): ?>
                                                <a href="<?php echo htmlspecialchars($doc['social_fb']); ?>" target="_blank" aria-label="Facebook">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($doc['social_tw']) && $doc['social_tw'] !== '#'): ?>
                                                <a href="<?php echo htmlspecialchars($doc['social_tw']); ?>" target="_blank" aria-label="Twitter">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($doc['social_ig']) && $doc['social_ig'] !== '#'): ?>
                                                <a href="<?php echo htmlspecialchars($doc['social_ig']); ?>" target="_blank" aria-label="Instagram">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($doc['social_in']) && $doc['social_in'] !== '#'): ?>
                                                <a href="<?php echo htmlspecialchars($doc['social_in']); ?>" target="_blank" aria-label="LinkedIn">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Right Column -->
                                    <div class="doctor-card-right">
                                        <span class="doctor-card-specialty">
                                            <?php echo getSpecialtyIcon($doc['specialty']); ?>
                                            <?php echo htmlspecialchars($doc['specialty'] ?: 'Specialist'); ?>
                                        </span>
                                        <h2 class="doctor-card-name"><?php echo htmlspecialchars($doc['name']); ?></h2>
                                        <span class="doctor-card-qual"><?php echo htmlspecialchars($doc['qualifications']); ?></span>
                                        <hr class="card-divider">
                                        
                                        <div class="doctor-card-meta-row">
                                            <div class="meta-item">
                                                <span class="meta-label">Experience</span>
                                                <strong><?php echo htmlspecialchars($doc['experience'] ?: '0'); ?> Years</strong>
                                            </div>
                                        </div>
                                        
                                        <div class="doctor-card-bio">
                                            <p><?php echo htmlspecialchars($doc['name']); ?> is a highly-regarded <?php echo htmlspecialchars($doc['specialty'] ?: 'Specialist'); ?> at Vindhya Hospital & Research Centre. With <?php echo htmlspecialchars($doc['experience'] ?: '0'); ?> years of dedicated medical experience, they are committed to providing personalized patient consultation, advanced clinical treatments, and ethical medical care.</p>
                                        </div>
                                        
                                        <div class="doctor-card-actions">
                                            <a href="doctor/<?php echo getDoctorSlug($doc['name']); ?>.php" class="btn-card-secondary">View Profile</a>
                                            <a href="index.php#appointment" class="btn-card-cta">Book Appointment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Live Filtering "No results found" container -->
                <div class="no-results" id="noResults" style="display: none;">
                    <div class="no-results-icon">🔍</div>
                    <h3>No Doctors Found</h3>
                    <p>We couldn't find any specialist matching your search. Try resetting the filter criteria.</p>
                </div>
            </div>
        </section>
    </main>



    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <!-- Interactive Live Filtering & Modal JS -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Real-time Filter & Search ---
        const specialtyFilter = document.getElementById('specialtyFilter');
        const doctorSearch = document.getElementById('doctorSearch');
        const docCards = document.querySelectorAll('.doctor-card-wrapper');
        const noResults = document.getElementById('noResults');

        let selectedSpecialty = 'all';
        let searchQuery = '';

        const filterDoctors = () => {
            let visibleCount = 0;

            docCards.forEach(card => {
                const cardSpec = card.getAttribute('data-specialty');
                const cardName = card.getAttribute('data-name');

                const matchesSpec = (selectedSpecialty === 'all' || cardSpec === selectedSpecialty);
                const matchesSearch = (searchQuery === '' || cardName.includes(searchQuery));

                if (matchesSpec && matchesSearch) {
                    card.style.display = 'flex';
                    card.style.animation = 'none';
                    card.offsetHeight; // trigger reflow
                    card.style.animation = 'fadeInUp 0.4s ease forwards';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        };

        if (specialtyFilter) {
            specialtyFilter.addEventListener('change', function() {
                selectedSpecialty = this.value;
                filterDoctors();
            });
        }

        if (doctorSearch) {
            doctorSearch.addEventListener('input', function() {
                searchQuery = this.value.toLowerCase().trim();
                filterDoctors();
            });
        }
    });
    </script>
</body>

</html>
