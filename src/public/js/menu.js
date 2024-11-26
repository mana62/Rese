"use strict";

document.getElementById("drawer_toggle").addEventListener("click", function () {
    const menu = document.getElementById("menu");
    const toggle = document.getElementById("drawer_toggle");

    if (menu.classList.contains("active")) {
        menu.classList.remove("active");
        toggle.classList.remove("open");
    } else {
        menu.classList.add("active");
        toggle.classList.add("open");
    }
});

document.getElementById("closeMenu").addEventListener("click", function () {
    const menu = document.getElementById("menu");
    const toggle = document.getElementById("drawer_toggle");

    menu.classList.remove("active");
    toggle.classList.remove("open");
});
