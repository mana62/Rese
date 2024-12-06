'use strict';

//入力確認部分
document.getElementById("reservationForm").addEventListener("input", function () {
    const date = document.getElementById("date").value;
    const time = document.getElementById("time").value;
    const guests = document.getElementById("guests").value;

    document.getElementById("confirmDateValue").textContent = date || "未選択";
    document.getElementById("confirmTimeValue").textContent = time || "未選択";
    document.getElementById("confirmGuestsValue").textContent = guests ? `${guests}人` : "未選択";
});
