/**
 * User Management Module JavaScript
 * Handles CRUD operations for user management
 */

class UserManager {
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
        document.getElementById('modalTitle').textContent = 'Add New User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('userModal').classList.remove('hidden');
    }

    closeModal() {
        document.getElementById('userModal').classList.add('hidden');
        this.editingUserId = null;
    }

    // User editing
    editUser(userId) {
        this.editingUserId = userId;
        document.getElementById('modalTitle').textContent = 'Edit User';
        
        this.fetchUserData(userId)
            .then(user => {
                document.getElementById('userId').value = user.user_id;
                document.getElementById('username').value = user.username;
                document.getElementById('email').value = user.email;
                document.getElementById('role').value = user.role;
                document.getElementById('password').value = '';
                document.getElementById('userModal').classList.remove('hidden');
            })
            .catch(() => {
                this.showAlert('Error loading user data', 'error');
            });
    }

    // API calls
    async fetchUserData(userId) {
        const response = await fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_user&user_id=${userId}`
        });
        return response.json();
    }

    async saveUser(formData) {
        const action = this.editingUserId ? 'update_user' : 'create_user';
        formData.append('action', action);
        
        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });
        return response.json();
    }

    async deleteUser(userId, username) {
        if (!confirm(`Delete user "${username}"? This action cannot be undone.`)) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'delete_user');
        formData.append('user_id', userId);

        try {
            const result = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(r => r.json());

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error deleting user', 'error');
        }
    }

    async suspendUser(userId) {
        if (!confirm('Are you sure you want to suspend this user account?')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'suspend_user');
        formData.append('user_id', userId);

        try {
            const result = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(r => r.json());

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error suspending user', 'error');
        }
    }

    async activateUser(userId) {
        if (!confirm('Are you sure you want to activate this user account?')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'activate_user');
        formData.append('user_id', userId);

        try {
            const result = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(r => r.json());

            if (result.success) {
                this.showAlert(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error activating user', 'error');
        }
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
        const role = document.getElementById('roleFilter').value;
        const status = document.getElementById('statusFilter').value;
        
        const params = new URLSearchParams(window.location.search);
        
        if (search) params.set('search', search);
        else params.delete('search');
        
        if (role) params.set('role', role);
        else params.delete('role');
        
        if (status) params.set('status', status);
        else params.delete('status');
        
        params.delete('page');
        window.location.search = params.toString();
    }

    clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('roleFilter').value = '';
        document.getElementById('statusFilter').value = '';
        
        const params = new URLSearchParams();
        window.location.search = params.toString();
    }

    // Event binding
    bindEvents() {
        // Form submission
        const form = document.getElementById('userForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(e);
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

    async handleFormSubmit(e) {
        const formData = new FormData(e.target);
        
        try {
            const result = await this.saveUser(formData);
            
            if (result.success) {
                this.showAlert(result.message, 'success');
                this.closeModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showAlert(result.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error saving user', 'error');
        }
    }

    setupFormValidation() {
        const form = document.getElementById('userForm');
        if (!form) return;

        const inputs = form.querySelectorAll('input[required]');
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
let userManager;

function openCreateModal() {
    userManager.openCreateModal();
}

function closeModal() {
    userManager.closeModal();
}

function editUser(userId) {
    userManager.editUser(userId);
}

function deleteUser(userId, username) {
    userManager.deleteUser(userId, username);
}

function applyFilters() {
    userManager.applyFilters();
}

function clearFilters() {
    userManager.clearFilters();
}

function suspendUser(userId) {
    userManager.suspendUser(userId);
}

function activateUser(userId) {
    userManager.activateUser(userId);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    userManager = new UserManager();
});
