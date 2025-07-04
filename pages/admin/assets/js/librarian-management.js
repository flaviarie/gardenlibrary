/**
 * Librarian Management Module JavaScript
 * Handles librarian promotion, demotion, and statistics
 */

class LibrarianManager {
    constructor() {
        this.editingUserId = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupFormValidation();
    }

    // Modal management
    openCreateModal() {
        this.editingUserId = null;
        document.getElementById('createForm').reset();
        document.getElementById('createModal').classList.remove('hidden');
    }

    openPromoteModal() {
        document.getElementById('promoteForm').reset();
        document.getElementById('promoteModal').classList.remove('hidden');
    }

    closeModal() {
        const modals = ['createModal', 'promoteModal', 'statsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        });
        this.editingUserId = null;
    }

    // Librarian operations
    async promoteToLibrarian() {
        const select = document.getElementById('promoteUser');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            this.showAlert('Please select a user to promote', 'warning');
            return;
        }

        const userId = selectedOption.value;
        const username = selectedOption.getAttribute('data-username');
        const email = selectedOption.getAttribute('data-email');

        const formData = new FormData();
        formData.append('action', 'promote_to_librarian');
        formData.append('user_id', userId);
        formData.append('username', username);
        formData.append('email', email);

        try {
            const result = await this.makeRequest(formData);
            
            if (result.success) {
                this.showAlert(result.message, 'success');
                this.closeModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error promoting user', 'error');
        }
    }

    async demoteLibrarian(userId, username, email) {
        if (!confirm(`Demote ${username} from librarian to regular user?`)) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'demote_librarian');
        formData.append('user_id', userId);
        formData.append('username', username);
        formData.append('email', email);

        try {
            const result = await this.makeRequest(formData);
            
            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error demoting librarian', 'error');
        }
    }

    async createLibrarian(formData) {
        formData.append('action', 'create_librarian');
        
        try {
            const result = await this.makeRequest(formData);
            
            if (result.success) {
                this.showAlert(result.message, 'success');
                this.closeModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error creating librarian', 'error');
        }
    }

    // Statistics
    async showLibrarianStats(userId, username) {
        try {
            const formData = new FormData();
            formData.append('action', 'get_librarian_stats');
            formData.append('user_id', userId);
            
            const stats = await this.makeRequest(formData);
            
            document.getElementById('statsModalTitle').textContent = `${username} - Statistics`;
            document.getElementById('statsContent').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-800">Total Issues</h4>
                        <p class="text-2xl font-bold text-blue-600">${stats.total_issues || 0}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-800">Total Returns</h4>
                        <p class="text-2xl font-bold text-green-600">${stats.total_returns || 0}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-orange-800">Active Issues</h4>
                        <p class="text-2xl font-bold text-orange-600">${stats.active_issues || 0}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('statsModal').classList.remove('hidden');
        } catch (error) {
            this.showAlert('Error loading statistics', 'error');
        }
    }

    // API helper
    async makeRequest(formData) {
        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });
        return response.json();
    }

    // UI helpers
    showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = 'transform translate-x-full transition-transform duration-300 mb-2';
        
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        
        const bgColor = colors[type] || colors.info;
        const icon = icons[type] || icons.info;
        
        alertDiv.innerHTML = `
            <div class="flex items-center text-white ${bgColor} px-4 py-3 rounded-lg">
                <i class="fas fa-${icon} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            alertDiv.classList.add('translate-x-full');
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    }

    // Filters and search
    applyFilters() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        
        const params = new URLSearchParams(window.location.search);
        
        if (search) params.set('search', search);
        else params.delete('search');
        
        if (status) params.set('status', status);
        else params.delete('status');
        
        params.delete('page');
        window.location.search = params.toString();
    }

    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        
        const params = new URLSearchParams();
        window.location.search = params.toString();
    }

    // Event binding
    bindEvents() {
        // Create form submission
        const createForm = document.getElementById('createForm');
        if (createForm) {
            createForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                this.createLibrarian(formData);
            });
        }

        // Promote form submission
        const promoteForm = document.getElementById('promoteForm');
        if (promoteForm) {
            promoteForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.promoteToLibrarian();
            });
        }

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.applyFilters();
                }
            });
        }
    }

    setupFormValidation() {
        const createForm = document.getElementById('createForm');
        if (!createForm) return;

        const inputs = createForm.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
            });
        });
    }

    validateField(input) {
        const value = input.value.trim();
        const isValid = value.length > 0;
        
        if (isValid) {
            input.classList.remove('border-red-500');
            input.classList.add('border-green-500');
        } else {
            input.classList.remove('border-green-500');
            input.classList.add('border-red-500');
        }
        
        return isValid;
    }
}

// Global functions for backward compatibility
let librarianManager;

function viewStats(userId) {
    librarianManager.showLibrarianStats(userId, 'Librarian');
}

function closeStatsModal() {
    librarianManager.closeModal();
}

function openCreateModal() {
    librarianManager.openCreateModal();
}

function closeCreateModal() {
    librarianManager.closeModal();
}

function openPromoteModal() {
    librarianManager.openPromoteModal();
}

function closePromoteModal() {
    librarianManager.closeModal();
}

function promoteToLibrarian() {
    librarianManager.promoteToLibrarian();
}

function demoteLibrarian(userId, username, email) {
    librarianManager.demoteLibrarian(userId, username, email);
}

function showLibrarianStats(userId, username) {
    librarianManager.showLibrarianStats(userId, username);
}

function applyFilters() {
    librarianManager.applyFilters();
}

function clearFilters() {
    librarianManager.clearFilters();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    librarianManager = new LibrarianManager();
});
