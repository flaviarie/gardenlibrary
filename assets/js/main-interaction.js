/**
 * Main Interaction JS - External scripts extracted from index.php
 * This file contains all the interactive JavaScript functionality for the main index page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Timeline progress marker animations
    const timelineItems = document.querySelectorAll('.timeline-item-1, .timeline-item-2, .timeline-item-3, .timeline-item-4');
    const timelineMarkers = document.querySelectorAll('.timeline-progress-marker');
    
    if (timelineItems.length && timelineMarkers.length) {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const index = Array.from(timelineItems).indexOf(entry.target);
                    if (index >= 0 && index < timelineMarkers.length) {
                        timelineMarkers[index].classList.add('active');
                    }
                }
            });
        }, observerOptions);
        
        timelineItems.forEach(item => {
            observer.observe(item);
        });
    }
    
    // Book showcase animations
    const bookShowcase = document.getElementById('book-showcase');
    if (bookShowcase) {
        window.addEventListener('scroll', () => {
            const scrollPosition = window.scrollY;
            const rotateValue = Math.min(scrollPosition / 100, 5);
            bookShowcase.style.transform = `rotate(${rotateValue}deg)`;
        });
    }
    
    // Button hover effects
    const ctaButtons = document.querySelectorAll('.btn-cta');
    ctaButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            const hoverEffect = this.querySelector('.btn-hover-effect');
            if (hoverEffect) {
                hoverEffect.style.transform = 'translateX(100%)';
            }
        });
        
        button.addEventListener('mouseleave', function() {
            const hoverEffect = this.querySelector('.btn-hover-effect');
            if (hoverEffect) {
                hoverEffect.style.transform = 'translateX(-100%)';
            }
        });
    });
});
