            </div>
        </main>
    </div>

    <!-- Custom Scripts -->
    <script>
        // Admin specific JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Admin dashboard loaded');
            
            // Initialize any admin-specific features here
            initializeAdminFeatures();
        });

        function initializeAdminFeatures() {
            // Add admin-specific functionality
            console.log('Admin features initialized');
        }

        // Resource loading error handling
        window.addEventListener('error', function(e) {
            if (e.target.tagName === 'LINK' || e.target.tagName === 'SCRIPT') {
                console.error('Failed to load resource:', e.target.src || e.target.href);
            }
        });
    </script>
</body>
</html>
