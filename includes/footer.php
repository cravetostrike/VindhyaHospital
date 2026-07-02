<?php
// Reusable Frontend Footer Component
// Expects:
//   $settings (array containing homepage settings)
?>
    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <!-- 1. Brand Profile & Mission Column -->
            <div class="footer-column brand-info">
                <a href="index.php" class="footer-logo-wrap" aria-label="VHRC Home">
                    <img src="images/logo.png" alt="VHRC Logo" class="footer-logo">
                </a>
                <p class="address-text">
                    Vindhya Hospital &amp; Research Centre (VHRC) Rewa is a premier 100-bedded multi-specialty institution offering patient-centered clinical care and 24x7 emergency medical trauma services.
                </p>
                <div class="contact-details">
                    <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $settings['header_phone'] ?? '+919589899826')); ?>" class="contact-line">
                        <div class="mini-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <span><?php echo htmlspecialchars($settings['header_phone'] ?? '+91 9589899826'); ?></span>
                    </a>
                    <a href="mailto:<?php echo htmlspecialchars($settings['header_email'] ?? 'vhrcrewa@gmail.com'); ?>" class="contact-line">
                        <div class="mini-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </div>
                        <span>vhrcrewa@gmail.com</span>
                    </a>
                </div>
            </div>

            <!-- 2. Quick Links Column -->
            <div class="footer-column links-nav">
                <h3>Quick Links</h3>
                <div class="footer-title-line"></div>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about/index.php">About Us</a></li>
                    <li><a href="departments/">Departments</a></li>
                    <li><a href="doctor/">Our Doctors</a></li>
                    <li><a href="index.php#gallery">Photo Gallery</a></li>
                    <li><a href="index.php#contact">Get In Touch</a></li>
                </ul>
            </div>

            <!-- 3. Key Specialties Column -->
            <div class="footer-column links-nav">
                <h3>Our Specialties</h3>
                <div class="footer-title-line"></div>
                <ul>
                    <li><a href="department/urology">Urology &amp; Urosurgery</a></li>
                    <li><a href="department/gynecology">Gynecology &amp; Obstetrics</a></li>
                    <li><a href="department/orthopedics">Orthopedics &amp; Trauma Centre</a></li>
                    <li><a href="department/laparoscopy">General &amp; Laparoscopic Surgery</a></li>
                    <li><a href="department/icu-dialysis">ICU &amp; Advanced Dialysis</a></li>
                    <li><a href="department/pediatrics">NICU &amp; Pediatrics</a></li>
                </ul>
            </div>

            <!-- 4. Clinical Working Hours Column -->
            <div class="footer-column hospital-hours">
                <h3>OPD Working Hours</h3>
                <div class="footer-title-line"></div>
                <div class="hours-list">
                    <div class="hours-row">
                        <span class="day">
                            <svg class="clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
                            Monday - Friday
                        </span>
                        <span class="time teal-highlight">08:00 - 20:00</span>
                    </div>
                    <div class="hours-row">
                        <span class="day">
                            <svg class="clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
                            Saturday
                        </span>
                        <span class="time">09:00 - 18:00</span>
                    </div>
                    <div class="hours-row">
                        <span class="day">
                            <svg class="clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 
                            Sunday
                        </span>
                        <span class="time">09:00 - 18:00</span>
                    </div>
                </div>
                <div class="emergency-footer-card">
                    <span class="live-pulse-dot"></span>
                    <span class="card-title">Emergency : 24 Hours</span>
                </div>
            </div>
        </div>

        <!-- Copyright bar -->
        <div class="footer-bottom-bar">
            <div class="bottom-bar-container">
                <p>Copyright &copy; 2026 <strong>Vindhya Hospital Rewa</strong> | Designed by <a href="#" class="agency-credit">Rainbow Shine Infotech</a></p>
            </div>
        </div>
    </footer>

    <!-- Custom JS Scripts -->
    <script src="js/main.js"></script>
