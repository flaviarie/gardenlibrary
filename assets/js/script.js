// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const mobileMenuButton = document.querySelector('button.md\\:hidden');
    const mobileMenu = document.createElement('div');
    mobileMenu.className = 'mobile-menu hidden fixed top-16 left-0 right-0 bg-white shadow-md py-4 z-50';
    mobileMenu.innerHTML = `        <div class="container mx-auto px-4 flex flex-col space-y-3">
            <a href="${window.location.pathname}" class="block py-2 px-4 hover:bg-light rounded transition-colors">Home</a>
            <a href="${window.location.pathname}#features" class="block py-2 px-4 hover:bg-light rounded transition-colors">Features</a>
            <a href="${window.location.pathname}#how-it-works" class="block py-2 px-4 hover:bg-light rounded transition-colors">How It Works</a>
            <a href="${window.location.pathname}#about-us" class="block py-2 px-4 hover:bg-light rounded transition-colors">About</a>
        </div>
    `;
    
    document.body.appendChild(mobileMenu);
    
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Smooth scroll
    const menuLinks = document.querySelectorAll('a[href*="#"]');
    
    for (const link of menuLinks) {
        link.addEventListener('click', function(e) {
            // Only apply to links that point to an anchor on the same page
            if (this.getAttribute('href').split('#')[0] === window.location.pathname || 
                this.getAttribute('href').split('#')[0] === '') {
                
                e.preventDefault();
                const targetId = this.getAttribute('href').split('#')[1];
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80, // Offset for header
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            }
        });
    }
});

// Toggle grid overlay for development
document.addEventListener('DOMContentLoaded', function() {
    // Create toggle button
    const toggleButton = document.createElement('button');
    toggleButton.textContent = 'Toggle Grid';    toggleButton.style.position = 'fixed';
    toggleButton.style.bottom = '50px';
    toggleButton.style.right = '20px';
    toggleButton.style.zIndex = '1001';
    toggleButton.style.padding = '8px 12px';
    toggleButton.style.backgroundColor = '#356d41';
    toggleButton.style.color = 'white';
    toggleButton.style.border = 'none';
    toggleButton.style.borderRadius = '4px';
    toggleButton.style.cursor = 'pointer';
    
    // Append button to body
    document.body.appendChild(toggleButton);
    
    // Add click event
    toggleButton.addEventListener('click', function() {
        const containers = document.querySelectorAll('.container');
        containers.forEach(container => {
            container.classList.toggle('show-grid');
        });
    });
});
