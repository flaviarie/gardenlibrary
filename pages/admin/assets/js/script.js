// Enhanced Admin Dashboard JavaScript with 60/30/10 Color Scheme
// Primary Blue (#3B82F6) - 60%, Secondary Gray (#6B7280) - 30%, Accent Red (#EF4444) - 10%

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminDashboard();
});

function initializeAdminDashboard() {
    // Initialize loading overlay
    initializeLoadingOverlay();
    
    // Initialize responsive features
    initializeResponsiveFeatures();
    
    // Initialize notifications
    initializeNotifications();
    
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize charts if available
    initializeCharts();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize form validation
    initializeFormValidation();
    
    console.log('Admin dashboard initialized successfully');
}

function initializeLoadingOverlay() {
    const loadingOverlay = document.getElementById('page-loading');
    
    // Show loading overlay
    function showLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.remove('hidden');
        }
    }
    
    // Hide loading overlay
    function hideLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.add('fade-out');
            setTimeout(() => {
                loadingOverlay.classList.add('hidden');
                loadingOverlay.remove();
            }, 500);
        }
    }
    
    // Hide loading overlay when page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(hideLoading, 800);
    });
    
    // Global loading functions
    window.showLoading = showLoading;
    window.hideLoading = hideLoading;
}

function initializeResponsiveFeatures() {
    // Mobile menu functionality
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-menu-overlay');
    const closeSidebar = document.getElementById('close-sidebar');

    function toggleSidebar() {
        if (sidebar) {
            sidebar.classList.toggle('-translate-x-full');
        }
        if (overlay) {
            overlay.classList.toggle('hidden');
        }
        document.body.classList.toggle('overflow-hidden');
    }

    function closeSidebarMenu() {
        if (sidebar) {
            sidebar.classList.add('-translate-x-full');
        }
        if (overlay) {
            overlay.classList.add('hidden');
        }
        document.body.classList.remove('overflow-hidden');
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleSidebar);
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', closeSidebarMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebarMenu);
    }
    
    // Enhanced touch interactions for mobile
    const touchElements = document.querySelectorAll('button, .cursor-pointer, a[class*="hover:"]');
    touchElements.forEach(element => {
        // Add minimum touch target size
        if (window.innerWidth <= 768) {
            const rect = element.getBoundingClientRect();
            if (rect.height < 44) {
                element.style.minHeight = '44px';
                element.style.display = 'flex';
                element.style.alignItems = 'center';
                element.style.justifyContent = 'center';
            }
        }
        
        // Add touch feedback
        element.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        }, { passive: true });
        
        element.addEventListener('touchend', function() {
            this.style.transform = '';
        }, { passive: true });
    });
}

function initializeNotifications() {
    // Create notification container if it doesn't exist
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        notificationContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(notificationContainer);
    }
    
    // Global notification function
    window.showNotification = function(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `
            notification bg-white rounded-lg shadow-lg border-l-4 p-4 
            transform transition-all duration-300 translate-x-full
            ${type === 'success' ? 'border-green-500' : 
              type === 'error' ? 'border-red-500' : 
              type === 'warning' ? 'border-yellow-500' : 'border-blue-500'}
        `;
        
        const icon = type === 'success' ? 'check-circle' : 
                    type === 'error' ? 'exclamation-triangle' : 
                    type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        
        const iconColor = type === 'success' ? 'text-green-500' : 
                         type === 'error' ? 'text-red-500' : 
                         type === 'warning' ? 'text-yellow-500' : 'text-blue-500';
        
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${icon} ${iconColor} mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        notificationContainer.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    };
}

function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip absolute z-50 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 pointer-events-none transition-opacity duration-200';
        tooltip.textContent = element.getAttribute('data-tooltip');
        
        element.addEventListener('mouseenter', function() {
            document.body.appendChild(tooltip);
            const rect = element.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            tooltip.classList.remove('opacity-0');
        });
        
        element.addEventListener('mouseleave', function() {
            tooltip.classList.add('opacity-0');
            setTimeout(() => {
                if (tooltip.parentNode) {
                    tooltip.parentNode.removeChild(tooltip);
                }
            }, 200);
        });
    });
}

function initializeCharts() {
    // Initialize chart containers if Chart.js is available
    if (typeof Chart !== 'undefined') {
        const chartContainers = document.querySelectorAll('.chart-container canvas');
        
        chartContainers.forEach(canvas => {
            const ctx = canvas.getContext('2d');
            
            // Default chart configuration with 60/30/10 color scheme
            const defaultConfig = {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Data',
                        data: [],
                        borderColor: '#3B82F6', // Primary Blue
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#D1D5DB' // Secondary Gray Light
                            }
                        },
                        x: {
                            grid: {
                                color: '#D1D5DB' // Secondary Gray Light
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#6B7280' // Secondary Gray
                            }
                        }
                    }
                }
            };
            
            new Chart(ctx, defaultConfig);
        });
    }
}

function initializeSearch() {
    const searchInputs = document.querySelectorAll('input[type="search"], .search-input');
    
    searchInputs.forEach(input => {
        const searchButton = input.parentElement.querySelector('.search-btn');
        
        // Search on Enter key
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(input.value);
            }
        });
        
        // Search on button click
        if (searchButton) {
            searchButton.addEventListener('click', function() {
                performSearch(input.value);
            });
        }
        
        // Live search (debounced)
        let searchTimeout;
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (input.value.length > 2 || input.value.length === 0) {
                    performSearch(input.value);
                }
            }, 300);
        });
    });
}

function performSearch(query) {
    console.log('Performing search:', query);
    // Implementation depends on specific page
}

function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                showNotification('Please correct the errors in the form', 'error');
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(input);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Email validation
    if (type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        errorMessage = 'Please enter a valid email address';
    }
    
    // Password validation
    if (type === 'password' && value && value.length < 6) {
        isValid = false;
        errorMessage = 'Password must be at least 6 characters long';
    }
    
    // Display validation result
    showFieldValidation(field, isValid, errorMessage);
    
    return isValid;
}

function showFieldValidation(field, isValid, errorMessage) {
    const errorElement = field.parentElement.querySelector('.field-error');
    
    if (isValid) {
        field.classList.remove('border-red-500');
        field.classList.add('border-green-500');
        if (errorElement) {
            errorElement.remove();
        }
    } else {
        field.classList.remove('border-green-500');
        field.classList.add('border-red-500');
        
        if (!errorElement) {
            const error = document.createElement('div');
            error.className = 'field-error text-red-500 text-sm mt-1';
            error.textContent = errorMessage;
            field.parentElement.appendChild(error);
        } else {
            errorElement.textContent = errorMessage;
        }
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Modal functionality
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Focus trap
        const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (focusableElements.length) {
            focusableElements[0].focus();
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Data table functionality
function initializeDataTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    // Add sorting functionality
    const headers = table.querySelectorAll('th[data-sortable]');
    headers.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const column = this.dataset.sortable;
            const currentOrder = this.dataset.order || 'asc';
            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            
            // Update header
            headers.forEach(h => {
                h.classList.remove('sorted-asc', 'sorted-desc');
                h.removeAttribute('data-order');
            });
            
            this.setAttribute('data-order', newOrder);
            this.classList.add(`sorted-${newOrder}`);
            
            // Sort table
            sortTable(table, column, newOrder);
        });
    });
}

function sortTable(table, column, order) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aValue = a.querySelector(`[data-column="${column}"]`)?.textContent || '';
        const bValue = b.querySelector(`[data-column="${column}"]`)?.textContent || '';
        
        const comparison = aValue.localeCompare(bValue);
        return order === 'asc' ? comparison : -comparison;
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

function formatDate(date) {
    return new Intl.DateTimeFormat().format(new Date(date));
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// API helpers
function makeRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    return fetch(url, finalOptions)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('Request failed:', error);
            showNotification('Request failed. Please try again.', 'error');
            throw error;
        });
}

// Export functions for global use
window.AdminDashboard = {
    openModal,
    closeModal,
    showNotification,
    initializeDataTable,
    makeRequest,
    formatNumber,
    formatDate,
    formatCurrency
};

// Add CSS for additional styling
const additionalStyles = `
    .fade-out {
        opacity: 0;
        transform: translateY(-20px);
    }
    
    .sorted-asc::after {
        content: ' ↑';
        color: #3B82F6;
    }
    
    .sorted-desc::after {
        content: ' ↓';
        color: #3B82F6;
    }
    
    .field-error {
        animation: shake 0.5s;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .notification {
        max-width: 400px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .tooltip {
        z-index: 1000;
    }
`;

// Inject additional styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);
