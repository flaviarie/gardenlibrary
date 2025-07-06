// Book Management JavaScript - Tailwind Enhanced

// Modal Functions
function showBookDetails(book) {
    // Populate book details
    document.getElementById('details_book_id').textContent = book.book_id;
    document.getElementById('details_title').textContent = book.title;
    document.getElementById('details_author').textContent = book.author;
    document.getElementById('details_category').textContent = getCategoryName(book.category);
    document.getElementById('details_publish_date').textContent = formatDate(book.publish_date);
    document.getElementById('details_description').textContent = book.description || 'No description available.';
    
    // Set book cover
    const coverImg = document.getElementById('details_book_cover');
    if (book.book_cover && book.book_cover !== 'default_book_cover.svg') {
        coverImg.src = '../assets/img/' + book.book_cover;
        coverImg.alt = book.title + ' cover';
    } else {
        coverImg.src = '../assets/img/default_book_cover.svg';
        coverImg.alt = 'Default book cover';
    }
    
    // Handle image error
    coverImg.onerror = function() {
        this.src = '../assets/img/default_book_cover.svg';
        this.alt = 'Default book cover';
    };
    
    // Set status with styling
    const statusElement = document.getElementById('details_status');
    statusElement.textContent = book.status.charAt(0).toUpperCase() + book.status.slice(1);
    
    // Remove existing status classes
    statusElement.className = 'text-sm font-semibold px-2 py-1 rounded-full';
    
    switch(book.status) {
        case 'available':
            statusElement.classList.add('bg-green-100', 'text-green-800');
            break;
        case 'borrowed':
            statusElement.classList.add('bg-red-100', 'text-red-800');
            break;
        case 'reserved':
            statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
            break;
        default:
            statusElement.classList.add('bg-gray-100', 'text-gray-800');
    }
    
    // Set button actions
    document.getElementById('details_edit_btn').onclick = function(e) {
        e.preventDefault();
        closeBookDetailsModal();
        setTimeout(() => {
            editBook(book.book_id, book.title, book.author, book.description, book.publish_date, book.category);
        }, 300);
    };
    
    document.getElementById('details_delete_btn').onclick = function(e) {
        e.preventDefault();
        closeBookDetailsModal();
        setTimeout(() => {
            deleteBook(book.book_id, book.title);
        }, 300);
    };
    
    // Show modal
    const modal = document.getElementById('bookDetailsModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBookDetailsModal() {
    const modal = document.getElementById('bookDetailsModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

function editBook(bookId, title, author, description, publishDate, category) {
    try {
        // Populate form
        document.getElementById('edit_book_id').value = bookId || '';
        document.getElementById('edit_title').value = title || '';
        document.getElementById('edit_author').value = author || '';
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_publish_date').value = publishDate || '';
        document.getElementById('edit_category').value = category || '';
        
        // Show edit modal
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Focus first input
        setTimeout(() => {
            document.getElementById('edit_title').focus();
        }, 100);
        
    } catch (error) {
        console.error('Error in editBook:', error);
        alert('Error opening edit form. Please try again.');
    }
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

function deleteBook(bookId, title) {
    // Create custom confirmation popup
    const confirmed = confirm(`Are you sure you want to delete "${title}"?\n\nThis action cannot be undone.`);
    
    if (confirmed) {
        // Show loading state
        const deleteBtn = document.getElementById('details_delete_btn');
        if (deleteBtn) {
            deleteBtn.innerHTML = '<div class="loading-spinner"></div> Deleting...';
            deleteBtn.disabled = true;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="book_id" value="${encodeURIComponent(bookId)}">
            <input type="hidden" name="delete_book" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function getCategoryName(category) {
    const categories = {
        'FIC': 'Fiction',
        'SCI': 'Science',
        'HIS': 'History',
        'TEC': 'Technology',
        'PHI': 'Philosophy',
        'BIO': 'Biography',
        'ART': 'Art',
        'REF': 'Reference',
        'KID': 'Kids',
        'OTH': 'Other'
    };
    return categories[category] || category;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (error) {
        return dateString;
    }
}

// Initialize modal event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Close modals on backdrop click
    ['bookDetailsModal', 'editModal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal || e.target.classList.contains('bg-gray-500')) {
                    if (modalId === 'bookDetailsModal') {
                        closeBookDetailsModal();
                    } else {
                        closeEditModal();
                    }
                }
            });
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const detailsModal = document.getElementById('bookDetailsModal');
            const editModal = document.getElementById('editModal');
            
            if (!detailsModal.classList.contains('hidden')) {
                closeBookDetailsModal();
            } else if (!editModal.classList.contains('hidden')) {
                closeEditModal();
            }
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('form[method="POST"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const title = form.querySelector('input[name="title"]');
            const author = form.querySelector('input[name="author"]');
            const publishDate = form.querySelector('input[name="publish_date"]');
            const category = form.querySelector('select[name="category"]');
            const bookCover = form.querySelector('input[name="book_cover"]');
            
            let isValid = true;
            let errorMessage = '';
            
            if (title && (title.value.trim().length < 2 || title.value.trim().length > 255)) {
                isValid = false;
                errorMessage += 'Title must be between 2 and 255 characters.\n';
            }
            
            if (author && (author.value.trim().length < 2 || author.value.trim().length > 255)) {
                isValid = false;
                errorMessage += 'Author name must be between 2 and 255 characters.\n';
            }
            
            if (publishDate && publishDate.value) {
                const selectedDate = new Date(publishDate.value);
                const today = new Date();
                if (selectedDate > today) {
                    isValid = false;
                    errorMessage += 'Publish date cannot be in the future.\n';
                }
            }
            
            if (category && !category.value) {
                isValid = false;
                errorMessage += 'Please select a category.\n';
            }
            
            if (bookCover && bookCover.name === 'book_cover' && !bookCover.files.length) {
                isValid = false;
                errorMessage += 'Please upload a book cover image.\n';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errorMessage);
                return false;
            }
        });
    });
    
    // File upload preview (optional enhancement)
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Could add preview functionality here
                    console.log('File selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    });
});
