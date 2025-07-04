// Reports JavaScript

// Tab switching functionality
document.querySelectorAll('.report-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active class from all tabs
        document.querySelectorAll('.report-tab').forEach(t => {
            t.classList.remove('active', 'text-blue-600', 'border-blue-600');
            t.classList.add('text-gray-500');
        });
        
        // Add active class to clicked tab
        this.classList.add('active', 'text-blue-600', 'border-blue-600');
        this.classList.remove('text-gray-500');
        
        // Hide all content
        document.querySelectorAll('.report-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show selected content
        const tabName = this.dataset.tab;
        document.getElementById(tabName + '-tab').classList.remove('hidden');
    });
});
