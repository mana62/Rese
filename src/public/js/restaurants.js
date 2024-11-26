'use strict';
document.addEventListener("DOMContentLoaded", () => {
    // ローカルストレージの機能を削除し、サーバーに状態を委ねる
});

// お気に入りボタンのトグル関数
function toggleFavorite(button, restaurantId) {
    const isFavorited = button.classList.contains("favorited");

    // ボタンの状態を切り替える
    button.classList.toggle("favorited");

    // サーバーにお気に入り操作をリクエスト
    fetch(`/restaurants/${restaurantId}/favorite`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status !== "added" && data.status !== "removed") {
                // サーバーエラー時に状態を元に戻す
                button.classList.toggle("favorited");
                alert("お気に入りの更新に失敗しました");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            button.classList.toggle("favorited"); // ネットワークエラー時も元に戻す
            alert("ネットワークエラーが発生しました");
        });
}
