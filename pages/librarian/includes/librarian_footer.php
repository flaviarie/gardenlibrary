</div>
        </main>
    </div>

    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Use the proper logout script that handles session destruction
                const currentPath = window.location.pathname;
                let logoutPath;
                
                if (currentPath.includes('/modules/')) {
                    logoutPath = '../../../includes/logout.php';
                } else {
                    logoutPath = '../../includes/logout.php';
                }
                
                window.location.href = logoutPath;
            }
        }
    </script>

</body>
</html>
