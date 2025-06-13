<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garden Library</title>

    <link rel="icon" href="<?php echo $site_url; ?>favicon.ico" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>      <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#53933D',
                        secondary: '#E1AA74',
                        light: '#F5FBF3',
                        dark: '#333333',
                    },
                    fontFamily: {
                        raleway: ['Raleway', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }    </script>    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/hero-fixes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/visibility-fixes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/about-section-enhancements.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/timeline-section.css?v=<?php echo time(); ?>">    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/timeline-animations.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/button-effects.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/about-section-size.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/sticky-footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $site_url; ?>assets/css/cta-section.css?v=<?php echo time(); ?>">
    <script src="<?php echo $site_url; ?>assets/js/particles.min.js"></script>
    <script src="<?php echo $site_url; ?>assets/js/script.js"></script>
    <script src="<?php echo $site_url; ?>assets/js/hero-animation.js"></script>
    <script src="<?php echo $site_url; ?>assets/js/visibility-fix.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-raleway bg-white">
    <header class="py-4 shadow-sm bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">                  
                <div class="flex items-center">
                    <div class="flex items-center ">
                        <img src="<?php echo $site_url; ?>assets/img/LogoCat.png" alt="Library Logo" class="w-12 h-12 object-cover rounded">
                        <h1 class="text-2xl font-bold">Garden Library</h1>
                    </div>
                </div>                
                <div class="flex items-center space-x-6">                    
                    <nav class="hidden md:flex items-center space-x-6">
                        <a href="<?php echo $site_url; ?>index.php" class="text-gray-800 hover:text-primary transition-colors font-semibold text-lg">Home</a>
                        <a href="<?php echo $site_url; ?>index.php#features" class="text-gray-800 hover:text-primary transition-colors font-semibold text-lg">Features</a>
                        <a href="<?php echo $site_url; ?>index.php#about-us" class="text-gray-800 hover:text-primary transition-colors font-semibold text-lg">About</a>
                        <a href="<?php echo $site_url; ?>index.php#how-it-works" class="text-gray-800 hover:text-primary transition-colors font-semibold text-lg">How It Works</a>
                        
                    </nav>
                    <a href="<?php echo $site_url; ?>pages/signup/index.php" class="bg-primary hover:bg-secondary text-white px-5 py-2 rounded-lg font-semibold transition-all duration-300">SIGN UP NOW</a>
                    
                    <!-- Mobile menu button -->
                    <button class="md:hidden text-gray-800 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
