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
// Fetch doctor details from database matching this file's slug
$current_slug = basename($_SERVER['SCRIPT_NAME'], '.php');
try {
    if (!function_exists('getDoctorSlugLocal')) {
        function getDoctorSlugLocal($name) {
            // Remove Dr. / Dr / DR / DR. prefix
            $name = preg_replace('/^dr\.\s*|^dr\s+/i', '', trim($name));
            // Convert to lowercase
            $name = strtolower($name);
            // Replace non-alphanumeric characters with hyphens
            $name = preg_replace('/[^a-z0-9]+/i', '-', $name);
            return trim($name, '-');
        }
    }
    $doc_stmt = $pdo->query("SELECT * FROM doctors");
    $db_doctors = $doc_stmt->fetchAll();
    $doctor = null;
    foreach ($db_doctors as $d) {
        if (getDoctorSlugLocal($d['name']) === $current_slug) {
            $doctor = $d;
            break;
        }
    }
} catch (PDOException $e) {
    $doctor = null;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Tariq Kamaal - Vindhya Hospital &amp; Research Centre</title>
    <meta name="description" content="View details and schedule an appointment with Dr. Tariq Kamaal at Vindhya Hospital &amp; Research Centre, Rewa.">
    <base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'; ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

        <!-- Website Favicon -->
    <?php if (!empty($settings['website_favicon']) && file_exists(__DIR__ . '/../' . $settings['website_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['website_favicon']); ?>?v=<?php echo filemtime(__DIR__ . '/../' . $settings['website_favicon']); ?>">
    <?php endif; ?>
</head>

<body>
    <?php
    $active_page = 'doctors';
    require_once __DIR__ . '/../includes/header.php';
    ?>

    <!-- PREMIUM HERO BANNER -->
    <section class="hero-premium-section">
        <div class="hero-premium-container">

            <!-- Left Side: Image Holder with Neon Edge glow -->
            <div class="hero-image-side">
                <div class="neon-image-wrapper">
                    <!-- Pointing to Dr. Tariq Kamaal's image asset -->
                    <img src="<?php echo !empty($doctor['image_path']) ? htmlspecialchars($doctor['image_path']) : 'images/dr-tariq-kamaal.jpg'; ?>" alt="Dr. Tariq Kamaal" class="hero-doctor-img">
                    <span class="status-pill-badge">
                        <span class="pulse-dot"></span> Available Today
                    </span>
                </div>
            </div>

            <!-- Right Side: Primary Meta Descriptions -->
            <div class="hero-content-side">
                <span class="hero-dept-label">MBBS, D.C.H - PEDIATRICIAN &amp; NEONATOLOGIST</span>
                <h1 class="hero-doctor-name">Dr. Tariq Kamaal</h1>
                <p class="hero-subtitle">Pediatrician &amp; Neonatologist</p>

                <!-- Degree Pill Blocks -->
                <div class="degree-pill-row">
                    <span class="degree-pill">MBBS</span>
                    <span class="degree-pill">D.C.H (Diploma in Child Health)</span>
                </div>

                <!-- Glassmorphism Stat Row Grid -->
                <div class="hero-stats-grid">
                    <div class="stat-glass-card">
                        <div class="stat-icon-box amber-bg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h4>10+ Years</h4>
                            <p>Experience</p>
                        </div>
                    </div>

                    <div class="stat-glass-card">
                        <div class="stat-icon-box blue-bg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h4>Thousands</h4>
                            <p>Children Served</p>
                        </div>
                    </div>

                    <div class="stat-glass-card">
                        <div class="stat-icon-box green-bg">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h4>Verified</h4>
                            <p>Child Specialist</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CONTENT BODY LAYOUT SECTION -->
    <main class="main-profile-content">
        <div class="content-layout-container">

            <!-- LEFT MAIN PROFILE COLUMN -->
            <div class="profile-left-column">

                <!-- Tabbed Box Header Element -->
                <div class="profile-card-block tabbed-block">
                    <div class="tab-nav-header">
                        <button class="tab-btn active" data-tab="tab-about">About</button>
                        <button class="tab-btn" data-tab="tab-education">Education</button>
                        <button class="tab-btn" data-tab="tab-experience">Experience</button>
                    </div>

                    <!-- Tab 1: About (Active by default) -->
                    <div class="tab-content-body tab-pane active" id="tab-about">
                        <p>Dr. Tariq Kamaal is a highly skilled and experienced Pediatrician &amp; Neonatologist at Vindhya Hospital &amp; Research Centre, Rewa (M.P.), with over 10 years of practice in child healthcare. He is widely recognized as one of the trusted Child Specialists in Rewa, offering expert care for infants, children, and adolescents.</p>
                        <p>He provides treatment for a wide range of pediatric conditions including acute and chronic illnesses, developmental disorders, thyroid problems, and behavioral issues. Along with medical treatment, he focuses on child counseling, preventive care, and overall growth and development.</p>
                    </div>

                    <!-- Tab 2: Education (Hidden by default) -->
                    <div class="tab-content-body tab-pane" id="tab-education">
                        <p><strong>MBBS:</strong> [University Name, Year]</p>
                        <p><strong>D.C.H (Diploma in Child Health):</strong> [University Name, Year]</p>
                        <p><em>*Education timelines and institutional records are currently being updated.</em></p>
                    </div>

                    <!-- Tab 3: Experience (Hidden by default) -->
                    <div class="tab-content-body tab-pane" id="tab-experience">
                        <p><strong>Current:</strong> Consultant Pediatrician &amp; Neonatologist at Vindhya Hospital &amp; Research Centre, Rewa (M.P.).</p>
                        <p>Over 10+ years of active clinical experience in Pediatrics &amp; Neonatology. Well-regarded across the region for delivering personalized, child-friendly care with a family-centered treatment approach.</p>
                    </div>
                </div>

                <!-- Specializations Block -->
                <div class="profile-card-block">
                    <h2 class="section-title-accent green-line">Areas of Expertise</h2>
                    <div class="expertise-grid">

                                                <div class="expertise-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="expertise-icon"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Comprehensive pediatric disease management</span>
                        </div>
                                                <div class="expertise-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="expertise-icon"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Neonatal care and newborn health monitoring</span>
                        </div>
                                                <div class="expertise-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="expertise-icon"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Acute diarrhea &amp; infectious disease treatment</span>
                        </div>
                                                <div class="expertise-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="expertise-icon"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Bed-wetting and urinary problems in children</span>
                        </div>
                                                <div class="expertise-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="expertise-icon"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Child growth &amp; developmental disorder treatment</span>
                        </div>
                                                <div class="expertise-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="expertise-icon"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Thyroid and hormonal disorder management in children</span>
                        </div>
                                                <div class="expertise-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="expertise-icon"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Vaccinations &amp; preventive pediatric healthcare</span>
                        </div>
                                                <div class="expertise-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="expertise-icon"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Diploma in Child Health</span>
                        </div>
                    </div>
                </div>

                <!-- Skills Percentage Progress Bars -->
                <div class="profile-card-block">
                    <h2 class="section-title-accent green-line">My Skills &amp; Clinical Strengths</h2>
                    <div class="skills-wrapper-grid">

                        <div class="skill-progress-row">
                            <div class="skill-label-info">
                                <h5>PEDIATRIC &amp; NEONATAL CARE</h5>
                                <span>97%</span>
                            </div>
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" style="width: 97%;"></div>
                            </div>
                        </div>

                        <div class="skill-progress-row">
                            <div class="skill-label-info">
                                <h5>MANAGEMENT OF ACUTE &amp; CHRONIC ILLNESSES</h5>
                                <span>95%</span>
                            </div>
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" style="width: 95%;"></div>
                            </div>
                        </div>

                        <div class="skill-progress-row">
                            <div class="skill-label-info">
                                <h5>CHILD DEVELOPMENT &amp; BEHAVIORAL COUNSELING</h5>
                                <span>94%</span>
                            </div>
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" style="width: 94%;"></div>
                            </div>
                        </div>

                        <div class="skill-progress-row">
                            <div class="skill-label-info">
                                <h5>PREVENTIVE &amp; NUTRITIONAL PEDIATRICS</h5>
                                <span>93%</span>
                            </div>
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" style="width: 93%;"></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- RIGHT STICKY INFO SIDEBAR COLUMN -->
            <aside class="profile-right-sidebar">

                <!-- Quick Info Panel Card -->
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title purple-accent">Quick Info</h3>

                    <div class="info-list-stack">
                        <div class="info-item-row">
                            <div class="info-icon bg-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </div>
                            <div class="info-text">
                                <span class="label">Available</span>
                                <p class="value">Monday - Saturday</p>
                            </div>
                        </div>

                        <div class="info-item-row">
                            <div class="info-icon bg-purple">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </div>
                            <div class="info-text">
                                <span class="label">Visiting Hours</span>
                                <p class="value">On Call</p>
                            </div>
                        </div>

                        <div class="info-item-row">
                            <div class="info-icon bg-pink">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </div>
                            <div class="info-text">
                                <span class="label">Location</span>
                                <p class="value font-small">Near Old Bus Stand, Bansh Ghat, Rewa (MP) 486001</p>
                            </div>
                        </div>

                        <div class="info-item-row">
                            <div class="info-icon bg-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <div class="info-text">
                                <span class="label">Gender</span>
                                <p class="value">Male</p>
                            </div>
                        </div>
                    </div>

                    <!-- Consultation Fee Highlight Block -->
                    <div class="consultation-fee-box">
                        <span class="fee-title">Consultation Fee</span>
                        <h2 class="fee-amount">₹500 <span>Per session</span></h2>
                    </div>
                </div>

                <!-- Contact Panel Card -->
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title teal-accent">Contact Information</h3>
                    <div class="contact-links-stack">
                        <a href="tel:+9107662406000" class="contact-link-row blue-tint">
                            <div class="contact-icon color-blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1 22 16.92z" />
                                </svg>
                            </div>
                            <span class="contact-text">+91 076624 06000</span>
                        </a>

                        <a href="mailto:vhrcrewa@gmail.com" class="contact-link-row pink-tint">
                            <div class="contact-icon color-pink">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </div>
                            <span class="contact-text">vhrcrewa@gmail.com</span>
                        </a>
                    </div>
                </div>

                <!-- Languages Spoken Card -->
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title orange-accent">Languages Spoken</h3>
                    <div class="language-badge-row">
                        <span class="lang-badge">English</span>
                        <span class="lang-badge">Hindi</span>
                    </div>
                </div>

            </aside>

        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));

                    button.classList.add('active');

                    const targetTabId = button.getAttribute('data-tab');
                    document.getElementById(targetTabId).classList.add('active');
                });
            });
        });
    </script>
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>