document.getElementById('reservationForm').addEventListener('input', function() {
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const guests = document.getElementById('guests').value;

    document.getElementById('confirmDate').innerHTML = date ? `Date ${date}&nbsp;` : 'Date&nbsp';
    document.getElementById('confirmTime').innerHTML = time ? `Time ${time}&nbsp;` : 'Time&nbsp';
    document.getElementById('confirmGuests').innerHTML = guests ? `Number ${guests}人&nbsp;` : 'Number&nbsp';
});

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

//ストレージ
document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('image');
    const fileNameSpan = document.getElementById('file-name');

    if (!imageInput || !fileNameSpan) {
        console.error('必要な要素が見つかりません。');
        return;
    }

    imageInput.addEventListener('change', function () {
        const fileName = this.files.length > 0 ? this.files[0].name : '選択されていません';
        fileNameSpan.textContent = fileName; // ファイル名を表示
    });
});

