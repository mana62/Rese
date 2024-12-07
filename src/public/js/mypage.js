"use strict";

//キャンセル処理部分
//サーバーにキャンセルリクエストを送る(cancelReservation)
//(reservationId)引数に指定
function cancelReservation(reservationId) {
    //サーバーにキャンセルリクエストを送る
    //fetch：URLにリクエストを送る関数
    fetch(`/reservations/${reservationId}/cancel`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            "Content-Type": "application/json",
        },
    })
        //サーバーからの返事を受け取りエラーの場合
        .then((response) => {
            if (!response.ok) {
                throw new Error("キャンセルに失敗しました");
            }

            //サーバーの返事をJSON形式で返す
            return response.json();
        })

        //成功の場合
        .then((data) => {
            if (data.message) {
                //メッセージを表示
                let messageContainer =
                    document.querySelector(".message-session");
                if (!messageContainer) {
                    //メッセージ表示欄がない場合新しく作る
                    messageContainer = document.createElement("div");
                    messageContainer.className = "message-session";
                    document
                        .querySelector(".message")
                        .appendChild(messageContainer);
                }

                //メッセージをセット
                messageContainer.innerText = data.message;

                //予約を削除
                const reservationCard = document.getElementById(
                    `reservation-card-${reservationId}`
                );

                //対象の予約カードを探す
                if (reservationCard) {
                    //対象のカードが見つかれば削除
                    reservationCard.remove();
                }
            }
        })

        //エラーが起きたときの処理
        .catch((error) => {
            console.error("Error:", error);
            alert("エラーが発生しました");
        });
}

//お気に入りの登録・解除部分（toggleFavorite）
function toggleFavorite(restaurantId) {
    fetch(`/restaurants/${restaurantId}/favorite`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            "Content-Type": "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("お気に入りの操作に失敗しました");
            }
            return response.json();
        })
        .then((data) => {
            if (data.status === "removed") {
                //お気に入り解除の場合
                const favoriteCard = document.getElementById(
                    `favorite-card-${restaurantId}`
                );

                //該当のレストランカードを探す
                if (favoriteCard) {
                    //削除
                    favoriteCard.remove();
                }

                //成功時
            } else if (data.status === "added") {
                alert("お気に入りに追加されました");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("エラーが発生しました");
        });

    //決済ステータス
    document.addEventListener("DOMContentLoaded", function () {
        const stripePayments = document.querySelectorAll("[data-status]");

        //各要素を順番に処理
        stripePayments.forEach((stripePayment) => {
            const status = stripePayment.dataset.status;

            if (status === "success") {
                //テキストを変更
                stripePayment.innerText = "支払い済み";
                //クリックを無効化
                stripePayment.style.pointerEvents = "none";
                //ボタンの見た目も無効にする
                stripePayment.style.cursor = "not-allowed";
            }
        });
    });
}
