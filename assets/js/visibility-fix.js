// Visibility fix for About section text
document.addEventListener('DOMContentLoaded', function() {
    // Find all paragraphs in the about section
    const aboutParagraphs = document.querySelectorAll('#about-us p');
    
    // Ensure visibility for each paragraph
    aboutParagraphs.forEach(paragraph => {
        // Apply inline styles to force visibility
        paragraph.style.opacity = '1';
        paragraph.style.visibility = 'visible';
        paragraph.style.color = '#333';
        paragraph.style.position = 'relative';
        paragraph.style.zIndex = '10';
    });
    
    // Optional: Add a slight animation to make them appear
    setTimeout(() => {
        aboutParagraphs.forEach((paragraph, index) => {
            setTimeout(() => {
                paragraph.style.animation = 'fadeIn 0.5s ease forwards';
            }, index * 200); // Stagger the animations
        });
    }, 100);
});

// Define the fadeIn animation if it doesn't exist
if (!document.querySelector('#visibility-fix-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'visibility-fix-styles';
    styleSheet.textContent = `
        @keyframes fadeIn {
            from { opacity: 0.5; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(styleSheet);
}
