document.getElementById('reservationForm').addEventListener('input', function() {
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const guests = document.getElementById('guests').value;

    document.getElementById('confirmDate').innerHTML = date ? `Date ${date}&nbsp;` : 'Date&nbsp';
    document.getElementById('confirmTime').innerHTML = time ? `Time ${time}&nbsp;` : 'Time&nbsp';
    document.getElementById('confirmGuests').innerHTML = guests ? `Number ${guests}人&nbsp;` : 'Number&nbsp';
});
