// Reservations Management

function fulfillReservation(reservationId, title) {
    if (confirm('Are you sure you want to fulfill the reservation for "' + title + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="reservation_id" value="' + reservationId + '"><input type="hidden" name="fulfill_reservation" value="1">';
        document.body.appendChild(form);
        form.submit();
    }
}

function cancelReservation(reservationId, title) {
    if (confirm('Are you sure you want to cancel the reservation for "' + title + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="reservation_id" value="' + reservationId + '"><input type="hidden" name="cancel_reservation" value="1">';
        document.body.appendChild(form);
        form.submit();
    }
}
