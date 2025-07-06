<?php
header('Content-Type: text/css');
require_once('../../config/config.php');
?>

/* Dynamic CSS with correct paths for live server compatibility */
.hero-library-bg {
    position: relative;
    background-image: url('<?php echo $site_url; ?>assets/img/hero/new-library-hero.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    width: 100%;
    min-height: 90vh;
    display: flex;
    align-items: center;
    z-index: 0;
}

/* Animation keyframes with dynamic paths */
@keyframes bgFade {
    0%, 33% { background-image: url('<?php echo $site_url; ?>assets/img/hero/new-library-hero.jpg'); }
    33.01%, 66% { background-image: url('<?php echo $site_url; ?>assets/img/library-background-alt.jpg'); }
    66.01%, 100% { background-image: url('<?php echo $site_url; ?>assets/img/library-background-2.jpg'); }
}

/* Apply animation if not on mobile */
@media (min-width: 769px) {
    .hero-library-bg {
        animation: bgFade 30s ease-in-out infinite;
        animation-delay: 5s;
    }
}

.hero-library-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('<?php echo $site_url; ?>assets/img/hero/new-library-hero.jpg');
    background-size: cover;
    background-position: center;
    z-index: -2;
    opacity: 0.98;
    animation: slowPulse 4s ease-in-out infinite alternate;
}

/* Different background image for mobile devices */
@media (max-width: 768px) {
    .hero-library-bg {
        background-image: url('<?php echo $site_url; ?>assets/img/library-background-alt.jpg');
        background-position: center center;
        min-height: 100vh;
    }
}

/* Preload background images with correct paths */
body::after {
    position: absolute;
    width: 0;
    height: 0;
    overflow: hidden;
    z-index: -10;
    content: url('<?php echo $site_url; ?>assets/img/hero/new-library-hero.jpg') url('<?php echo $site_url; ?>assets/img/library-background-alt.jpg') url('<?php echo $site_url; ?>assets/img/library-background-2.jpg');
}

@keyframes slowPulse {
    0% { opacity: 0.97; }
    100% { opacity: 1; }
}

/* Enhanced glow for headings */
.text-secondary {
    filter: drop-shadow(0 0 8px rgba(225, 170, 116, 0.6));
    position: relative;
    display: inline-block;
    padding: 0 2px;
}

/* Highlight underline effect */
.text-secondary::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, transparent, rgba(225, 170, 116, 0.8), transparent);
    border-radius: 2px;
    animation: shine 2s ease-in-out infinite;
}

@keyframes shine {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.8; }
}
