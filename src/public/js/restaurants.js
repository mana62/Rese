"use strict";

document.addEventListener("DOMContentLoaded", () => {});

function toggleFavorite(button, restaurantId) {
    const isFavorited = button.classList.contains("favorited");
    button.classList.toggle("favorited");
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
                button.classList.toggle("favorited");
                alert("お気に入りの更新に失敗しました");
            }
        })

        .catch((error) => {
            console.error("Error:", error);
            button.classList.toggle("favorited");
            alert("ネットワークエラーが発生しました");
        });
}
