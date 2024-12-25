"use strict";

function cancelReservation(reservationId) {
    fetch(`/reservations/${reservationId}/cancel`, {
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
                throw new Error("キャンセルに失敗しました");
            }

            return response.json();
        })

        .then((data) => {
            if (data.message) {
                let messageContainer =
                    document.querySelector(".message-session");
                if (!messageContainer) {
                    messageContainer = document.createElement("div");
                    messageContainer.className = "message-session";
                    document
                        .querySelector(".message")
                        .appendChild(messageContainer);
                }
                messageContainer.innerText = data.message;
                const reservationCard = document.getElementById(
                    `reservation-card-${reservationId}`
                );
                if (reservationCard) {
                    reservationCard.remove();
                }
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("エラーが発生しました");
        });
}
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
                const favoriteCard = document.getElementById(
                    `favorite-card-${restaurantId}`
                );
                if (favoriteCard) {
                    favoriteCard.remove();
                }

            } else if (data.status === "added") {
                alert("お気に入りに追加されました");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("エラーが発生しました");
        });

    document.addEventListener("DOMContentLoaded", function () {
        const stripePayments = document.querySelectorAll("[data-status]");

        stripePayments.forEach((stripePayment) => {
            const status = stripePayment.dataset.status;

            if (status === "success") {
                stripePayment.innerText = "支払い済み";
                stripePayment.style.pointerEvents = "none";ƒ
                stripePayment.style.cursor = "not-allowed";
            }
        });
    });
}
