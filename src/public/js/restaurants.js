document.querySelector(".nav-search__area-select").addEventListener("change", applyFilters);
document.querySelector(".nav-search__genre-select").addEventListener("change", applyFilters);
document.querySelector(".search__form-input").addEventListener("input", applyFilters);

function applyFilters() {
    const areaSelect = document.querySelector(".nav-search__area-select");
    const genreSelect = document.querySelector(".nav-search__genre-select");
    const inputField = document.querySelector(".search__form-input");

    if (!areaSelect || !genreSelect || !inputField) {
        console.error("Required elements are missing.");
        return;
    }

    const areaId = areaSelect.value;
    const genreId = genreSelect.value;
    const input = inputField.value;

    fetch(`/restaurants?area=${encodeURIComponent(areaId)}&genre=${encodeURIComponent(genreId)}&input=${encodeURIComponent(input)}`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.text();
    })
    .then((html) => {
        document.querySelector(".all-shop").innerHTML = html;
    })
    .catch((error) => console.error("Error:", error));
}
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
        body: JSON.stringify({ is_favorited: !isFavorited }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status !== "success") {
                button.classList.toggle("favorited");
                alert("お気に入りの更新に失敗しました");
            }
        });
}
