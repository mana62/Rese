document.getElementById('menuIcon').addEventListener('click', function () {
    const menu = document.getElementById('menu');
    menu.classList.add('active');
});

document.getElementById('closeMenu').addEventListener('click', function () {
    const menu = document.getElementById('menu');
    menu.classList.remove('active');
});
