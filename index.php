<?php
// Include the configuration file
require_once('./config/config.php');

// Include the header
include($include_path . 'includes/header.php');
?>

<div class="site-content">
<main><!-- Hero section with library background image -->     
    <div class="row">
        <div class="col-12">                
            <section class="hero-library-bg relative py-16 md:py-20 lg:py-24 overflow-hidden min-h-[60vh]">                   
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/40 z-0"></div>
                    <!-- Radial gradient for focal point -->
                    <div class="absolute inset-0 bg-radial-gradient z-0"></div>
                    
                    <!-- Particles container will be injected here by JS -->
                    
                    <div class="container mx-auto px-4 relative z-10">
                        <div class="grid grid-cols-1 lg:grid-cols-12 items-center gap-8">                            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">                                <span class="inline-block px-3 py-1 bg-white/10 backdrop-blur-sm text-white rounded-full mb-3 text-sm border border-white/20">Welcome to the Garden Library</span>                                <h1 class="font-raleway font-bold text-4xl md:text-5xl lg:text-6xl tracking-normal text-white">
                                    Discover New Worlds <br> Through <span class="text-secondary drop-shadow-lg animate-pulse font-extrabold">Literature</span>
                                </h1>                                <div class="mt-4">
                                    <p class="font-raleway text-base md:text-lg text-gray-200 leading-relaxed tracking-wider mb-2">Where books bloom and ideas flourish. Our digital library connects you to a world of resources.</p>
                                    <p class="font-raleway text-base md:text-lg text-gray-200 leading-relaxed tracking-wider">From ancient classics to modern bestsellers, academic journals to leisure reading all in one beautiful interface.</p>
                                </div>
                                <div class="flex items-center mt-6 space-x-2">
                                    <div class="flex -space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">JD</div>
                                        <div class="w-8 h-8 rounded-full bg-red-500 border-2 border-white flex items-center justify-center text-white text-xs">SM</div>
                                        <div class="w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">AK</div>
                                    </div>
                                    <span class="text-sm text-white/80">Join <span class="text-white font-medium">5,000+ students</span> already using Garden Library</span>
                                </div>
                                  <!-- Enhanced CTA Button with pulse effect -->                                <div class="mt-6">
                                    <a href="./pages/login/index.php" class="relative inline-flex items-center group px-6 py-3 bg-primary text-white font-bold rounded-lg shadow-xl overflow-hidden transition-all duration-500 transform hover:-translate-y-1 hover:scale-105 z-10">
                                        <span class="relative z-10">Begin Your Journey</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 relative z-10 transform group-hover:translate-x-1 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                        <span class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
                                        <span class="absolute -inset-px bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 rounded-lg animate-pulse transition-opacity duration-500"></span>
                                    </a>
                                    <a href="./pages/signup/index.php" class="relative inline-flex items-center px-5 py-2 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-lg ml-3 hover:bg-white/20 transition-all duration-300 border border-white/20 overflow-hidden group hover:shadow-lg">
                                        <span class="relative z-10">Discover More</span>
                                        <span class="absolute inset-0 bg-white/5 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                                    </a>
                                </div>

                            </div>
                            
                            <!-- Book showcase with floating elements -->
                            <div class="lg:col-span-5 relative flex justify-center lg:justify-end mt-10 lg:mt-0">
                                <div class="relative w-full max-w-md lg:max-w-none overflow-visible">                                    <!-- Enhanced interactive glass card effect with 3D perspective -->
                                    <div class="relative z-10 backdrop-blur-lg bg-white/20 rounded-2xl shadow-2xl overflow-hidden p-8 border border-white/30 transform hover:scale-[1.03] hover:rotate-1 transition-all duration-500 glass-effect" id="book-showcase">
                                        <div class="flex flex-col items-center">
                                            <h3 class="text-white text-2xl font-bold mb-4">Explore Our World of Books</h3>
                                            
                                            <!-- Stacked book cards with 3D effect -->
                                            <div class="relative mb-8 w-full">
                                                <!-- First book card -->
                                                <div class="bg-white/90 rounded-xl p-4 mb-2 shadow-lg transform -rotate-3 hover:rotate-0 hover:-translate-y-2 transition-all duration-500 hover:z-10 relative z-[3] animate-tilt">
                                                    <div class="flex items-center mb-1">
                                                        <span class="text-secondary text-lg mr-2">📚</span>
                                                        <p class="text-gray-800 font-bold">Literature & Fiction</p>
                                                    </div>
                                                    <p class="text-gray-700 text-sm pl-7">Discover worlds of imagination</p>
                                                </div>
                                                
                                                <!-- Second book card -->
                                                <div class="bg-white/90 rounded-xl p-4 mb-2 shadow-lg transform rotate-1 hover:rotate-0 hover:-translate-y-2 transition-all duration-500 hover:z-10 relative z-[2] animate-tilt" style="animation-delay: 1s;">
                                                    <div class="flex items-center mb-1">
                                                        <span class="text-primary text-lg mr-2">🎓</span>
                                                        <p class="text-gray-800 font-bold">Academic Resources</p>
                                                    </div>
                                                    <p class="text-gray-700 text-sm pl-7">Support for your studies</p>
                                                </div>
                                                
                                                <!-- Third book card -->
                                                <div class="bg-white/90 rounded-xl p-4 shadow-lg transform rotate-3 hover:rotate-0 hover:-translate-y-2 transition-all duration-500 hover:z-10 relative z-[1] animate-tilt" style="animation-delay: 2s;">
                                                    <div class="flex items-center mb-1">
                                                        <span class="text-secondary text-lg mr-2">🌐</span>
                                                        <p class="text-gray-800 font-bold">Digital Collection</p>
                                                    </div>
                                                    <p class="text-gray-700 text-sm pl-7">Access anywhere, anytime</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Statistics counter -->
                                            <div class="flex justify-between w-full mb-6">
                                                <div class="text-center text-white">
                                                    <span class="block text-3xl font-bold">10k+</span>
                                                    <span class="text-sm opacity-80">Book Titles</span>
                                                </div>
                                                <div class="text-center text-white">
                                                    <span class="block text-3xl font-bold">24/7</span>
                                                    <span class="text-sm opacity-80">Access</span>
                                                </div>
                                                <div class="text-center text-white">
                                                    <span class="block text-3xl font-bold">5k+</span>
                                                    <span class="text-sm opacity-80">Members</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Interactive button with animated arrow -->
                                            <a href="#features" class="group inline-flex items-center px-6 py-3 mt-2 bg-primary text-white font-semibold rounded-lg shadow-xl hover:bg-secondary transform hover:-translate-y-1 transition-all duration-300 border border-white/10">
                                                <span>Discover Our Features</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </a>
                                        </div>
                                        
                                        <!-- Enhanced decorative elements -->
                                        <div class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 border-white/60 rounded-tl-xl animate-float"></div>
                                        <div class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 border-white/60 rounded-tr-xl animate-float" style="animation-delay: 0.5s"></div>
                                        <div class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 border-white/60 rounded-bl-xl animate-float" style="animation-delay: 1s"></div>
                                        <div class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 border-white/60 rounded-br-xl animate-float" style="animation-delay: 1.5s"></div>
                                    </div>
                                    
                                    <!-- Enhanced floating elements -->
                                    <div class="hidden lg:block absolute -bottom-6 -left-6 w-16 h-16 rounded-full bg-primary z-20 animate-bounce shadow-lg"></div>
                                    <div class="hidden lg:block absolute -top-6 right-1/4 w-12 h-12 rounded-full bg-secondary z-20 animate-pulse shadow-lg"></div>
                                    <div class="hidden lg:block absolute top-1/2 -right-4 w-8 h-8 rounded-full bg-white/80 z-20 animate-pulse delay-700 shadow-lg"></div>
                                    
                                    <!-- New magical elements -->
                                    <div class="hidden lg:block absolute -left-10 top-1/3 w-6 h-6 rounded-full bg-yellow-300/70 blur-sm animate-ping" style="animation-duration: 3s"></div>
                                    <div class="hidden lg:block absolute -right-10 bottom-1/3 w-4 h-4 rounded-full bg-yellow-500/60 blur-sm animate-ping" style="animation-duration: 4s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>        
        </div>
    
    <!-- Rest of content uses container -->

        </div>
        <div class="row">
            <div class="col-12">
                <section id="features" class="py-16 md:py-24">
                    <div class="container mx-auto px-4">                        
                        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 relative">
                            <span class="relative z-10">Why <span class= "choose-word relative">Choose</span> eLibrary?</span>
                            <span class="absolute bottom-0 w-24 h-1 bg-secondary rounded-full" style="left: calc(47% - 40px);"></span>
                        </h2>
                          <!-- First row with cards 1-3 -->
                        <div class="grid grid-cols-12 gap-6 md:gap-8">
                            <!-- Feature Card 1 -->
                            <div class="col-span-12 md:col-span-4 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 group hover:-translate-y-1 flex flex-col h-full">
                                <div class="flex justify-center pt-8">
                                    <div class="bg-light p-5 rounded-full w-20 h-20 flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                                        <img src="assets/img/find.png" alt="Book Catalog" class="w-10 h-10 object-contain">
                                    </div>
                                </div>
                                <div class="flex-grow flex flex-col justify-between p-6">
                                    <h3 class="text-xl font-bold mb-3 text-center group-hover:text-primary transition-colors">Smart Book Search</h3>
                                    <p class="text-gray-600 text-center">Track all your borrowed books and get personalized recommendations based on your reading habits.</p>
                                </div>
                            </div>
                                <!-- Feature Card 2 -->
                            <div class="col-span-12 md:col-span-4 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 group hover:-translate-y-1 flex flex-col h-full">
                                <div class="flex justify-center pt-8">
                                    <div class="bg-light p-5 rounded-full w-20 h-20 flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                                        <img src="assets/img/books.png" alt="Easy Borrowing" class="w-10 h-10 object-contain">
                                    </div>
                                </div>
                                <div class="flex-grow flex flex-col justify-between p-6">
                                    <h3 class="text-xl font-bold mb-3 text-center group-hover:text-primary transition-colors">Online Borrowing</h3>
                                    <p class="text-gray-600 text-center">Easily review your reading history and discover book suggestions tailored to your interests.</p>
                                </div>
                            </div>
                              
                            <!-- Feature Card 3-->
                            <div class="col-span-12 md:col-span-4 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 group hover:-translate-y-1 flex flex-col h-full">
                                <div class="flex justify-center pt-8">
                                    <div class="bg-light p-5 rounded-full w-20 h-20 flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                                        <img src="assets/img/alarm-clock.png" alt="Timely Reminders" class="w-10 h-10 object-contain">
                                    </div>
                                </div>
                                <div class="flex-grow flex flex-col justify-between p-6">
                                    <h3 class="text-xl font-bold mb-3 text-center group-hover:text-primary transition-colors">Due Date Reminders</h3>
                                    <p class="text-gray-600 text-center">Never miss a return date with automatic alerts and reminders sent directly to you.</p>
                                </div>
                            </div>
                        </div>
                          <!-- Second row with cards 4-5 centered -->
                        <div class="grid grid-cols-12 gap-6 md:gap-8 mt-6 md:mt-8">
                            
                            <div class="hidden md:block md:col-span-2"></div>
                            
                            <!-- Feature Card 4 -->
                            <div class="col-span-12 md:col-span-4 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 group hover:-translate-y-1 flex flex-col h-full">
                                <div class="flex justify-center pt-8">
                                    <div class="bg-light p-5 rounded-full w-20 h-20 flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                                        <img src="assets/img/chat.png" alt="Instant Support" class="w-10 h-10 object-contain">
                                    </div>
                                </div>
                                <div class="flex-grow flex flex-col justify-between p-6">
                                    <h3 class="text-xl font-bold mb-3 text-center group-hover:text-primary transition-colors">Instant Support</h3>
                                    <p class="text-gray-600 text-center">Stay on top of what you've read and explore new titles matched to your preferences.</p>
                                </div>
                            </div>
                            
                            <!-- Feature Card 5  -->
                            <div class="col-span-12 md:col-span-4 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 group hover:-translate-y-1 flex flex-col h-full">
                                <div class="flex justify-center pt-8">
                                    <div class="bg-light p-5 rounded-full w-20 h-20 flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                                        <img src="assets/img/trending-topic.png" alt="Reading History" class="w-10 h-10 object-contain">
                                    </div>
                                </div>                                <div class="flex-grow flex flex-col justify-between p-6">
                                    <h3 class="text-xl font-bold mb-3 text-center group-hover:text-primary transition-colors">Reading History Tracker</h3>
                                    <p class="text-gray-600 text-center">Your reading journey, all in one place see your history and get curated book picks just for you.</p>
                                </div>
                            </div>
                            <div class="hidden md:block md:col-span-2"></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-12">          
        <section id="about-us" class="py-20 md:py-28 lg:py-32 relative">
            <!-- Enhanced decorative elements for the section -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-secondary/10 rounded-full translate-y-1/4 -translate-x-1/4 blur-2xl"></div>
            <div class="absolute top-1/2 left-1/4 w-40 h-40 bg-primary/5 rounded-full -translate-y-1/2 blur-xl"></div>
            
            <div class="container mx-auto px-4 relative">
                <div class="grid grid-cols-1 lg:grid-cols-12 items-center gap-8">                    <!-- Hero image on LEFT with improved sizing -->
                    <div class="lg:col-span-5">
                        <div class="relative">
                            <img src="assets/img/library-background-2.jpg" alt="Library Image" class="w-full h-auto rounded-xl shadow-xl transform hover:scale-[1.02] transition-all duration-500 object-cover max-h-[450px]">
                            <!-- Enhanced decorative elements -->
                            <div class="absolute -bottom-4 -right-4 w-16 h-16 rounded-full border-4 border-primary bg-white/10 z-10 hidden lg:block animate-pulse"></div>
                            <div class="absolute -top-4 -left-4 w-12 h-12 rounded-full bg-secondary z-10 hidden lg:block animate-bounce"></div>
                            <div class="absolute top-1/2 right-0 transform translate-x-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/70 z-10 hidden lg:block"></div>
                        </div>
                    </div>
                      <!-- Enhanced Text content on RIGHT with fixed visibility -->
                    <div class="lg:col-span-7 space-y-6">
                        <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Your Knowledge Sanctuary</h2>
                        <p class="text-gray-700 leading-relaxed mb-4 text-lg font-medium">Welcome to the <span class="font-semibold text-primary">Garden Library</span>, your digital gateway to a world of knowledge and imagination. Our library is designed to provide students with easy access to a vast collection of books, journals, and resources that cater to diverse interests and academic needs.</p>
                        
                        <p class="text-gray-700 leading-relaxed mb-4 text-lg font-medium">Our mission is to foster a love for reading and learning by providing a user-friendly platform that makes accessing educational materials simple and enjoyable. With our intuitive interface, finding and borrowing books has never been easier.</p>
                          <p class="text-gray-700 leading-relaxed mb-6 text-lg font-medium">We believe that knowledge should bloom freely, just like plants in a garden. That's why we've created this digital space where ideas can flourish and minds can grow.</p>
                    </div>
                </div>
            </div>
        </section>    
    </div>
</div>

<!-- How It Works Section with Timeline -->
<div class="row">
    <div class="col-12">        
        <section id="how-it-works" class="py-16 md:py-24 bg-white relative overflow-hidden timeline-bg-pattern">
            <!-- Decorative elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-primary/5 rounded-full -translate-y-1/2 -translate-x-1/2 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-64 h-64 bg-secondary/5 rounded-full translate-y-1/2 translate-x-1/2 blur-3xl"></div>
            <div class="absolute top-1/2 right-0 w-32 h-32 bg-secondary/5 rounded-full translate-x-1/2 blur-xl"></div>
            <div class="absolute bottom-1/3 left-0 w-24 h-24 bg-primary/5 rounded-full -translate-x-1/2 blur-lg"></div>
            
            <div class="container mx-auto px-4">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-16 relative">
                    <span class="relative z-10">How It <span class="relative text-primary">Works</span></span>
                    
                </h2>
                
                <!-- Timeline Container -->
                <div class="relative">                <!-- Timeline Line -->
                    <div class="hidden md:block absolute left-1/2 transform -translate-x-1/2 h-full w-1 bg-gradient-to-b from-primary via-secondary to-primary rounded opacity-30"></div>
                    <div class="timeline-glow hidden md:block absolute left-1/2 transform -translate-x-1/2 h-full w-3 bg-gradient-to-b from-primary/10 via-secondary/10 to-primary/10 rounded blur-sm"></div>
                    
                    <!-- Progress indicators -->
                    <div class="timeline-progress hidden md:block">
                        <div class="timeline-progress-marker absolute left-1/2 transform -translate-x-1/2 top-1/4 w-2 h-2 bg-secondary rounded-full shadow-lg shadow-secondary/40"></div>
                        <div class="timeline-progress-marker absolute left-1/2 transform -translate-x-1/2 top-1/2 w-2 h-2 bg-secondary rounded-full shadow-lg shadow-secondary/40"></div>
                        <div class="timeline-progress-marker absolute left-1/2 transform -translate-x-1/2 top-3/4 w-2 h-2 bg-secondary rounded-full shadow-lg shadow-secondary/40"></div>
                    </div>
                    
                    <!-- Timeline Items -->
                    <div class="space-y-24 md:space-y-0 relative">                        <!-- Step 1 -->
                        <div class="timeline-item-1 md:grid md:grid-cols-12 md:gap-8 md:items-center relative md:mb-24"><!-- Timeline marker for desktop -->
                            <div class="hidden md:block absolute left-1/2 top-24 transform -translate-x-1/2 -translate-y-1/2">
                                <div class="marker-circle w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-lg border-2 border-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                              <!-- Left Content (image on left) -->
                            <div class="md:col-span-5 md:text-right mb-6 md:mb-0 flex md:justify-end">
                                <div class="timeline-card bg-light rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 w-full md:max-w-xs transform relative overflow-hidden">
                                    <div class="flex justify-center mb-4">
                                        <img src="assets/img/number-1.png" alt="Sign Up" class="timeline-icon w-16 h-16 object-contain">
                                    </div>
                                    <h3 class="text-xl font-bold mb-3 text-center text-primary">Sign Up</h3>
                                </div>
                            </div>
                            
                            <!-- Right Content (text on right) -->
                            <div class="md:col-span-5 md:col-start-8">
                                <p class="text-gray-700 leading-relaxed text-lg">
                                    Create your free account using your student or faculty ID. Registration is quick and secure.
                                </p>
                            </div>
                        </div>
                          <!-- Step 2 -->
                        <div class="timeline-item-2 md:grid md:grid-cols-12 md:gap-8 md:items-center relative md:mb-24"><!-- Timeline marker for desktop -->
                            <div class="hidden md:block absolute left-1/2 top-24 transform -translate-x-1/2 -translate-y-1/2">
                                <div class="marker-circle w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-lg border-2 border-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Left Content (text on left) -->
                            <div class="md:col-span-5 md:text-right mb-6 md:mb-0">
                                <p class="text-gray-700 leading-relaxed text-lg md:text-right">
                                    Search and filter through thousands of books by title, author, subject, or category—anytime, anywhere.
                                </p>
                            </div>
                              <!-- Right Content (image on right) -->
                            <div class="md:col-span-5 md:col-start-8 flex md:justify-start">
                                <div class="timeline-card bg-light rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 w-full md:max-w-xs transform relative overflow-hidden">
                                    <div class="flex justify-center mb-4">
                                        <img src="assets/img/two.png" alt="Browse the Catalog" class="timeline-icon w-16 h-16 object-contain">
                                    </div>
                                    <h3 class="text-xl font-bold mb-3 text-center text-primary">Browse the Catalog</h3>
                                </div>
                            </div>
                        </div>
                          <!-- Step 3 -->
                        <div class="timeline-item-3 md:grid md:grid-cols-12 md:gap-8 md:items-center relative md:mb-24"><!-- Timeline marker for desktop -->
                            <div class="hidden md:block absolute left-1/2 top-24 transform -translate-x-1/2 -translate-y-1/2">
                                <div class="marker-circle w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-lg border-2 border-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            </div>
                              <!-- Left Content (image on left) -->
                            <div class="md:col-span-5 md:text-right mb-6 md:mb-0 flex md:justify-end">
                                <div class="timeline-card bg-light rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 w-full md:max-w-xs transform relative overflow-hidden">
                                    <div class="flex justify-center mb-4">
                                        <img src="assets/img/number-3.png" alt="Borrow Books" class="timeline-icon w-16 h-16 object-contain">
                                    </div>
                                    <h3 class="text-xl font-bold mb-3 text-center text-primary">Borrow Books</h3>
                                </div>
                            </div>
                            
                            <!-- Right Content (text on right) -->
                            <div class="md:col-span-5 md:col-start-8">
                                <p class="text-gray-700 leading-relaxed text-lg">
                                    With a single click, borrow available books instantly. Choose between digital downloads or in library pickups.
                                </p>
                            </div>
                        </div>
                          <!-- Step 4 -->
                        <div class="timeline-item-4 md:grid md:grid-cols-12 md:gap-8 md:items-center relative"><!-- Timeline marker for desktop -->
                            <div class="hidden md:block absolute left-1/2 top-24 transform -translate-x-1/2 -translate-y-1/2">
                                <div class="marker-circle w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-lg border-2 border-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Left Content (text on left) -->
                            <div class="md:col-span-5 md:text-right mb-6 md:mb-0">
                                <p class="text-gray-700 leading-relaxed text-lg md:text-right">
                                    Keep tabs on your due dates, get reminders, and easily return books through your dashboard.
                                </p>
                            </div>
                              <!-- Right Content (image on right) -->
                            <div class="md:col-span-5 md:col-start-8 flex md:justify-start">
                                <div class="timeline-card bg-light rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 w-full md:max-w-xs transform relative overflow-hidden">
                                    <div class="flex justify-center mb-4">
                                        <img src="assets/img/number-4.png" alt="Track & Return" class="timeline-icon w-16 h-16 object-contain">
                                    </div>
                                    <h3 class="text-xl font-bold mb-3 text-center text-primary">Track & Return</h3>
                                </div>
                            </div>
                        </div>                    </div>
                </div>
            </div>          </section>        
        <div class="row">
    <div class="col-12">            <section id="cta-section" class="py-20 md:py-28 lg:py-32 relative overflow-hidden bg-gradient-to-br from-primary/5 via-white to-secondary/5">
            <!-- Enhanced decorative elements for the section -->
            <div class="absolute top-0 right-0 w-[30rem] h-[30rem] bg-primary/10 rounded-full -translate-y-1/2 translate-x-1/4 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-[25rem] h-[25rem] bg-secondary/10 rounded-full translate-y-1/4 -translate-x-1/4 blur-3xl"></div>
            <div class="absolute top-1/2 right-1/4 w-[15rem] h-[15rem] bg-yellow-400/5 rounded-full -translate-y-1/2 blur-2xl"></div>
            
            <!-- Book decorations -->
            <div class="hidden lg:block absolute top-20 left-20 w-16 h-24 bg-primary/30 rounded-r-lg rounded-l-sm transform -rotate-12 border-l-4 border-primary shadow-xl"></div>
            <div class="hidden lg:block absolute top-24 left-24 w-16 h-20 bg-secondary/30 rounded-r-lg rounded-l-sm transform -rotate-6 border-l-4 border-secondary shadow-xl"></div>
            <div class="hidden lg:block absolute bottom-20 right-20 w-16 h-24 bg-primary/30 rounded-r-lg rounded-l-sm transform rotate-12 border-l-4 border-primary shadow-xl"></div>
            <div class="hidden lg:block absolute bottom-24 right-24 w-16 h-20 bg-secondary/30 rounded-r-lg rounded-l-sm transform rotate-6 border-l-4 border-secondary shadow-xl"></div>
            
            <div class="container mx-auto px-4 relative">
                <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 md:p-12 lg:p-16 max-w-4xl mx-auto border border-white/40 relative overflow-hidden">
                    <!-- Inner decorative accents -->
                    <div class="absolute top-0 left-0 w-24 h-24 border-t-8 border-l-8 border-primary/30 rounded-tl-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-24 h-24 border-b-8 border-r-8 border-secondary/30 rounded-br-3xl"></div>
                
                    <div class="text-center relative z-10">
                        <span class="inline-block px-6 py-2 bg-primary/10 text-primary font-semibold mb-6 rounded-full transform hover:scale-105 transition-transform duration-300">Ready to make borrowing books easier?</span>
                        
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-800 mb-6 leading-tight">
                            Sign up and get <span class="relative inline-block text-primary">
                                INSTANT
                                <span class="absolute -bottom-1 left-0 w-full h-2 bg-gradient-to-r from-primary via-secondary to-primary rounded-full"></span>
                            </span> access to your library.
                        </h2>
                        
                        <p class="text-gray-600 text-lg mb-8 max-w-2xl mx-auto">Join thousands of students already enjoying our vast collection of books, journals, and educational resources.</p>
                        
                        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                            <a href="./pages/signup/index.php" class="relative overflow-hidden group px-8 py-4 bg-gradient-to-r from-primary to-secondary text-white text-lg font-bold rounded-xl shadow-xl transition-all duration-500 transform hover:scale-105 hover:shadow-2xl">
                                <span class="relative z-10 flex items-center">
                                    Sign Up Now
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </span>
                                <span class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-500"></span>
                            </a>
                            
                            <a href="#features" class="px-8 py-4 border-2 border-primary/30 text-primary font-semibold rounded-xl hover:bg-primary/5 transition-colors duration-300">
                                Learn More
                            </a>
                        </div>
                        
                        <!-- Indicators of community -->
                        <div class="mt-8 flex justify-center items-center">
                            <div class="flex -space-x-2 mr-4">
                                <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">JD</div>
                                <div class="w-8 h-8 rounded-full bg-red-500 border-2 border-white flex items-center justify-center text-white text-xs">SM</div>
                                <div class="w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">AK</div>
                                <div class="w-8 h-8 rounded-full bg-yellow-500 border-2 border-white flex items-center justify-center text-white text-xs">+</div>
                            </div>
                            <span class="text-sm text-gray-600">Join <span class="font-medium text-primary">5,000+ students</span> already using Garden Library</span>
                        </div>
                    </div>
                </div>            </div>        </section>    
    </div>
</div>
    </div>
</div>
</main>
</div>
<!-- Removed white space between CTA and footer -->
<?php
// Include the footer
include($include_path . 'includes/footer.php');
?>
