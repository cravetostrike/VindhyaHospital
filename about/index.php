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
    <title>About Us - Vindhya Hospital & Research Centre</title>
    <meta name="description" content="Learn more about Vindhya Hospital & Research Centre (VHRC). Serving Rewa with advanced clinical care, expert specialists, and 24/7 emergency services.">
    <base href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/'; ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Website Favicon -->
    <?php if (!empty($settings['website_favicon']) && file_exists(__DIR__ . '/../' . $settings['website_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['website_favicon']); ?>?v=<?php echo filemtime(__DIR__ . '/../' . $settings['website_favicon']); ?>">
    <?php else: ?>
        <link rel="icon" href="images/logo.png">
    <?php endif; ?>
</head>

<body>

    <?php
    $active_page = 'about';
    include __DIR__ . '/../includes/header.php';
    ?>

    <main>
        <!-- 1. Hero Banner Section -->
        <section class="about-hero-section" style="background-image: url('images/hero_banner.png');">
            <div class="about-hero-overlay"></div>
            <div class="container">
                <div class="about-hero-content">
                    <span class="about-hero-tag">👉 About Our Institution</span>
                    <h1 class="about-hero-title">Dedicated to Your Health &amp; Well-being</h1>
                    <p class="about-hero-desc">Vindhya Hospital &amp; Research Centre is Rewa's leading healthcare provider, delivering compassionate clinical excellence.</p>
                </div>
            </div>
        </section>

        <!-- 2. About details section -->
        <section class="about-details-section">
            <div class="container">
                <div class="about-grid">
                    <div class="about-image-wrap">
                        <img src="images/about_facade.png" alt="Vindhya Hospital Facade" class="about-facade-img">
                        <div class="image-accent-block"></div>
                    </div>
                    <div class="about-content-wrap">
                        <span class="section-badge">About Hospital</span>
                        <h2 class="about-section-title">Vindhya Hospital &amp; Research Centre</h2>
                        <p class="about-text">
                            Vindhya Hospital and Research Centre is the first 100 bedded super speciality hospital of the entire Vindhya Region in Rewa, M.P. which is catering and fulfilling the advanced health care needs of the people in the region. The hospital is situated in the heart of the Rewa city and is easily accessible by all means of transportation to the people of Rewa, Satna, Sidhi, Singroli, Umariya, Anuppur, Katni, Panna, Shahdol and the townships in their vicinity.
                        </p>
                        <p class="about-text">
                            We firmly believe in providing affordable, holistic, individualized and quality healthcare to the patients of all strata of society. Keeping “Your Health, Our Mission” in mind Dr. Vishal Mishra, renowned urologist and Dr. Alok Pratap Singh, seasoned anaesthetist envisaged this dream project which was joined supported by Shri Narendra Singh Baghel, Dr. B.P. Mishra, Dr. Nileshwar Sharma and Shri Narendra Sharma.
                        </p>
                        <p class="about-text">
                            At VHRC, the fulltime inhouse team of expert doctors and highly trained and qualified paramedical and nursing staff are committed to provide best in class treatment to the patients and hospitality to their attendants and relatives under one roof.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Stats Strip Section -->
        <section class="about-stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">24+</div>
                        <div class="stat-label">Doctors</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">29000+</div>
                        <div class="stat-label">Happy Patients</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Medical Beds</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">150+</div>
                        <div class="stat-label">Winning Awards</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Testimonials Slider Section -->
        <section class="about-testimonials-section">
            <div class="container">
                <div class="section-header text-center">
                    <span class="section-badge">Testimonial</span>
                    <h2 class="section-title">What Our Patients Say</h2>
                </div>

                <div class="testimonials-slider-container">
                    <div class="testimonials-slider-track">
                        <!-- Testimonial 1 -->
                        <div class="testimonial-slide active">
                            <div class="testimonial-quote-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.192 15.757c0-.962-.186-1.91-.558-2.845a7.078 7.078 0 0 1-1.395-2.775c.011-.113.051-.23.121-.35c.12-.206.277-.444.47-.715a37.217 37.217 0 0 1 1.344-1.748c.415-.515.622-.962.622-1.34 0-.412-.175-.764-.526-1.055-.35-.29-.76-.436-1.228-.436-.597 0-1.119.294-1.564.882l-2.907 4.103a8.917 8.917 0 0 0-1.34 3.993v2.836c0 .756.248 1.393.742 1.911.495.519 1.127.778 1.897.778h2.348c.722 0 1.312-.224 1.77-.673.458-.45.688-1.026.688-1.73zm11 0c0-.962-.186-1.91-.558-2.845a7.078 7.078 0 0 1-1.395-2.775c.011-.113.051-.23.121-.35.12-.206.277-.444.47-.715a37.217 37.217 0 0 1 1.344-1.748c.415-.515.622-.962.622-1.34 0-.412-.175-.764-.526-1.055-.35-.29-.76-.436-1.228-.436-.597 0-1.119.294-1.564.882l-2.907 4.103a8.917 8.917 0 0 0-1.34 3.993v2.836c0 .756.248 1.393.742 1.911.495.519 1.127.778 1.897.778h2.348c.722 0 1.312-.224 1.77-.673.458-.45.688-1.026.688-1.73z"/>
                                </svg>
                            </div>
                            <p class="testimonial-quote">
                                "It was a good experience. Staff and doctors are very polite and humble. Dr. Gunjan ma'ms given best advice and treatment for my wife during the pregnancy and also during delivery. Hospitality was great.. Vindhya Hospital and Research centre."
                            </p>
                            <div class="testimonial-author-row">
                                <div class="author-avatar-wrap">
                                    <img src="images/doctors/Dr.-Gunjan-goswami.png" alt="Dr. Gunjan Goswami" class="author-avatar">
                                </div>
                                <div class="author-info">
                                    <h4 class="author-name">Roshni Khan</h4>
                                    <span class="author-details">Patient &bull; Private ward room no. 201</span>
                                </div>
                            </div>
                        </div>
                        <!-- Testimonial 2 -->
                        <div class="testimonial-slide">
                            <div class="testimonial-quote-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.192 15.757c0-.962-.186-1.91-.558-2.845a7.078 7.078 0 0 1-1.395-2.775c.011-.113.051-.23.121-.35c.12-.206.277-.444.47-.715a37.217 37.217 0 0 1 1.344-1.748c.415-.515.622-.962.622-1.34 0-.412-.175-.764-.526-1.055-.35-.29-.76-.436-1.228-.436-.597 0-1.119.294-1.564.882l-2.907 4.103a8.917 8.917 0 0 0-1.34 3.993v2.836c0 .756.248 1.393.742 1.911.495.519 1.127.778 1.897.778h2.348c.722 0 1.312-.224 1.77-.673.458-.45.688-1.026.688-1.73zm11 0c0-.962-.186-1.91-.558-2.845a7.078 7.078 0 0 1-1.395-2.775c.011-.113.051-.23.121-.35.12-.206.277-.444.47-.715a37.217 37.217 0 0 1 1.344-1.748c.415-.515.622-.962.622-1.34 0-.412-.175-.764-.526-1.055-.35-.29-.76-.436-1.228-.436-.597 0-1.119.294-1.564.882l-2.907 4.103a8.917 8.917 0 0 0-1.34 3.993v2.836c0 .756.248 1.393.742 1.911.495.519 1.127.778 1.897.778h2.348c.722 0 1.312-.224 1.77-.673.458-.45.688-1.026.688-1.73z"/>
                                </svg>
                            </div>
                            <p class="testimonial-quote">
                                "I am very satisfied with the treatment and care I received during my stay at the hospital. The doctors explained everything clearly, and I felt confident in the treatment. Special thanks to Dr. Dhirendra Gautam for his dedicated guidance and support throughout. The nursing staff was very caring and attentive, always available when needed, especially Nurse Kiran and Nurse Sudha, Nurse sheetal and puspendra,whose kindness and patience made recovery easier. The hospital facilities were clean and comfortable, and the admission and discharge process was smooth. Overall, my experience was excellent, and I would strongly recommend this hospital to others."
                            </p>
                            <div class="testimonial-author-row">
                                <div class="author-avatar-wrap">
                                    <img src="images/doctors/Dr.Dheerendra-gautam.png" alt="Dr. Dhirendra Gautam" class="author-avatar">
                                </div>
                                <div class="author-info">
                                    <h4 class="author-name">Shalini Singh</h4>
                                    <span class="author-details">Patient &bull; Verified Care Recipient</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slider Controls -->
                    <button class="testimonial-arrow prev-testimonial" aria-label="Previous Testimonial">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </button>
                    <button class="testimonial-arrow next-testimonial" aria-label="Next Testimonial">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </button>

                    <!-- Slider Indicators -->
                    <div class="testimonial-dots">
                        <span class="testimonial-dot active" data-slide="0"></span>
                        <span class="testimonial-dot" data-slide="1"></span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php
    include __DIR__ . '/../includes/footer.php';
    ?>

    <!-- Testimonial Modal Popup -->
    <div class="review-modal-overlay" id="testimonialModal">
        <div class="review-modal-card">
            <button class="review-modal-close" aria-label="Close modal">&times;</button>
            <div class="review-modal-header" style="border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 15px; margin-bottom: 15px; display: flex; align-items: center;">
                <div class="author-avatar-wrap" style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; margin-right: 15px; border: 2px solid var(--clr-accent);">
                    <img src="" id="testimonialModalAvatar" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="user-meta" style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                    <h4 id="testimonialModalAuthor" style="margin: 0; color: var(--clr-brand); font-size: 1.1rem; font-weight: 700; font-family: var(--font-heading);"></h4>
                    <span id="testimonialModalDetails" style="font-size: 0.85rem; color: var(--clr-text-muted);"></span>
                </div>
            </div>
            <div class="review-modal-content" style="max-height: 350px; overflow-y: auto;">
                <p id="testimonialModalText" style="color: var(--clr-text-main); line-height: 1.75; font-size: 1rem; white-space: pre-line; font-style: italic;"></p>
            </div>
        </div>
    </div>

    <!-- Custom JS Scripts -->
    <script src="js/main.js"></script>

    <!-- Testimonial Slider & Modal JS -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const slides = document.querySelectorAll('.testimonial-slide');
        const dots = document.querySelectorAll('.testimonial-dot');
        const prevBtn = document.querySelector('.prev-testimonial');
        const nextBtn = document.querySelector('.next-testimonial');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            currentSlide = (index + slides.length) % slides.length;
            
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                showSlide(currentSlide - 1);
                resetAutoPlay();
            });
            nextBtn.addEventListener('click', () => {
                showSlide(currentSlide + 1);
                resetAutoPlay();
            });
        }

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                showSlide(idx);
                resetAutoPlay();
            });
        });

        function startAutoPlay() {
            slideInterval = setInterval(() => {
                showSlide(currentSlide + 1);
            }, 8000); // 8 seconds per slide for reading ease
        }

        function resetAutoPlay() {
            clearInterval(slideInterval);
            startAutoPlay();
        }

        startAutoPlay();

        // --- TESTIMONIAL VIEW MORE MODAL LOGIC ---
        const testimonialModal = document.getElementById('testimonialModal');
        const modalAvatar = document.getElementById('testimonialModalAvatar');
        const modalAuthor = document.getElementById('testimonialModalAuthor');
        const modalDetails = document.getElementById('testimonialModalDetails');
        const modalText = document.getElementById('testimonialModalText');
        const modalClose = testimonialModal ? testimonialModal.querySelector('.review-modal-close') : null;

        const openTestimonialModal = (slide) => {
            const avatarImg = slide.querySelector('.author-avatar');
            const authorName = slide.querySelector('.author-name').textContent;
            const authorDetails = slide.querySelector('.author-details').innerHTML;
            const quoteText = slide.querySelector('.testimonial-quote').textContent;

            modalAvatar.src = avatarImg ? avatarImg.src : '';
            modalAvatar.alt = authorName;
            modalAuthor.textContent = authorName;
            modalDetails.innerHTML = authorDetails;
            modalText.textContent = quoteText.replace(/^"|"$/g, ''); // strip surrounding quotes if present

            testimonialModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeTestimonialModal = () => {
            testimonialModal.classList.remove('active');
            document.body.style.overflow = '';
        };

        const quotes = document.querySelectorAll('.testimonial-slide .testimonial-quote');
        quotes.forEach(quote => {
            const slide = quote.closest('.testimonial-slide');
            
            // To accurately measure scrollHeight vs clientHeight, we need slide to be layouted (block)
            const originalDisplay = window.getComputedStyle(slide).display;
            const isHidden = (originalDisplay === 'none');
            
            if (isHidden) {
                slide.style.display = 'block';
                slide.style.visibility = 'hidden';
                slide.style.position = 'absolute';
            }
            
            // Apply clamped class
            quote.classList.add('clamped');
            
            const overflows = (quote.scrollHeight > quote.clientHeight);
            
            // Restore slide styles
            if (isHidden) {
                slide.style.display = '';
                slide.style.visibility = '';
                slide.style.position = '';
            }
            
            if (overflows) {
                const viewMoreBtn = document.createElement('button');
                viewMoreBtn.className = 'testimonial-read-more';
                viewMoreBtn.textContent = 'View More';
                
                // Insert after quote
                quote.parentNode.insertBefore(viewMoreBtn, quote.nextSibling);
                
                // Add click listener
                viewMoreBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openTestimonialModal(slide);
                });
            } else {
                quote.classList.remove('clamped');
            }
        });

        // Close listeners
        if (modalClose) {
            modalClose.addEventListener('click', closeTestimonialModal);
        }
        if (testimonialModal) {
            testimonialModal.addEventListener('click', (e) => {
                if (e.target === testimonialModal) {
                    closeTestimonialModal();
                }
            });
        }
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && testimonialModal && testimonialModal.classList.contains('active')) {
                closeTestimonialModal();
            }
        });
    });
    </script>
</body>

</html>
