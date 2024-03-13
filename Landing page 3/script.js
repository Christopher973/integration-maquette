let btn = document.querySelector(".menu-btn");
let nav = document.querySelector(".navigation");
let close = document.querySelector(".close-btn");

btn.addEventListener('click', () => {
    nav.classList.toggle("active");
    btn.classList.toggle("show")
    close.classList.toggle("close")
});

close.addEventListener('click', () => {
    nav.classList.toggle("active");
    btn.classList.toggle("show")
    close.classList.toggle("close")
});