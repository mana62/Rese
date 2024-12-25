'use strict';

document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating .star');
    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            stars.forEach((s, i) => {
                s.style.color = i <= index ? 'gold' : '#ccc';
            });
        });
    });
});