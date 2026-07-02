<?php
// Reusable Frontend Header Component
// Expects:
//   $active_page (string, e.g., 'home', 'about', 'departments', 'treatments', 'doctor')
//   $settings (array containing homepage settings)
?>
    <!-- Custom CSS for Navigation Emergency Button -->
    <style>
        .nav-emergency-item {
            display: flex;
            align-items: center;
            margin-left: 15px;
        }
        .nav-emergency-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff !important;
            border: 1px solid #ffffff;
            color: #ef4444 !important;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
        .nav-emergency-btn:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.45);
        }
        .emergency-pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
        }
        .nav-emergency-btn:hover .emergency-pulse-dot {
            background-color: #ffffff;
        }
        .emergency-pulse-dot::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid #ef4444;
            opacity: 0;
            animation: emergencyPulse 1.8s infinite;
        }
        .nav-emergency-btn:hover .emergency-pulse-dot::after {
            border-color: #ffffff;
        }
        @keyframes emergencyPulse {
            0% {
                transform: scale(0.6);
                opacity: 1;
            }
            100% {
                transform: scale(1.6);
                opacity: 0;
            }
        }
        @media (min-width: 992px) {
            .nav-menu {
                gap: 1rem !important; /* Prevent links from wrapping by narrowing gaps */
            }
        }
        @media (max-width: 991px) {
            .nav-emergency-item {
                margin: 15px 0 0 0;
                justify-content: center;
                width: 100%;
            }
            .nav-emergency-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <!-- Infinite Scrolling Alert Ticker -->
    <div class="ticker-wrap">
        <div class="ticker">
            <div class="ticker__group">
                <span>👉 Your Health, Our Priority &bull; 24&times;7 Emergency Services &bull; VHRC Rewa: Committed to Medical Excellence &bull; Emergency Hotline: +91 9589899826</span>
                <span>👉 Your Health, Our Priority &bull; 24&times;7 Emergency Services &bull; VHRC Rewa: Committed to Medical Excellence &bull; Emergency Hotline: +91 9589899826</span>
            </div>
            <div class="ticker__group" aria-hidden="true">
                <span>👉 Your Health, Our Priority &bull; 24&times;7 Emergency Services &bull; VHRC Rewa: Committed to Medical Excellence &bull; Emergency Hotline: +91 9589899826</span>
                <span>👉 Your Health, Our Priority &bull; 24&times;7 Emergency Services &bull; VHRC Rewa: Committed to Medical Excellence &bull; Emergency Hotline: +91 9589899826</span>
            </div>
        </div>
    </div>

    <!-- Site Header -->
    <header class="site-header">

        <!-- 1. Top Contact & Social Bar -->
        <div class="top-bar">
            <div class="container">
                <div class="top-info">
                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <span><?php echo htmlspecialchars($settings['header_hours'] ?? 'Mon - Sun 0900 - 2100'); ?></span>
                    </div>
                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $settings['header_phone'] ?? '+919589899826')); ?>"><?php echo htmlspecialchars($settings['header_phone'] ?? '+91 9589899826'); ?></a>
                    </div>
                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail">
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                        <a href="mailto:<?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?>"><?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?></a>
                    </div>
                </div>

                <div class="top-socials">
                    <a href="<?php echo htmlspecialchars($settings['social_fb'] ?? '#'); ?>" class="social-link" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['social_in'] ?? '#'); ?>" class="social-link" aria-label="LinkedIn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                            <rect x="2" y="9" width="4" height="12"></rect>
                            <circle cx="4" cy="4" r="2"></circle>
                        </svg>
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['social_pin'] ?? '#'); ?>" class="social-link" aria-label="Pinterest">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.966 1.406-5.966s-.359-.72-.359-1.781c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.621 0 11.988-5.367 11.988-11.988C24.005 5.368 18.638 0 12.017 0z" />
                        </svg>
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['social_tw'] ?? '#'); ?>" class="social-link" aria-label="Twitter">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                        </svg>
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['social_yt'] ?? '#'); ?>" class="social-link" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                        </svg>
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['social_ig'] ?? '#'); ?>" class="social-link" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Middle Brand & Badges Bar -->
        <div class="brand-bar">
            <div class="container">
                <div class="header-badges">
                    <div class="header-badge">
                        <div class="badge-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>
                        <div class="badge-content">
                            <span class="badge-label"><?php echo htmlspecialchars($settings['accred1_title'] ?? 'Trusted By'); ?></span>
                            <span class="badge-value"><?php echo htmlspecialchars($settings['accred1_desc'] ?? '120,000+ People'); ?></span>
                        </div>
                    </div>
                    <div class="header-badge">
                        <div class="badge-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="7" />
                                <path d="M8.21 13.89 7 23l5-3 5 3-1.21-9.12" />
                            </svg>
                        </div>
                        <div class="badge-content">
                            <span class="badge-label"><?php echo htmlspecialchars($settings['accred2_title'] ?? 'Best Hospital'); ?></span>
                            <span class="badge-value"><?php echo htmlspecialchars($settings['accred2_desc'] ?? 'Rewa (M.P.)'); ?></span>
                        </div>
                    </div>
                    <div class="header-badge">
                        <div class="badge-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                            </svg>
                        </div>
                        <div class="badge-content">
                            <span class="badge-label">Open 24 Hours</span>
                            <span class="badge-value">Services & Facilities</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Bottom Main Navigation Bar (Sticky) -->
        <nav class="main-nav-bar">
            <div class="container">
                <div class="nav-overlay"></div>

                <!-- Logo Container with Diagonal Background wrapper -->
                <div class="nav-logo-bg-wrap">
                    <a href="index.php" class="logo-container" aria-label="VHRC Home">
                        <img src="images/logo.png" alt="VHRC Logo" class="site-logo">
                    </a>
                </div>

                <div class="nav-menu-wrapper">
                    <ul class="nav-menu">
                        <li class="<?php echo ($active_page === 'home') ? 'active' : ''; ?>"><a href="index.php" class="nav-link">Home</a></li>
                        <li class="nav-item-dropdown <?php echo ($active_page === 'about') ? 'active' : ''; ?>">
                            <a href="about/index.php" class="nav-link dropdown-trigger">
                                About Us
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </a>
                            <div class="dropdown-menu">
                                <a href="about/mission-values.php">Our Mission &amp; Values</a>
                                <a href="about/policies-procedures.php">Policies &amp; Procedures</a>
                                <a href="about/consultation-care.php">Consultation &amp; Advanced Care</a>
                                <a href="about/admission-prep.php">Preparing For Admission</a>
                                <a href="about/quality-safety.php">Quality Care &amp; Patient Safety</a>
                                <a href="about/diversity-specialty.php">Diversity is our Specialty</a>
                            </div>
                        </li>
                        <li class="nav-item-dropdown <?php echo ($active_page === 'departments') ? 'active' : ''; ?>">
                            <a href="departments/" class="nav-link dropdown-trigger">
                                Department
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </a>
                            <div class="dropdown-menu">
                                <a href="departments/urology.php">Urology &amp; Urosurgery</a>
                                <a href="departments/gynecology.php">Obstetrics, Gynecology &amp; Infertility</a>
                                <a href="departments/ivf.php">IVF (In Vitro Fertilization)</a>
                                <a href="departments/laparoscopy.php">General &amp; Laparoscopic Surgery</a>
                                <a href="departments/orthopedics.php">Orthopedics &amp; Adv. Trauma Centre</a>
                                <a href="departments/medicine.php">General Medicine</a>
                                <a href="departments/gastroenterology.php">Gastroenterology</a>
                                <a href="departments/plastic-surgery.php">Plastic Surgery &amp; Burn Unit</a>
                                <a href="departments/pediatrics.php">Peadiatrics and Neonatology</a>
                                <a href="departments/icu-dialysis.php">ICU &amp; Dialysis Department</a>
                                <a href="departments/spine-surgery.php">Spine Surgery</a>
                                <a href="departments/neurosurgery.php">Neurosurgery</a>
                                <a href="departments/anaesthesiology.php">Anaesthesiology</a>
                                <a href="departments/oncology.php">Oncology Department</a>
                                <a href="departments/pulmonology.php">Pulmonology</a>
                                <a href="departments/psychiatry.php">Psychiatry &amp; Mental Health</a>
                                <a href="departments/dental.php">Dental, Oral &amp; Maxillofacial Surgery</a>
                                <a href="departments/ent.php">ENT Department</a>
                                <a href="departments/pathology.php">Advanced Pathology Lab</a>
                                <a href="departments/bloodbank.php">Blood Bank</a>
                                <a href="departments/health-checkup.php">Health Checkup</a>
                            </div>
                        </li>
                        <li class="<?php echo ($active_page === 'treatments') ? 'active' : ''; ?>"><a href="treatments/" class="nav-link">Treatment</a></li>
                        <li class="<?php echo ($active_page === 'doctor') ? 'active' : ''; ?>"><a href="doctor/" class="nav-link">Our Doctor</a></li>
                        <li class="nav-item-dropdown <?php echo ($active_page === 'gallery') ? 'active' : ''; ?>">
                            <a href="gallery/" class="nav-link dropdown-trigger">
                                Gallery
                                <svg class="dropdown-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </a>
                            <div class="dropdown-menu">
                                <a href="gallery/press-and-news.php">News &amp; Press</a>
                                <a href="gallery/video-gallary.php">Video Gallery</a>
                            </div>
                        </li>
                        <li><a href="#" class="nav-link">Blog</a></li>
                        <li><a href="index.php#contact" class="nav-link">Contact</a></li>
                        <li class="nav-emergency-item">
                            <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $settings['header_phone'] ?? '+919589899826')); ?>" class="nav-emergency-btn">
                                <span class="emergency-pulse-dot"></span>
                                <span>24*7: <?php echo htmlspecialchars($settings['header_phone'] ?? '+91 9589899826'); ?></span>
                            </a>
                        </li>
                    </ul>

                    <a href="index.php#appointment" class="btn-cta">
                        <span>Book An Appointment</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Hamburger menu button (mobile only) -->
                <button class="mobile-toggle" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </nav>
    </header>
