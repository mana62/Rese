document.addEventListener("DOMContentLoaded", () => {
    // ローカルストレージの機能を削除し、サーバーに状態を委ねる
});

// お気に入りボタンのトグル関数
function toggleFavorite(button, restaurantId) {
    const isFavorited = button.classList.contains("favorited");

    // 状態をトグル
    button.classList.toggle("favorited");

    // サーバーへのリクエスト
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
                // サーバー側の処理が失敗した場合は、元の状態に戻す
                button.classList.toggle("favorited"); // 状態を元に戻す
                alert("お気に入りの更新に失敗しました");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            button.classList.toggle("favorited"); // エラーの場合も状態を元に戻す
            alert("ネットワークエラーが発生しました");
        });
}
