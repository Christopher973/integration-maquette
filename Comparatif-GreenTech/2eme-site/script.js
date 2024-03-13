// Librairie pour les animations
AOS.init({

    once: true

});

// Menu responsive
let toggle = document.querySelector(".toggle");
let body = document.querySelector("body");

toggle.addEventListener('click', function(){
    body.classList.toggle('open');
});
///////////////////////////////////

// background du nav au scroll
window.addEventListener("scroll", function(){
    var header = this.document.querySelector("header");
    const btnScroll = document.querySelector("#btn-scroll");

    header.classList.toggle("sticky", window.scrollY > 20 );
    btnScroll.classList.toggle("active", window.scrollY > 600);
    
});
///////////////////////////////////

// Bouton de scroll
const btnScroll = document.querySelector("#btn-scroll");
    btnScroll.addEventListener("click", function(){
    window.scrollTo(0, 0);
});
