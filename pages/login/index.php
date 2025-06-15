<?php
// Include the configuration file
require_once('../../config/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title>Login - Garden Library</title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $site_url; ?>assets/img/LogoCat.png" type="image/png">
    <link rel="shortcut icon" href="<?php echo $site_url; ?>assets/img/LogoCat.png" type="image/png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {

                    fontFamily: {
                        'raleway': ['Raleway'],
                    },
                    
                    colors: {
                        'primary': '#53933D',
                        'secondary': '#E1AA74',
                        'light': '#F5FBF3',
                        'dark': '#333333',
                        'primary-dark': '#3d6b2d',
                        'primary-light': '#6ba84f',
                    },
                    animation: {
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .glass-effect {
            background: rgba(245, 251, 243, 0.95);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }
        
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(83, 147, 61, 0.1);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #53933D 0%, #6ba84f 100%);
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #6ba84f 0%, #E1AA74 100%);
        }
    </style>
</head>
<body class="h-screen overflow-hidden font-raleway">
    
    <div class="min-h-screen relative overflow-hidden">
        
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('assets/img/modern-library.jpg');">
        </div>
        
       
        <div class="absolute inset-0 bg-gradient-to-br from-dark/60 via-primary/30 to-secondary/40"></div>
        
       
        <div class="absolute inset-0 opacity-10" 
             style="background-image: 
               linear-gradient(45deg, transparent 49%, rgba(245,251,243,0.1) 50%, transparent 51%),
               linear-gradient(-45deg, transparent 49%, rgba(245,251,243,0.1) 50%, transparent 51%);
               background-size: 60px 60px;">
        </div>
        
      
        <div class="absolute top-20 left-20 w-6 h-6 bg-light opacity-20 rounded-lg animate-float"></div>
        <div class="absolute top-32 right-32 w-4 h-4 bg-secondary opacity-15 rounded-full animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-40 left-1/4 w-8 h-2 bg-primary opacity-20 rounded-full animate-float" style="animation-delay: 4s;"></div>
        
        
        <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
            <div class="glass-effect rounded-3xl shadow-2xl border border-white/20 p-10 w-full max-w-md text-center animate-slide-up">
                
                <!-- Logo section -->
                <div class="mb-8">
                    <h1 class="text-5xl font-extrabold text-primary mb-2 tracking-tight whitespace-nowrap flex items-center justify-center gap-3">
                        <img src="../../assets/img/LogoCat.png" alt="Library Cat" class="w-12 h-12">
                        ELibrary
                    </h1>
                    <p class="text-dark/70 text-2xl font-medium">
                        Your modern reading sanctuary
                    </p>
                </div>
                
                <!-- Sign in form -->
                <div class="space-y-6">
                    <h2 class="text-3xl font-semibold text-primary mb-8">
                        Sign In
                    </h2>
                    
                    <form action="login_process.php" method="post" class="space-y-5">
                        <!-- Username input -->
                        <div class="space-y-2">
                            <input 
                                type="text" 
                                name="username" 
                                placeholder="Username" 
                                required
                                class="w-full px-5 py-4 border-2 border-primary/20 rounded-xl text-base bg-white/80 transition-all duration-300 outline-none input-focus focus:border-primary focus:bg-white focus:-translate-y-0.5 placeholder-dark/50"
                            >
                        </div>
                        
                        <!-- Password input -->
                        <div class="space-y-2">
                            <input 
                                type="password" 
                                name="password" 
                                placeholder="Password" 
                                required
                                class="w-full px-5 py-4 border-2 border-primary/20 rounded-xl text-base bg-white/80 transition-all duration-300 outline-none input-focus focus:border-primary focus:bg-white focus:-translate-y-0.5 placeholder-dark/50"
                            >
                        </div>
                        
                        <!-- Submit button -->
                        <button 
                            type="submit" 
                            class="w-full py-4 btn-gradient text-white border-none rounded-xl text-base font-semibold cursor-pointer transition-all duration-300 mt-6 shadow-lg shadow-primary/30 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary/40 active:translate-y-0"
                        >
                            Sign In
                        </button>
                    </form>
                    
                    <!-- Auth links -->
                    <div class="mt-8 space-y-4">
                        <p class="text-dark/70 text-sm">
                            Don't have an account? 
                            <a href="../signup/index.php" class="text-primary font-medium hover:text-secondary transition-colors duration-300 hover:underline">
                                Sign up here
                            </a>
                        </p>
                        
                        <a 
                            href="<?php echo $site_url; ?>index.php" 
                            class="inline-block text-secondary text-sm mt-3 opacity-80 hover:opacity-100 transition-opacity duration-300"
                        >
                            ← Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>