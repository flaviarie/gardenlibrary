// Enhanced Dashboard JavaScript for Icon and Font Management

document.addEventListener('DOMContentLoaded', function() {
    // Check if FontAwesome is loaded
    checkFontAwesome();
    
    // Check if Tailwind CSS is loaded
    checkTailwindCSS();
    
    // Initialize tooltips and interactions
    initializeInteractions();
});

function checkFontAwesome() {
    // Wait a bit for FontAwesome to load
    setTimeout(function() {
        const testElement = document.createElement('i');
        testElement.className = 'fas fa-book';
        testElement.style.visibility = 'hidden';
        document.body.appendChild(testElement);
        
        const computedStyle = window.getComputedStyle(testElement, ':before');
        const content = computedStyle.getPropertyValue('content');
        
        document.body.removeChild(testElement);
        
        // If FontAwesome didn't load properly, apply fallbacks
        if (!content || content === 'none' || content === '""') {
            console.warn('FontAwesome not loaded properly, applying fallbacks...');
            applyIconFallbacks();
        }
    }, 1500);
}

function applyIconFallbacks() {
    // Icon mapping for fallbacks
    const iconMap = {
        'fa-book': '📚',
        'fa-check-circle': '✅',
        'fa-exclamation-triangle': '⚠️',
        'fa-bookmark': '🔖',
        'fa-plus': '➕',
        'fa-arrow-right': '→',
        'fa-hand-point-right': '👉',
        'fa-undo': '↩️',
        'fa-chart-pie': '📊',
        'fa-exchange-alt': '🔄',
        'fa-users': '👥',
        'fa-chart-bar': '📈',
        'fa-calendar-alt': '📅',
        'fa-cog': '⚙️',
        'fa-user': '👤',
        'fa-sign-out-alt': '🚪'
    };
    
    // Apply fallback icons
    Object.keys(iconMap).forEach(function(iconClass) {
        const elements = document.querySelectorAll('.fa.' + iconClass + ', .fas.' + iconClass);
        elements.forEach(function(element) {
            element.innerHTML = iconMap[iconClass];
            element.style.fontFamily = 'Arial, sans-serif';
            element.style.fontSize = '1.2em';
            element.className = element.className.replace(/fa[s|r|l|b]?\s+fa-[\w-]+/g, '');
            element.classList.add('icon-fallback');
        });    });
    
    // Log fallback notification instead of showing it
    console.log('Icons loaded with fallback symbols');
}

function checkTailwindCSS() {
    // Check if Tailwind classes are working
    const testElement = document.createElement('div');
    testElement.className = 'bg-blue-500 text-white p-4 hidden';
    document.body.appendChild(testElement);
    
    const computedStyle = window.getComputedStyle(testElement);
    const backgroundColor = computedStyle.backgroundColor;
    
    document.body.removeChild(testElement);
    
    // If Tailwind isn't working, add basic fallback styles
    if (!backgroundColor || backgroundColor === 'rgba(0, 0, 0, 0)' || backgroundColor === 'transparent') {
        console.warn('Tailwind CSS not loaded properly, applying basic styles...');
        addBasicStyles();
    }
}

function addBasicStyles() {
    const style = document.createElement('style');
    style.innerHTML = `
        /* Basic fallback styles */
        .grid { display: grid; }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        .gap-8 { gap: 2rem; }
        .bg-white { background-color: white; }
        .bg-gray-50 { background-color: #f9fafb; }
        .p-8 { padding: 2rem; }
        .p-4 { padding: 1rem; }
        .rounded-2xl { border-radius: 1rem; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .text-white { color: white; }
        .text-gray-700 { color: #374151; }
        .text-blue-600 { color: #2563eb; }
        .text-green-600 { color: #059669; }
        .text-yellow-600 { color: #d97706; }
        .text-red-600 { color: #dc2626; }
        .font-bold { font-weight: bold; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .space-x-2 > * + * { margin-left: 0.5rem; }
        .space-x-4 > * + * { margin-left: 1rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .w-16 { width: 4rem; }
        .h-16 { height: 4rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-4xl { font-size: 2.25rem; }
        .hover\\:scale-105:hover { transform: scale(1.05); }
        .transition-all { transition: all 0.3s ease; }
        .cursor-pointer { cursor: pointer; }    `;
    document.head.appendChild(style);
    
    // Removed notification as per user request
    console.log('Basic styling applied as fallback');
}

function initializeInteractions() {
    // Add hover effects for cards
    const cards = document.querySelectorAll('.stat-card, [class*="bg-gradient-to"]');
    cards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 10px 25px -3px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
        });
    });
    
    // Add click effects for buttons
    const buttons = document.querySelectorAll('button, .btn');
    buttons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.pointerEvents = 'none';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(function() {
                ripple.remove();
            }, 600);
        });
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm`;
    
    const colors = {
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-white',
        error: 'bg-red-500 text-white',
        success: 'bg-green-500 text-white'
    };
    
    notification.className += ' ' + colors[type];
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(function() {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Add CSS for ripple animation
const rippleCSS = document.createElement('style');
rippleCSS.innerHTML = `
    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleCSS);

// Handle network issues
window.addEventListener('online', function() {
    showNotification('Connection restored', 'success');
});

window.addEventListener('offline', function() {
    showNotification('Connection lost - using offline mode', 'warning');
});

// Preload critical resources
function preloadResources() {
    const resources = [
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        'https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap'
    ];
    
    resources.forEach(function(url) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'style';
        link.href = url;
        document.head.appendChild(link);
    });
}
