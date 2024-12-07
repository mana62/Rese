"use strict";

//QRコードをスキャンするためのオブジェクトを作る
const html5QrCode = new Html5Qrcode("reader");

//カメラを起動し、QRコードを読み取ったときに実行
html5QrCode.start({ facingMode: "environment" }, {}, (decodedText) => {
    //サーバーにQRコードのデータを送る
    fetch("/verify-qr-code", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ qr_data: decodedText }),
    })
        .then((response) => {
            if (!response.ok) {
                //サーバーが失敗を返したとき
                return response.json().then((data) => {
                    throw new Error(data.error);
                });
            }
            return response.json();
        })

        //成功
        .then((data) => {
            alert("予約確認完了: " + data.reservation.id);
        })

        //失敗
        .catch((error) => {
            alert("エラー: " + error.message);
        });
});
