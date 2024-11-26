'use strict';
function cancelReservation(reservationId) {
    fetch(`/reservations/${reservationId}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("キャンセルに失敗しました");
        }
        return response.json();
    })
    .then(data => {
        if (data.message) {
            //メッセージを表示、存在しない場合は新しく追加
            let messageContainer = document.querySelector('.message-session');
            if (!messageContainer) {
                messageContainer = document.createElement('div');
                messageContainer.className = 'message-session';
                document.querySelector('.message').appendChild(messageContainer);
            }
            messageContainer.innerText = data.message;

            //予約を削除
            const reservationCard = document.getElementById(`reservation-card-${reservationId}`);
            if (reservationCard) {
                reservationCard.remove();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("エラーが発生しました");
    });
}


function toggleFavorite(restaurantId) {
    fetch(`/restaurants/${restaurantId}/favorite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("お気に入りの操作に失敗しました");
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'removed') {
            // 成功時に該当カードを削除
            const favoriteCard = document.getElementById(`favorite-card-${restaurantId}`);
            if (favoriteCard) {
                favoriteCard.remove();
            }
        } else if (data.status === 'added') {
            alert("お気に入りに追加されました");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("エラーが発生しました");
    });
}

