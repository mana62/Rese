'use strict';
//ストレージ
document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById("image");
    const fileNameSpan = document.getElementById("file-name");

    //要素が存在しない場合のエラー処理
    if (!imageInput || !fileNameSpan) {
        console.error("値が見つかりません");
        return;
    }

    //ファイル選択時の処理
    imageInput.addEventListener("change", function () {
        const fileName =
            this.files.length > 0 ? this.files[0].name : "選択されていません";
        fileNameSpan.textContent = fileName; //ファイル名を表示
    });
});

//入力確認部分
document.getElementById("reservationForm").addEventListener("input", function () {
    const date = document.getElementById("date").value;
    const time = document.getElementById("time").value;
    const guests = document.getElementById("guests").value;

    document.getElementById("confirmDateValue").textContent = date || "未選択";
    document.getElementById("confirmTimeValue").textContent = time || "未選択";
    document.getElementById("confirmGuestsValue").textContent = guests ? `${guests}人` : "未選択";
});
