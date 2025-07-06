// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    // Detect if we're on homepage for mobile menu
    const isHomepage = window.location.pathname.endsWith('/index.php') && !window.location.pathname.includes('/pages/');
    const navPrefix = isHomepage ? '' : '../';
    const navSuffix = isHomepage ? '' : 'index.php';
    
    // Mobile menu functionality
    const mobileMenuButton = document.querySelector('button.md\\:hidden');
    const mobileMenu = document.createElement('div');
    mobileMenu.className = 'mobile-menu hidden fixed top-16 left-0 right-0 bg-white shadow-md py-4 z-50';
    mobileMenu.innerHTML = `
        <div class="container mx-auto px-4 flex flex-col space-y-3">
            <a href="${navPrefix}${navSuffix ? navSuffix : 'index.php'}" class="block py-2 px-4 hover:bg-light rounded transition-colors">Home</a>
            <a href="${navPrefix}${navSuffix}#features" class="block py-2 px-4 hover:bg-light rounded transition-colors">Explore Library</a>
            <a href="${navPrefix}${navSuffix}#about-us" class="block py-2 px-4 hover:bg-light rounded transition-colors">Our Vision</a>
            <a href="${navPrefix}${navSuffix}#how-it-works" class="block py-2 px-4 hover:bg-light rounded transition-colors">Begin Journey</a>
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
            const href = this.getAttribute('href');
            const parts = href.split('#');
            const targetId = parts[1];
            
            // Check if this is a same-page anchor link (no path or same path)
            const isSamePage = parts[0] === '' || 
                              parts[0] === window.location.pathname ||
                              (parts[0] === 'index.php' && isHomepage);
            
            if (isSamePage && targetId) {
                e.preventDefault();
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
            // For cross-page navigation, let the browser handle it normally
        });
    }
});


