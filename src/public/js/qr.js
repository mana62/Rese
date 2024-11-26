'use strict';
const html5QrCode = new Html5Qrcode("reader");
html5QrCode.start({ facingMode: "environment" }, {}, (decodedText) => {
    fetch('/verify-qr-code', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ qr_data: decodedText })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => { throw new Error(data.error); });
        }
        return response.json();
    })
    .then(data => {
        alert("予約確認完了: " + data.reservation.id);
    })
    .catch(error => {
        alert("エラー: " + error.message);
    });
});
