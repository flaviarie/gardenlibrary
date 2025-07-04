/**
 * Admin Dashboard Core JavaScript
 * Handles responsive interactions and UI enhancements
 */

class AdminDashboard {
    constructor() {
        this.init();
    }

    init() {
        this.enhanceMobileExperience();
        this.handleResponsiveLayout();
        this.setupEventListeners();
        this.validateResources();
    }

    // Mobile touch enhancements
    enhanceMobileExperience() {
        const buttons = document.querySelectorAll('button, .cursor-pointer, a[class*="hover:"]');
        buttons.forEach(button => {
            if (window.innerWidth <= 768) {
                const rect = button.getBoundingClientRect();
                if (rect.height < 44) {
                    button.style.minHeight = '44px';
                    button.style.display = 'flex';
                    button.style.alignItems = 'center';
                    button.style.justifyContent = 'center';
                }
            }
            
            // Touch feedback
            button.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            }, { passive: true });
            
            button.addEventListener('touchend', function() {
                this.style.transform = '';
            }, { passive: true });
        });
        
        // Table scroll enhancements
        const tableContainer = document.querySelector('.overflow-x-auto');
        if (tableContainer && window.innerWidth >= 640) {
            let isScrolling = false;
            
            tableContainer.addEventListener('scroll', function() {
                if (!isScrolling) {
                    isScrolling = true;
                    this.style.boxShadow = 'inset -10px 0 10px -10px rgba(0,0,0,0.1)';
                    
                    setTimeout(() => {
                        if (this.scrollLeft === 0) {
                            this.style.boxShadow = '';
                        }
                        isScrolling = false;
                    }, 150);
                }
            }, { passive: true });
        }
    }

    // Layout adjustments
    handleResponsiveLayout() {
        const viewport = window.innerWidth;
        const systemOverview = document.querySelector('.bg-white.rounded-2xl');
        
        if (viewport <= 640) {
            systemOverview?.classList.add('mx-2');
            
            const mobileCards = document.querySelectorAll('.block.sm\\:hidden .bg-white');
            mobileCards.forEach(card => {
                card.style.touchAction = 'manipulation';
            });
        } else {
            systemOverview?.classList.remove('mx-2');
        }
    }

    // Event listeners setup
    setupEventListeners() {
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.handleResponsiveLayout();
                this.enhanceMobileExperience();
            }, 150);
        }, { passive: true });

        // Button loading states
        const addUserButton = document.querySelector('button[class*="bg-white"][class*="text-red-700"]');
        if (addUserButton) {
            addUserButton.addEventListener('click', function() {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                }, 2000);
            });
        }

        // Accessibility improvements
        document.querySelectorAll('button, a[role="button"]').forEach(element => {
            if (!element.getAttribute('aria-label') && !element.textContent.trim()) {
                const icon = element.querySelector('i[class*="fa-"]');
                if (icon) {
                    const iconClass = Array.from(icon.classList).find(cls => cls.startsWith('fa-'));
                    if (iconClass) {
                        element.setAttribute('aria-label', iconClass.replace('fa-', '').replace('-', ' '));
                    }
                }
            }
        });
    }

    // Resource validation
    validateResources() {
        setTimeout(() => {
            const icons = document.querySelectorAll('i[class*="fa-"]');
            console.log('Icons loaded:', icons.length);
            
            let missingIcons = 0;
            icons.forEach(icon => {
                const computedStyle = window.getComputedStyle(icon, ':before');
                const content = computedStyle.getPropertyValue('content');
                if (!content || content === 'none' || content === '""') {
                    missingIcons++;
                }
            });
            
            if (missingIcons > 0) {
                console.warn('Missing icons:', missingIcons);
            }
        }, 1000);

        // Font loading validation
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(() => {
                console.log('Fonts loaded');
            });
        }
    }

    // Error notification handler
    static showResourceError() {
        let notification = document.querySelector('.resource-error-notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.className = 'resource-error-notification fixed top-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-lg z-50 max-w-sm';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <div class="flex-1">
                        <strong class="block">Resource Loading Issue</strong>
                        <span class="text-sm">Some resources failed to load, but the page should still work.</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-yellow-700 hover:text-yellow-900 text-xl leading-none">&times;</button>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification && notification.parentElement) {
                    notification.remove();
                }
            }, 8000);
        }
    }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
    new AdminDashboard();
});

// Resource error handling
window.addEventListener('error', (e) => {
    if (e.target.tagName === 'LINK' || e.target.tagName === 'SCRIPT') {
        console.error('Resource failed:', e.target.src || e.target.href);
        AdminDashboard.showResourceError();
    }
}, true);
