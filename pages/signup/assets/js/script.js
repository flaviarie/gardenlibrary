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
