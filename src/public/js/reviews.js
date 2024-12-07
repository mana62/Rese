'use strict';

//bladeでページが読み込まれたら処理を実行（DOMContentLoaded）
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating .star');
    //(indexは星の番号）
    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            //全ての星を順番に確認して、クリックした星の番号まで色を変える
            stars.forEach((s, i) => {
                s.style.color = i <= index ? 'gold' : '#ccc';
            });
        });
    });
});