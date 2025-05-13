document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('nav ul li a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerHeight = document.querySelector('header').offsetHeight;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                navLinks.forEach(link => link.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
    
    function updateActiveLink() {
        let currentSection = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            const headerHeight = document.querySelector('header').offsetHeight;
            
            if (window.pageYOffset >= sectionTop - headerHeight - 100) {
                currentSection = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${currentSection}`) {
                link.classList.add('active');
            }
        });
    }
    
    // Set active link based on scroll position
    updateActiveLink();
    
    // Update active link on scroll
    window.addEventListener('scroll', updateActiveLink);
    
    // Animation for elements on scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.service-image, .service-text, .testimonial-card, .event-item');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementPosition < windowHeight * 0.8) {
                element.classList.add('animate');
            }
        });
    };
    
    // Add animation class to CSS
    const style = document.createElement('style');
    style.textContent = `
        .service-image, .service-text, .testimonial-card, .event-item {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .animate {
            opacity: 1;
            transform: translateY(0);
        }
    `;
    document.head.appendChild(style);
    
    // Run on load and scroll
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
});