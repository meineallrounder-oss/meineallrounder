// Mobile Navigation Toggle
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');

hamburger.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    hamburger.classList.toggle('active');
});

// Smooth Scrolling (optimized)
const anchors = document.querySelectorAll('a[href^="#"]');
anchors.forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const href = this.getAttribute('href');
        const target = document.querySelector(href);
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            if (navMenu) navMenu.classList.remove('active');
        }
    }, { passive: true });
});

// Gallery Filter
const filterButtons = document.querySelectorAll('.filter-btn');
const galleryItems = document.querySelectorAll('.gallery-item');

filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons
        filterButtons.forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        button.classList.add('active');
        
        const filterValue = button.getAttribute('data-filter');
        
        galleryItems.forEach(item => {
            if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Lightbox
let currentImageIndex = 0;
const galleryImages = document.querySelectorAll('.gallery-image img');
const lightbox = document.getElementById('lightbox');
const lightboxImage = document.getElementById('lightboxImage');

function openLightbox(index) {
    currentImageIndex = index;
    lightboxImage.src = galleryImages[index].src;
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    lightbox.classList.remove('active');
    document.body.style.overflow = 'auto';
}

function changeImage(direction) {
    currentImageIndex += direction;
    
    if (currentImageIndex < 0) {
        currentImageIndex = galleryImages.length - 1;
    } else if (currentImageIndex >= galleryImages.length) {
        currentImageIndex = 0;
    }
    
    lightboxImage.src = galleryImages[currentImageIndex].src;
}

// Close lightbox on click outside or close button
document.querySelector('.close-lightbox').addEventListener('click', closeLightbox);
lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
        closeLightbox();
    }
});

// Keyboard navigation for lightbox
document.addEventListener('keydown', (e) => {
    if (lightbox.classList.contains('active')) {
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeImage(1);
        }
    }
});

// FAQ Toggle
const faqItems = document.querySelectorAll('.faq-item');

faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    question.addEventListener('click', () => {
        const isActive = item.classList.contains('active');
        
        // Close all FAQ items
        faqItems.forEach(faqItem => {
            faqItem.classList.remove('active');
        });
        
        // Open clicked item if it wasn't active
        if (!isActive) {
            item.classList.add('active');
        }
    });
});

// Contact Form
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Disable submit button
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Wird gesendet...';
        
        // Get form data
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value
        };
        
        try {
            // Use Node.js API on Vercel, PHP fallback for other servers
            const isVercel = window.location.hostname.includes('vercel.app') || window.location.hostname.includes('meineallrounder.de');
            const apiUrl = isVercel ? '/api/contact' : 'contact-form.php';
            
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                alert(data.message || 'Vielen Dank für Ihre Nachricht! Wir werden uns bald bei Ihnen melden.');
                contactForm.reset();
            } else {
                alert(data.message || 'Es gab einen Fehler beim Senden Ihrer Nachricht. Bitte versuchen Sie es später erneut.');
            }
        } catch (error) {
            console.error('Error sending form:', error);
            alert('Es gab einen Fehler beim Senden Ihrer Nachricht. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt unter info@meineallrounder.de');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

// Quick Quote Form
const quickQuoteForm = document.querySelector('.quick-quote-form');

if (quickQuoteForm) {
    quickQuoteForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Disable submit button
        const submitBtn = quickQuoteForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Wird gesendet...';
        
        // Get form data
        const serviceValue = document.getElementById('quote-service').value;
        const areaValue = document.getElementById('quote-area').value;
        
        const formData = {
            name: document.getElementById('quote-name').value,
            phone: document.getElementById('quote-phone').value,
            service: serviceValue,
            area: areaValue,
            subject: 'Angebotsanfrage: ' + serviceValue,
            message: 'Angebotsanfrage für ' + serviceValue + (areaValue ? ' (Fläche: ' + areaValue + ' m²)' : '')
        };
        
        try {
            // Use Node.js API on Vercel, PHP fallback for other servers
            const isVercel = window.location.hostname.includes('vercel.app') || window.location.hostname.includes('meineallrounder.de');
            const apiUrl = isVercel ? '/api/contact' : 'contact-form.php';
            
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                alert(data.message || 'Vielen Dank für Ihre Anfrage! Wir werden Ihnen innerhalb von 24 Stunden ein unverbindliches Angebot zusenden.');
                quickQuoteForm.reset();
            } else {
                alert(data.message || 'Es gab einen Fehler beim Senden Ihrer Anfrage. Bitte versuchen Sie es später erneut.');
            }
        } catch (error) {
            console.error('Error sending quote form:', error);
            alert('Es gab einen Fehler beim Senden Ihrer Anfrage. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt unter info@meineallrounder.de');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

// Cookie Consent
const cookieConsent = document.getElementById('cookieConsent');
const acceptCookies = document.getElementById('acceptCookies');
const declineCookies = document.getElementById('declineCookies');

// Check if user has already made a choice
function checkCookieConsent() {
    const consent = localStorage.getItem('cookieConsent');
    if (!consent) {
        // Show cookie banner after a short delay
        setTimeout(() => {
            if (cookieConsent) {
                cookieConsent.classList.add('show');
            }
        }, 1000);
    }
}

// Accept cookies
if (acceptCookies) {
    acceptCookies.addEventListener('click', () => {
        localStorage.setItem('cookieConsent', 'accepted');
        if (cookieConsent) {
            cookieConsent.classList.remove('show');
        }
    });
}

// Decline cookies
if (declineCookies) {
    declineCookies.addEventListener('click', () => {
        localStorage.setItem('cookieConsent', 'declined');
        if (cookieConsent) {
            cookieConsent.classList.remove('show');
        }
    });
}

// Initialize cookie consent check
checkCookieConsent();

// Throttle function for performance
function throttle(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Back to Top Button
const backToTop = document.getElementById('backToTop');

if (backToTop) {
    const handleScroll = throttle(() => {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    }, 100);

    window.addEventListener('scroll', handleScroll, { passive: true });

    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Scroll Progress Bar
const scrollProgressBar = document.querySelector('.scroll-progress-bar');

if (scrollProgressBar) {
    const handleProgressScroll = throttle(() => {
        const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (window.pageYOffset / windowHeight) * 100;
        scrollProgressBar.style.width = scrolled + '%';
    }, 50);

    window.addEventListener('scroll', handleProgressScroll, { passive: true });
}

// Newsletter Form
const newsletterForm = document.getElementById('newsletterForm');

if (newsletterForm) {
    newsletterForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const emailInput = document.getElementById('newsletterEmail');
        const email = emailInput ? emailInput.value : newsletterForm.querySelector('input[type="email"]').value;
        
        // Disable submit button
        const submitBtn = newsletterForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Wird abonniert...';
        
        try {
            const formData = {
                name: 'Newsletter Abonnent',
                email: email,
                subject: 'Newsletter Anmeldung',
                message: 'Neue Newsletter-Anmeldung von der Website'
            };
            
            const response = await fetch('contact-form.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                alert('Vielen Dank für Ihr Abonnement! Sie erhalten ab sofort unsere Neuigkeiten.');
                newsletterForm.reset();
            } else {
                alert(data.message || 'Es gab einen Fehler beim Abonnieren. Bitte versuchen Sie es später erneut.');
            }
        } catch (error) {
            console.error('Error subscribing to newsletter:', error);
            alert('Es gab einen Fehler beim Abonnieren. Bitte versuchen Sie es später erneut.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

// Hero Scroll Indicator
const heroScrollIndicator = document.querySelector('.hero-scroll-indicator');

if (heroScrollIndicator) {
    heroScrollIndicator.addEventListener('click', () => {
        window.scrollTo({
            top: window.innerHeight,
            behavior: 'smooth'
        });
    });
}

// Google Reviews - Sample Reviews (Replace with actual Google Reviews API)
const reviewsList = document.getElementById('reviewsList');

if (reviewsList) {
    const sampleReviews = [
        {
            author: 'Michael Schmidt',
            rating: 5,
            date: 'vor 2 Wochen',
            text: 'Ausgezeichnete Arbeit! Das Team war pünktlich, sauber und sehr professionell. Unser Badezimmer sieht jetzt fantastisch aus. Sehr empfehlenswert!'
        },
        {
            author: 'Anna Müller',
            rating: 5,
            date: 'vor 1 Monat',
            text: 'Wir sind sehr zufrieden mit der Renovierung. Alles wurde termingerecht abgeschlossen und die Qualität ist hervorragend. Vielen Dank!'
        },
        {
            author: 'Thomas Weber',
            rating: 5,
            date: 'vor 3 Wochen',
            text: 'Professionelle Ausführung von Anfang bis Ende. Das Team war freundlich, kompetent und hat unsere Wünsche perfekt umgesetzt.'
        },
        {
            author: 'Sarah Becker',
            rating: 5,
            date: 'vor 2 Monaten',
            text: 'Top Service! Von der Beratung bis zur Fertigstellung war alles perfekt organisiert. Wir können Meine Allrounder nur weiterempfehlen.'
        }
    ];

    sampleReviews.forEach(review => {
        const reviewItem = document.createElement('div');
        reviewItem.className = 'review-item';
        reviewItem.innerHTML = `
            <div class="review-header">
                <div class="review-author">
                    <div class="review-avatar">${review.author.charAt(0)}</div>
                    <div class="review-author-info">
                        <h4>${review.author}</h4>
                        <p class="review-date">${review.date}</p>
                    </div>
                </div>
                <div class="review-stars">
                    ${'<i class="fas fa-star"></i>'.repeat(review.rating)}
                </div>
            </div>
            <p class="review-text">${review.text}</p>
        `;
        reviewsList.appendChild(reviewItem);
    });
}

// Video Carousel
function initVideoCarousel() {
    const videoCarousel = document.querySelector('.video-carousel');
    const videoSlides = document.querySelectorAll('.video-slide');
    const videoPrevBtn = document.querySelector('.ba-video-wrapper .carousel-prev');
    const videoNextBtn = document.querySelector('.ba-video-wrapper .carousel-next');
    const videoIndicators = document.querySelectorAll('.video-indicators .indicator');
    
    if (!videoCarousel || videoSlides.length === 0) return;
    
    let currentVideoSlide = 0;
    
    function showVideoSlide(index) {
        videoSlides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        videoIndicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === index);
        });
        currentVideoSlide = index;
    }
    
    function nextVideoSlide() {
        const next = (currentVideoSlide + 1) % videoSlides.length;
        showVideoSlide(next);
    }
    
    function prevVideoSlide() {
        const prev = (currentVideoSlide - 1 + videoSlides.length) % videoSlides.length;
        showVideoSlide(prev);
    }
    
    if (videoNextBtn) videoNextBtn.addEventListener('click', nextVideoSlide);
    if (videoPrevBtn) videoPrevBtn.addEventListener('click', prevVideoSlide);
    
    videoIndicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => showVideoSlide(index));
    });
}

// Gallery Carousel
function initGalleryCarousel() {
    const galleryCarousel = document.querySelector('.ba-gallery-carousel');
    const gallerySlides = document.querySelectorAll('.ba-gallery-slide');
    const galleryPrevBtn = document.querySelector('.ba-gallery-wrapper .carousel-prev');
    const galleryNextBtn = document.querySelector('.ba-gallery-wrapper .carousel-next');
    const galleryIndicators = document.querySelectorAll('.gallery-indicators .indicator');
    
    if (!galleryCarousel || gallerySlides.length === 0) return;
    
    let currentGallerySlide = 0;
    
    function showGallerySlide(index) {
        gallerySlides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        galleryIndicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === index);
        });
        currentGallerySlide = index;
    }
    
    function nextGallerySlide() {
        const next = (currentGallerySlide + 1) % gallerySlides.length;
        showGallerySlide(next);
    }
    
    function prevGallerySlide() {
        const prev = (currentGallerySlide - 1 + gallerySlides.length) % gallerySlides.length;
        showGallerySlide(prev);
    }
    
    if (galleryNextBtn) galleryNextBtn.addEventListener('click', nextGallerySlide);
    if (galleryPrevBtn) galleryPrevBtn.addEventListener('click', prevGallerySlide);
    
    galleryIndicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => showGallerySlide(index));
    });
}

// Initialize carousels when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initVideoCarousel();
    initGalleryCarousel();
});

// Navbar scroll effect with glassmorphism (optimized)
let lastScroll = 0;
const navbar = document.querySelector('.navbar');

if (navbar) {
    const handleNavbarScroll = throttle(() => {
        const currentScroll = window.pageYOffset;
        
    if (currentScroll > 100) {
        navbar.style.background = 'rgba(255, 255, 255, 0.2)';
        navbar.style.backdropFilter = 'blur(70px) saturate(180%)';
        navbar.style.webkitBackdropFilter = 'blur(70px) saturate(180%)';
        navbar.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(255, 255, 255, 0.6) inset';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.1)';
        navbar.style.backdropFilter = 'blur(60px) saturate(180%)';
        navbar.style.webkitBackdropFilter = 'blur(60px) saturate(180%)';
        navbar.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(255, 255, 255, 0.5) inset';
    }
        
        lastScroll = currentScroll;
    }, 100);

    window.addEventListener('scroll', handleNavbarScroll, { passive: true });
}

// AOS je uklonjen - svi elementi su vidljivi odmah kroz CSS
// Show all elements immediately without animation
document.addEventListener('DOMContentLoaded', function() {
    // Prevent scroll jump on page load
    if (window.scrollY === 0) {
        window.scrollTo(0, 0);
    }
    
    const aosElements = document.querySelectorAll('[data-aos]');
    aosElements.forEach(element => {
        element.style.opacity = '1';
        element.style.transform = 'none';
        element.style.transition = 'none';
    });
});

// Counter animation for stats
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = target + '+';
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start) + '+';
        }
    }, 16);
}

// Observe stats for counter animation
const observerOptions = {
    threshold: 0.5
};

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statItem = entry.target;
            const numberElement = statItem.querySelector('h3');
            const text = numberElement.textContent;
            const number = parseInt(text);
            if (!isNaN(number)) {
                numberElement.textContent = '0+';
                animateCounter(numberElement, number);
                statsObserver.unobserve(statItem);
            }
        }
    });
}, observerOptions);

document.querySelectorAll('.stat-item').forEach(item => {
    statsObserver.observe(item);
});

