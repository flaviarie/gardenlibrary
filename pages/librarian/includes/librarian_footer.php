            </div>
        </main>
    </div>

    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Determine the correct path based on current directory
                const currentPath = window.location.pathname;
                let logoutPath;
                
                if (currentPath.includes('/modules/')) {
                    logoutPath = '../../../pages/login/index.php';
                } else {
                    logoutPath = '../../pages/login/index.php';
                }
                
                window.location.href = logoutPath;
            }
        }
    </script>

</body>
</html>
