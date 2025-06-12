// Enhanced animations for the hero section
document.addEventListener('DOMContentLoaded', function() {
    // Initialize particles.js
    const heroSection = document.querySelector('.hero-library-bg');
    
    if (heroSection) {
        // Preload background images to ensure they work properly
        const preloadBackgroundImages = () => {
            const basePath = document.querySelector('base')?.getAttribute('href') || '';
            const bgImages = [
                basePath + 'assets/img/library-background.jpg',
                basePath + 'assets/img/library-background-alt.jpg',
                basePath + 'assets/img/library-background-2.jpg'
            ];
            
            bgImages.forEach(imgPath => {
                const img = new Image();
                img.src = imgPath;
                img.onload = () => console.log(`Preloaded: ${imgPath}`);
            });
        };
        
        // Call the preload function
        preloadBackgroundImages();
        
        // Create particles container
        const particlesContainer = document.createElement('div');
        particlesContainer.id = 'particles-js';
        particlesContainer.className = 'absolute inset-0 z-[1]';
        heroSection.prepend(particlesContainer);
        
        // Initialize particles
        if (typeof particlesJS !== 'undefined') {
            // Get the base path
            let basePath = '';
            const baseElement = document.querySelector('base');
            if (baseElement) {
                basePath = baseElement.getAttribute('href') || '';
            }
            
            // Load particles config with the proper path
            particlesJS.load('particles-js', basePath + 'assets/js/particles-config.json');
        }
          // Parallax effect on the background - adjusted for smaller hero
        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY;
            if (scrollPosition < 400) { // Reduced threshold for smaller hero
                heroSection.style.backgroundPositionY = (scrollPosition * 0.4) + 'px';
            }
        });
        
        // Add sparkle effects to text-secondary elements
        const addSparkleEffects = () => {
            const highlights = document.querySelectorAll('.text-secondary');
            
            highlights.forEach(highlight => {
                // Create 3 sparkles per highlighted text
                for (let i = 0; i < 3; i++) {
                    const sparkle = document.createElement('span');
                    sparkle.className = 'sparkle';
                    highlight.appendChild(sparkle);
                    
                    // Random position within the highlight
                    const posX = Math.random() * highlight.offsetWidth;
                    const posY = Math.random() * highlight.offsetHeight;
                    sparkle.style.left = `${posX}px`;
                    sparkle.style.top = `${posY}px`;
                    
                    // Add animation with random delay
                    sparkle.style.animation = `sparkle 1.5s ease-in-out ${Math.random() * 3}s infinite`;
                }
            });
        };
        
        // Call the sparkle effect after a short delay to ensure elements are rendered
        setTimeout(addSparkleEffects, 1000);
    }
      // Enhanced text reveal animation
    const heroTitle = document.querySelector('.hero-library-bg h1');
    const heroParagraphs = document.querySelectorAll('.hero-library-bg p');
    const heroCta = document.querySelector('.hero-cta');
    const glassCard = document.querySelector('#book-showcase');
    const heroIntroTag = document.querySelector('.hero-library-bg .inline-block');
    const avatarSection = document.querySelector('.hero-library-bg .flex-space-x-2');
    const bookCards = document.querySelectorAll('.animate-tilt');
    
    // Staggered entrance animations
    if (heroIntroTag) {
        setTimeout(() => {
            heroIntroTag.style.opacity = '1';
            heroIntroTag.style.transform = 'translateY(0)';
        }, 100);
    }
    
    if (heroTitle) {
        setTimeout(() => {
            heroTitle.style.opacity = '1';
            heroTitle.style.transform = 'translateY(0)';
        }, 300);
    }
    
    if (heroParagraphs.length > 0) {
        heroParagraphs.forEach((p, index) => {
            setTimeout(() => {
                p.style.opacity = '1';
                p.style.transform = 'translateY(0)';
            }, 500 + (index * 200));
        });
    }
    
    if (avatarSection) {
        setTimeout(() => {
            avatarSection.style.opacity = '1';
            avatarSection.style.transform = 'translateY(0)';
        }, 900);
    }
    
    if (heroCta) {
        setTimeout(() => {
            heroCta.style.opacity = '1';
            heroCta.style.transform = 'translateY(0)';
        }, 1100);
    }
    
    if (glassCard) {
        setTimeout(() => {
            glassCard.style.opacity = '1';
            glassCard.style.transform = 'translateX(0) rotate(0)';
        }, 1300);
    }
    
    // 3D hover effect for book cards
    if (bookCards.length > 0) {
        bookCards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const angleX = (y - centerY) / 20;
                const angleY = (centerX - x) / 20;
                
                card.style.transform = `perspective(1000px) rotateX(${angleX}deg) rotateY(${angleY}deg) scale3d(1.05, 1.05, 1.05)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
            });
        });
    }
    
    // Subtle floating animation for decorative elements
    const floatingElements = document.querySelectorAll('.hero-library-bg .animate-float');
    if (floatingElements.length > 0) {
        floatingElements.forEach(element => {
            setInterval(() => {
                element.classList.toggle('translate-y-1');
            }, 2000);
        });
    }
});
