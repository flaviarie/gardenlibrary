// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    const menuLinks = document.querySelectorAll('.main-menu a[href*="#"]');
    
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
