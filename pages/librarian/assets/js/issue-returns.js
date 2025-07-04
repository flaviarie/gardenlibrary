// Issue and Returns Management
function returnBook(bookId, userId, title) {
    if (confirm('Are you sure you want to return "' + title + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="book_id" value="' + bookId + '"><input type="hidden" name="user_id" value="' + userId + '"><input type="hidden" name="return_book" value="1">';
        document.body.appendChild(form);
        form.submit();
    }
}
