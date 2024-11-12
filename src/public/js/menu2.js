document.getElementById('menuIcon').addEventListener('click', function () {
    const menu = document.getElementById('menu2');
    menu.classList.add('active');
});

document.getElementById('closeMenu').addEventListener('click', function () {
    const menu = document.getElementById('menu2');
    menu.classList.remove('active');
});

