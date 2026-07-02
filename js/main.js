/**
 * VHRC Modern Premium Interactive Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Elements
    const body = document.body;
    const siteHeader = document.querySelector('.site-header');
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navOverlay = document.querySelector('.nav-overlay');
    const dropdownItems = document.querySelectorAll('.nav-item-dropdown');

    // Header Scroll Contraction (collapses middle brand bar on scroll)
    if (siteHeader) {
        const handleScroll = () => {
            if (window.scrollY > 40) {
                siteHeader.classList.add('header-scrolled');
            } else {
                siteHeader.classList.remove('header-scrolled');
            }
        };
        handleScroll(); // Initial check on load
        window.addEventListener('scroll', handleScroll, { passive: true });
    }



    // Mobile Menu Toggle logic
    if (mobileToggle && navOverlay) {
        const toggleMenu = () => {
            body.classList.toggle('menu-open');
        };

        mobileToggle.addEventListener('click', toggleMenu);
        navOverlay.addEventListener('click', toggleMenu);

        // Close menu when clicking a nav link (except dropdown triggers on mobile)
        const navLinks = document.querySelectorAll('.nav-link:not(.dropdown-trigger)');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (body.classList.contains('menu-open')) {
                    toggleMenu();
                }
            });
        });
    }

    // Dropdown toggle for Mobile devices
    dropdownItems.forEach(item => {
        const trigger = item.querySelector('.nav-link');
        
        trigger.addEventListener('click', (e) => {
            // Only apply on mobile screen widths (<= 768px)
            if (window.innerWidth <= 768) {
                e.preventDefault(); // Prevent standard nav behavior
                
                // Toggle active class on parent
                const isActive = item.classList.contains('dropdown-active');
                
                // Close other dropdowns
                dropdownItems.forEach(otherItem => {
                    otherItem.classList.remove('dropdown-active');
                });

                if (!isActive) {
                    item.classList.add('dropdown-active');
                }
            }
        });
    });

    // Handle window resize (cleanup classes if user changes window size)
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            body.classList.remove('menu-open');
            dropdownItems.forEach(item => {
                item.classList.remove('dropdown-active');
            });
        }
    });

    // Hero Slider logic
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dots .dot');
    const prevArrow = document.querySelector('.prev-arrow');
    const nextArrow = document.querySelector('.next-arrow');
    let currentSlide = 0;
    let slideInterval;
    const intervalTime = 6000; // 6 seconds auto-transition

    if (slides.length > 0) {
        const showSlide = (index) => {
            // Remove active classes
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Set new active slide
            currentSlide = (index + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            if (dots[currentSlide]) {
                dots[currentSlide].classList.add('active');
            }
        };

        const nextSlide = () => {
            showSlide(currentSlide + 1);
        };

        const prevSlide = () => {
            showSlide(currentSlide - 1);
        };

        // Auto transition timer
        const startSlideShow = () => {
            slideInterval = setInterval(nextSlide, intervalTime);
        };

        const resetSlideShow = () => {
            clearInterval(slideInterval);
            startSlideShow();
        };

        // Event Listeners for controls
        if (nextArrow) {
            nextArrow.addEventListener('click', () => {
                nextSlide();
                resetSlideShow();
            });
        }

        if (prevArrow) {
            prevArrow.addEventListener('click', () => {
                prevSlide();
                resetSlideShow();
            });
        }

        // Dot indicators click events
        dots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                const targetIndex = parseInt(e.target.getAttribute('data-slide'), 10);
                showSlide(targetIndex);
                resetSlideShow();
            });
        });

        // Initialize slideshow
        startSlideShow();
    }

    // Lightbox Modal Logic for Clinical Posters
    const lightboxModal = document.getElementById('lightboxModal');
    const lightboxImage = lightboxModal ? lightboxModal.querySelector('.lightbox-image') : null;
    const lightboxClose = lightboxModal ? lightboxModal.querySelector('.lightbox-close') : null;
    const lightboxOverlay = lightboxModal ? lightboxModal.querySelector('.lightbox-overlay') : null;
    const graphicCards = document.querySelectorAll('.graphic-card');

    if (lightboxModal && lightboxImage) {
        const openLightbox = (card) => {
            const img = card.querySelector('.graphic-img');
            
            // Check if the image loaded successfully
            if (img && img.style.display !== 'none') {
                lightboxImage.src = img.src;
            } else {
                // Fallback if the image file is missing (uses a placeholder or the default logo graphic)
                lightboxImage.src = 'images/hero_banner.png';
            }
            
            lightboxModal.classList.add('active');
            lightboxModal.setAttribute('aria-hidden', 'false');
            body.style.overflow = 'hidden'; // Disable scroll while modal is active
        };

        const closeLightbox = () => {
            lightboxModal.classList.remove('active');
            lightboxModal.setAttribute('aria-hidden', 'true');
            body.style.overflow = ''; // Restore scroll
        };

        // Attach click listeners to cards
        graphicCards.forEach(card => {
            card.addEventListener('click', () => openLightbox(card));
        });

        // Close on button click
        if (lightboxClose) {
            lightboxClose.addEventListener('click', closeLightbox);
        }

        // Close on backdrop overlay click
        if (lightboxOverlay) {
            lightboxOverlay.addEventListener('click', closeLightbox);
        }

        // Close on Escape key press
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightboxModal.classList.contains('active')) {
                closeLightbox();
            }
        });
    }

    // Doctors Carousel Navigation Slider Logic
    const docTrack = document.querySelector('.doctors-carousel-track');
    const docPrevBtn = document.querySelector('.carousel-nav-btn.prev');
    const docNextBtn = document.querySelector('.carousel-nav-btn.next');
    const progressBar = document.querySelector('.carousel-progress-bar');

    if (docTrack && docPrevBtn && docNextBtn) {
        const getScrollAmount = () => {
            const card = docTrack.querySelector('.doctor-slide-card');
            if (card) {
                // Scroll amount equals card width + gap
                const cardWidth = card.getBoundingClientRect().width;
                const gap = parseFloat(window.getComputedStyle(docTrack).gap) || 32;
                return cardWidth + gap;
            }
            return docTrack.clientWidth * 0.75; // fallback
        };

        docPrevBtn.addEventListener('click', () => {
            docTrack.scrollBy({
                left: -getScrollAmount(),
                behavior: 'smooth'
            });
        });

        docNextBtn.addEventListener('click', () => {
            docTrack.scrollBy({
                left: getScrollAmount(),
                behavior: 'smooth'
            });
        });

        // Hide/Show navigation arrows depending on scroll boundary (on desktops)
        const toggleCarouselArrows = () => {
            const maxScroll = docTrack.scrollWidth - docTrack.clientWidth;
            
            if (docTrack.scrollLeft <= 5) {
                docPrevBtn.style.opacity = '0.3';
                docPrevBtn.style.pointerEvents = 'none';
            } else {
                docPrevBtn.style.opacity = '1';
                docPrevBtn.style.pointerEvents = 'auto';
            }

            if (docTrack.scrollLeft >= maxScroll - 5) {
                docNextBtn.style.opacity = '0.3';
                docNextBtn.style.pointerEvents = 'none';
            } else {
                docNextBtn.style.opacity = '1';
                docNextBtn.style.pointerEvents = 'auto';
            }
        };

        // Update progress bar indicator
        const updateProgressBar = () => {
            const maxScroll = docTrack.scrollWidth - docTrack.clientWidth;
            const scrollPercentage = maxScroll > 0 ? (docTrack.scrollLeft / maxScroll) * 100 : 0;
            if (progressBar) {
                progressBar.style.width = `${scrollPercentage}%`;
            }
        };

        // Combine arrow toggling and progress bar updates
        const handleScroll = () => {
            toggleCarouselArrows();
            updateProgressBar();
        };

        // Run initially and attach scroll listener
        handleScroll();
        docTrack.addEventListener('scroll', handleScroll, { passive: true });
        
        // Recalculate on resize
        window.addEventListener('resize', handleScroll, { passive: true });

        // Mouse Drag-to-Scroll (Swipe) functionality
        let isDown = false;
        let startX;
        let scrollLeft;

        docTrack.addEventListener('mousedown', (e) => {
            isDown = true;
            docTrack.classList.add('grabbing');
            startX = e.pageX - docTrack.offsetLeft;
            scrollLeft = docTrack.scrollLeft;
            
            // Temporarily disable scroll-snap and smooth behavior to avoid conflict during drag
            docTrack.style.scrollSnapType = 'none';
            docTrack.style.scrollBehavior = 'auto';
            stopAutoScroll(); // Pause autoplay during active drag
        });

        const stopDragging = () => {
            if (!isDown) return;
            isDown = false;
            docTrack.classList.remove('grabbing');
            
            // Restore original scroll snap and smooth transitions
            docTrack.style.scrollSnapType = '';
            docTrack.style.scrollBehavior = '';
            startAutoScroll(); // Resume autoplay after dragging stops
        };

        docTrack.addEventListener('mouseleave', stopDragging);
        docTrack.addEventListener('mouseup', stopDragging);

        docTrack.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - docTrack.offsetLeft;
            const walk = (x - startX) * 1.5; // Drag speed multiplier
            docTrack.scrollLeft = scrollLeft - walk;
        });

        // Infinite Loop Autoplay Scroller Logic
        let autoScrollInterval = null;
        const autoScrollDelay = 4000; // Auto-scroll every 4 seconds

        const startAutoScroll = () => {
            stopAutoScroll();
            autoScrollInterval = setInterval(() => {
                const maxScroll = docTrack.scrollWidth - docTrack.clientWidth;
                if (maxScroll <= 0) return; // No space to scroll
                
                if (docTrack.scrollLeft >= maxScroll - 10) {
                    // Loop back to start smoothly
                    docTrack.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                } else {
                    // Scroll by one card increment
                    docTrack.scrollBy({
                        left: getScrollAmount(),
                        behavior: 'smooth'
                    });
                }
            }, autoScrollDelay);
        };

        const stopAutoScroll = () => {
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
                autoScrollInterval = null;
            }
        };

        // Initialize autoplay
        startAutoScroll();

        // Pause autoplay on mouse enter / resume on mouse leave
        docTrack.addEventListener('mouseenter', stopAutoScroll);
        docTrack.addEventListener('mouseleave', startAutoScroll);

        // Pause autoplay on arrow clicks, then resume after 5s of inactivity
        if (docPrevBtn) {
            docPrevBtn.addEventListener('click', () => {
                stopAutoScroll();
                clearTimeout(docPrevBtn.timeoutId);
                docPrevBtn.timeoutId = setTimeout(startAutoScroll, 5000);
            });
        }
        if (docNextBtn) {
            docNextBtn.addEventListener('click', () => {
                stopAutoScroll();
                clearTimeout(docNextBtn.timeoutId);
                docNextBtn.timeoutId = setTimeout(startAutoScroll, 5000);
            });
        }
    }

    // --- Google Reviews Slider & Modal Popup Logic ---
    const reviewSlider = document.querySelector('.slider-wrapper');
    const reviewPrevBtn = document.querySelector('.nav-arrow.prev-btn');
    const reviewNextBtn = document.querySelector('.nav-arrow.next-btn');

    if (reviewSlider && reviewPrevBtn && reviewNextBtn) {
        const getReviewScrollAmount = () => {
            const card = reviewSlider.querySelector('.review-card');
            if (card) {
                const cardWidth = card.getBoundingClientRect().width;
                const gap = parseFloat(window.getComputedStyle(reviewSlider.querySelector('.reviews-track')).gap) || 24;
                return cardWidth + gap;
            }
            return reviewSlider.clientWidth * 0.5;
        };

        reviewPrevBtn.addEventListener('click', () => {
            reviewSlider.scrollBy({
                left: -getReviewScrollAmount(),
                behavior: 'smooth'
            });
        });

        reviewNextBtn.addEventListener('click', () => {
            reviewSlider.scrollBy({
                left: getReviewScrollAmount(),
                behavior: 'smooth'
            });
        });

        const toggleReviewArrows = () => {
            const maxScroll = reviewSlider.scrollWidth - reviewSlider.clientWidth;
            
            if (reviewSlider.scrollLeft <= 5) {
                reviewPrevBtn.style.opacity = '0.3';
                reviewPrevBtn.style.pointerEvents = 'none';
            } else {
                reviewPrevBtn.style.opacity = '1';
                reviewPrevBtn.style.pointerEvents = 'auto';
            }

            if (reviewSlider.scrollLeft >= maxScroll - 5) {
                reviewNextBtn.style.opacity = '0.3';
                reviewNextBtn.style.pointerEvents = 'none';
            } else {
                reviewNextBtn.style.opacity = '1';
                reviewNextBtn.style.pointerEvents = 'auto';
            }
        };

        reviewSlider.addEventListener('scroll', toggleReviewArrows, { passive: true });
        window.addEventListener('resize', toggleReviewArrows, { passive: true });
        
        // Initial call
        setTimeout(toggleReviewArrows, 150);
    }

    // Google Reviews Modal Logic
    const reviewModal = document.getElementById('reviewModal');
    const modalAvatar = document.getElementById('modalAvatar');
    const modalAuthor = document.getElementById('modalAuthor');
    const modalTime = document.getElementById('modalTime');
    const modalText = document.getElementById('modalText');
    const modalClose = reviewModal ? reviewModal.querySelector('.review-modal-close') : null;

    if (reviewModal && modalClose) {
        const openReviewModal = (card) => {
            const avatar = card.querySelector('.user-avatar');
            const author = card.querySelector('.user-meta h4').textContent;
            const time = card.querySelector('.time-ago').textContent;
            const fullText = card.getAttribute('data-full-text');

            // Copy avatar content and classes (for background coloring)
            modalAvatar.textContent = avatar.textContent;
            modalAvatar.className = avatar.className;

            modalAuthor.textContent = author;
            modalTime.textContent = time;
            modalText.textContent = fullText;

            reviewModal.classList.add('active');
            body.style.overflow = 'hidden'; // Disable scroll under overlay
        };

        const closeReviewModal = () => {
            reviewModal.classList.remove('active');
            body.style.overflow = ''; // Restore scroll
        };

        // Attach listeners to all "Read more" buttons
        const readMoreBtns = document.querySelectorAll('.read-more-link');
        readMoreBtns.forEach(btn => {
            const card = btn.closest('.review-card');
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                openReviewModal(card);
            });
        });

        // Close actions
        modalClose.addEventListener('click', closeReviewModal);
        reviewModal.addEventListener('click', (e) => {
            if (e.target === reviewModal) {
                closeReviewModal();
            }
        });
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && reviewModal.classList.contains('active')) {
                closeReviewModal();
            }
        });
    }
});
     function switchTab(event, tabId) {
  // Remove active class from all buttons
      const buttons = document.querySelectorAll('.tab-btn');
      buttons.forEach(btn => btn.classList.remove('active'));
  
  // Remove active class from all content panels
      const panels = document.querySelectorAll('.tab-panel');
      panels.forEach(panel => panel.classList.remove('active'));
  
  // Add active class to clicked tab button and targeted panel
      event.currentTarget.classList.add('active');
     document.getElementById(tabId).classList.add('active');
    }
