"use strict";

document.addEventListener("DOMContentLoaded", () => {});

//お気に入りボタンの状態を切り替える関数(toggleFavorite)
function toggleFavorite(button, restaurantId) {
    //現在「お気に入り」状態かを確認
    const isFavorited = button.classList.contains("favorited");

    //ボタンの状態を切り替える(classList.toggle)
    button.classList.toggle("favorited");

    //サーバーに「お気に入り」状態を送る
    fetch(`/restaurants/${restaurantId}/favorite`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json", //josn形式で送信
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        //サーバーから返ってきた情報を JSON形式で読み取る
        .then((response) => response.json())
        .then((data) => {
            if (data.status !== "added" && data.status !== "removed") {
                //サーバーからの返事が失敗だった場合
                button.classList.toggle("favorited");
                alert("お気に入りの更新に失敗しました");
            }
        })

        //エラー時の場合
        .catch((error) => {
            console.error("Error:", error);
            button.classList.toggle("favorited");
            alert("ネットワークエラーが発生しました");
        });
}
