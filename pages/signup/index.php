<?php
// Include the configuration file
require_once('../../config/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Garden Library</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo asset_url('assets/img/LogoCat.png'); ?>">
    
    <!-- Google Fonts - Raleway -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'raleway': ['Raleway', 'sans-serif'],
                    },
                    colors: {
                        'garden': {
                            '50': '#f0f9f4',
                            '100': '#dcf2e4',
                            '200': '#bce5cd',
                            '300': '#8dd1a8',
                            '400': '#57b57d',
                            '500': '#339959',
                            '600': '#267d46',
                            '700': '#20643a',
                            '800': '#1d5030',
                            '900': '#1a4229',
                        },
                        'warm': {
                            '50': '#fefdf9',
                            '100': '#fef7e7',
                            '200': '#fdecc4',
                            '300': '#fbdc96',
                            '400': '#f7c566',
                            '500': '#f4b041',
                            '600': '#e89611',
                            '700': '#c17918',
                            '800': '#9b5f1b',
                            '900': '#7d4f1a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-raleway min-h-screen bg-gradient-to-br from-garden-50 via-warm-50 to-garden-100 flex items-center justify-center p-4"
      style="background-image: url('../../assets/img/library-background-2.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
    
    <!-- Overlay for better text readability -->
    <div class="absolute inset-0 bg-gradient-to-br from-garden-900/60 via-garden-800/50 to-garden-900/70"></div>
      <div class="relative z-10 w-full max-w-md">        
        <!-- Logo and Title -->
        <div class="flex items-center justify-center mb-8 space-x-3">
            <img src="<?php echo asset_url('assets/img/LogoCat.png'); ?>" alt="Garden Library Logo" class="w-16 h-16 drop-shadow-lg">
            <h1 class="text-2xl font-bold text-white drop-shadow-lg">ELibrary</h1>
        </div>
        
        <!-- Signup Form -->
        <div class="backdrop-blur-md bg-white/80 rounded-2xl shadow-2xl border border-white/50 p-8">
            <h2 class="text-2xl font-semibold text-garden-800 text-center mb-6">Create an Account</h2>
            
            <form action="signup_process.php" method="post" class="space-y-4">
                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-garden-700 mb-1">Username</label>
                    <input type="text" 
                           id="username"
                           name="username" 
                           placeholder="Choose a username" 
                           required
                           class="w-full px-4 py-3 rounded-lg border border-garden-200 bg-white/70 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-garden-500 focus:border-transparent transition-all duration-200 placeholder-gray-500">
                </div>
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-garden-700 mb-1">Email Address</label>
                    <input type="email" 
                           id="email"
                           name="email" 
                           placeholder="your.email@example.com" 
                           required
                           class="w-full px-4 py-3 rounded-lg border border-garden-200 bg-white/70 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-garden-500 focus:border-transparent transition-all duration-200 placeholder-gray-500">
                </div>
                
                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-garden-700 mb-1">Password</label>
                    <input type="password" 
                           id="password"
                           name="password" 
                           placeholder="Create a secure password" 
                           required
                           class="w-full px-4 py-3 rounded-lg border border-garden-200 bg-white/70 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-garden-500 focus:border-transparent transition-all duration-200 placeholder-gray-500">
                </div>
                
                <!-- Confirm Password Field -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-garden-700 mb-1">Confirm Password</label>
                    <input type="password" 
                           id="confirm_password"
                           name="confirm_password" 
                           placeholder="Confirm your password" 
                           required
                           class="w-full px-4 py-3 rounded-lg border border-garden-200 bg-white/70 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-garden-500 focus:border-transparent transition-all duration-200 placeholder-gray-500">
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-garden-500 to-garden-600 text-white py-3 px-4 rounded-lg font-medium hover:from-garden-600 hover:to-garden-700 focus:outline-none focus:ring-2 focus:ring-garden-500 focus:ring-offset-2 transform hover:scale-[1.02] transition-all duration-200 shadow-lg">
                    Create Account
                </button>
            </form>
            
            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-garden-700">
                    Already have an account? 
                    <a href="../login/index.php" class="text-garden-600 hover:text-garden-800 font-medium hover:underline transition-colors duration-200">
                        Sign in here
                    </a>
                </p>
            </div>
            
            <!-- Back to Home -->
            <div class="mt-4 text-center">
                <a href="../../index.php" class="text-garden-500 hover:text-garden-700 text-sm hover:underline transition-colors duration-200">
                    ← Back to Home
                </a>
            </div>
        </div>
          <!-- Footer Text -->
        <div class="text-center mt-6">
            <p class="text-white text-sm font-medium drop-shadow-lg">Join our community of book lovers</p>
        </div>
    </div>
</body>
</html>

